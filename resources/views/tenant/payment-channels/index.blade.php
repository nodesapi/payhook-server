@extends('layouts.tenant')

@section('title', 'Payment Matrix')

@section('content')

<!-- Page Header -->
<div class="mb-12">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tight uppercase">Payment <span class="text-supabase-accent">Matrix</span></h1>
            <p class="text-supabase-muted mt-2">Manage your financial entry points and QRIS distribution nodes.</p>
        </div>
        <a href="{{ route('tenant.payment-channels.create') }}" class="sb-button-primary !w-auto !py-3 !px-8">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Provision New Node
        </a>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-12">
    @foreach([
        ['Total', $stats['total'], 'bg-supabase-muted/20 text-white', 'M3 10h18'],
        ['Active', $stats['active'], 'bg-green-500/20 text-green-500', 'M9 12l2 2 4-4'],
        ['QRIS', $stats['qris'], 'bg-supabase-accent/20 text-supabase-accent', 'M12 4v1'],
        ['E-Wallet', $stats['ewallet'], 'bg-purple-500/20 text-purple-500', 'M17 9V7'],
        ['Bank', $stats['bank'], 'bg-blue-500/20 text-blue-500', 'M8 14v3']
    ] as [$label, $value, $color, $path])
    <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-6 relative overflow-hidden group">
        <p class="text-[8px] font-black text-supabase-muted uppercase tracking-[0.2em] mb-2">{{ $label }}</p>
        <p class="text-2xl font-black text-white leading-none">{{ $value }}</p>
        <div class="absolute -right-2 -bottom-2 opacity-5 group-hover:scale-125 transition-transform">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"></path></svg>
        </div>
    </div>
    @endforeach
</div>

<!-- Success Message -->
@if(session('success'))
    <div class="mb-8 bg-green-500/10 border border-green-500/20 text-green-500 px-6 py-4 rounded-2xl flex items-center shadow-lg">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="text-xs font-black uppercase tracking-widest">{{ session('success') }}</span>
    </div>
@endif

