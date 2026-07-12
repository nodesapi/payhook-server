<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

/**
 * Builds and delivers the v1 merchant webhook payload (event: payment.status.updated)
 * for a transaction. Shared by MerchantApiController (API-triggered resend/refund) and
 * Tenant\TransactionController (dashboard-triggered manual actions) so both paths always
 * notify merchants with the exact same payload shape.
 */
class TransactionWebhookService
{
    public function resolveInvoice(Transaction $transaction): ?Invoice
    {
        return Invoice::where('tenant_id', $transaction->tenant_id)
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
    }

    public function buildPayload(Tenant $tenant, Transaction $transaction, ?Invoice $invoice = null): array
    {
        $invoice ??= $this->resolveInvoice($transaction);

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
                'paid_at' => optional($invoice->paid_at)->toIso8601String(),
            ] : null,
        ];
    }

    public function dispatch(Tenant $tenant, Transaction $transaction, array $payload, string $reason): array
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
}
