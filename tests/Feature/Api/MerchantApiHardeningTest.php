<?php

namespace Tests\Feature\Api;

use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MerchantApiHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_cancel_invoice_supports_idempotent_replay(): void
    {
        $tenant = $this->createTenant();
        [$invoice, $transaction] = $this->createInvoiceAndTransaction($tenant);

        $headers = $this->authHeaders($tenant, [
            'Idempotency-Key' => 'cancel-001',
        ]);

        $first = $this->postJson('/api/v1/invoices/' . $invoice->invoice_number . '/cancel', [
            'reason' => 'Customer changed mind',
        ], $headers);

        $first->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'cancelled');

        $second = $this->postJson('/api/v1/invoices/' . $invoice->invoice_number . '/cancel', [
            'reason' => 'Customer changed mind',
        ], $headers);

        $second->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'cancelled');

        $transaction->refresh();
        $this->assertSame('failed', $transaction->status);
    }

    public function test_cancel_invoice_rejects_idempotency_key_with_different_payload(): void
    {
        $tenant = $this->createTenant();
        [$invoice] = $this->createInvoiceAndTransaction($tenant);

        $headers = $this->authHeaders($tenant, [
            'Idempotency-Key' => 'cancel-002',
        ]);

        $this->postJson('/api/v1/invoices/' . $invoice->invoice_number . '/cancel', [
            'reason' => 'First reason',
        ], $headers)->assertOk();

        $this->postJson('/api/v1/invoices/' . $invoice->invoice_number . '/cancel', [
            'reason' => 'Different reason',
        ], $headers)
            ->assertStatus(409)
            ->assertJsonPath('success', false);
    }

    public function test_retry_webhook_requires_signed_headers(): void
    {
        $tenant = $this->createTenant([
            'webhook_url' => 'https://merchant.test/webhook',
            'webhook_secret' => 'secretsecretsecretsecret',
            'webhook_enabled' => true,
        ]);

        [, $transaction] = $this->createInvoiceAndTransaction($tenant);

        $this->postJson('/api/v1/webhook-events/' . $transaction->transaction_id . '/retry', [], $this->authHeaders($tenant))
            ->assertStatus(401)
            ->assertJsonPath('success', false);
    }

    public function test_retry_webhook_rejects_replay_request(): void
    {
        $tenant = $this->createTenant([
            'webhook_url' => 'https://merchant.test/webhook',
            'webhook_secret' => 'secretsecretsecretsecret',
            'webhook_enabled' => true,
        ]);

        [, $transaction] = $this->createInvoiceAndTransaction($tenant);

        Http::fake([
            'https://merchant.test/*' => Http::response(['ok' => true], 200),
        ]);

        $path = 'api/v1/webhook-events/' . $transaction->transaction_id . '/retry';
        $timestamp = (string) now()->timestamp;
        $body = '[]'; // postJson($url, []) serializes empty array as JSON array []
        $signature = hash_hmac('sha256', $timestamp . "\nPOST\n" . $path . "\n" . $body, $tenant->webhook_secret);

        $headers = $this->authHeaders($tenant, [
            'X-Request-Timestamp' => $timestamp,
            'X-Request-Signature' => $signature,
            'X-Request-Nonce' => 'nonce-abc',
        ]);

        $this->postJson('/' . $path, [], $headers)
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->postJson('/' . $path, [], $headers)
            ->assertStatus(409)
            ->assertJsonPath('success', false);
    }

    private function createTenant(array $overrides = []): Tenant
    {
        return Tenant::create(array_merge([
            'name' => 'Tenant A',
            'slug' => 'tenant-a',
            'email' => 'tenant@example.com',
            'api_key_production' => 'sk_live_test_key_123456789012345678901234567890123456',
            'api_key_sandbox' => 'sk_test_test_key_123456789012345678901234567890123456',
            'webhook_enabled' => true,
            'is_active' => true,
            'mode' => 'sandbox',
        ], $overrides));
    }

    private function createInvoiceAndTransaction(Tenant $tenant): array
    {
        $invoice = Invoice::create([
            'tenant_id' => $tenant->id,
            'customer_name' => 'Customer One',
            'amount' => 10000,
            'status' => 'pending',
            'expires_at' => now()->addDay(),
        ]);

        $transaction = Transaction::create([
            'tenant_id' => $tenant->id,
            'external_id' => 'ORDER-001',
            'amount' => $invoice->unique_amount,
            'fee' => 0,
            'net_amount' => $invoice->unique_amount,
            'status' => 'pending',
            'payment_reference' => $invoice->invoice_number,
            'metadata' => [
                'source' => 'merchant_api_v1',
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
            ],
        ]);

        return [$invoice, $transaction];
    }

    private function authHeaders(Tenant $tenant, array $extra = []): array
    {
        return array_merge([
            'Authorization' => 'Bearer ' . $tenant->api_key_sandbox,
            'Accept' => 'application/json',
        ], $extra);
    }
}