<!-- Payment Channels List -->
@if($channels->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($channels as $channel)
            @php
                $brandMap = [
                    'gopay' => ['label' => 'GoPay', 'bg' => 'from-sky-500/20 to-sky-600/5', 'border' => 'border-sky-500/30', 'text' => 'text-sky-400'],
                    'dana' => ['label' => 'DANA', 'bg' => 'from-blue-500/20 to-blue-700/5', 'border' => 'border-blue-500/30', 'text' => 'text-blue-400'],
                    'ovo' => ['label' => 'OVO', 'bg' => 'from-purple-500/20 to-purple-700/5', 'border' => 'border-purple-500/30', 'text' => 'text-purple-400'],
                    'shopeepay' => ['label' => 'ShopeePay', 'bg' => 'from-orange-500/20 to-orange-700/5', 'border' => 'border-orange-500/30', 'text' => 'text-orange-400'],
                    'qris' => ['label' => 'QRIS Node', 'bg' => 'from-supabase-accent/20 to-supabase-accent/5', 'border' => 'border-supabase-accent/30', 'text' => 'text-supabase-accent'],
                ];
                $brand = $brandMap[$channel->channel_type] ?? ['label' => strtoupper($channel->channel_type), 'bg' => 'from-supabase-muted/20 to-transparent', 'border' => 'border-supabase-border', 'text' => 'text-white'];
            @endphp
            <div class="bg-supabase-surface border border-supabase-border rounded-3xl overflow-hidden shadow-2xl hover:border-supabase-accent/30 transition-all duration-300 group">
                
                <!-- Channel Visual -->
                <div class="bg-gradient-to-br {{ $brand['bg'] }} p-8 flex items-center justify-center border-b border-supabase-border h-64 relative overflow-hidden">
                    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fbbf24_1px,transparent_1px)] [background-size:16px_16px]"></div>
                    
                    @if($channel->channel_type === 'qris' && $channel->qr_code_path)
                        <div class="relative z-10 p-3 bg-white rounded-2xl shadow-2xl shadow-supabase-accent/20 group-hover:scale-105 transition-transform duration-500">
                            <img src="{{ asset('storage/' . $channel->qr_code_path) }}" alt="QR Code" class="w-40 h-40 object-contain">
                        </div>
                    @else
                        <div class="relative z-10 text-center">
                            <div class="w-24 h-24 rounded-3xl bg-supabase-dark border {{ $brand['border'] }} flex items-center justify-center mx-auto mb-4 shadow-2xl {{ $brand['text'] }}">
                                <span class="text-3xl font-black">{{ substr($brand['label'], 0, 1) }}</span>
                            </div>
                            <p class="text-[10px] font-black uppercase tracking-[0.3em] {{ $brand['text'] }}">{{ $brand['label'] }}</p>
                        </div>
                    @endif
                </div>

                <!-- Channel Info -->
                <div class="p-8 space-y-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-lg font-black text-white leading-tight uppercase tracking-tight">{{ $channel->channel_name }}</h3>
                            <p class="text-[10px] font-bold text-supabase-muted uppercase tracking-widest mt-1">{{ $channel->channel_type_name }}</p>
                        </div>
                        <div class="flex items-center space-x-2 bg-supabase-dark border border-supabase-border px-2 py-1 rounded-full">
                            <div class="w-1.5 h-1.5 rounded-full {{ $channel->is_active ? 'bg-green-500 animate-pulse' : 'bg-supabase-muted' }}"></div>
                            <span class="text-[8px] font-black uppercase text-white">{{ $channel->is_active ? 'Online' : 'Paused' }}</span>
                        </div>
                    </div>

                    <!-- Metrics -->
                    <div class="grid grid-cols-2 gap-4 py-4 border-y border-supabase-border/50">
                        <div class="text-left">
                            <p class="text-[8px] font-black text-supabase-muted uppercase tracking-widest mb-1">Total Payload</p>
                            <p class="text-sm font-black text-white">{{ number_format($channel->transactions_count ?? 0) }} <span class="text-[10px] text-supabase-muted ml-1 font-bold">TX</span></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[8px] font-black text-supabase-muted uppercase tracking-widest mb-1">Commission</p>
                            <p class="text-sm font-black text-supabase-accent">
                                @if($channel->fee_percentage > 0)
                                    {{ $channel->fee_percentage }}%
                                @elseif($channel->fee_fixed > 0)
                                    {{ number_format($channel->fee_fixed) }}
                                @else
                                    0.00
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3">
                        <a href="{{ route('tenant.payment-channels.edit', $channel) }}" class="flex-1 sb-button-secondary !w-auto !py-2.5 !text-[10px] !font-black uppercase tracking-widest">
                            Config
                        </a>
                        
                        <form action="{{ route('tenant.payment-channels.toggle', $channel) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2.5 text-[10px] font-black rounded-xl border {{ $channel->is_active ? 'border-amber-500/30 text-amber-500 bg-amber-500/5 hover:bg-amber-500 hover:text-white' : 'border-green-500/30 text-green-500 bg-green-500/5 hover:bg-green-500 hover:text-white' }} transition-all uppercase tracking-widest">
                                {{ $channel->is_active ? 'Pause' : 'Resume' }}
                            </button>
                        </form>

                        <button 
                            onclick="deleteChannel({{ $channel->id }}, '{{ $channel->channel_name }}')"
                            class="px-4 py-2.5 bg-red-500/5 border border-red-500/30 text-red-500 hover:bg-red-500 hover:text-white rounded-xl transition-all"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <!-- Empty State -->
    <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-20 text-center relative overflow-hidden shadow-2xl">
        <div class="absolute inset-0 opacity-5 bg-[radial-gradient(#fbbf24_1px,transparent_1px)] [background-size:24px_24px]"></div>
        <div class="relative z-10 max-w-md mx-auto">
            <div class="w-24 h-24 bg-supabase-input border border-supabase-border rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-2xl">
                <svg class="w-12 h-12 text-supabase-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </div>
            <h3 class="text-2xl font-black text-white uppercase tracking-tight mb-4">No Distribution Nodes</h3>
            <p class="text-supabase-muted text-xs font-bold uppercase tracking-widest leading-relaxed mb-10">Initialize your first payment channel to start receiving automated bank and e-wallet notifications.</p>
            <a href="{{ route('tenant.payment-channels.create') }}" class="sb-button-primary !w-auto !py-4 !px-12">Initialize Primary Node</a>
        </div>
    </div>
@endif

<x-confirm-modal />

@push('scripts')
<script>
function deleteChannel(id, name) {
    showConfirmModal({
        type: 'danger',
        title: 'Decommission Node?',
        message: `Confirm decommissioning of node "${name}". This will terminate all transaction routing for this channel.`,
        confirmText: 'Decommission',
        onConfirm: () => {
            const form = document.createElement('form');
            form.method = 'POST'; form.action = `/tenant/payment-channels/${id}`;
            form.innerHTML = `@csrf @method('DELETE')`;
            document.body.appendChild(form); form.submit();
        }
    });
}
</script>
@endpush

@endsection
