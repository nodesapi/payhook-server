@extends('layouts.tenant')

@section('title', 'Transaction Ledger')

@section('content')

<!-- Page Header -->
<div class="mb-12">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tight uppercase">Audit <span class="text-supabase-accent">Ledger</span></h1>
            <p class="text-supabase-muted mt-2">Comprehensive record of all incoming financial payloads and reconciliation status.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('tenant.transactions.export') }}?{{ http_build_query(request()->except('page')) }}" class="sb-button-secondary !w-auto !py-3 !px-6 !text-[10px] uppercase font-black tracking-widest flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Matrix
            </a>
            <button onclick="window.location.reload()" class="p-3 bg-supabase-surface border border-supabase-border rounded-xl text-supabase-muted hover:text-white transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
    @foreach([
        ['Total Volume', number_format($stats['total']), 'bg-supabase-muted/20 text-white', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7'],
        ['Confirmed', number_format($stats['success']), 'bg-green-500/20 text-green-500', 'M9 12l2 2 4-4'],
        ['In Queue', number_format($stats['pending']), 'bg-amber-500/20 text-amber-500', 'M12 8v4l3 3'],
        ['Anomalies', number_format($stats['failed']), 'bg-red-500/20 text-red-500', 'M10 14l2-2']
    ] as [$label, $value, $color, $path])
    <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-6 relative overflow-hidden group">
        <p class="text-[8px] font-black text-supabase-muted uppercase tracking-[0.2em] mb-2">{{ $label }}</p>
        <p class="text-2xl font-black text-white leading-none tracking-tighter">{{ $value }}</p>
        <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:scale-125 transition-transform">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"></path></svg>
        </div>
    </div>
    @endforeach
</div>

<!-- Filters -->
<div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 mb-12 shadow-2xl relative overflow-hidden">
    <div class="absolute inset-0 opacity-5 bg-[radial-gradient(#fbbf24_1px,transparent_1px)] [background-size:24px_24px]"></div>
    <form method="GET" action="{{ route('tenant.transactions.index') }}" class="relative z-10 space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-supabase-muted uppercase tracking-widest">Search Index</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ID, Identity, Email..." class="sb-input">
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black text-supabase-muted uppercase tracking-widest">Status Filter</label>
                <select name="status" class="sb-input bg-supabase-dark">
                    <option value="">All States</option>
                    @foreach(['pending' => 'Pending', 'success' => 'Success', 'failed' => 'Failed', 'expired' => 'Expired'] as $k => $v)
                        <option value="{{ $k }}" {{ request('status') == $k ? 'selected' : '' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black text-supabase-muted uppercase tracking-widest">Input Node</label>
                <select name="channel" class="sb-input bg-supabase-dark">
                    <option value="">All Channels</option>
                    @foreach($channels as $channel)
                        <option value="{{ $channel->id }}" {{ request('channel') == $channel->id ? 'selected' : '' }}>{{ $channel->channel_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black text-supabase-muted uppercase tracking-widest">Temporal Range</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="sb-input">
            </div>
        </div>

        <div class="flex items-center justify-between pt-8 border-t border-supabase-border/50">
            <div class="flex items-center space-x-4">
                <button type="submit" class="sb-button-primary !w-auto !py-3 !px-8">Query Database</button>
                @if(request()->hasAny(['search', 'status', 'channel', 'date_from']))
                    <a href="{{ route('tenant.transactions.index') }}" class="text-[10px] font-black text-supabase-muted uppercase tracking-widest hover:text-white transition-colors">Clear Parameters</a>
                @endif
            </div>
            <p class="text-[10px] font-black text-supabase-muted uppercase tracking-widest">
                Retrieved <span class="text-white">{{ $transactions->total() }}</span> entries
            </p>
        </div>
    </form>
</div>

<!-- Transactions Table -->
<div class="bg-supabase-surface border border-supabase-border rounded-3xl overflow-hidden shadow-2xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-supabase-dark/50 border-b border-supabase-border">
                    <th class="px-8 py-6 text-xs font-black text-supabase-muted uppercase tracking-widest">Node ID</th>
                    <th class="px-8 py-6 text-xs font-black text-supabase-muted uppercase tracking-widest">Entity</th>
                    <th class="px-8 py-6 text-xs font-black text-supabase-muted uppercase tracking-widest">Source</th>
                    <th class="px-8 py-6 text-xs font-black text-supabase-muted uppercase tracking-widest text-right">Magnitude</th>
                    <th class="px-8 py-6 text-xs font-black text-supabase-muted uppercase tracking-widest text-center">Status</th>
                    <th class="px-8 py-6 text-xs font-black text-supabase-muted uppercase tracking-widest">Timestamp</th>
                    <th class="px-8 py-6 text-xs font-black text-supabase-muted uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-supabase-border/30">
                @forelse($transactions as $transaction)
                    <tr class="hover:bg-white/[0.02] transition-colors group">
                        <td class="px-8 py-6">
                            <span class="font-mono text-sm font-black text-white group-hover:text-supabase-accent transition-colors tracking-tighter">{{ $transaction->transaction_id }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-xs font-black text-white uppercase tracking-tight">{{ $transaction->customer_name ?? 'Anonymous' }}</p>
                            <p class="text-[10px] text-supabase-muted font-bold uppercase mt-0.5">{{ $transaction->customer_email ?? 'no-email@identity.net' }}</p>
                        </td>
                        <td class="px-8 py-6">
                            @if($transaction->paymentChannel)
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg bg-supabase-input border border-supabase-border flex items-center justify-center text-[10px] font-black text-supabase-accent">
                                        {{ substr($transaction->paymentChannel->channel_type, 0, 1) }}
                                    </div>
                                    <p class="text-xs font-black text-white uppercase tracking-tight">{{ $transaction->paymentChannel->channel_name }}</p>
                                </div>
                            @else
                                <span class="text-supabase-muted text-xs font-black">LEGACY</span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-right">
                            <p class="text-sm font-black text-white">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</p>
                            <p class="text-[10px] text-supabase-muted font-bold uppercase mt-0.5">NET: Rp {{ number_format($transaction->amount - $transaction->fee_amount, 0, ',', '.') }}</p>
                        </td>
                        <td class="px-8 py-6 text-center">
                            @php
                                $statusColors = [
                                    'success' => 'bg-green-500/10 text-green-500 border-green-500/20',
                                    'pending' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                                    'failed' => 'bg-red-500/10 text-red-500 border-red-500/20',
                                    'expired' => 'bg-supabase-muted/10 text-supabase-muted border-supabase-muted/20'
                                ];
                                $color = $statusColors[$transaction->status] ?? $statusColors['pending'];
                            @endphp
                            <span class="inline-flex px-3 py-1 rounded-full border text-[10px] font-black uppercase tracking-widest {{ $color }}">
                                {{ $transaction->status }}
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-xs font-black text-white uppercase tracking-tight">{{ $transaction->created_at->setTimezone('Asia/Jakarta')->translatedFormat('d F Y') }}</p>
                            <p class="text-[10px] text-supabase-muted font-bold uppercase mt-0.5">{{ $transaction->created_at->setTimezone('Asia/Jakarta')->format('H:i:s') }} WIB</p>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('tenant.transactions.show', $transaction) }}" class="p-2 bg-supabase-input border border-supabase-border rounded-lg text-supabase-muted hover:text-white transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </a>
                                @if($transaction->status === 'success' && !$transaction->webhook_sent)
                                    <form method="POST" action="{{ route('tenant.transactions.resend-webhook', $transaction) }}">
                                        @csrf
                                        <button type="submit" class="p-2 bg-supabase-accent/10 border border-supabase-accent/20 rounded-lg text-supabase-accent hover:bg-supabase-accent hover:text-supabase-dark transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-8 py-24 text-center">
                            <div class="max-w-xs mx-auto space-y-4">
                                <div class="w-16 h-16 bg-supabase-input border border-supabase-border rounded-2xl flex items-center justify-center mx-auto text-supabase-muted">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <h3 class="text-xs font-black text-white uppercase tracking-widest">Null Set Returned</h3>
                                <p class="text-[10px] text-supabase-muted font-bold uppercase tracking-tighter">No transaction data found matching your query parameters.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($transactions->hasPages())
        <div class="px-8 py-6 bg-supabase-dark/30 border-t border-supabase-border">
            {{ $transactions->links() }}
        </div>
    @endif
</div>

@endsection
