<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PaymentLog;
use App\Models\Tenant;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        Log::info('DEBUG ANDROID APP REQUEST', [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'ip' => $request->ip()
        ]);

        // Try extracting API key manually for debug
        $debugApiKey = $request->bearerToken() 
            ?: $request->header('X-API-KEY') 
            ?: $request->header('api-key')
            ?: $request->input('api_key');

        // Authenticate tenant
        $tenant = $this->authenticateTenant($request);
        
        if (!$tenant) {
            return response()->json([
                'error' => 'Unauthorized - Invalid API key',
                'debug_received_key' => $debugApiKey,
                'debug_bearer' => $request->bearerToken(),
                'debug_headers' => $request->headers->all(),
                'debug_url' => $request->url()
            ], 401);
        }

        // Verify HMAC signature if provided
        $signature = $request->header('X-Webhook-Signature');
        if ($signature && $tenant->webhook_secret) {
            $payload = $request->getContent();
            $expectedSignature = hash_hmac('sha256', $payload, $tenant->webhook_secret);
            
            if (!hash_equals($expectedSignature, $signature)) {
                Log::warning('Webhook invalid signature', [
                    'tenant_id' => $tenant->id,
                    'ip' => $request->ip()
                ]);
                return response()->json(['error' => 'Unauthorized - Invalid signature'], 401);
            }
        }

        Log::info('Webhook authenticated', [
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name
        ]);

        $validated = $request->validate([
            'amount' => 'required|numeric',
            'source' => 'required|string',
            'package_name' => 'required|string',
            'notification_title' => 'nullable|string',
            'notification_text' => 'nullable|string',
            'timestamp' => 'required|string',
        ]);

        $amount = $validated['amount'];
        $source = $validated['source'];
        $notificationText = $validated['notification_text'] ?? '';
        $notificationTimestamp = $validated['timestamp'];

        // Find matching invoices for this tenant only
        $matchingInvoices = Invoice::pending()
            ->where('tenant_id', $tenant->id)
            ->where('unique_amount', $amount)
            ->get();

        if ($matchingInvoices->isEmpty()) {
            // Legacy fallback: match pending transaction directly by amount
            $matchingTransactions = Transaction::where('tenant_id', $tenant->id)
                ->where('status', 'pending')
                ->where('amount', $amount)
                ->orderByDesc('id')
                ->get();

            if ($matchingTransactions->count() === 1) {
                $transaction = $matchingTransactions->first();
                $transaction->update([
                    'status' => 'success',
                    'paid_at' => now(),
                    'payment_reference' => $source,
                    'webhook_sent' => true,
                    'webhook_sent_at' => now(),
                    'webhook_attempts' => ($transaction->webhook_attempts ?? 0) + 1,
                    'webhook_response' => 'Auto-confirmed via webhook (legacy transaction match)',
                ]);

                $this->logPayment($validated, null, 'matched', 'Payment confirmed via legacy transaction match', $tenant->id);

                return response()->json([
                    'status' => 'confirmed',
                    'message' => 'Payment matched and confirmed (legacy transaction).',
                    'transaction_id' => $transaction->transaction_id,
                    'amount' => $transaction->amount,
                    'paid_at' => optional($transaction->paid_at)->toIso8601String(),
                ], 200);
            }

            if ($matchingTransactions->count() > 1) {
                $this->logPayment($validated, null, 'ambiguous', 'Multiple legacy transactions found', $tenant->id);

                return response()->json([
                    'status' => 'ambiguous',
                    'message' => 'Multiple pending transactions match this amount. Manual confirmation required.',
                    'matching_count' => $matchingTransactions->count(),
                ], 200);
            }

            $this->logPayment($validated, null, 'no_match', 'No pending invoice matches this amount', $tenant->id);

            return response()->json([
                'status' => 'no_match',
                'message' => 'No pending invoice matches the payment amount.',
                'amount' => $amount,
            ], 200);
        }

        if ($matchingInvoices->count() > 1) {
            $this->logPayment($validated, null, 'ambiguous', 'Multiple invoices found', $tenant->id);

            return response()->json([
                'status' => 'ambiguous',
                'message' => 'Multiple pending invoices match this amount. Manual confirmation required.',
                'matching_count' => $matchingInvoices->count(),
                'invoices' => $matchingInvoices->map(function ($inv) {
                    return [
                        'invoice_number' => $inv->invoice_number,
                        'customer' => $inv->customer_name,
                        'amount' => $inv->unique_amount,
                    ];
                }),
            ], 200);
        }

        $invoice = $matchingInvoices->first();
        $invoice->markAsPaid($source, $notificationText);

        // Dispatch outgoing callback to tenant if configured
        $invoiceTenant = Tenant::find($invoice->tenant_id);
        if ($invoiceTenant && $invoiceTenant->webhook_enabled && $invoiceTenant->callback_url) {
            $this->dispatchTenantWebhook($invoiceTenant, $invoice);
        }

        // Sync transaction list status for legacy/transition flows.
        // Priority 1: transaction linked directly to this invoice via metadata.
        $matchedTransaction = Transaction::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->whereJsonContains('metadata->invoice_id', $invoice->id)
            ->orderByDesc('id')
            ->first();

        // Fallback: match by amount if metadata linkage is not available.
        if (!$matchedTransaction) {
            $matchedTransaction = Transaction::where('tenant_id', $tenant->id)
                ->where('status', 'pending')
                ->where(function ($q) use ($invoice) {
                    $q->where('amount', $invoice->unique_amount)
                      ->orWhere('amount', $invoice->amount);
                })
                ->orderByDesc('id')
                ->first();
        }

        if ($matchedTransaction) {
            $matchedTransaction->update([
                'status' => 'success',
                'paid_at' => now(),
                'payment_reference' => $source,
                'webhook_sent' => true,
                'webhook_sent_at' => now(),
                'webhook_attempts' => ($matchedTransaction->webhook_attempts ?? 0) + 1,
                'webhook_response' => 'Auto-confirmed via invoice match: ' . $invoice->invoice_number,
            ]);
        }

        $this->logPayment($validated, $invoice->id, 'matched', 'Payment confirmed automatically', $tenant->id);

        Log::info('Payment confirmed', [
            'invoice_number' => $invoice->invoice_number,
            'amount' => $amount,
            'source' => $source,
        ]);

        return response()->json([
            'status' => 'confirmed',
            'message' => 'Payment matched and confirmed automatically.',
            'invoice_number' => $invoice->invoice_number,
            'customer' => $invoice->customer_name,
            'amount' => $invoice->unique_amount,
            'paid_at' => $invoice->paid_at->toIso8601String(),
        ], 200);
    }

    private function logPayment(array $data, ?int $invoiceId, string $status, string $notes, ?int $tenantId = null): void
    {
        PaymentLog::create([
            'invoice_id' => $invoiceId,
            'tenant_id' => $tenantId,
            'amount' => $data['amount'],
            'source' => $data['source'],
            'package_name' => $data['package_name'],
            'notification_title' => $data['notification_title'] ?? null,
            'notification_text' => $data['notification_text'] ?? null,
            'notification_timestamp' => $data['timestamp'],
            'status' => $status,
            'notes' => $notes,
        ]);
    }

    public function pendingInvoices(Request $request): JsonResponse
    {
        // Authenticate tenant
        $tenant = $this->authenticateTenant($request);
        
        if (!$tenant) {
            return response()->json(['error' => 'Unauthorized - Invalid API key'], 401);
        }

        $invoices = Invoice::pending()
            ->where('tenant_id', $tenant->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($inv) {
                return [
                    'invoice_number' => $inv->invoice_number,
                    'customer' => $inv->customer_name,
                    'amount' => $inv->amount,
                    'unique_amount' => $inv->unique_amount,
                    'created_at' => $inv->created_at->toIso8601String(),
                    'expires_at' => $inv->expires_at->toIso8601String(),
                ];
            });

        return response()->json([
            'count' => $invoices->count(),
            'invoices' => $invoices,
        ]);
    }

    public function confirm(Request $request, Invoice $invoice): JsonResponse
    {
        // Authenticate tenant
        $tenant = $this->authenticateTenant($request);
        
        if (!$tenant) {
            return response()->json(['error' => 'Unauthorized - Invalid API key'], 401);
        }

        // Verify invoice belongs to this tenant
        if ($invoice->tenant_id !== $tenant->id) {
            return response()->json(['error' => 'Forbidden - Invoice does not belong to this tenant'], 403);
        }

        if ($invoice->status !== 'pending') {
            return response()->json([
                'error' => 'Invoice is not pending',
                'current_status' => $invoice->status,
            ], 400);
        }

        $invoice->markAsPaid(
            $request->input('source', 'Manual Confirmation'),
            $request->input('notes')
        );

        return response()->json([
            'status' => 'confirmed',
            'message' => 'Invoice confirmed manually.',
            'invoice_number' => $invoice->invoice_number,
        ]);
    }

    /**
     * Authenticate tenant from API key
     */
    private function authenticateTenant(Request $request): ?Tenant
    {
        // Try all possible locations for the API Key
        $apiKey = $request->bearerToken() 
            ?: $request->header('X-API-KEY') 
            ?: $request->header('api-key')
            ?: $request->input('api_key');
        
        if (!$apiKey) {
            // Also try grabbing from Authorization header if it wasn't Bearer (e.g. Basic or just raw key)
            $authHeader = $request->header('Authorization');
            if ($authHeader && !str_starts_with(strtolower($authHeader), 'bearer ')) {
                $apiKey = $authHeader;
            }
        }

        if (!$apiKey) {
            return null;
        }

        return Tenant::where('api_key_production', $apiKey)
            ->orWhere('api_key_sandbox', $apiKey)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Dispatch outgoing callback notification to tenant panel.
     */
    private function dispatchTenantWebhook(Tenant $tenant, Invoice $invoice): void
    {
        $payload = [
            'event'   => 'payment.status.updated',
            'invoice' => [
                'invoice_number' => $invoice->invoice_number,
                'status'         => 'paid',
                'amount'         => $invoice->amount,
                'unique_amount'  => $invoice->unique_amount,
                'paid_at'        => optional($invoice->paid_at)->toIso8601String(),
            ],
            'transaction' => [
                'paid_at' => optional($invoice->paid_at)->toIso8601String(),
            ],
        ];

        $body      = json_encode($payload);
        $signature = hash_hmac('sha256', $body, $tenant->webhook_secret ?? '');

        try {
            $response = Http::timeout(3)
                ->withHeaders([
                    'Content-Type'        => 'application/json',
                    'X-Webhook-Signature' => $signature,
                ])
                ->withBody($body, 'application/json')
                ->post($tenant->callback_url);

            Log::info('Callback dispatched to tenant', [
                'tenant'      => $tenant->name,
                'callback_url'=> $tenant->callback_url,
                'status'      => $response->status(),
                'invoice'     => $invoice->invoice_number,
            ]);
        } catch (\Exception $e) {
            Log::error('Callback dispatch failed', [
                'tenant'      => $tenant->name,
                'callback_url'=> $tenant->callback_url,
                'error'       => $e->getMessage(),
                'invoice'     => $invoice->invoice_number,
            ]);
        }
    }
}
