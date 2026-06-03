<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PaymentChannel;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Services\QrisService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class MerchantApiController extends Controller
{
    public function getWebhookConfig(Request $request): JsonResponse
    {
        $tenant = $this->authenticateTenant($request);
        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'webhook_url' => $tenant->webhook_url,
                'webhook_enabled' => (bool) $tenant->webhook_enabled,
                'mode' => $tenant->mode,
                'is_active' => (bool) $tenant->is_active,
                'secret_masked' => $this->maskSecret($tenant->webhook_secret),
                'updated_at' => optional($tenant->updated_at)->toIso8601String(),
            ],
        ]);
    }

    public function listWebhookEvents(Request $request): JsonResponse
    {
        $tenant = $this->authenticateTenant($request);
        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'status' => 'nullable|in:sent,pending,failed',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $limit = (int) ($validated['limit'] ?? 20);
        $status = $validated['status'] ?? null;

        $query = Transaction::where('tenant_id', $tenant->id)
            ->whereNotNull('payment_reference')
            ->latest('id');

        if ($status === 'sent') {
            $query->where('webhook_sent', true);
        }

        if ($status === 'pending') {
            $query->where('webhook_sent', false)
                ->whereIn('status', ['pending', 'processing']);
        }

        if ($status === 'failed') {
            $query->where('webhook_sent', false)
                ->where('webhook_attempts', '>', 0);
        }

        $events = $query->limit($limit)->get()->map(function (Transaction $trx) {
            $eventStatus = $trx->webhook_sent
                ? 'sent'
                : (($trx->webhook_attempts ?? 0) > 0 ? 'failed' : 'pending');

            return [
                'event_id' => $trx->transaction_id,
                'transaction_id' => $trx->transaction_id,
                'invoice_number' => $trx->payment_reference,
                'event_type' => 'payment_status_update',
                'status' => $eventStatus,
                'transaction_status' => $trx->status,
                'attempts' => (int) ($trx->webhook_attempts ?? 0),
                'response' => $trx->webhook_response,
                'sent_at' => optional($trx->webhook_sent_at)->toIso8601String(),
                'updated_at' => optional($trx->updated_at)->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $events->count(),
            'data' => $events,
        ]);
    }

    public function getChannels(Request $request): JsonResponse
    {
        $tenant = $this->authenticateTenant($request);
        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $channels = $tenant->paymentChannels()
            ->where('is_active', true)
            ->get()
            ->map(function ($channel) {
                // Determine logo url based on provider code via MasterPaymentChannel
                $master = \App\Models\MasterPaymentChannel::where('code', $channel->provider)->first();
                $logoUrl = $master ? $master->logo_url : null;
                
                return [
                    'id' => $channel->id,
                    'type' => $channel->channel_type, // 'qris', 'ewallet', 'bank_transfer', etc
                    'code' => $channel->provider,     // 'bca_va', 'dana', etc
                    'name' => $channel->channel_name,
                    'provider_name' => $master ? $master->name : $channel->provider,
                    'fee_percentage' => (float) $channel->fee_percentage,
                    'fee_fixed' => (float) $channel->fee_fixed,
                    'logo_url' => $logoUrl,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $channels,
        ]);
    }

    public function updateWebhookConfig(Request $request): JsonResponse
    {
        $tenant = $this->authenticateTenant($request);
        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'webhook_url' => ['nullable', 'string', 'max:2048', 'regex:/^https?:\/\/.+/i'],
            'webhook_enabled' => 'nullable|boolean',
            'webhook_secret' => 'nullable|string|min:16|max:128',
            'rotate_secret' => 'nullable|boolean',
        ]);

        $before = [
            'webhook_url' => $tenant->webhook_url,
            'webhook_enabled' => (bool) $tenant->webhook_enabled,
            'webhook_secret' => $tenant->webhook_secret,
        ];

        if (array_key_exists('webhook_url', $validated)) {
            $tenant->webhook_url = $validated['webhook_url'] ?: null;
        }

        if (array_key_exists('webhook_enabled', $validated)) {
            $tenant->webhook_enabled = (bool) $validated['webhook_enabled'];
        }

        $secretChanged = false;
        if (!empty($validated['rotate_secret'])) {
            $tenant->webhook_secret = Str::random(32);
            $secretChanged = true;
        } elseif (array_key_exists('webhook_secret', $validated) && !empty($validated['webhook_secret'])) {
            $tenant->webhook_secret = $validated['webhook_secret'];
            $secretChanged = true;
        }

        $changedFields = [];
        if ($before['webhook_url'] !== $tenant->webhook_url) {
            $changedFields[] = 'webhook_url';
        }
        if ($before['webhook_enabled'] !== (bool) $tenant->webhook_enabled) {
            $changedFields[] = 'webhook_enabled';
        }
        if ($secretChanged) {
            $changedFields[] = 'webhook_secret';
        }

        $settings = is_array($tenant->settings) ? $tenant->settings : [];
        $settings['webhook_audit'] = [
            'updated_at' => now()->toIso8601String(),
            'updated_via' => 'merchant_api_v1',
            'api_key_type' => $this->detectApiKeyType((string) $request->bearerToken()),
            'updated_from_ip' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
            'changed_fields' => $changedFields,
        ];

        $tenant->settings = $settings;
        $tenant->save();

        return response()->json([
            'success' => true,
            'data' => [
                'webhook_url' => $tenant->webhook_url,
                'webhook_enabled' => (bool) $tenant->webhook_enabled,
                'secret_masked' => $this->maskSecret($tenant->webhook_secret),
                'audit' => $settings['webhook_audit'],
                'updated_at' => optional($tenant->updated_at)->toIso8601String(),
            ],
        ]);
    }

    public function retryWebhookEvent(Request $request, string $transactionId): JsonResponse
    {
        $tenant = $this->authenticateTenant($request);
        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $signedValidation = $this->validateSignedActionRequest($request, $tenant);
        if ($signedValidation) {
            return $signedValidation;
        }

        if (!$tenant->webhook_enabled) {
            return response()->json([
                'success' => false,
                'message' => 'Webhook is disabled for this merchant',
            ], 422);
        }

        if (empty($tenant->webhook_url)) {
            return response()->json([
                'success' => false,
                'message' => 'Webhook URL is not configured',
            ], 422);
        }

        $transaction = Transaction::where('tenant_id', $tenant->id)
            ->where(function ($q) use ($transactionId) {
                $q->where('transaction_id', $transactionId)
                    ->orWhere('external_id', $transactionId);
            })
            ->latest('id')
            ->first();

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
        }

        $payload = $this->buildWebhookPayload($tenant, $transaction);
        $result = $this->dispatchWebhook($tenant, $transaction, $payload, 'manual_retry');

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'data' => [
                'transaction_id' => $transaction->transaction_id,
                'webhook_status' => $result['success'] ? 'sent' : 'failed',
                'attempts' => $transaction->webhook_attempts,
                'sent_at' => optional($transaction->webhook_sent_at)->toIso8601String(),
                'response' => $transaction->webhook_response,
            ],
        ], $result['success'] ? 200 : 502);
    }

    public function createInvoice(Request $request): JsonResponse
    {
        $tenant = $this->authenticateTenant($request);
        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
            'external_id' => 'nullable|string|max:255',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'payment_channel_id' => 'nullable|integer|exists:payment_channels,id',
            'channel_type' => 'nullable|in:qris,gopay,dana,ovo,linkaja,shopeepay,bank_transfer,virtual_account',
            'expires_in_minutes' => 'nullable|integer|min:5|max:10080',
        ]);

        $channel = $this->resolveChannel($tenant, $validated['payment_channel_id'] ?? null, $validated['channel_type'] ?? null);
        if (!$channel) {
            return response()->json([
                'success' => false,
                'message' => 'No active payment channel found for this request',
            ], 422);
        }

        $idempotencyKey = $this->extractIdempotencyKey($request);
        $fingerprint = $this->buildIdempotencyFingerprint($validated, $channel);

        if ($idempotencyKey) {
            $existingTransaction = $this->findTransactionByIdempotencyKey($tenant, $idempotencyKey);
            if ($existingTransaction) {
                $existingFingerprint = data_get($existingTransaction->metadata, 'idempotency_fingerprint');

                if ($existingFingerprint && $existingFingerprint !== $fingerprint) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Idempotency key already used with different payload',
                    ], 409);
                }

                $existingInvoice = Invoice::where('tenant_id', $tenant->id)
                    ->where('id', (int) data_get($existingTransaction->metadata, 'invoice_id'))
                    ->first();

                if ($existingInvoice) {
                    $qrisSvg = null;
                    if ($channel->channel_type === 'qris' && !empty($existingInvoice->qris_string)) {
                        $qrisSvg = app(QrisService::class)->generateQRCode($existingInvoice->qris_string);
                    }

                    return response()->json([
                        'success' => true,
                        'idempotent_replayed' => true,
                        'data' => $this->buildInvoiceResponseData($existingInvoice, $existingTransaction, $channel, $qrisSvg),
                    ], 200);
                }
            }
        }

        $invoice = Invoice::create([
            'tenant_id' => $tenant->id,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_phone' => $validated['customer_phone'] ?? null,
            'description' => $validated['description'] ?? ('Merchant API - ' . ($validated['external_id'] ?? 'No external ID')),
            'amount' => $validated['amount'],
            'status' => 'pending',
            'expires_at' => isset($validated['expires_in_minutes'])
                ? now()->addMinutes((int) $validated['expires_in_minutes'])
                : now()->addDays(7),
        ]);

        $invoice->refresh();

        $qrisSvg = null;
        if ($channel->channel_type === 'qris') {
            $invoice->generateQris();
            $invoice->refresh();

            if (!empty($invoice->qris_string)) {
                $qrisSvg = app(QrisService::class)->generateQRCode($invoice->qris_string);
            }
        }

        $transaction = Transaction::create([
            'tenant_id' => $tenant->id,
            'payment_channel_id' => $channel->id,
            'external_id' => $validated['external_id'] ?? null,
            'amount' => $invoice->unique_amount,
            'fee' => 0,
            'net_amount' => $invoice->unique_amount,
            'status' => 'pending',
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_phone' => $validated['customer_phone'] ?? null,
            'payment_method' => $channel->channel_type_name,
            'payment_reference' => $invoice->invoice_number,
            'metadata' => [
                'source' => 'merchant_api_v1',
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'idempotency_key' => $idempotencyKey,
                'idempotency_fingerprint' => $fingerprint,
            ],
        ]);

        return response()->json([
            'success' => true,
            'idempotent_replayed' => false,
            'data' => $this->buildInvoiceResponseData($invoice, $transaction, $channel, $qrisSvg),
        ], 201);
    }

    public function getInvoice(Request $request, string $invoice): JsonResponse
    {
        $tenant = $this->authenticateTenant($request);
        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $invoiceModel = $this->findInvoiceForTenant($tenant, $invoice);
        if (!$invoiceModel) {
            return response()->json(['success' => false, 'message' => 'Invoice not found'], 404);
        }

        $transaction = Transaction::where('tenant_id', $tenant->id)
            ->where('payment_reference', $invoiceModel->invoice_number)
            ->latest('id')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'invoice_id' => $invoiceModel->id,
                'invoice_number' => $invoiceModel->invoice_number,
                'external_id' => $transaction?->external_id,
                'status' => $invoiceModel->status,
                'base_amount' => (float) $invoiceModel->amount,
                'pay_amount' => (float) $invoiceModel->unique_amount,
                'paid_at' => optional($invoiceModel->paid_at)->toIso8601String(),
                'expires_at' => optional($invoiceModel->expires_at)->toIso8601String(),
                'payment_source' => $invoiceModel->payment_source,
            ],
        ]);
    }

    public function cancelInvoice(Request $request, string $invoice): JsonResponse
    {
        $tenant = $this->authenticateTenant($request);
        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        $idempotencyKey = $this->extractIdempotencyKey($request);
        $fingerprint = hash('sha256', json_encode([
            'action' => 'cancel_invoice',
            'invoice' => $invoice,
            'reason' => (string) ($validated['reason'] ?? ''),
        ]));

        if ($idempotencyKey) {
            $replay = $this->resolveActionIdempotencyReplay($tenant, $idempotencyKey, $fingerprint);
            if ($replay) {
                return response()->json($replay['response'], (int) ($replay['http_status'] ?? 200));
            }
        }

        $invoiceModel = $this->findInvoiceForTenant($tenant, $invoice);
        if (!$invoiceModel) {
            return response()->json(['success' => false, 'message' => 'Invoice not found'], 404);
        }

        if ($invoiceModel->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending invoice can be cancelled',
            ], 422);
        }

        $reason = $validated['reason'] ?? 'Cancelled by merchant API';

        $invoiceModel->update([
            'status' => 'cancelled',
            'payment_source' => 'merchant_api_cancel',
            'payment_notification_text' => $reason,
            'expires_at' => now(),
        ]);

        $updatedTransactions = Transaction::where('tenant_id', $tenant->id)
            ->where(function ($q) use ($invoiceModel) {
                $q->whereJsonContains('metadata->invoice_id', $invoiceModel->id)
                    ->orWhere('payment_reference', $invoiceModel->invoice_number);
            })
            ->whereIn('status', ['pending', 'processing'])
            ->update([
                'status' => 'failed',
                'notes' => 'Cancelled by merchant API: ' . $reason,
                'webhook_sent' => false,
            ]);

        $response = [
            'success' => true,
            'data' => [
                'invoice_id' => $invoiceModel->id,
                'invoice_number' => $invoiceModel->invoice_number,
                'status' => $invoiceModel->status,
                'cancelled_at' => now()->toIso8601String(),
                'reason' => $reason,
                'affected_transactions' => $updatedTransactions,
            ],
        ];

        if ($idempotencyKey) {
            $this->storeActionIdempotencyResult($tenant, $idempotencyKey, $fingerprint, $response, 200);
        }

        return response()->json($response);
    }

    public function refundInvoice(Request $request, string $invoice): JsonResponse
    {
        $tenant = $this->authenticateTenant($request);
        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
            'send_webhook' => 'nullable|boolean',
        ]);

        $idempotencyKey = $this->extractIdempotencyKey($request);
        $fingerprint = hash('sha256', json_encode([
            'action' => 'refund_invoice',
            'invoice' => $invoice,
            'reason' => (string) ($validated['reason'] ?? ''),
            'send_webhook' => (bool) ($validated['send_webhook'] ?? true),
        ]));

        if ($idempotencyKey) {
            $replay = $this->resolveActionIdempotencyReplay($tenant, $idempotencyKey, $fingerprint);
            if ($replay) {
                return response()->json($replay['response'], (int) ($replay['http_status'] ?? 200));
            }
        }

        $invoiceModel = $this->findInvoiceForTenant($tenant, $invoice);
        if (!$invoiceModel) {
            return response()->json(['success' => false, 'message' => 'Invoice not found'], 404);
        }

        if ($invoiceModel->status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Only paid invoice can be refunded',
            ], 422);
        }

        $transaction = Transaction::where('tenant_id', $tenant->id)
            ->where(function ($q) use ($invoiceModel) {
                $q->whereJsonContains('metadata->invoice_id', $invoiceModel->id)
                    ->orWhere('payment_reference', $invoiceModel->invoice_number);
            })
            ->latest('id')
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Related transaction not found for this invoice',
            ], 404);
        }

        if ($transaction->status === 'refund') {
            return response()->json([
                'success' => false,
                'message' => 'Transaction already refunded',
            ], 409);
        }

        $reason = $validated['reason'] ?? 'Refunded by merchant API';
        $sendWebhook = (bool) ($validated['send_webhook'] ?? true);

        $metadata = is_array($transaction->metadata) ? $transaction->metadata : [];
        $metadata['refund'] = [
            'reason' => $reason,
            'refunded_at' => now()->toIso8601String(),
            'source' => 'merchant_api_v1',
        ];

        $transaction->update([
            'status' => 'refund',
            'notes' => $reason,
            'webhook_sent' => false,
            'webhook_sent_at' => null,
            'metadata' => $metadata,
        ]);

        $invoiceModel->update([
            'payment_source' => 'merchant_api_refund',
            'payment_notification_text' => $reason,
        ]);

        $webhook = [
            'attempted' => false,
            'sent' => false,
            'message' => 'Webhook not requested',
        ];

        if ($sendWebhook && $tenant->webhook_enabled && !empty($tenant->webhook_url)) {
            $payload = $this->buildWebhookPayload($tenant, $transaction);
            $result = $this->dispatchWebhook($tenant, $transaction, $payload, 'refund_update');

            $webhook = [
                'attempted' => true,
                'sent' => $result['success'],
                'message' => $result['message'],
            ];
        }

        $response = [
            'success' => true,
            'data' => [
                'invoice_id' => $invoiceModel->id,
                'invoice_number' => $invoiceModel->invoice_number,
                'transaction_id' => $transaction->transaction_id,
                'status' => $transaction->status,
                'reason' => $reason,
                'webhook' => $webhook,
            ],
        ];

        if ($idempotencyKey) {
            $this->storeActionIdempotencyResult($tenant, $idempotencyKey, $fingerprint, $response, 200);
        }

        return response()->json($response);
    }

    public function listInvoices(Request $request): JsonResponse
    {
        $tenant = $this->authenticateTenant($request);
        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'status' => 'nullable|in:pending,paid,cancelled,expired',
            'external_id' => 'nullable|string|max:255',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $limit = (int) ($validated['limit'] ?? 20);
        $externalId = $validated['external_id'] ?? null;

        $query = Invoice::where('tenant_id', $tenant->id)->latest('id');

        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        $invoices = $query->limit($limit)->get();

        $transactionMap = Transaction::where('tenant_id', $tenant->id)
            ->whereIn('payment_reference', $invoices->pluck('invoice_number')->all())
            ->latest('id')
            ->get()
            ->groupBy('payment_reference');

        $data = $invoices->map(function (Invoice $inv) use ($transactionMap, $externalId) {
            $trx = $transactionMap->get($inv->invoice_number)?->first();

            if ($externalId && (!$trx || $trx->external_id !== $externalId)) {
                return null;
            }

            return [
                'invoice_id' => $inv->id,
                'invoice_number' => $inv->invoice_number,
                'external_id' => $trx?->external_id,
                'status' => $inv->status,
                'base_amount' => (float) $inv->amount,
                'pay_amount' => (float) $inv->unique_amount,
                'paid_at' => optional($inv->paid_at)->toIso8601String(),
                'expires_at' => optional($inv->expires_at)->toIso8601String(),
            ];
        })->filter()->values();

        return response()->json([
            'success' => true,
            'count' => $data->count(),
            'data' => $data,
        ]);
    }

    public function listTransactions(Request $request): JsonResponse
    {
        $tenant = $this->authenticateTenant($request);
        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'status' => 'nullable|in:pending,processing,success,failed,expired,refund',
            'limit' => 'nullable|integer|min:1|max:100',
        ]);

        $limit = (int) ($validated['limit'] ?? 20);

        $query = Transaction::where('tenant_id', $tenant->id)
            ->with('paymentChannel')
            ->latest('id');

        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        $transactions = $query->limit($limit)->get()->map(function (Transaction $trx) {
            return [
                'transaction_id' => $trx->transaction_id,
                'external_id' => $trx->external_id,
                'status' => $trx->status,
                'amount' => (float) $trx->amount,
                'net_amount' => (float) $trx->net_amount,
                'payment_method' => $trx->payment_method,
                'channel' => [
                    'id' => $trx->payment_channel_id,
                    'name' => $trx->paymentChannel?->channel_name,
                    'type' => $trx->paymentChannel?->channel_type,
                ],
                'paid_at' => optional($trx->paid_at)->toIso8601String(),
                'created_at' => optional($trx->created_at)->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'count' => $transactions->count(),
            'data' => $transactions,
        ]);
    }

    public function getTransaction(Request $request, string $transactionId): JsonResponse
    {
        $tenant = $this->authenticateTenant($request);
        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $transaction = Transaction::where('tenant_id', $tenant->id)
            ->where(function ($q) use ($transactionId) {
                $q->where('transaction_id', $transactionId)
                    ->orWhere('external_id', $transactionId);
            })
            ->with('paymentChannel')
            ->latest('id')
            ->first();

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'transaction_id' => $transaction->transaction_id,
                'external_id' => $transaction->external_id,
                'status' => $transaction->status,
                'amount' => (float) $transaction->amount,
                'fee' => (float) $transaction->fee,
                'net_amount' => (float) $transaction->net_amount,
                'payment_method' => $transaction->payment_method,
                'payment_reference' => $transaction->payment_reference,
                'customer_name' => $transaction->customer_name,
                'customer_email' => $transaction->customer_email,
                'customer_phone' => $transaction->customer_phone,
                'channel' => [
                    'id' => $transaction->payment_channel_id,
                    'name' => $transaction->paymentChannel?->channel_name,
                    'type' => $transaction->paymentChannel?->channel_type,
                    'provider' => $transaction->paymentChannel?->provider,
                ],
                'paid_at' => optional($transaction->paid_at)->toIso8601String(),
                'expired_at' => optional($transaction->expired_at)->toIso8601String(),
                'created_at' => optional($transaction->created_at)->toIso8601String(),
            ],
        ]);
    }

    private function buildInvoiceResponseData(Invoice $invoice, Transaction $transaction, PaymentChannel $channel, ?string $qrisSvg): array
    {
        return [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'external_id' => $transaction->external_id,
            'status' => $invoice->status,
            'base_amount' => (float) $invoice->amount,
            'pay_amount' => (float) $invoice->unique_amount,
            'expires_at' => optional($invoice->expires_at)->toIso8601String(),
            'channel' => [
                'id' => $channel->id,
                'type' => $channel->channel_type,
                'name' => $channel->channel_name,
                'provider' => $channel->provider,
            ],
            'payment_instruction' => $this->buildPaymentInstruction($channel, $invoice, $qrisSvg),
        ];
    }

    private function buildPaymentInstruction(PaymentChannel $channel, Invoice $invoice, ?string $qrisSvg): array
    {
        if ($channel->channel_type === 'qris') {
            return [
                'type' => 'qris',
                'qris_string' => $invoice->qris_string,
                'qris_svg' => $qrisSvg,
                'message' => 'Scan QRIS dan bayar dengan nominal unik sesuai pay_amount.',
            ];
        }

        return [
            'type' => $channel->channel_type,
            'account_number' => $channel->account_number,
            'account_name' => $channel->account_name,
            'provider' => $channel->provider,
            'message' => 'Transfer ke akun tujuan dengan nominal persis sesuai pay_amount.',
        ];
    }

    private function findInvoiceForTenant(Tenant $tenant, string $invoice): ?Invoice
    {
        return Invoice::where('tenant_id', $tenant->id)
            ->where(function ($q) use ($invoice) {
                $q->where('invoice_number', $invoice);

                if (ctype_digit($invoice)) {
                    $q->orWhere('id', (int) $invoice);
                }
            })
            ->first();
    }

    private function resolveChannel(Tenant $tenant, ?int $channelId = null, ?string $channelType = null): ?PaymentChannel
    {
        $query = PaymentChannel::where('tenant_id', $tenant->id)->where('is_active', true);

        if ($channelId) {
            return (clone $query)->where('id', $channelId)->first();
        }

        if ($channelType) {
            return (clone $query)->where('channel_type', $channelType)->first();
        }

        return (clone $query)->orderByDesc('id')->first();
    }

    private function extractIdempotencyKey(Request $request): ?string
    {
        $key = trim((string) ($request->header('Idempotency-Key') ?: $request->header('X-Idempotency-Key') ?: ''));

        if ($key === '') {
            return null;
        }

        return Str::limit($key, 128, '');
    }

    private function buildIdempotencyFingerprint(array $validated, PaymentChannel $channel): string
    {
        $payload = [
            'amount' => (float) ($validated['amount'] ?? 0),
            'external_id' => (string) ($validated['external_id'] ?? ''),
            'customer_name' => (string) ($validated['customer_name'] ?? ''),
            'customer_email' => (string) ($validated['customer_email'] ?? ''),
            'customer_phone' => (string) ($validated['customer_phone'] ?? ''),
            'description' => (string) ($validated['description'] ?? ''),
            'payment_channel_id' => (int) $channel->id,
            'channel_type' => (string) $channel->channel_type,
            'expires_in_minutes' => (int) ($validated['expires_in_minutes'] ?? 10080),
        ];

        ksort($payload);

        return hash('sha256', json_encode($payload));
    }

    private function findTransactionByIdempotencyKey(Tenant $tenant, string $idempotencyKey): ?Transaction
    {
        return Transaction::where('tenant_id', $tenant->id)
            ->where('metadata->source', 'merchant_api_v1')
            ->where('metadata->idempotency_key', $idempotencyKey)
            ->latest('id')
            ->first();
    }

    private function buildWebhookPayload(Tenant $tenant, Transaction $transaction): array
    {
        $invoice = Invoice::where('tenant_id', $tenant->id)
            ->where(function ($q) use ($transaction) {
                $invoiceId = data_get($transaction->metadata, 'invoice_id');
                $invoiceNumber = $transaction->payment_reference;

                if ($invoiceId) {
                    $q->where('id', $invoiceId);
                }

                if (!empty($invoiceNumber)) {
                    if ($invoiceId) {
                        $q->orWhere('invoice_number', $invoiceNumber);
                    } else {
                        $q->where('invoice_number', $invoiceNumber);
                    }
                }
            })
            ->latest('id')
            ->first();

        return [
            'event' => 'payment.status.updated',
            'occurred_at' => now()->toIso8601String(),
            'merchant' => [
                'id' => $tenant->id,
                'name' => $tenant->name,
            ],
            'transaction' => [
                'transaction_id' => $transaction->transaction_id,
                'external_id' => $transaction->external_id,
                'status' => $transaction->status,
                'amount' => (float) $transaction->amount,
                'fee' => (float) $transaction->fee,
                'net_amount' => (float) $transaction->net_amount,
                'payment_method' => $transaction->payment_method,
                'paid_at' => optional($transaction->paid_at)->toIso8601String(),
            ],
            'invoice' => $invoice ? [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'status' => $invoice->status,
                'amount' => (float) $invoice->amount,
                'pay_amount' => (float) $invoice->unique_amount,
            ] : null,
        ];
    }

    private function dispatchWebhook(Tenant $tenant, Transaction $transaction, array $payload, string $reason): array
    {
        $attempts = ((int) ($transaction->webhook_attempts ?? 0)) + 1;
        $signature = $tenant->webhook_secret
            ? hash_hmac('sha256', json_encode($payload), $tenant->webhook_secret)
            : null;

        try {
            $client = Http::timeout(15)->acceptJson()->withHeaders(array_filter([
                'Content-Type' => 'application/json',
                'User-Agent' => 'PayHook-MerchantApi/1.0',
                'X-Webhook-Signature' => $signature,
                'X-Webhook-Event' => 'payment.status.updated',
                'X-Webhook-Reason' => $reason,
            ]));

            $response = $client->post($tenant->webhook_url, $payload);
            $success = $response->successful();

            $transaction->update([
                'webhook_attempts' => $attempts,
                'webhook_sent' => $success,
                'webhook_sent_at' => $success ? now() : null,
                'webhook_response' => Str::limit('HTTP ' . $response->status() . ': ' . $response->body(), 2000, ''),
            ]);

            return [
                'success' => $success,
                'message' => $success ? 'Webhook sent successfully' : 'Webhook delivery failed',
            ];
        } catch (Throwable $e) {
            $transaction->update([
                'webhook_attempts' => $attempts,
                'webhook_sent' => false,
                'webhook_sent_at' => null,
                'webhook_response' => Str::limit('ERROR: ' . $e->getMessage(), 2000, ''),
            ]);

            return [
                'success' => false,
                'message' => 'Webhook delivery failed: ' . $e->getMessage(),
            ];
        }
    }

    private function detectApiKeyType(string $apiKey): string
    {
        return Str::startsWith($apiKey, 'sk_live_') ? 'production' : 'sandbox';
    }

    private function validateSignedActionRequest(Request $request, Tenant $tenant): ?JsonResponse
    {
        if (empty($tenant->webhook_secret)) {
            return response()->json([
                'success' => false,
                'message' => 'Webhook secret is not configured for signed action requests',
            ], 422);
        }

        $timestamp = trim((string) $request->header('X-Request-Timestamp', ''));
        $signature = trim((string) $request->header('X-Request-Signature', ''));
        $nonce = trim((string) $request->header('X-Request-Nonce', ''));

        if ($timestamp === '' || $signature === '') {
            return response()->json([
                'success' => false,
                'message' => 'Missing required signed request headers',
            ], 401);
        }

        if (!ctype_digit($timestamp)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request timestamp format',
            ], 401);
        }

        $timestampInt = (int) $timestamp;
        $skew = abs(now()->timestamp - $timestampInt);
        if ($skew > 300) {
            return response()->json([
                'success' => false,
                'message' => 'Request timestamp expired',
            ], 401);
        }

        $canonical = $timestamp . "\n" . strtoupper($request->method()) . "\n" . $request->path() . "\n" . $request->getContent();
        $expected = hash_hmac('sha256', $canonical, $tenant->webhook_secret);

        if (!hash_equals($expected, $signature)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid signed request signature',
            ], 401);
        }

        $replayKey = 'merchant_api_replay:' . $tenant->id . ':' . sha1(($nonce !== '' ? $nonce : $signature) . ':' . $timestamp);
        $isFresh = Cache::add($replayKey, 1, now()->addMinutes(10));

        if (!$isFresh) {
            return response()->json([
                'success' => false,
                'message' => 'Replay request detected',
            ], 409);
        }

        return null;
    }

    private function resolveActionIdempotencyReplay(Tenant $tenant, string $idempotencyKey, string $fingerprint): ?array
    {
        $settings = is_array($tenant->settings) ? $tenant->settings : [];
        $registry = $settings['merchant_action_idempotency'] ?? [];
        $entry = $registry[$idempotencyKey] ?? null;

        if (!$entry) {
            return null;
        }

        if (($entry['fingerprint'] ?? null) !== $fingerprint) {
            return [
                'http_status' => 409,
                'response' => [
                    'success' => false,
                    'message' => 'Idempotency key already used with different payload',
                ],
            ];
        }

        return [
            'http_status' => (int) ($entry['http_status'] ?? 200),
            'response' => $entry['response'] ?? [
                'success' => false,
                'message' => 'Idempotency replay data is invalid',
            ],
        ];
    }

    private function storeActionIdempotencyResult(Tenant $tenant, string $idempotencyKey, string $fingerprint, array $response, int $httpStatus): void
    {
        $settings = is_array($tenant->settings) ? $tenant->settings : [];
        $registry = $settings['merchant_action_idempotency'] ?? [];

        // Keep only recent entries to prevent unbounded growth.
        $threshold = now()->subDays(7);
        foreach ($registry as $key => $entry) {
            $createdAt = data_get($entry, 'created_at');
            if ($createdAt && strtotime((string) $createdAt) < $threshold->timestamp) {
                unset($registry[$key]);
            }
        }

        $registry[$idempotencyKey] = [
            'fingerprint' => $fingerprint,
            'http_status' => $httpStatus,
            'response' => $response,
            'created_at' => now()->toIso8601String(),
        ];

        $settings['merchant_action_idempotency'] = $registry;
        $tenant->settings = $settings;
        $tenant->save();
    }

    private function authenticateTenant(Request $request): ?Tenant
    {
        $apiKey = $request->bearerToken();

        if (!$apiKey) {
            return null;
        }

        return Tenant::where('api_key_production', $apiKey)
            ->orWhere('api_key_sandbox', $apiKey)
            ->where('is_active', true)
            ->first();
    }

    private function maskSecret(?string $secret): ?string
    {
        if (!$secret) {
            return null;
        }

        $visible = min(4, strlen($secret));

        return str_repeat('*', max(0, strlen($secret) - $visible)) . substr($secret, -$visible);
    }
}
