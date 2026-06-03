<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TransactionController extends Controller
{
    private function getTenant()
    {
        $user = auth()->user();
        return Tenant::where('email', $user->email)->firstOrFail();
    }

    public function index(Request $request)
    {
        $tenant = $this->getTenant();

        $query = Transaction::where('tenant_id', $tenant->id)
            ->with('paymentChannel');

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('channel')) {
            $query->where('payment_channel_id', $request->channel);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhere('external_id', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(20);

        // Get payment channels for filter
        $channels = $tenant->paymentChannels()->where('is_active', true)->get();

        // Stats
        $stats = [
            'total' => Transaction::where('tenant_id', $tenant->id)->count(),
            'success' => Transaction::where('tenant_id', $tenant->id)->where('status', 'success')->count(),
            'pending' => Transaction::where('tenant_id', $tenant->id)->where('status', 'pending')->count(),
            'failed' => Transaction::where('tenant_id', $tenant->id)->where('status', 'failed')->count(),
        ];

        return view('tenant.transactions.index', compact('tenant', 'transactions', 'channels', 'stats'));
    }

    public function export(Request $request)
    {
        $tenant = $this->getTenant();

        $query = Transaction::where('tenant_id', $tenant->id)
            ->with('paymentChannel');

        // Apply same filters as index
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('channel')) {
            $query->where('payment_channel_id', $request->channel);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhere('external_id', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->get();

        // Create CSV content
        $filename = 'transactions_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Transaction ID',
                'External ID',
                'Date & Time',
                'Customer Name',
                'Customer Email',
                'Customer Phone',
                'Payment Channel',
                'Channel Type',
                'Amount (Rp)',
                'Fee (Rp)',
                'Net Amount (Rp)',
                'Status',
                'Payment Reference',
                'Webhook Sent',
                'Webhook Attempts',
                'Paid At'
            ]);

            // CSV Data
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->transaction_id,
                    $transaction->external_id ?? '-',
                    $transaction->created_at->format('Y-m-d H:i:s'),
                    $transaction->customer_name ?? '-',
                    $transaction->customer_email ?? '-',
                    $transaction->customer_phone ?? '-',
                    $transaction->paymentChannel?->channel_name ?? '-',
                    $transaction->paymentChannel?->channel_type ?? '-',
                    number_format($transaction->amount, 0, ',', '.'),
                    number_format($transaction->fee_amount ?? 0, 0, ',', '.'),
                    number_format($transaction->net_amount ?? $transaction->amount, 0, ',', '.'),
                    ucfirst($transaction->status),
                    $transaction->payment_reference ?? '-',
                    $transaction->webhook_sent ? 'Yes' : 'No',
                    $transaction->webhook_attempts ?? 0,
                    $transaction->paid_at ? $transaction->paid_at->format('Y-m-d H:i:s') : '-'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(Transaction $transaction)
    {
        $tenant = $this->getTenant();
        
        if ($transaction->tenant_id !== $tenant->id) {
            abort(403);
        }

        $transaction->load('paymentChannel');

        return view('tenant.transactions.show', compact('tenant', 'transaction'));
    }

    public function resendWebhook(Transaction $transaction)
    {
        $tenant = $this->getTenant();
        
        if ($transaction->tenant_id !== $tenant->id) {
            abort(403);
        }

        if (!$tenant->webhook_url) {
            return back()->with('error', 'Webhook URL not configured!');
        }

        try {
            $payload = [
                'event' => 'payment.success',
                'transaction_id' => $transaction->transaction_id,
                'external_id' => $transaction->external_id,
                'amount' => $transaction->amount,
                'net_amount' => $transaction->net_amount,
                'fee' => $transaction->fee,
                'status' => $transaction->status,
                'customer_name' => $transaction->customer_name,
                'customer_email' => $transaction->customer_email,
                'payment_method' => $transaction->payment_method,
                'paid_at' => $transaction->paid_at?->toIso8601String(),
                'created_at' => $transaction->created_at->toIso8601String(),
            ];

            $signature = hash_hmac('sha256', json_encode($payload), $tenant->webhook_secret);

            $response = Http::timeout(10)
                ->withHeaders([
                    'X-Webhook-Signature' => $signature,
                    'Content-Type' => 'application/json',
                ])
                ->post($tenant->webhook_url, $payload);

            $transaction->webhook_attempts++;
            $transaction->webhook_sent_at = now();
            $transaction->webhook_response = $response->body();
            $transaction->webhook_sent = $response->successful();
            $transaction->save();

            if ($response->successful()) {
                return back()->with('success', 'Webhook sent successfully!');
            } else {
                return back()->with('error', "Webhook failed: {$response->status()}");
            }
        } catch (\Exception $e) {
            return back()->with('error', "Webhook error: {$e->getMessage()}");
        }
    }

    public function refund(Transaction $transaction)
    {
        $tenant = $this->getTenant();
        
        if ($transaction->tenant_id !== $tenant->id) {
            abort(403);
        }

        if ($transaction->status !== 'success') {
            return back()->with('error', 'Only successful transactions can be refunded!');
        }

        $transaction->status = 'refund';
        $transaction->save();

        // TODO: Implement actual refund logic with payment provider

        return back()->with('success', 'Transaction marked as refunded!');
    }
}
