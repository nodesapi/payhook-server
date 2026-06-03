<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Services\QrisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ApiPlaygroundController extends Controller
{
    private function getTenant()
    {
        $user = auth()->user();
        return Tenant::where('email', $user->email)->firstOrFail();
    }

    public function index()
    {
        $tenant = $this->getTenant();
        $channels = $tenant->paymentChannels()->where('is_active', true)->get();

        // Check Android app connection status
        $latestPaymentLog = $tenant->paymentLogs()
            ->where('package_name', 'com.payhook.app')
            ->latest()
            ->first();

        $androidAppConnected = $latestPaymentLog && 
            $latestPaymentLog->created_at->isAfter(now()->subHours(24));

        return view('tenant.api-playground', compact('tenant', 'channels', 'androidAppConnected', 'latestPaymentLog'));
    }

    public function testWebhook(Request $request)
    {
        $tenant = $this->getTenant();

        $validated = $request->validate([
            'webhook_url' => 'required|url',
        ]);

        $webhookUrl = $validated['webhook_url'];
        
        // Check if URL is same server
        $serverHost = parse_url(config('app.url'), PHP_URL_HOST);
        $webhookHost = parse_url($webhookUrl, PHP_URL_HOST);
        $isSameServer = in_array($webhookHost, [$serverHost, 'localhost', '127.0.0.1', '192.168.3.105']);

        if ($isSameServer) {
            return response()->json([
                'success' => false,
                'error' => 'Cannot test webhook to same server',
                'message' => 'Server cannot connect to itself. Please use one of these alternatives:',
                'alternatives' => [
                    '1. Use webhook.site for external testing',
                    '2. Create a real test invoice and scan with Android app',
                    '3. Check Android app connection status above',
                ],
                'android_test_available' => true,
            ], 400);
        }

        try {
            // Use Android app format for test payload
            $timestamp = now()->format('Y-m-d H:i:s');
            $payload = [
                'amount' => 10000,
                'source' => 'NodePay Hook Test (API Playground)',
                'reference' => 'NPH-TEST-' . time(),
                'timestamp' => $timestamp,
                'package_name' => 'com.nodepay.playground',
                'notification_title' => 'Test Payment',
                'notification_text' => 'This is a test webhook from API Playground',
                'sent_by' => 'NodePay Hook API Playground',
            ];

            $jsonPayload = json_encode($payload);
            $signature = hash_hmac('sha256', $jsonPayload, $tenant->webhook_secret);

            $startTime = microtime(true);
            
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $tenant->api_key_production,
                    'X-Webhook-Signature' => $signature,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'User-Agent' => 'NodePay-Hook-Playground/1.0',
                ])
                ->post($webhookUrl, $payload);
                
            $duration = round((microtime(true) - $startTime) * 1000);

            return response()->json([
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'duration_ms' => $duration,
                'response_body' => $response->body(),
                'payload_sent' => $payload,
                'signature' => $signature,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function createTransaction(Request $request)
    {
        $tenant = $this->getTenant();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
            'payment_channel_id' => 'required|exists:payment_channels,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'customer_phone' => 'nullable|string',
            'external_id' => 'nullable|string|max:255',
        ]);

        // Verify channel belongs to tenant
        $channel = $tenant->paymentChannels()->findOrFail($validated['payment_channel_id']);

        // Create invoice (unique amount used for payment matching)
        $invoice = Invoice::create([
            'tenant_id' => $tenant->id,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'] ?? null,
            'customer_phone' => $validated['customer_phone'] ?? null,
            'description' => 'API Playground Test - ' . ($validated['external_id'] ?? 'No external ID'),
            'amount' => $validated['amount'],
            'status' => 'pending',
        ]);

        // Only generate QRIS QR code for QRIS-type channels
        $qrSvg = null;
        if ($channel->channel_type === 'qris') {
            $invoice->generateQris();
            $invoice->refresh();
            if (!empty($invoice->qris_string)) {
                $qrSvg = app(QrisService::class)->generateQRCode($invoice->qris_string);
            }
        }

        // Keep transaction table in sync so channel stats and transaction list are accurate.
        Transaction::create([
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
                'source' => 'api_playground',
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
            ],
        ]);

        return response()->json([
            'success' => true,
            'transaction' => [
                'id' => $invoice->invoice_number,
                'amount' => $invoice->amount,
                'fee' => 0,
                'net_amount' => $invoice->unique_amount,
                'status' => $invoice->status,
                'unique_amount' => $invoice->unique_amount,
                'channel_type' => $channel->channel_type,
                'channel_name' => $channel->channel_name,
                'account_number' => $channel->account_number,
                'account_name' => $channel->account_name,
                'provider' => $channel->provider,
                'qr_svg' => $qrSvg,
                'invoice_id' => $invoice->id,
                'expires_at' => optional($invoice->expires_at)->toIso8601String(),
                'paid_at' => optional($invoice->paid_at)->toIso8601String(),
            ],
        ]);
    }

    public function invoiceStatus(Invoice $invoice)
    {
        $tenant = $this->getTenant();

        if ($invoice->tenant_id !== $tenant->id) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'invoice' => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'status' => $invoice->status,
                'amount' => $invoice->amount,
                'unique_amount' => $invoice->unique_amount,
                'expires_at' => optional($invoice->expires_at)->toIso8601String(),
                'paid_at' => optional($invoice->paid_at)->toIso8601String(),
            ],
        ]);
    }
}
