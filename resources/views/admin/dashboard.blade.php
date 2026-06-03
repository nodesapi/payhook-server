@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-12">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tight uppercase">System <span class="text-supabase-accent">Overview</span></h1>
            <p class="text-supabase-muted mt-2">Global infrastructure metrics and tenant management.</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.tenants.create') }}" class="sb-button-primary !w-auto">
                + Create Tenant
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-6 relative overflow-hidden group shadow-2xl">
            <div class="absolute top-0 right-0 w-32 h-32 bg-supabase-accent/5 blur-3xl rounded-full"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-supabase-muted uppercase tracking-widest mb-2">Total Tenants</p>
                    <h3 class="text-3xl font-black text-white leading-none">{{ $stats['total_tenants'] }}</h3>
                    <p class="mt-2 text-[10px] font-bold text-green-500 uppercase tracking-widest">{{ $stats['active_tenants'] }} Active Now</p>
                </div>
                <div class="w-12 h-12 bg-supabase-accent/10 border border-supabase-accent/20 rounded-xl flex items-center justify-center text-supabase-accent">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-6 relative overflow-hidden group shadow-2xl">
            <div class="absolute top-0 right-0 w-32 h-32 bg-green-500/5 blur-3xl rounded-full"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-supabase-muted uppercase tracking-widest mb-2">Global Revenue</p>
                    <h3 class="text-3xl font-black text-white leading-none">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
                    <p class="mt-2 text-[10px] font-bold text-supabase-muted uppercase tracking-widest">{{ $stats['paid_invoices'] }} Paid Invoices</p>
                </div>
                <div class="w-12 h-12 bg-green-500/10 border border-green-500/20 rounded-xl flex items-center justify-center text-green-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-6 relative overflow-hidden group shadow-2xl">
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 blur-3xl rounded-full"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-supabase-muted uppercase tracking-widest mb-2">Today's Tx</p>
                    <h3 class="text-3xl font-black text-white leading-none">{{ $stats['today_transactions'] }}</h3>
                    <p class="mt-2 text-[10px] font-bold text-blue-500 uppercase tracking-widest">Rp {{ number_format($stats['today_revenue'], 0, ',', '.') }} Today</p>
                </div>
                <div class="w-12 h-12 bg-blue-500/10 border border-blue-500/20 rounded-xl flex items-center justify-center text-blue-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
            </div>
        </div>

        <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-6 relative overflow-hidden group shadow-2xl">
            <div class="absolute top-0 right-0 w-32 h-32 bg-amber-500/5 blur-3xl rounded-full"></div>
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-supabase-muted uppercase tracking-widest mb-2">Pending Invoices</p>
                    <h3 class="text-3xl font-black text-white leading-none">{{ $stats['pending_invoices'] }}</h3>
                    <p class="mt-2 text-[10px] font-bold text-amber-500 uppercase tracking-widest">Awaiting Payment</p>
                </div>
                <div class="w-12 h-12 bg-amber-500/10 border border-amber-500/20 rounded-xl flex items-center justify-center text-amber-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Area -->
    <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-8 shadow-2xl relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-8 opacity-5">
            <svg class="w-32 h-32 text-supabase-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
        </div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-black text-white uppercase tracking-tighter">Revenue <span class="text-supabase-accent">Analytics</span></h3>
                    <p class="text-[10px] font-bold text-supabase-muted uppercase tracking-[0.2em] mt-1">Platform-wide transaction flow (Last 7 Days)</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-supabase-accent shadow-[0_0_15px_rgba(251,191,36,0.5)] animate-pulse"></span>
                    <span class="text-xs font-black text-white uppercase tracking-widest">Real-time Performance</span>
                </div>
            </div>
            <div class="h-[320px] w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Transactions -->
        <div class="bg-supabase-surface border border-supabase-border rounded-2xl overflow-hidden shadow-2xl">
            <div class="px-8 py-6 border-b border-supabase-border flex items-center justify-between bg-supabase-surface/50">
                <h3 class="text-lg font-black text-white uppercase tracking-tighter">Recent Transactions</h3>
                <span class="text-[10px] font-bold text-supabase-muted uppercase tracking-widest">Live Flow</span>
            </div>
            <div class="divide-y divide-supabase-border">
                @forelse($recent_transactions->take(10) as $transaction)
                    <div class="p-6 group hover:bg-white/[0.02] transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-2 h-2 rounded-full bg-supabase-accent animate-pulse"></div>
                                <div>
                                    <p class="text-xs font-mono text-white group-hover:text-supabase-accent transition-colors">{{ $transaction->invoice_number }}</p>
                                    <p class="text-[10px] font-bold text-supabase-muted uppercase tracking-widest mt-1">{{ $transaction->tenant->name ?? 'Unknown' }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black text-white">Rp {{ number_format($transaction->unique_amount, 0, ',', '.') }}</p>
                                <p class="text-[10px] text-supabase-muted mt-1 uppercase">{{ $transaction->paid_at?->format('H:i') ?? $transaction->created_at->format('H:i') }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-16 text-center text-supabase-muted italic">
                        No transactions detected
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Tenants -->
        <div class="bg-supabase-surface border border-supabase-border rounded-2xl overflow-hidden shadow-2xl">
            <div class="px-8 py-6 border-b border-supabase-border flex items-center justify-between bg-supabase-surface/50">
                <h3 class="text-lg font-black text-white uppercase tracking-tighter">New Tenants</h3>
                <a href="{{ route('admin.tenants.index') }}" class="text-[10px] font-bold text-supabase-accent hover:text-white transition-colors uppercase tracking-widest underline">Manage All</a>
            </div>
            <div class="divide-y divide-supabase-border">
                @forelse($recent_tenants as $tenant)
                    <div class="p-6 group hover:bg-white/[0.02] transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 rounded-xl bg-supabase-input border border-supabase-border flex items-center justify-center text-supabase-accent font-bold group-hover:border-supabase-accent/50 transition-colors">
                                    {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="flex items-center">
                                        <p class="text-sm font-bold text-white leading-none">{{ $tenant->name }}</p>
                                        @if($tenant->is_active)
                                            <span class="ml-2 px-1.5 py-0.5 text-[8px] font-black bg-green-500/10 text-green-500 rounded uppercase">Active</span>
                                        @else
                                            <span class="ml-2 px-1.5 py-0.5 text-[8px] font-black bg-supabase-muted/10 text-supabase-muted rounded uppercase">Inactive</span>
                                        @endif
                                    </div>
                                    <p class="text-[10px] text-supabase-muted mt-1">{{ $tenant->email }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-[10px] font-black px-2 py-1 bg-supabase-input text-white rounded border border-supabase-border uppercase tracking-widest">{{ $tenant->mode }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-16 text-center text-supabase-muted italic">
                        No tenants registered
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Webhook Stats -->
    <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-8 relative overflow-hidden group">
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-supabase-accent to-transparent opacity-20"></div>
        <h3 class="text-lg font-black text-white uppercase tracking-tighter mb-8 flex items-center">
            <svg class="w-5 h-5 mr-3 text-supabase-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            Webhook Delivery Statistics
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="p-6 bg-supabase-dark/50 border border-supabase-border rounded-2xl text-center">
                <p class="text-4xl font-black text-white mb-2 leading-none">{{ number_format($webhook_stats['total_logs']) }}</p>
                <p class="text-[10px] font-bold text-supabase-muted uppercase tracking-[0.2em]">Total Deliveries</p>
            </div>
            <div class="p-6 bg-supabase-dark/50 border border-supabase-border rounded-2xl text-center">
                <p class="text-4xl font-black text-green-500 mb-2 leading-none">{{ number_format($webhook_stats['successful']) }}</p>
                <p class="text-[10px] font-bold text-supabase-muted uppercase tracking-[0.2em]">Success Rate</p>
            </div>
            <div class="p-6 bg-supabase-dark/50 border border-supabase-border rounded-2xl text-center">
                <p class="text-4xl font-black text-red-500 mb-2 leading-none">{{ number_format($webhook_stats['failed']) }}</p>
                <p class="text-[10px] font-bold text-supabase-muted uppercase tracking-[0.2em]">Failure Count</p>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Prepare data from PHP
    const chartData = @json($chart_data);
    const labels = chartData.map(item => {
        const d = new Date(item.date);
        return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
    });
    const dataPoints = chartData.map(item => item.revenue);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue',
                data: dataPoints,
                borderColor: '#fbbf24',
                borderWidth: 4,
                backgroundColor: 'rgba(251, 191, 36, 0.05)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#fbbf24',
                pointBorderColor: '#1a1a1a',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(255, 255, 255, 0.03)', drawBorder: false },
                    ticks: { 
                        color: '#6b7280', 
                        font: { size: 10, weight: 'bold' },
                        callback: function(value) {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { color: '#6b7280', font: { size: 10, weight: 'bold' } }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            }
        }
    });
});
</script>
@endpush
@endsection
