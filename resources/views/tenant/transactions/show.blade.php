@extends('layouts.tenant')

@section('title', 'Transaction Audit')

@section('content')

<!-- Header & Actions -->
<div class="mb-12 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
    <div>
        <a href="{{ route('tenant.transactions.index') }}" class="inline-flex items-center space-x-2 text-[10px] font-bold text-supabase-muted uppercase tracking-wider hover:text-white transition-colors mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            <span>Back to Ledger</span>
        </a>
        <h1 class="text-4xl font-bold text-white tracking-normal uppercase">Audit <span class="text-supabase-accent">Trail</span></h1>
        <p class="text-supabase-muted mt-2 font-mono text-xs">Node ID: {{ $transaction->transaction_id }}</p>
    </div>
    <div class="flex items-center space-x-3">
        @if(in_array($transaction->status, ['pending', 'processing'], true))
            <form method="POST" action="{{ route('tenant.transactions.confirm', $transaction) }}" onsubmit="return confirm('Tandai transaksi ini SUKSES secara manual? Gunakan hanya jika dana sudah benar-benar diterima (mis. notifikasi otomatis tidak terdeteksi). Aksi ini akan mengirim webhook ke merchant.')">
                @csrf
                <button type="submit" class="px-6 py-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 hover:bg-emerald-500 hover:text-white rounded-xl text-[10px] font-bold uppercase tracking-wider transition-all">
                    Tandai Sukses (Manual)
                </button>
            </form>
        @endif
        <form method="POST" action="{{ route('tenant.transactions.resend-webhook', $transaction) }}">
            @csrf
            <button type="submit" class="sb-button-secondary !w-auto !py-3 !px-6 !text-[10px] uppercase font-bold tracking-wider">
                Force Webhook Sync
            </button>
        </form>
        @if($transaction->status === 'success')
            <form method="POST" action="{{ route('tenant.transactions.refund', $transaction) }}" onsubmit="return confirm('Execute refund protocol?')">
                @csrf
                <button type="submit" class="px-6 py-3 bg-red-500/10 border border-red-500/20 text-red-500 hover:bg-red-500 hover:text-white rounded-xl text-[10px] font-bold uppercase tracking-wider transition-all">
                    Initiate Refund
                </button>
            </form>
        @endif
    </div>
</div>

