@extends('layouts.tenant')

@section('title', 'Configure Node')

@section('content')

<!-- Page Header -->
<div class="mb-12">
    <div class="flex items-center space-x-4">
        <a href="{{ route('tenant.payment-channels.index') }}" class="p-3 bg-supabase-surface border border-supabase-border rounded-xl text-supabase-muted hover:text-white transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-4xl font-bold text-white tracking-normal uppercase">Configure <span class="text-supabase-accent">Node</span></h1>
            <p class="text-supabase-muted mt-2">Adjust operational parameters for the <span class="text-white">{{ $paymentChannel->channel_name }}</span> matrix.</p>
        </div>
    </div>
</div>

@php
    $type = $paymentChannel->channel_type;
@endphp

<!-- Form Container -->
<div class="w-full">
    <form action="{{ route('tenant.payment-channels.update', $paymentChannel) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- Node Status & Type (Read Only) -->
        <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 space-y-8 shadow-2xl">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center space-x-6">
                    <div class="w-20 h-20 rounded-3xl bg-supabase-dark border border-supabase-border flex items-center justify-center shadow-2xl overflow-hidden group">
                        @if($type === 'qris' && $paymentChannel->qr_code_path)
                            <img src="{{ asset('storage/' . $paymentChannel->qr_code_path) }}" alt="QRIS" class="w-16 h-16 object-contain group-hover:scale-110 transition-transform">
                        @else
                            <div class="text-2xl font-bold text-supabase-accent uppercase">{{ substr($type, 0, 1) }}</div>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-white uppercase tracking-wider">{{ $paymentChannel->channel_type_name }}</h3>
                        <p class="text-[8px] text-supabase-muted font-bold uppercase tracking-normal mt-1">Infrastructure type is locked after provisioning</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4 p-4 bg-supabase-dark border border-supabase-border rounded-2xl">
                    <div class="w-2 h-2 rounded-full {{ $paymentChannel->is_active ? 'bg-green-500 animate-pulse' : 'bg-supabase-muted' }}"></div>
                    <span class="text-[10px] font-bold uppercase text-white tracking-wider">{{ $paymentChannel->is_active ? 'Operational' : 'Paused' }}</span>
                </div>
            </div>

            <div class="border-t border-supabase-border pt-8 space-y-2">
                <label class="block text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Internal Node Alias</label>
                <input type="text" name="channel_name" value="{{ old('channel_name', $paymentChannel->channel_name) }}" class="sb-input" required>
                @error('channel_name')<p class="mt-1 text-[10px] font-bold text-red-500 uppercase tracking-wider">{{ $message }}</p>@enderror
            </div>
        </div>

        <!-- Conditional Parameters -->
        <div class="space-y-8">
            @if(in_array($type, ['gopay', 'dana', 'ovo', 'linkaja', 'shopeepay']))
                <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 space-y-6">
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-4 flex items-center">
                        <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-3 shadow-[0_0_10px_rgba(59,130,246,0.5)]"></span>
                        E-Wallet Sync
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Phone Identifier</label>
                            <input type="text" name="account_number" value="{{ old('account_number', $paymentChannel->account_number) }}" class="sb-input">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Owner Identity</label>
                            <input type="text" name="account_name" value="{{ old('account_name', $paymentChannel->account_name) }}" class="sb-input">
                        </div>
                    </div>
                </div>
            @endif

            @if($type === 'qris')
                <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 space-y-8">
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-4 flex items-center">
                        <span class="w-1.5 h-1.5 bg-supabase-accent rounded-full mr-3 shadow-[0_0_10px_rgba(251,191,36,0.5)]"></span>
                        QRIS Matrix Update
                    </h3>
                    <div class="max-w-xl mx-auto text-center space-y-6">
                        <div class="relative group">
                            <input type="file" name="qr_code" id="qr_code" accept="image/*" class="sr-only" onchange="previewQR(event)">
                            <label for="qr_code" class="block bg-supabase-dark border-2 border-dashed border-supabase-border rounded-3xl p-12 hover:border-supabase-accent/50 transition-all cursor-pointer group">
                                <div id="qr-preview-container" class="mb-6">
                                    <img id="qr-preview" src="{{ $paymentChannel->qr_code_path ? asset('storage/' . $paymentChannel->qr_code_path) : '' }}" class="w-64 h-64 object-contain mx-auto rounded-2xl shadow-2xl shadow-supabase-accent/10">
                                </div>
                                <p class="text-[10px] font-bold text-white uppercase tracking-wider">Replace Matrix Image</p>
                                <p class="text-[8px] text-supabase-muted font-bold uppercase tracking-normal mt-1">PNG, JPG (MAX 2MB)</p>
                            </label>
                        </div>
                    </div>
                </div>
            @endif

            @if($type === 'bank_transfer')
                <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 space-y-8">
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-4 flex items-center">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-3"></span>
                        Bank Protocol Settings
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Bank Institution</label>
                            <input type="text" name="provider" value="{{ old('provider', $paymentChannel->provider) }}" class="sb-input">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Account Number</label>
                            <input type="text" name="account_number" value="{{ old('account_number', $paymentChannel->account_number) }}" class="sb-input">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Account Holder</label>
                            <input type="text" name="account_name" value="{{ old('account_name', $paymentChannel->account_name) }}" class="sb-input">
                        </div>
                    </div>
                </div>
            @endif

            @if($type === 'virtual_account')
                <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 space-y-8">
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-4 flex items-center">
                        <span class="w-1.5 h-1.5 bg-purple-500 rounded-full mr-3"></span>
                        Virtual Account Matrix
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Bank Provider</label>
                            <input type="text" name="provider" value="{{ old('provider', $paymentChannel->provider) }}" class="sb-input">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Display Identity</label>
                            <input type="text" name="account_name" value="{{ old('account_name', $paymentChannel->account_name) }}" class="sb-input">
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Commission Management -->
        <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 space-y-8 shadow-2xl">
            <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-4 flex items-center">
                <span class="w-1.5 h-1.5 bg-white rounded-full mr-3"></span>
                Commission Pipeline
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Percentage MDR (%)</label>
                    <input type="number" name="fee_percentage" value="{{ old('fee_percentage', $paymentChannel->fee_percentage) }}" min="0" max="100" step="0.01" class="sb-input">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Fixed Convenience Fee (Rp)</label>
                    <input type="number" name="fee_fixed" value="{{ old('fee_fixed', $paymentChannel->fee_fixed) }}" min="0" step="100" class="sb-input">
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 space-y-2">
            <label class="block text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Internal Description</label>
            <textarea name="description" rows="3" class="sb-input resize-none">{{ old('description', $paymentChannel->description) }}</textarea>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-6 pt-8 border-t border-supabase-border">
            <a href="{{ route('tenant.payment-channels.index') }}" class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider hover:text-white transition-colors">Discard</a>
            <button type="submit" class="sb-button-primary !w-auto !py-4 !px-16">
                Commit Updates
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
function previewQR(event) {
    const file = event.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        document.getElementById('qr-preview').src = e.target.result;
        document.getElementById('qr-preview-container').classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}
</script>
@endpush

@endsection
