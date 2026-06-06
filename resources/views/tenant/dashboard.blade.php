@extends('layouts.tenant')

@section('title', 'Overview')

@section('breadcrumb')
    <div class="flex items-center space-x-2">
        <span class="text-supabase-muted">Pages</span>
        <span class="text-supabase-muted">/</span>
        <span class="text-white">Dashboard</span>
    </div>
@endsection

@section('content')

@php
    $mobileAppBrandMap = [
        'gopay' => ['label' => 'GoPay', 'mark' => 'GP', 'tile' => 'bg-sky-500', 'ring' => 'border-sky-400/40', 'text' => 'text-sky-300'],
        'dana' => ['label' => 'DANA', 'mark' => 'DA', 'tile' => 'bg-blue-500', 'ring' => 'border-blue-400/40', 'text' => 'text-blue-300'],
        'ovo' => ['label' => 'OVO', 'mark' => 'OV', 'tile' => 'bg-purple-500', 'ring' => 'border-purple-400/40', 'text' => 'text-purple-300'],
        'linkaja' => ['label' => 'LinkAja', 'mark' => 'LA', 'tile' => 'bg-red-500', 'ring' => 'border-red-400/40', 'text' => 'text-red-300'],
        'shopeepay' => ['label' => 'ShopeePay', 'mark' => 'SP', 'tile' => 'bg-orange-500', 'ring' => 'border-orange-400/40', 'text' => 'text-orange-300'],
        'qris' => ['label' => 'QRIS', 'mark' => 'QR', 'tile' => 'bg-supabase-accent', 'ring' => 'border-supabase-accent/40', 'text' => 'text-supabase-accent'],
        'bank_transfer' => ['label' => 'Bank App', 'mark' => 'BK', 'tile' => 'bg-emerald-500', 'ring' => 'border-emerald-400/40', 'text' => 'text-emerald-300'],
        'virtual_account' => ['label' => 'VA App', 'mark' => 'VA', 'tile' => 'bg-cyan-500', 'ring' => 'border-cyan-400/40', 'text' => 'text-cyan-300'],
    ];

    $fallbackMobileApps = collect([
        ['channel_type' => 'bank_transfer', 'channel_name' => 'BCA Mobile', 'provider' => 'Bank App', 'is_active' => false, 'transactions_count' => 0],
        ['channel_type' => 'dana', 'channel_name' => 'DANA Wallet', 'provider' => 'E-Wallet', 'is_active' => false, 'transactions_count' => 0],
        ['channel_type' => 'gopay', 'channel_name' => 'GoPay Merchant', 'provider' => 'E-Wallet', 'is_active' => false, 'transactions_count' => 0],
        ['channel_type' => 'qris', 'channel_name' => 'QRIS Node', 'provider' => 'QR Payment', 'is_active' => false, 'transactions_count' => 0],
    ]);

    $mobileApps = $monitored_channels->isNotEmpty() ? $monitored_channels : $fallbackMobileApps;
    $listenerOnline = $tenant->is_active && $stats['active_channels'] > 0;
@endphp

