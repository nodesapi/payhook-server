<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Invoice;
use App\Models\PaymentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $pendingUpgradeQuery = Tenant::with('plan')
            ->where('settings->upgrade_request->status', 'pending');

        // Statistics
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::active()->count(),
            'total_invoices' => Invoice::count(),
            'paid_invoices' => Invoice::where('status', 'paid')->count(),
            'pending_invoices' => Invoice::where('status', 'pending')->count(),
            'pending_upgrade_requests' => (clone $pendingUpgradeQuery)->count(),
            'total_revenue' => Invoice::where('status', 'paid')->sum('unique_amount'),
            'today_transactions' => Invoice::where('status', 'paid')
                ->whereDate('paid_at', today())
                ->count(),
            'today_revenue' => Invoice::where('status', 'paid')
                ->whereDate('paid_at', today())
                ->sum('unique_amount'),
        ];

        // Recent Tenants
        $recent_tenants = Tenant::latest()->take(5)->get();

        // Recent Transactions
        $recent_transactions = Invoice::with(['tenant', 'qrisTemplate'])
            ->where('status', 'paid')
            ->latest('paid_at')
            ->take(10)
            ->get();

        $pending_upgrade_requests = (clone $pendingUpgradeQuery)
            ->latest('updated_at')
            ->take(8)
            ->get()
            ->map(function (Tenant $tenant) {
                $tenant->upgrade_request_details = $tenant->getUpgradeRequestDetails();

                return $tenant;
            });

        // Chart Data - Last 7 days
        $chart_data = Invoice::where('status', 'paid')
            ->where('paid_at', '>=', now()->subDays(7))
            ->select(
                DB::raw('DATE(paid_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(unique_amount) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Webhook Stats
        $webhook_stats = [
            'total_logs' => PaymentLog::count(),
            'successful' => PaymentLog::where('status', 'success')->count(),
            'failed' => PaymentLog::where('status', 'failed')->count(),
        ];

        return view('admin.dashboard', compact(
            'stats',
            'recent_tenants',
            'recent_transactions',
            'pending_upgrade_requests',
            'chart_data',
            'webhook_stats'
        ));
    }
}
