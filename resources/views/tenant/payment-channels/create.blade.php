@extends('layouts.tenant')

@section('title', 'Provision Channel')

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
            <h1 class="text-4xl font-bold text-white tracking-normal uppercase">Provision <span class="text-supabase-accent">Channel</span></h1>
            <p class="text-supabase-muted mt-2">Initialize a new financial entry node for automated reconciliation.</p>
        </div>
    </div>
</div>

<!-- Form Container -->
<div class="w-full">
    <form action="{{ route('tenant.payment-channels.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- Channel Type Selection -->
        <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 space-y-8 shadow-2xl">
            <div>
                <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-6 flex items-center">
                    <span class="w-1.5 h-1.5 bg-supabase-accent rounded-full mr-3 shadow-[0_0_10px_rgba(251,191,36,0.5)]"></span>
                    Select Infrastructure Type
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach($masterChannels as $channel)
                        <label class="relative flex flex-col p-6 bg-supabase-dark border border-supabase-border rounded-2xl cursor-pointer hover:border-supabase-accent/50 transition-all group overflow-hidden">
                            <input type="radio" name="master_code" value="{{ $channel->code }}" data-base-type="{{ $channel->type }}" class="sr-only peer channel-type-radio" required onchange="handleChannelTypeChange()">
                            <div class="absolute inset-0 bg-supabase-accent/5 opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                            <div class="relative z-10">
                                <div class="w-10 h-10 rounded-xl bg-supabase-input border border-supabase-border flex items-center justify-center mb-4 text-supabase-muted peer-checked:text-supabase-accent transition-colors overflow-hidden">
                                    @if($channel->logo_url)
                                        <img src="{{ $channel->logo_url }}" class="w-full h-full object-contain p-1 bg-white" alt="{{ $channel->name }}">
                                    @else
                                        @if($channel->type === 'qris')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                        @elseif($channel->type === 'ewallet')
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        @else
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                                        @endif
                                    @endif
                                </div>
                                <span class="text-[10px] font-bold text-white uppercase tracking-wider peer-checked:text-supabase-accent">{{ $channel->name }}</span>
                                <p class="text-[8px] text-supabase-muted uppercase font-bold tracking-normal mt-1">{{ str_replace('_', ' ', $channel->type) }}</p>
                            </div>
                            <div class="absolute bottom-0 right-0 w-8 h-8 flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity">
                                <div class="w-3 h-3 bg-supabase-accent rounded-full shadow-[0_0_10px_rgba(251,191,36,0.8)]"></div>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('channel_type')<p class="mt-4 text-[10px] font-bold text-red-500 uppercase tracking-wider">{{ $message }}</p>@enderror
            </div>

            <!-- Identity Input -->
            <div class="space-y-2">
                <label class="block text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Internal Node Alias</label>
                <input type="text" name="channel_name" value="{{ old('channel_name') }}" placeholder="e.g. Master QRIS Branch A" class="sb-input" required>
                <p class="text-[8px] text-supabase-muted uppercase font-bold tracking-normal">Recognition label for dashboard metrics</p>
                @error('channel_name')<p class="mt-1 text-[10px] font-bold text-red-500 uppercase tracking-wider">{{ $message }}</p>@enderror
            </div>
        </div>

        <!-- Conditional Fields Section -->
        <div id="dynamic-fields-container" class="space-y-8">
            <!-- E-Wallet Section -->
            <div id="ewallet-fields" class="hidden bg-supabase-surface border border-supabase-border rounded-3xl p-8 space-y-6 animate-in fade-in slide-in-from-top-4 duration-500">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-4 flex items-center">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-3 shadow-[0_0_10px_rgba(59,130,246,0.5)]"></span>
                    E-Wallet Parameters
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Phone Identifier</label>
                        <input type="text" name="account_number" id="ewallet_phone" value="{{ old('account_number') }}" placeholder="08XXXXXXXXXX" class="sb-input">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Owner Identity</label>
                        <input type="text" name="account_name" id="ewallet_name" value="{{ old('account_name') }}" placeholder="As listed in application" class="sb-input">
                    </div>
                </div>
            </div>

            <!-- QRIS Section -->
            <div id="qris-fields" class="hidden bg-supabase-surface border border-supabase-border rounded-3xl p-8 space-y-8 animate-in fade-in slide-in-from-top-4 duration-500">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-4 flex items-center">
                    <span class="w-1.5 h-1.5 bg-supabase-accent rounded-full mr-3 shadow-[0_0_10px_rgba(251,191,36,0.5)]"></span>
                    QRIS Core Matrix
                </h3>
                <div class="max-w-xl mx-auto text-center space-y-6">
                    <div class="relative group">
                        <input type="file" name="qr_code" id="qr_code" accept="image/*" class="sr-only" onchange="previewQR(event)">
                        <label for="qr_code" class="block bg-supabase-dark border-2 border-dashed border-supabase-border rounded-3xl p-12 hover:border-supabase-accent/50 transition-all cursor-pointer group">
                            <div id="qr-preview-container" class="hidden mb-6">
                                <img id="qr-preview" class="w-64 h-64 object-contain mx-auto rounded-2xl shadow-2xl shadow-supabase-accent/10">
                            </div>
                            <div id="qr-upload-placeholder" class="space-y-4">
                                <div class="w-20 h-20 bg-supabase-input border border-supabase-border rounded-2xl flex items-center justify-center mx-auto text-supabase-muted group-hover:text-supabase-accent transition-colors">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-bold text-white uppercase tracking-wider">Inject Static QRIS</p>
                                    <p class="text-[8px] text-supabase-muted font-bold uppercase tracking-normal">Supports PNG, JPG (MAX 2MB)</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Bank Transfer Section -->
            <div id="bank-fields" class="hidden bg-supabase-surface border border-supabase-border rounded-3xl p-8 space-y-8 animate-in fade-in slide-in-from-top-4 duration-500">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-4 flex items-center">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-3 shadow-[0_0_10px_rgba(34,197,94,0.5)]"></span>
                    Bank Protocol Settings
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Bank Institution</label>
                        <input type="text" name="provider" id="bank_name" placeholder="e.g. BCA, BNI" class="sb-input">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Account Number</label>
                        <input type="text" name="account_number" id="bank_account" placeholder="XXXXXXXXXX" class="sb-input">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Account Holder</label>
                        <input type="text" name="account_name" id="bank_holder" placeholder="Legal Account Name" class="sb-input">
                    </div>
                </div>
            </div>

            <!-- Virtual Account Section -->
            <div id="va-fields" class="hidden bg-supabase-surface border border-supabase-border rounded-3xl p-8 space-y-8 animate-in fade-in slide-in-from-top-4 duration-500">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-4 flex items-center">
                    <span class="w-1.5 h-1.5 bg-purple-500 rounded-full mr-3 shadow-[0_0_10px_rgba(168,85,247,0.5)]"></span>
                    Virtual Account Configuration
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Bank Provider</label>
                        <select name="provider" id="va_bank" class="sb-input bg-supabase-dark">
                            <option value="">Select Institution</option>
                            <option value="BCA">BCA</option>
                            <option value="BNI">BNI</option>
                            <option value="BRI">BRI</option>
                            <option value="Mandiri">Mandiri</option>
                            <option value="Permata">Permata</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Merchant Name Display</label>
                        <input type="text" name="account_name" id="va_holder" placeholder="Name shown on ATM/Mobile" class="sb-input">
                    </div>
                </div>
            </div>
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
                    <input type="number" name="fee_percentage" value="{{ old('fee_percentage', 0) }}" min="0" max="100" step="0.01" class="sb-input">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Fixed Convenience Fee (Rp)</label>
                    <input type="number" name="fee_fixed" value="{{ old('fee_fixed', 0) }}" min="0" step="100" class="sb-input">
                </div>
            </div>
            <div class="p-6 bg-supabase-dark border border-supabase-border rounded-2xl flex items-center space-x-6">
                <div class="w-10 h-10 bg-supabase-accent/20 rounded-xl flex items-center justify-center text-supabase-accent">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-white uppercase tracking-wider leading-tight">Net Logic Calculation</p>
                    <p class="text-[8px] text-supabase-muted uppercase font-bold tracking-normal mt-1">Total Fee = (Gross × Percentage) + Fixed</p>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-6 pt-8 border-t border-supabase-border">
            <a href="{{ route('tenant.payment-channels.index') }}" class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider hover:text-white transition-colors">Cancel Provisioning</a>
            <button type="submit" class="sb-button-primary !w-auto !py-4 !px-16">
                Activate Node
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
function handleChannelTypeChange() {
    const selectedType = document.querySelector('input[name="master_code"]:checked');
    if (!selectedType) return;
    
    const type = selectedType.dataset.baseType; // Use the base type for UI logic
    
    // Hide all sections
    ['ewallet-fields', 'qris-fields', 'bank-fields', 'va-fields'].forEach(id => {
        document.getElementById(id).classList.add('hidden');
    });
    
    // Reset inputs
    const conditionalInputs = [
        'ewallet_phone', 'ewallet_name', 'qr_code', 'bank_name', 'bank_account', 'bank_holder', 'va_bank', 'va_holder'
    ];
    
    conditionalInputs.forEach(id => {
        const elem = document.getElementById(id);
        if (elem) {
            elem.setAttribute('disabled', 'disabled');
            elem.removeAttribute('required');
        }
    });
    
    // Show relevant section
    if (type === 'qris') {
        document.getElementById('qris-fields').classList.remove('hidden');
        document.getElementById('qr_code').removeAttribute('disabled');
        document.getElementById('qr_code').setAttribute('required', 'required');
    } else if (type === 'ewallet') {
        document.getElementById('ewallet-fields').classList.remove('hidden');
        ['ewallet_phone', 'ewallet_name'].forEach(id => {
            document.getElementById(id).removeAttribute('disabled');
            document.getElementById(id).setAttribute('required', 'required');
        });
    } else if (type === 'bank_transfer') {
        document.getElementById('bank-fields').classList.remove('hidden');
        ['bank_name', 'bank_account', 'bank_holder'].forEach(id => {
            document.getElementById(id).removeAttribute('disabled');
            document.getElementById(id).setAttribute('required', 'required');
        });
    } else if (type === 'virtual_account') {
        document.getElementById('va-fields').classList.remove('hidden');
        ['va_bank', 'va_holder'].forEach(id => {
            document.getElementById(id).removeAttribute('disabled');
            document.getElementById(id).setAttribute('required', 'required');
        });
    }
}

function previewQR(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('qr-preview').src = e.target.result;
            document.getElementById('qr-preview-container').classList.remove('hidden');
            document.getElementById('qr-upload-placeholder').classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
}

document.addEventListener('DOMContentLoaded', handleChannelTypeChange);
</script>
@endpush

@endsection