<!-- Header Area -->
<div class="mb-12">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-4xl font-bold text-white tracking-normal">Overview</h1>
            <p class="text-supabase-muted mt-2">Welcome back, <span class="text-supabase-accent font-bold">{{ $tenant->name }}</span>. Here's your business performance.</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="px-4 py-2 bg-supabase-surface border border-supabase-border rounded-xl">
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 rounded-full {{ $tenant->mode === 'production' ? 'bg-green-500' : 'bg-amber-500' }}"></div>
                    <span class="text-xs font-bold uppercase tracking-wider text-white">{{ strtoupper($tenant->mode) }}</span>
                </div>
            </div>
            @if($tenant->plan)
                <div class="px-4 py-2 bg-supabase-accent/10 border border-supabase-accent/20 rounded-xl">
                    <div class="flex items-center space-x-2 text-supabase-accent">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                        <span class="text-[10px] font-bold uppercase tracking-wider">{{ $tenant->plan->name }}</span>
                    </div>
                </div>
            @endif
            <a href="{{ route('tenant.payment-channels.create') }}" class="sb-button-primary !w-auto">
                Add Channel
            </a>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
    <!-- Revenue -->
    <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-6 relative overflow-hidden group shadow-2xl">
        <div class="absolute top-0 right-0 w-32 h-32 bg-green-500/5 blur-3xl rounded-full transition-all group-hover:bg-green-500/10"></div>
        <div class="relative z-10">
            <div class="w-10 h-10 rounded-xl bg-green-500/10 border border-green-500/20 flex items-center justify-center mb-4 text-green-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="text-xs font-bold text-supabase-muted uppercase tracking-wider mb-1">Total Revenue</p>
            <h3 class="text-2xl font-bold text-white">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</h3>
        </div>
    </div>

    <!-- Total Transactions -->
    <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-6 relative overflow-hidden group shadow-2xl">
        <div class="absolute top-0 right-0 w-32 h-32 bg-supabase-accent/5 blur-3xl rounded-full transition-all group-hover:bg-supabase-accent/10"></div>
        <div class="relative z-10">
            <div class="w-10 h-10 rounded-xl bg-supabase-accent/10 border border-supabase-accent/20 flex items-center justify-center mb-4 text-supabase-accent">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            </div>
            <p class="text-xs font-bold text-supabase-muted uppercase tracking-wider mb-1">Success Tx</p>
            <h3 class="text-2xl font-bold text-white">{{ number_format($stats['success_transactions']) }}</h3>
        </div>
    </div>

    <!-- Today -->
    <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-6 relative overflow-hidden group shadow-2xl">
        <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/5 blur-3xl rounded-full transition-all group-hover:bg-blue-500/10"></div>
        <div class="relative z-10">
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center mb-4 text-blue-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
            <p class="text-xs font-bold text-supabase-muted uppercase tracking-wider mb-1">Today</p>
            <h3 class="text-2xl font-bold text-white" id="live-today">{{ number_format($stats['today_transactions']) }}</h3>
        </div>
    </div>

    <!-- Active Channels -->
    <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-6 relative overflow-hidden group shadow-2xl">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-500/5 blur-3xl rounded-full transition-all group-hover:bg-purple-500/10"></div>
        <div class="relative z-10">
            <div class="w-10 h-10 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center mb-4 text-purple-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </div>
            <p class="text-xs font-bold text-supabase-muted uppercase tracking-wider mb-1">Channels</p>
            <h3 class="text-2xl font-bold text-white">{{ $stats['active_channels'] }} / {{ $stats['total_channels'] }}</h3>
        </div>
    </div>
</div>

