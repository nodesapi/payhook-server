@extends('layouts.admin')

@section('page-title', 'Event Diagnostics')

@section('content')
<div class="w-full max-w-full space-y-8 overflow-x-hidden">
    {{-- Header --}}
    <div>
        <a href="{{ route('admin.webhook-logs.index') }}"
           class="inline-flex items-center gap-2 text-[10px] font-bold text-supabase-muted uppercase tracking-wider hover:text-white transition-colors mb-6">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Bridge Logs
        </a>
        <h1 class="text-4xl font-bold text-white tracking-normal uppercase">Event <span class="text-supabase-accent">Diagnostics</span></h1>
        <p class="text-supabase-muted mt-2 font-mono text-xs tracking-wider">TRACE ID: #{{ $log->id }}</p>
    </div>

    {{-- Details Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 w-full max-w-full">
        {{-- Payload Metadata --}}
        <div class="bg-supabase-surface border border-supabase-border rounded-3xl overflow-hidden shadow-2xl">
            <div class="px-8 py-5 border-b border-supabase-border bg-supabase-dark/50">
                <h3 class="text-[10px] font-bold text-white uppercase tracking-wider">Payload Metadata</h3>
            </div>
            <div class="p-8 space-y-6">
                @foreach([
                    'System Origin' => $log->source ?? 'Unknown Terminal',
                    'Application Package' => $log->package_name ?? 'N/A',
                    'Event Magnitude' => $log->amount ? 'IDR ' . number_format($log->amount) : '0.00',
                    'Ingestion Time' => $log->created_at->format('M d, Y H:i:s') . ' UTC',
                    'Client Timestamp' => $log->notification_timestamp ? $log->notification_timestamp->format('M d, Y H:i:s') : 'N/A'
                ] as $label => $value)
                <div class="flex items-center justify-between border-b border-supabase-border/30 pb-4 last:border-0 last:pb-0">
                    <span class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">{{ $label }}</span>
                    <span class="text-[10px] font-bold text-white uppercase tracking-normal text-right">{{ $value }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Reconciliation State --}}
        <div class="bg-supabase-surface border border-supabase-border rounded-3xl overflow-hidden shadow-2xl">
            <div class="px-8 py-5 border-b border-supabase-border bg-supabase-dark/50">
                <h3 class="text-[10px] font-bold text-white uppercase tracking-wider">Logic State</h3>
            </div>
            <div class="p-8 space-y-6">
                <div class="flex items-center justify-between border-b border-supabase-border/30 pb-4">
                    <span class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Processing Result</span>
                    @php
                        $statusMap = [
                            'matched'   => 'bg-green-500/10 text-green-500 border-green-500/20',
                            'unmatched' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                            'failed'    => 'bg-red-500/10 text-red-500 border-red-500/20',
                            'duplicate' => 'bg-supabase-muted/10 text-supabase-muted border-supabase-muted/20',
                        ];
                        $cls = $statusMap[$log->status] ?? 'bg-supabase-muted/10 text-supabase-muted';
                    @endphp
                    <span class="px-3 py-1 rounded-full border text-[8px] font-bold uppercase tracking-wider {{ $cls }}">{{ $log->status }}</span>
                </div>

                <div class="flex items-center justify-between border-b border-supabase-border/30 pb-4">
                    <span class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Assigned Tenant</span>
                    @if($log->tenant)
                        <a href="{{ route('admin.tenants.edit', $log->tenant->id) }}" class="text-[10px] font-bold text-supabase-accent hover:text-white transition-colors uppercase tracking-normal">{{ $log->tenant->name }}</a>
                    @else
                        <span class="text-[10px] font-bold text-white uppercase tracking-normal">ORPHAN</span>
                    @endif
                </div>

                <div class="flex items-center justify-between border-b border-supabase-border/30 pb-4">
                    <span class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Linked Invoice</span>
                    <span class="text-[10px] font-bold text-white uppercase font-mono tracking-normal">{{ $log->invoice?->invoice_number ?? 'NONE' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Notification Content --}}
    <div class="bg-supabase-surface border border-supabase-border rounded-3xl overflow-hidden shadow-2xl">
        <div class="px-8 py-5 border-b border-supabase-border bg-supabase-dark/50">
            <h3 class="text-[10px] font-bold text-white uppercase tracking-wider">Device Payload</h3>
        </div>
        <div class="p-8 space-y-6">
            <div>
                <label class="text-[8px] font-bold text-supabase-muted uppercase tracking-wider block mb-2">Notification Title</label>
                <div class="p-4 bg-supabase-dark border border-supabase-border rounded-xl text-white font-bold text-sm uppercase tracking-normal">
                    {{ $log->notification_title ?? 'EMPTY_TITLE' }}
                </div>
            </div>
            <div>
                <label class="text-[8px] font-bold text-supabase-muted uppercase tracking-wider block mb-2">Notification Body</label>
                <div class="p-4 bg-supabase-dark border border-supabase-border rounded-xl text-supabase-muted font-bold text-xs leading-relaxed">
                    {{ $log->notification_text ?? 'EMPTY_BODY' }}
                </div>
            </div>
            @if($log->notes)
            <div>
                <label class="text-[8px] font-bold text-supabase-muted uppercase tracking-wider block mb-2">System Analysis Notes</label>
                <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 font-bold text-xs italic">
                    {{ $log->notes }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
