@extends('layouts.admin')

@section('page-title', 'Onboard Tenant')

@section('content')
<div class="w-full mx-auto">
    <!-- Header -->
    <div class="mb-12">
        <div class="flex items-center space-x-2 text-[10px] font-bold text-supabase-muted uppercase tracking-widest mb-4">
            <a href="{{ route('admin.tenants.index') }}" class="hover:text-supabase-accent transition-colors">Tenants</a>
            <span>/</span>
            <span class="text-white">New Registry</span>
        </div>
        <h1 class="text-4xl font-black text-white tracking-tight uppercase">Onboard <span class="text-supabase-accent">Merchant</span></h1>
        <p class="text-supabase-muted mt-2">Initialize a new business entity into the Cekbayar ecosystem.</p>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('admin.tenants.store') }}" class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Identity -->
            <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-8 space-y-6">
                <h3 class="text-xs font-black text-white uppercase tracking-[0.2em] mb-4 flex items-center">
                    <span class="w-1.5 h-1.5 bg-supabase-accent rounded-full mr-3"></span>
                    Merchant Identity
                </h3>
                
                <div class="space-y-2">
                    <label for="name" class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Legal Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required class="sb-input" placeholder="e.g. PT Maju Jaya Digital"/>
                    @error('name')<p class="text-[10px] text-red-500 font-bold uppercase mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label for="email" class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Primary Contact Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required class="sb-input" placeholder="owner@merchant.com"/>
                    @error('email')<p class="text-[10px] text-red-500 font-bold uppercase mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Subscription -->
            <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-8 space-y-6">
                <h3 class="text-xs font-black text-white uppercase tracking-[0.2em] mb-4 flex items-center">
                    <span class="w-1.5 h-1.5 bg-supabase-accent rounded-full mr-3"></span>
                    Subscription Plan
                </h3>
                
                <div class="space-y-2">
                    <label for="plan_id" class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Select Plan Matrix</label>
                    <select id="plan_id" name="plan_id" class="sb-input bg-supabase-dark">
                        <option value="">No Active Plan</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} (Rp {{ number_format($plan->price) }} / {{ $plan->duration_days }} Days)
                            </option>
                        @endforeach
                    </select>
                    @error('plan_id')<p class="text-[10px] text-red-500 font-bold uppercase mt-1">{{ $message }}</p>@enderror
                </div>
                <p class="text-[8px] text-supabase-muted font-bold uppercase tracking-tight">Assigning a plan will automatically calculate the initial expiry date based on the plan duration.</p>
            </div>

            <!-- Connectivity -->
            <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-8 space-y-6">
                <h3 class="text-xs font-black text-white uppercase tracking-[0.2em] mb-4 flex items-center">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-3"></span>
                    Webhook Architecture
                </h3>

                <div class="space-y-2">
                    <label for="webhook_url" class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Endpoint URL</label>
                    <input type="url" id="webhook_url" name="webhook_url" value="{{ old('webhook_url') }}" required class="sb-input" placeholder="https://api.merchant.com/Cekbayar"/>
                    @error('webhook_url')<p class="text-[10px] text-red-500 font-bold uppercase mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label for="webhook_secret" class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Signing Secret</label>
                    <div class="relative group">
                        <input type="text" id="webhook_secret" name="webhook_secret" value="{{ old('webhook_secret') }}" class="sb-input pr-12" placeholder="Auto-generated if blank"/>
                        <button type="button" onclick="copyField('webhook_secret')" class="absolute right-3 top-1/2 -translate-y-1/2 text-supabase-muted hover:text-supabase-accent transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security -->
        <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                <div>
                    <h3 class="text-xs font-black text-white uppercase tracking-[0.2em] flex items-center">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-3"></span>
                        Android App Authentication
                    </h3>
                    <p class="text-[10px] text-supabase-muted font-bold uppercase mt-1">Credentials for the Android Notification Relay</p>
                </div>
                <button type="button" onclick="generatePassword()" class="text-[10px] font-black bg-supabase-accent text-supabase-dark px-4 py-2 rounded-lg uppercase tracking-widest hover:scale-105 transition-transform active:scale-95 shadow-lg shadow-supabase-accent/10">
                    Auto-Generate Keys
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label for="password" class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Master Password</label>
                    <div class="relative">
                        <input type="text" id="password" name="password" required class="sb-input font-mono" placeholder="Min. 8 characters"/>
                        <button type="button" onclick="copyField('password')" class="absolute right-3 top-1/2 -translate-y-1/2 text-supabase-muted hover:text-supabase-accent transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        </button>
                    </div>
                    @error('password')<p class="text-[10px] text-red-500 font-bold uppercase mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="space-y-2">
                    <label for="password_confirmation" class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Confirm Logic</label>
                    <input type="text" id="password_confirmation" name="password_confirmation" required class="sb-input font-mono" placeholder="Verify password"/>
                </div>
            </div>

            <div id="password-strength" class="mt-6 hidden">
                <div class="flex gap-1.5 mb-2">
                    <div class="h-1 flex-1 rounded-full bg-supabase-input border border-supabase-border overflow-hidden"><div class="h-full transition-all duration-500 w-0" id="str-1"></div></div>
                    <div class="h-1 flex-1 rounded-full bg-supabase-input border border-supabase-border overflow-hidden"><div class="h-full transition-all duration-500 w-0" id="str-2"></div></div>
                    <div class="h-1 flex-1 rounded-full bg-supabase-input border border-supabase-border overflow-hidden"><div class="h-full transition-all duration-500 w-0" id="str-3"></div></div>
                    <div class="h-1 flex-1 rounded-full bg-supabase-input border border-supabase-border overflow-hidden"><div class="h-full transition-all duration-500 w-0" id="str-4"></div></div>
                </div>
                <p id="str-label" class="text-[8px] font-black uppercase tracking-[0.2em] text-supabase-muted text-right"></p>
            </div>
        </div>

        <!-- Info Card -->
        <div class="bg-blue-500/5 border border-blue-500/20 rounded-2xl p-8">
            <div class="flex items-start space-x-6">
                <div class="w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center text-blue-500 flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="space-y-4">
                    <h4 class="text-sm font-black text-white uppercase tracking-widest">Onboarding Protocol</h4>
                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3">
                        <li class="flex items-center text-[10px] font-bold text-blue-400 uppercase tracking-wider">
                            <span class="w-1 h-1 bg-blue-400 rounded-full mr-3"></span>
                            Auto-Generate Sandbox Keys
                        </li>
                        <li class="flex items-center text-[10px] font-bold text-blue-400 uppercase tracking-wider">
                            <span class="w-1 h-1 bg-blue-400 rounded-full mr-3"></span>
                            Initialize Android Sync
                        </li>
                        <li class="flex items-center text-[10px] font-bold text-blue-400 uppercase tracking-wider">
                            <span class="w-1 h-1 bg-blue-400 rounded-full mr-3"></span>
                            Enable Webhook Pipeline
                        </li>
                        <li class="flex items-center text-[10px] font-bold text-blue-400 uppercase tracking-wider">
                            <span class="w-1 h-1 bg-blue-400 rounded-full mr-3"></span>
                            Grant Dashboard Access
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-end space-x-6 pt-8 border-t border-supabase-border">
            <a href="{{ route('admin.tenants.index') }}" class="text-[10px] font-black text-supabase-muted uppercase tracking-widest hover:text-white transition-colors">Abort Changes</a>
            <button type="submit" class="sb-button-primary !w-auto !py-3 !px-12">
                Deploy Tenant
            </button>
        </div>
    </form>