<!-- Monitoring Area -->
<div class="grid grid-cols-1 xl:grid-cols-5 gap-8 mb-12">
    <div class="xl:col-span-3 bg-supabase-surface border border-supabase-border rounded-2xl p-8 shadow-2xl relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-8 opacity-5">
            <svg class="w-32 h-32 text-supabase-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
        </div>
        <div class="relative z-10">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between mb-8">
                <div>
                    <h3 class="text-xl font-bold text-white tracking-normal">Transaction <span class="text-supabase-accent">Activity</span></h3>
                    <p class="text-[10px] font-bold text-supabase-muted tracking-wider mt-1 uppercase">Volume monitoring (Last 7 Days)</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-full bg-supabase-accent shadow-[0_0_15px_rgba(251,191,36,0.5)] animate-pulse"></span>
                    <span class="text-xs font-bold text-white tracking-wider">Live Flow</span>
                </div>
            </div>
            <div class="h-[320px] w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <div class="xl:col-span-2 bg-supabase-surface border border-supabase-border rounded-2xl p-6 shadow-2xl overflow-hidden relative">
        <div class="absolute inset-x-0 top-0 h-24 bg-gradient-to-b from-supabase-accent/10 to-transparent"></div>
        <div class="relative z-10 flex items-start justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-bold text-white tracking-normal">Mobile App <span class="text-supabase-accent">Relay</span></h3>
                <p class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider mt-1">Apps being watched on Android</p>
            </div>
            <div class="flex items-center gap-2 rounded-xl border border-supabase-border bg-supabase-dark/60 px-3 py-2">
                <span class="h-2 w-2 rounded-full {{ $listenerOnline ? 'bg-green-500 shadow-[0_0_12px_rgba(34,197,94,0.7)] animate-pulse' : 'bg-supabase-muted' }}"></span>
                <span class="text-[9px] font-bold uppercase tracking-wider text-white">{{ $listenerOnline ? 'Listening' : 'Standby' }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-[190px_1fr] xl:grid-cols-1 2xl:grid-cols-[190px_1fr] gap-6 items-start">
            <div class="mx-auto w-[190px] rounded-[2rem] border border-white/10 bg-[#0d0d0d] p-3 shadow-2xl">
                <div class="rounded-[1.5rem] border border-supabase-border bg-supabase-dark p-3 min-h-[330px] overflow-hidden">
                    <div class="mb-5 flex items-center justify-between text-[8px] font-bold uppercase tracking-wider text-supabase-muted">
                        <span>9:41</span>
                        <div class="flex items-center gap-1">
                            <span class="h-1.5 w-3 rounded-full border border-supabase-muted/50"></span>
                            <span class="h-1.5 w-1.5 rounded-full bg-supabase-accent"></span>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-supabase-border bg-supabase-input/80 p-3 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-xl bg-supabase-accent text-supabase-dark flex items-center justify-center font-bold text-xs">CB</div>
                            <div>
                                <p class="text-[10px] font-bold text-white uppercase tracking-wider">Cekbayar</p>
                                <p class="text-[8px] font-bold text-supabase-muted uppercase">Notification listener</p>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach($mobileApps->take(6) as $channel)
                            @php
                                $type = data_get($channel, 'channel_type', 'unknown');
                                $brand = $mobileAppBrandMap[$type] ?? ['label' => strtoupper($type), 'mark' => strtoupper(substr($type, 0, 2)), 'tile' => 'bg-supabase-muted', 'ring' => 'border-supabase-border', 'text' => 'text-slate-300'];
                                $isActive = (bool) data_get($channel, 'is_active', false);
                            @endphp
                            <div class="min-w-0 text-center">
                                <div class="relative mx-auto mb-2 h-12 w-12 rounded-2xl {{ $brand['tile'] }} {{ $type === 'qris' ? 'text-supabase-dark' : 'text-white' }} flex items-center justify-center border {{ $brand['ring'] }} shadow-lg">
                                    <span class="text-[11px] font-bold tracking-normal">{{ $brand['mark'] }}</span>
                                    <span class="absolute -right-1 -top-1 h-3 w-3 rounded-full border-2 border-[#0d0d0d] {{ $isActive ? 'bg-green-500' : 'bg-supabase-muted' }}"></span>
                                </div>
                                <p class="truncate text-[8px] font-bold uppercase tracking-wider text-white">{{ $brand['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                @foreach($mobileApps->take(5) as $channel)
                    @php
                        $type = data_get($channel, 'channel_type', 'unknown');
                        $brand = $mobileAppBrandMap[$type] ?? ['label' => strtoupper($type), 'mark' => strtoupper(substr($type, 0, 2)), 'tile' => 'bg-supabase-muted', 'ring' => 'border-supabase-border', 'text' => 'text-slate-300'];
                        $channelName = data_get($channel, 'channel_name', $brand['label']);
                        $provider = data_get($channel, 'provider') ?: data_get($channel, 'channel_type_name', $brand['label']);
                        $isActive = (bool) data_get($channel, 'is_active', false);
                        $payloadCount = data_get($channel, 'transactions_count', 0);
                    @endphp
                    <div class="flex items-center gap-4 rounded-2xl border border-supabase-border bg-supabase-dark/50 px-4 py-3">
                        <div class="h-11 w-11 flex-shrink-0 rounded-xl {{ $brand['tile'] }} {{ $type === 'qris' ? 'text-supabase-dark' : 'text-white' }} flex items-center justify-center font-bold text-[11px] shadow-lg">
                            {{ $brand['mark'] }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <p class="truncate text-xs font-bold text-white">{{ $channelName }}</p>
                                <span class="h-1.5 w-1.5 flex-shrink-0 rounded-full {{ $isActive ? 'bg-green-500' : 'bg-supabase-muted' }}"></span>
                            </div>
                            <p class="truncate text-[9px] font-bold uppercase tracking-wider text-supabase-muted">{{ $provider }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-white">{{ number_format($payloadCount) }}</p>
                            <p class="text-[8px] font-bold uppercase tracking-wider text-supabase-muted">TX</p>
                        </div>
                    </div>
                @endforeach

                @if($monitored_channels->isEmpty())
                    <a href="{{ route('tenant.payment-channels.create') }}" class="sb-button-secondary !py-3 !text-[10px]">
                        Add Monitored App
                    </a>
                @else
                    <a href="{{ route('tenant.payment-channels.index') }}" class="sb-button-secondary !py-3 !text-[10px]">
                        Manage App Mapping
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left Column: Transactions -->
    <div class="lg:col-span-2 space-y-8">
        <div class="bg-supabase-surface border border-supabase-border rounded-2xl overflow-hidden shadow-2xl">
            <div class="px-8 py-6 border-b border-supabase-border flex items-center justify-between bg-supabase-surface/50">
                <div>
                    <h3 class="text-lg font-bold text-white">Recent Transactions</h3>
                    <p class="text-xs text-supabase-muted uppercase tracking-wider mt-1">Live monitoring</p>
                </div>
                <a href="{{ route('tenant.transactions.index') }}" class="text-xs font-bold text-supabase-accent hover:text-white transition-colors">View All &rarr;</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-supabase-dark/50 text-[10px] uppercase tracking-wider font-bold text-supabase-muted">
                        <tr>
                            <th class="px-8 py-4 text-left">ID / Customer</th>
                            <th class="px-8 py-4 text-left">Amount</th>
                            <th class="px-8 py-4 text-left">Status</th>
                            <th class="px-8 py-4 text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-supabase-border" id="live-invoice-list">
                        @forelse($recent_transactions as $transaction)
                        <tr class="group hover:bg-white/[0.02] transition-colors">
                            <td class="px-8 py-4">
                                <div class="flex flex-col">
                                    <span class="text-xs font-mono text-white mb-1 group-hover:text-supabase-accent transition-colors">{{ $transaction->transaction_id }}</span>
                                    <span class="text-xs text-supabase-muted">{{ $transaction->customer_name ?? 'Anonymous' }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-4">
                                <div class="text-sm font-bold text-white">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-8 py-4">
                                {!! $transaction->statusBadge !!}
                            </td>
                            <td class="px-8 py-4 text-right">
                                <span class="text-xs text-supabase-muted">{{ $transaction->created_at->diffForHumans() }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-8 py-16 text-center">
                                <p class="text-supabase-muted italic">No transactions found.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Quick Info -->
    <div class="space-y-8">
        <!-- API Card -->
        <div class="bg-supabase-accent rounded-2xl p-8 text-supabase-dark shadow-2xl shadow-supabase-accent/10 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-20 group-hover:scale-110 transition-transform">
                <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
            </div>
            <div class="relative z-10">
                <h3 class="text-xl font-bold tracking-normal mb-4">API Secrets</h3>
                <div class="bg-supabase-dark/10 p-4 rounded-xl mb-6 backdrop-blur-sm">
                    <p class="text-[10px] font-bold uppercase tracking-wider opacity-60 mb-2">Active API Key</p>
                    <div class="flex items-center justify-between">
                        <code class="text-xs font-mono font-bold">{{ substr($tenant->getActiveApiKey() ?? '', 0, 16) }}...</code>
                        <button onclick="copyApiKey('{{ $tenant->getActiveApiKey() }}')" class="hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        </button>
                    </div>
                </div>
                <a href="{{ route('tenant.settings') }}" class="text-xs font-bold underline uppercase tracking-wider">Manage Keys</a>
            </div>
        </div>

        <!-- Webhook Status -->
        <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-white">Webhook</h3>
                <div class="w-2 h-2 rounded-full {{ $tenant->webhook_enabled ? 'bg-green-500 shadow-[0_0_10px_rgba(34,197,94,0.5)]' : 'bg-supabase-muted' }}"></div>
            </div>
            @if($tenant->webhook_url)
                <div class="p-4 bg-supabase-input border border-supabase-border rounded-xl mb-4">
                    <p class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider mb-1">Target URL</p>
                    <p class="text-xs font-mono text-white truncate">{{ $tenant->webhook_url }}</p>
                </div>
            @else
                <div class="text-center py-6 border-2 border-dashed border-supabase-border rounded-2xl mb-4">
                    <p class="text-xs text-supabase-muted">Not Configured</p>
                </div>
            @endif
            <a href="{{ route('tenant.settings') }}" class="sb-button-secondary py-2 text-xs">Settings</a>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function copyApiKey(key) {
    if (!key) return;
    navigator.clipboard.writeText(key).then(() => {
        alert('API Key copied to clipboard!');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    const chartData = @json($chart_data);
    
    const labels = chartData.map(item => item.date);
    const dataPoints = chartData.map(item => item.amount);

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

// Simple live update logic
(function liveDashboard() {
    const todayEl = document.getElementById('live-today');
    const invoiceList = document.getElementById('live-invoice-list');
    if (!todayEl) return;

    setInterval(() => {
        fetch('/tenant/dashboard/live-stats', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (todayEl) todayEl.textContent = data.today_transactions ?? 0;
        })
        .catch(() => {});
    }, 10000);
})();
</script>
@endpush

@endsection