<!-- Magnitude Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 relative overflow-hidden shadow-2xl">
        <p class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider mb-2">Gross Magnitude</p>
        <p class="text-3xl font-bold text-white">IDR {{ number_format($transaction->amount) }}</p>
        <div class="absolute -right-2 -bottom-2 opacity-5">
            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
    </div>
    <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 relative overflow-hidden shadow-2xl">
        <p class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider mb-2">Protocol Fee</p>
        <p class="text-3xl font-bold text-white">IDR {{ number_format($transaction->fee ?? $transaction->fee_amount ?? 0) }}</p>
        <div class="absolute -right-2 -bottom-2 opacity-5 text-red-500">
            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
    </div>
    <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 relative overflow-hidden shadow-2xl">
        <p class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider mb-2">Net Settlement</p>
        <p class="text-3xl font-bold text-supabase-accent">IDR {{ number_format($transaction->net_amount ?? ($transaction->amount - $transaction->fee_amount)) }}</p>
        <div class="absolute -right-2 -bottom-2 opacity-10 text-supabase-accent">
            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Infrastructure Details -->
    <div class="bg-supabase-surface border border-supabase-border rounded-3xl overflow-hidden shadow-2xl">
        <div class="px-8 py-6 border-b border-supabase-border bg-supabase-dark/30">
            <h3 class="text-xs font-bold text-white uppercase tracking-wider">Transaction Metadata</h3>
        </div>
        <div class="p-8 space-y-6">
            @foreach([
                'Status' => $transaction->status,
                'Input Node' => $transaction->paymentChannel?->channel_name ?? 'Legacy Terminal',
                'Methodology' => $transaction->payment_method ?? 'Not Specified',
                'External ID' => $transaction->external_id ?? 'None',
                'Reference' => $transaction->payment_reference ?? 'None',
                'Initialized' => $transaction->created_at?->format('M d, Y H:i:s') . ' UTC',
                'Finalized' => $transaction->paid_at?->format('M d, Y H:i:s') . ' UTC'
            ] as $label => $value)
                <div class="flex items-center justify-between py-1">
                    <span class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">{{ $label }}</span>
                    @if($label === 'Status')
                        <span class="px-3 py-1 rounded-full border border-supabase-accent/20 bg-supabase-accent/10 text-supabase-accent text-[8px] font-bold uppercase tracking-wider">{{ $value }}</span>
                    @else
                        <span class="text-[10px] font-bold text-white uppercase tracking-normal">{{ $value }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Entity & Sync -->
    <div class="bg-supabase-surface border border-supabase-border rounded-3xl overflow-hidden shadow-2xl">
        <div class="px-8 py-6 border-b border-supabase-border bg-supabase-dark/30">
            <h3 class="text-xs font-bold text-white uppercase tracking-wider">Entity & Sync Diagnostics</h3>
        </div>
        <div class="p-8 space-y-6">
            @foreach([
                'Entity Name' => $transaction->customer_name ?? 'Anonymous',
                'Communication' => $transaction->customer_email ?? 'N/A',
                'Contact' => $transaction->customer_phone ?? 'N/A',
                'Sync State' => $transaction->webhook_sent ? 'DELIVERED' : 'PENDING',
                'Sync Attempts' => $transaction->webhook_attempts ?? 0,
                'Last Payload' => $transaction->webhook_sent_at?->format('M d, Y H:i:s') . ' UTC'
            ] as $label => $value)
                <div class="flex items-center justify-between py-1">
                    <span class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">{{ $label }}</span>
                    @if($label === 'Sync State')
                        <span class="px-3 py-1 rounded-full border {{ $transaction->webhook_sent ? 'border-green-500/20 bg-green-500/10 text-green-500' : 'border-amber-500/20 bg-amber-500/10 text-amber-500' }} text-[8px] font-bold uppercase tracking-wider">{{ $value }}</span>
                    @else
                        <span class="text-[10px] font-bold text-white uppercase tracking-normal">{{ $value }}</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- QR Visualization -->
@if($transaction->paymentChannel?->qr_code_path)
<div class="mt-8 bg-supabase-surface border border-supabase-border rounded-3xl p-8 shadow-2xl">
    <div class="flex flex-col lg:flex-row items-center gap-12">
        <div class="relative group">
            <div class="absolute inset-0 bg-supabase-accent/20 blur-3xl opacity-20 group-hover:opacity-40 transition-opacity"></div>
            <div class="relative bg-white p-4 rounded-3xl shadow-2xl group-hover:scale-105 transition-transform duration-500">
                <img src="{{ asset('storage/' . $transaction->paymentChannel->qr_code_path) }}" alt="QR Matrix" class="w-64 h-64 object-contain">
            </div>
        </div>
        <div class="flex-1 space-y-6">
            <div class="space-y-2 text-center lg:text-left">
                <h4 class="text-xl font-bold text-white uppercase tracking-normal">Active QR <span class="text-supabase-accent">Matrix</span></h4>
                <p class="text-[10px] text-supabase-muted font-bold uppercase tracking-wider leading-relaxed">This QR code was utilized for the current transaction sequence. Ensure the terminal amount matches the requested magnitude.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach([
                    '1. Initialize scan via banking app.',
                    '2. Verify magnitude: IDR ' . number_format($transaction->amount),
                    '3. Execute secure authorization.',
                    '4. Wait for Node Sync confirmation.'
                ] as $step)
                    <div class="px-4 py-3 bg-supabase-dark border border-supabase-border rounded-xl text-[8px] font-bold text-supabase-muted uppercase tracking-wider">
                        {{ $step }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

<!-- Webhook Logs -->
@if($transaction->webhook_response)
<div class="mt-8 bg-supabase-surface border border-supabase-border rounded-3xl overflow-hidden shadow-2xl">
    <div class="px-8 py-6 border-b border-supabase-border bg-supabase-dark/30 flex items-center justify-between">
        <h3 class="text-xs font-bold text-white uppercase tracking-wider">Callback Diagnostics</h3>
        <span class="text-[8px] font-bold text-supabase-muted uppercase tracking-wider">JSON Output</span>
    </div>
    <div class="p-8">
        <pre class="bg-supabase-dark border border-supabase-border rounded-2xl p-6 overflow-x-auto text-supabase-accent font-mono text-xs">{{ json_encode(json_decode($transaction->webhook_response), JSON_PRETTY_PRINT) }}</pre>
    </div>
</div>
@endif

@endsection