</div>

<script>
function generatePassword() {
    const chars = 'ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%^&*';
    let pwd = '';
    for (let i = 0; i < 16; i++) pwd += chars.charAt(Math.floor(Math.random() * chars.length));
    document.getElementById('password').value = pwd;
    document.getElementById('password_confirmation').value = pwd;
    checkStrength(pwd);
}

function copyField(id) {
    const el = document.getElementById(id);
    if (!el || !el.value) return;
    el.select();
    navigator.clipboard.writeText(el.value).then(() => {
        alert('Copied to system clipboard!');
    });
}

function checkStrength(pwd) {
    const bars = [document.getElementById('str-1'), document.getElementById('str-2'), document.getElementById('str-3'), document.getElementById('str-4')];
    const label = document.getElementById('str-label');
    const wrap = document.getElementById('password-strength');
    if (!pwd) { wrap.classList.add('hidden'); return; }
    wrap.classList.remove('hidden');
    let score = 0;
    if (pwd.length >= 8) score++;
    if (pwd.length >= 12) score++;
    if (/[A-Z]/.test(pwd) && /[0-9]/.test(pwd)) score++;
    if (/[!@#$%^&*]/.test(pwd)) score++;
    const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500'];
    const labels = ['Insecure', 'Moderate', 'Strong', 'Military Grade'];
    bars.forEach((b, i) => {
        b.className = 'h-full transition-all duration-500 ' + (i < score ? colors[score - 1] : 'w-0');
    });
    label.textContent = labels[score - 1] || 'Scanning...';
    label.className = 'text-[8px] font-black uppercase tracking-[0.2em] text-right ' + (score > 0 ? colors[score-1].replace('bg-', 'text-') : 'text-supabase-muted');
}

document.getElementById('password').addEventListener('input', e => checkStrength(e.target.value));
</script>
@endsection
