@extends('layouts.admin')

@section('page-title', 'Configure Tenant')

@section('content')
<div class="w-full mx-auto">
    <!-- Header -->
    <div class="mb-12">
        <div class="flex items-center space-x-2 text-[10px] font-bold text-supabase-muted uppercase tracking-widest mb-4">
            <a href="{{ route('admin.tenants.index') }}" class="hover:text-supabase-accent transition-colors">Tenants</a>
            <span>/</span>
            <span class="text-white">{{ $tenant->name }}</span>
            <span>/</span>
            <span class="text-white">Configuration</span>
        </div>
        <h1 class="text-4xl font-black text-white tracking-tight uppercase">Update <span class="text-supabase-accent">Parameters</span></h1>
        <p class="text-supabase-muted mt-2">Modify environment settings and access credentials for this merchant node.</p>
    </div>

    <form method="POST" action="{{ route('admin.tenants.update', $tenant) }}" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- FIRST ROW: Identity & KYC, SECOND ROW: Subscription & Webhook -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Merchant Identity -->
            <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-8 space-y-6">
                <h3 class="text-xs font-black text-white uppercase tracking-[0.2em] mb-4 flex items-center">
                    <span class="w-1.5 h-1.5 bg-supabase-accent rounded-full mr-3"></span>
                    Merchant Identity
                </h3>
                
                <div class="space-y-2">
                    <label for="name" class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Legal Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $tenant->name) }}" required class="sb-input"/>
                    @error('name')<p class="text-[10px] text-red-500 font-bold uppercase mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label for="email" class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Primary Contact Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $tenant->email) }}" required class="sb-input"/>
                    @error('email')<p class="text-[10px] text-red-500 font-bold uppercase mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- KYC Configuration -->
            <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xs font-black text-white uppercase tracking-[0.2em] flex items-center">
                        <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-3"></span>
                        KYC Verification
                    </h3>
                    <span class="px-2 py-1 text-[10px] font-black uppercase tracking-widest rounded 
                        @if($tenant->kyc_status === 'VERIFIED') bg-green-500/10 text-green-500
                        @elseif($tenant->kyc_status === 'PENDING') bg-yellow-500/10 text-yellow-500
                        @else bg-red-500/10 text-red-500 @endif">
                        {{ $tenant->kyc_status }}
                    </span>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[8px] font-black text-supabase-muted uppercase tracking-widest">KTP Name</p>
                            <p class="text-sm font-bold text-white mt-1">{{ $tenant->ktp_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[8px] font-black text-supabase-muted uppercase tracking-widest">KTP NIK</p>
                            <p class="text-sm font-bold text-white mt-1">{{ $tenant->ktp_number ?? '-' }}</p>
                        </div>
                    </div>

                    @if($tenant->kyc_reject_reason)
                    <div>
                        <p class="text-[8px] font-black text-red-500 uppercase tracking-widest">Reject Reason</p>
                        <p class="text-xs text-red-400 mt-1">{{ $tenant->kyc_reject_reason }}</p>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-supabase-border/50">
                        @if($tenant->ktp_image_path)
                        <a href="{{ asset('storage/' . $tenant->ktp_image_path) }}" target="_blank" class="flex items-center justify-center p-3 bg-supabase-dark border border-supabase-border rounded-xl hover:border-supabase-accent/50 transition-colors group">
                            <svg class="w-4 h-4 text-supabase-muted group-hover:text-supabase-accent mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <span class="text-[10px] font-black text-white uppercase tracking-widest">View KTP</span>
                        </a>
                        @else
                        <div class="flex items-center justify-center p-3 bg-supabase-dark border border-supabase-border/50 rounded-xl text-[10px] font-bold text-supabase-muted uppercase">
                            No KTP
                        </div>
                        @endif

                        @if($tenant->payment_proof_path)
                        <a href="{{ asset('storage/' . $tenant->payment_proof_path) }}" target="_blank" class="flex items-center justify-center p-3 bg-supabase-dark border border-supabase-border rounded-xl hover:border-yellow-500/50 transition-colors group">
                            <svg class="w-4 h-4 text-supabase-muted group-hover:text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <span class="text-[10px] font-black text-white uppercase tracking-widest">Bukti Transfer</span>
                        </a>
                        @else
                        <div class="flex items-center justify-center p-3 bg-supabase-dark border border-supabase-border/50 rounded-xl text-[10px] font-bold text-supabase-muted uppercase">
                            No Payment
                        </div>
                        @endif
                    </div>

                    @if($tenant->kyc_status === 'PENDING')
                    <div class="flex space-x-3 pt-4">
                        <button type="button" onclick="document.getElementById('approve-kyc-form').submit();" class="flex-1 sb-button-primary !py-2.5 bg-green-500 text-white border-none hover:bg-green-600">
                            Approve KYC
                        </button>
                        <button type="button" onclick="showRejectKycModal()" class="flex-1 p-2.5 bg-red-500/10 border border-red-500/20 text-red-500 text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-red-500 hover:text-white transition-colors">
                            Reject
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Subscription Matrix -->
            <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-8 space-y-6">
                <h3 class="text-xs font-black text-white uppercase tracking-[0.2em] mb-4 flex items-center">
                    <span class="w-1.5 h-1.5 bg-supabase-accent rounded-full mr-3"></span>
                    Subscription Matrix
                </h3>
                
                <div class="space-y-2">
                    <label for="plan_id" class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Active Plan Tier</label>
                    <select id="plan_id" name="plan_id" class="sb-input bg-supabase-dark">
                        <option value="">No Active Plan</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id', $tenant->plan_id) == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} (Rp {{ number_format($plan->price) }} / {{ $plan->duration_days }} Days)
                            </option>
                        @endforeach
                    </select>
                    @error('plan_id')<p class="text-[10px] text-red-500 font-bold uppercase mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="p-4 bg-supabase-dark border border-supabase-border rounded-xl flex items-center justify-between">
                    <div>
                        <p class="text-[8px] font-black text-supabase-muted uppercase tracking-widest">Temporal Expiry</p>
                        <p class="text-[10px] font-black text-white uppercase mt-1">
                            {{ $tenant->expired_at ? $tenant->expired_at->format('M d, Y') : 'INFINITY' }}
                        </p>
                    </div>
                    @if($tenant->expired_at)
                        <div class="text-right">
                            <p class="text-[8px] font-black text-supabase-muted uppercase tracking-widest">Status</p>
                            <span class="text-[10px] font-black {{ $tenant->isSubscriptionActive() ? 'text-green-500' : 'text-red-500' }} uppercase">
                                {{ $tenant->isSubscriptionActive() ? 'ACTIVE' : 'EXPIRED' }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Webhook Pipeline -->
            <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-8 space-y-6">
                <h3 class="text-xs font-black text-white uppercase tracking-[0.2em] mb-4 flex items-center">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-3"></span>
                    Webhook Pipeline
                </h3>

                <div class="space-y-2">
                    <label for="webhook_url" class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Android Sync URL</label>
                    <input type="url" id="webhook_url" name="webhook_url" value="{{ old('webhook_url', $tenant->webhook_url) }}" required class="sb-input"/>
                    @error('webhook_url')<p class="text-[10px] text-red-500 font-bold uppercase mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label for="callback_url" class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Panel Callback URL</label>
                    <input type="url" id="callback_url" name="callback_url" value="{{ old('callback_url', $tenant->callback_url) }}" class="sb-input"/>
                    @error('callback_url')<p class="text-[10px] text-red-500 font-bold uppercase mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- API Keys -->
        <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-8">
            <h3 class="text-xs font-black text-white uppercase tracking-[0.2em] mb-8 flex items-center">
                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-3"></span>
                Infrastucture API Keys
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Sandbox Environment Key</label>
                    <div class="relative">
                        <input type="text" id="api_sandbox" readonly value="{{ $tenant->api_key_sandbox }}" class="sb-input font-mono text-supabase-muted bg-supabase-dark border-supabase-border/50 pr-12 cursor-default"/>
                        <button type="button" onclick="copyField('api_sandbox')" class="absolute right-3 top-1/2 -translate-y-1/2 text-supabase-muted hover:text-supabase-accent transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Production Environment Key</label>
                    <div class="relative">
                        <input type="text" id="api_production" readonly value="{{ $tenant->api_key_production }}" class="sb-input font-mono text-supabase-muted bg-supabase-dark border-supabase-border/50 pr-12 cursor-default"/>
                        <button type="button" onclick="copyField('api_production')" class="absolute right-3 top-1/2 -translate-y-1/2 text-supabase-muted hover:text-supabase-accent transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Android App Credentials -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-8 space-y-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-black text-white uppercase tracking-[0.2em] flex items-center">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-3"></span>
                        Sync Credentials
                    </h3>
                    <button type="button" onclick="generatePassword()" class="text-[8px] font-black text-supabase-accent uppercase tracking-widest hover:underline">Regenerate</button>
                </div>
                
                <div class="space-y-4">
                    <div class="space-y-2">
                        <label for="password" class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">New Sync Password</label>
                        <input type="text" id="password" name="password" class="sb-input font-mono" placeholder="Leave blank to keep current"/>
                        @error('password')<p class="text-[10px] text-red-500 font-bold uppercase mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="space-y-2">
                        <label for="password_confirmation" class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Confirm Sync Password</label>
                        <input type="text" id="password_confirmation" name="password_confirmation" class="sb-input font-mono" placeholder="Verify new password"/>
                    </div>
                </div>
            </div>

            <!-- Status Control -->
            <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-8 flex flex-col justify-between">
                <div>
                    <h3 class="text-xs font-black text-white uppercase tracking-[0.2em] mb-6 flex items-center">
                        <span class="w-1.5 h-1.5 bg-white rounded-full mr-3"></span>
                        Node Lifecycle
                    </h3>
                    <label class="flex items-center space-x-4 cursor-pointer group bg-supabase-input border border-supabase-border p-4 rounded-xl hover:border-supabase-accent/50 transition-colors">
                        <div class="relative">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $tenant->is_active) ? 'checked' : '' }} class="sr-only peer"/>
                            <div class="w-11 h-6 bg-supabase-dark border border-supabase-border rounded-full peer peer-checked:bg-supabase-accent transition-colors"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-supabase-muted rounded-full peer-checked:translate-x-5 peer-checked:bg-supabase-dark transition-transform"></div>
                        </div>
                        <div>
                            <p class="text-xs font-black text-white uppercase tracking-widest">Active State</p>
                            <p class="text-[10px] text-supabase-muted font-bold uppercase mt-0.5">Toggle notification pipeline</p>
                        </div>
                    </label>
                </div>

                <div class="mt-8 pt-6 border-t border-supabase-border/50 flex items-center justify-between text-[10px] font-bold text-supabase-muted uppercase tracking-widest">
                    <span>Provisioned</span>
                    <span class="text-white">{{ $tenant->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-6 pt-8 border-t border-supabase-border">
            <a href="{{ route('admin.tenants.index') }}" class="text-[10px] font-black text-supabase-muted uppercase tracking-widest hover:text-white transition-colors">Discard</a>
            <button type="submit" class="sb-button-primary !w-auto !py-3 !px-12">
                Commit Changes
            </button>
        </div>
    </form>
</div>

<!-- KYC Action Forms -->
@if($tenant->kyc_status === 'PENDING')
<form id="approve-kyc-form" method="POST" action="{{ route('admin.tenants.approve-kyc', $tenant) }}" class="hidden">
    @csrf
</form>

<div id="reject-kyc-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-6 bg-black/50 backdrop-blur-sm">
    <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 max-w-md w-full shadow-2xl">
        <h3 class="text-xl font-black text-white uppercase tracking-tight mb-2">Reject <span class="text-red-500">KYC</span></h3>
        <p class="text-supabase-muted text-[10px] font-bold uppercase mb-6">Please specify the reason for rejection.</p>
        
        <form id="reject-kyc-form" method="POST" action="{{ route('admin.tenants.reject-kyc', $tenant) }}" class="space-y-6">
            @csrf
            <div class="space-y-2">
                <label class="text-[10px] font-black text-supabase-muted uppercase tracking-widest">Reason</label>
                <textarea name="kyc_reject_reason" rows="3" class="sb-input w-full" required placeholder="E.g. Document is blurry..."></textarea>
            </div>
            <div class="flex items-center justify-end space-x-4 pt-4">
                <button type="button" onclick="hideRejectKycModal()" class="text-[10px] font-black text-supabase-muted uppercase tracking-widest">Cancel</button>
                <button type="submit" class="sb-button-primary !w-auto !py-2.5 !px-8 !bg-red-500 hover:!bg-red-600 !border-none">Submit Rejection</button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
function generatePassword() {
    const chars = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%^&*';
    let pwd = '';
    for (let i = 0; i < 16; i++) pwd += chars.charAt(Math.floor(Math.random() * chars.length));
    document.getElementById('password').value = pwd;
    document.getElementById('password_confirmation').value = pwd;
}

function copyField(id) {
    const el = document.getElementById(id);
    if (!el || !el.value) return;
    el.select();
    navigator.clipboard.writeText(el.value).then(() => {
        alert('Copied to system clipboard!');
    });
}

function showRejectKycModal() {
    document.getElementById('reject-kyc-modal').classList.remove('hidden');
}

function hideRejectKycModal() {
    document.getElementById('reject-kyc-modal').classList.add('hidden');
}
</script>
@endsection
