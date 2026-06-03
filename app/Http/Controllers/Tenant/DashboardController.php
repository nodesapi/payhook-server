<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\PaymentChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get tenant from authenticated user
        $user = auth()->user();
        $tenant = Tenant::where('email', $user->email)->first();
        
        if (!$tenant) {
            return view('tenant.no-tenant')->with('error', 'Belum ada tenant. Silakan hubungi administrator.');
        }

        // Tenant statistics
        $stats = [
            'total_transactions' => Transaction::where('tenant_id', $tenant->id)->count(),
            'success_transactions' => Transaction::where('tenant_id', $tenant->id)->where('status', 'success')->count(),
            'pending_transactions' => Transaction::where('tenant_id', $tenant->id)->where('status', 'pending')->count(),
            'total_revenue' => Transaction::where('tenant_id', $tenant->id)->where('status', 'success')->sum('net_amount'),
            'today_transactions' => Transaction::where('tenant_id', $tenant->id)
                ->where('status', 'success')
                ->whereDate('paid_at', today())
                ->count(),
            'active_channels' => PaymentChannel::where('tenant_id', $tenant->id)->where('is_active', true)->count(),
            'total_channels' => $tenant->plan->max_channels ?? PaymentChannel::where('tenant_id', $tenant->id)->count(),
        ];

        // Recent transactions
        $recent_transactions = Transaction::where('tenant_id', $tenant->id)
            ->with('paymentChannel')
            ->latest()
            ->take(10)
            ->get();

        $monitored_channels = PaymentChannel::where('tenant_id', $tenant->id)
            ->withCount('transactions')
            ->orderByDesc('is_active')
            ->latest()
            ->take(8)
            ->get();

        // Transaction chart data (last 7 days)
        $chart_data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chart_data[] = [
                'date' => $date->format('M d'),
                'count' => Transaction::where('tenant_id', $tenant->id)
                    ->whereDate('created_at', $date)
                    ->count(),
                'amount' => Transaction::where('tenant_id', $tenant->id)
                    ->whereDate('created_at', $date)
                    ->where('status', 'success')
                    ->sum('net_amount'),
            ];
        }

        return view('tenant.dashboard', compact(
            'tenant',
            'stats',
            'recent_transactions',
            'monitored_channels',
            'chart_data'
        ));
    }

    public function downloadApk()
    {
        $apk_path = public_path('downloads/PayHook.apk');
        
        if (!file_exists($apk_path)) {
            return back()->with('error', 'APK file not found. Please contact administrator.');
        }

        return response()->download($apk_path, 'PayHook.apk');
    }

    public function testWebhook(Request $request)
    {
        $request->validate([
            'webhook_url' => 'required|url',
        ]);

        $tenant = Tenant::first(); // TODO: Get from authenticated user

        try {
            $payload = [
                'event' => 'payment.received',
                'test' => true,
                'amount' => 10000,
                'source' => 'Test Payment',
                'invoice_number' => 'TEST-' . now()->format('YmdHis'),
                'customer_name' => 'Test Customer',
                'paid_at' => now()->toIso8601String(),
            ];

            $response = Http::timeout(10)
                ->withHeaders([
                    'X-Webhook-Signature' => hash_hmac('sha256', json_encode($payload), $tenant->webhook_secret ?? 'test'),
                    'Content-Type' => 'application/json',
                ])
                ->post($request->webhook_url, $payload);

            if ($response->successful()) {
                return back()->with('success', "✅ Webhook test successful! Response: {$response->status()}");
            } else {
                return back()->with('error', "❌ Webhook returned error: {$response->status()} - {$response->body()}");
            }
        } catch (\Exception $e) {
            return back()->with('error', "❌ Connection failed: {$e->getMessage()}");
        }
    }

    public function testPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'customer_name' => 'required|string|max:255',
        ]);

        $tenant = Tenant::first(); // TODO: Get from authenticated user

        try {
            // Create test invoice
            $invoice = Invoice::create([
                'tenant_id' => $tenant->id,
                'customer_name' => $request->customer_name,
                'description' => 'Test Payment - ' . now()->format('Y-m-d H:i:s'),
                'amount' => $request->amount,
                'status' => 'pending',
            ]);

            // Generate QRIS if template exists
            if ($tenant->qrisTemplates()->active()->exists()) {
                $invoice->generateQris();
            }

            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', '✅ Test invoice created! You can now pay using QRIS or bank transfer.');
        } catch (\Exception $e) {
            return back()->with('error', "❌ Failed to create test invoice: {$e->getMessage()}");
        }
    }

    public function setupGuide()
    {
        $tenant = Tenant::first(); // TODO: Get from authenticated user

        return view('tenant.setup-guide', compact('tenant'));
    }

    public function liveStats(Request $request)
    {
        $user   = auth()->user();
        $tenant = Tenant::where('email', $user->email)->first();

        if (!$tenant) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $recent = Invoice::where('tenant_id', $tenant->id)
            ->orderByDesc('created_at')
            ->take(10)
            ->get(['invoice_number', 'customer_name', 'unique_amount', 'status', 'paid_at', 'created_at']);

        $todaySuccess = Invoice::where('tenant_id', $tenant->id)->where('status', 'paid')->whereDate('paid_at', today())->count();

        return response()->json([
            'pending_count'  => Invoice::where('tenant_id', $tenant->id)->where('status', 'pending')->where('expires_at', '>', now())->count(),
            'today_success'  => $todaySuccess,
            'today_transactions' => $todaySuccess,
            'recent_invoices'=> $recent,
        ]);
    }
}
