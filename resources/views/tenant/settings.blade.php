@extends('layouts.tenant')

@section('content')
<div class="w-full space-y-12 pb-20">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-4xl font-bold text-white tracking-normal uppercase">Node <span class="text-supabase-accent">Settings</span></h1>
            <p class="text-supabase-muted mt-2">Manage your merchant profile, security, and integration parameters.</p>
        </div>
        <div class="flex items-center px-4 py-2 bg-supabase-surface border border-supabase-border rounded-xl">
            <span class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider mr-3">Status:</span>
            <div class="flex items-center">
                <div class="w-2 h-2 rounded-full {{ $tenant->is_active ? 'bg-green-500 animate-pulse' : 'bg-red-500' }} mr-2"></div>
                <span class="text-[10px] font-bold text-white uppercase tracking-wider">{{ $tenant->is_active ? 'Active' : 'Offline' }}</span>
            </div>
        </div>
    </div>

    <!-- Feedback -->
    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-500 px-6 py-4 rounded-2xl flex items-center shadow-lg">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-xs font-bold uppercase tracking-wider">{{ session('success') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-500 px-6 py-4 rounded-2xl shadow-lg">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-xs font-bold uppercase tracking-wider">Validation Failed</span>
            </div>
            <ul class="text-[10px] font-bold uppercase tracking-wider space-y-1 ml-8">
                @foreach($errors->all() as $error)
                    <li>&bull; {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        <!-- Sidebar Navigation (Quick Links) -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-2 sticky top-24">
                <button onclick="scrollToSection('business-profile')" class="w-full flex items-center space-x-4 px-4 py-3 rounded-xl hover:bg-white/[0.03] transition-colors group text-left">
                    <div class="w-8 h-8 rounded-lg bg-supabase-input border border-supabase-border flex items-center justify-center text-supabase-muted group-hover:text-supabase-accent transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <span class="text-[10px] font-bold text-white uppercase tracking-wider">Business Identity</span>
                </button>
                <button onclick="scrollToSection('api-keys')" class="w-full flex items-center space-x-4 px-4 py-3 rounded-xl hover:bg-white/[0.03] transition-colors group text-left">
                    <div class="w-8 h-8 rounded-lg bg-supabase-input border border-supabase-border flex items-center justify-center text-supabase-muted group-hover:text-supabase-accent transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                    </div>
                    <span class="text-[10px] font-bold text-white uppercase tracking-wider">Infrastructure Keys</span>
                </button>
                <button onclick="scrollToSection('subscription-plan')" class="w-full flex items-center space-x-4 px-4 py-3 rounded-xl hover:bg-white/[0.03] transition-colors group text-left">
                    <div class="w-8 h-8 rounded-lg bg-supabase-input border border-supabase-border flex items-center justify-center text-supabase-muted group-hover:text-supabase-accent transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <span class="text-[10px] font-bold text-white uppercase tracking-wider">Subscription Plan</span>
                </button>
                <button onclick="scrollToSection('two-factor-auth')" class="w-full flex items-center space-x-4 px-4 py-3 rounded-xl hover:bg-white/[0.03] transition-colors group text-left">
                    <div class="w-8 h-8 rounded-lg bg-supabase-input border border-supabase-border flex items-center justify-center text-supabase-muted group-hover:text-supabase-accent transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <span class="text-[10px] font-bold text-white uppercase tracking-wider">{{ __('Two-Factor Auth') }}</span>
                </button>
                <button onclick="scrollToSection('mobile-sync')" class="w-full flex items-center space-x-4 px-4 py-3 rounded-xl hover:bg-white/[0.03] transition-colors group text-left">
                    <div class="w-8 h-8 rounded-lg bg-supabase-input border border-supabase-border flex items-center justify-center text-supabase-muted group-hover:text-supabase-accent transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                    <span class="text-[10px] font-bold text-white uppercase tracking-wider">Mobile Relay</span>
                </button>
            </div>

            <!-- Download APK Card -->
            <div class="bg-gradient-to-br from-supabase-accent/20 to-transparent border border-supabase-accent/20 rounded-3xl p-8 relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500">
                    <svg class="w-32 h-32 text-supabase-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                </div>
                <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Relay Engine</h4>
                <p class="text-[10px] text-supabase-muted font-bold uppercase leading-relaxed mb-6">Deploy the Cekbayar Android client to start intercepting payment notifications.</p>
                <a href="#" class="sb-button-primary block text-center !py-3">Download APK</a>
            </div>
        </div>

        <!-- Main Settings Form -->
        <div class="lg:col-span-2 space-y-12">
            <!-- Subscription Plan Section -->
            <section id="subscription-plan" class="space-y-8">
                <div class="bg-supabase-surface border border-supabase-border rounded-2xl overflow-hidden">
                    <div class="px-8 py-6 border-b border-supabase-border bg-supabase-dark/30 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-xs font-bold text-white uppercase tracking-wider">Subscription Plan</h3>
                            <p class="mt-1 text-[10px] font-bold uppercase tracking-wider text-supabase-muted">Current package capacity and upgrade path</p>
                        </div>
                        @if(data_get($tenant->settings, 'upgrade_request.status') === 'pending')
                            <span class="inline-flex items-center rounded-lg border border-amber-500/20 bg-amber-500/10 px-3 py-1 text-[9px] font-bold uppercase tracking-wider text-amber-400">
                                Upgrade Pending
                            </span>
                        @endif
                    </div>

                    <div class="p-8 space-y-8">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                            <div class="md:col-span-2 rounded-xl border border-supabase-border bg-supabase-dark/40 p-5">
                                <p class="text-[9px] font-bold uppercase tracking-wider text-supabase-muted">Active Package</p>
                                <h4 class="mt-2 text-2xl font-bold uppercase tracking-normal text-white">{{ $tenant->plan?->name ?? 'Custom Plan' }}</h4>
                                <p class="mt-2 text-xs font-bold text-supabase-muted">{{ $tenant->plan?->description ?? 'Your subscription is managed manually by the admin team.' }}</p>
                            </div>
                            <div class="rounded-xl border border-supabase-border bg-supabase-dark/40 p-5">
                                <p class="text-[9px] font-bold uppercase tracking-wider text-supabase-muted">Price</p>
                                <p class="mt-3 text-lg font-bold text-white">
                                    {{ $tenant->plan ? 'Rp ' . number_format($tenant->plan->price, 0, ',', '.') : '-' }}
                                </p>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-supabase-muted">{{ $tenant->plan?->duration_days ?? 0 }} days cycle</p>
                            </div>
                            <div class="rounded-xl border border-supabase-border bg-supabase-dark/40 p-5">
                                <p class="text-[9px] font-bold uppercase tracking-wider text-supabase-muted">Capacity</p>
                                <p class="mt-3 text-lg font-bold text-white">{{ $tenant->plan?->max_channels ?? $tenant->monthly_limit ?? '-' }}</p>
                                <p class="text-[10px] font-bold uppercase tracking-wider text-supabase-muted">Active nodes</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="rounded-xl border border-supabase-border bg-supabase-input/30 p-5">
                                <p class="text-[9px] font-bold uppercase tracking-wider text-supabase-muted">Subscription Status</p>
                                <div class="mt-3 flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full {{ $tenant->isSubscriptionActive() ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                    <span class="text-xs font-bold uppercase tracking-wider text-white">{{ $tenant->isSubscriptionActive() ? 'Active' : 'Expired' }}</span>
                                </div>
                            </div>
                            <div class="rounded-xl border border-supabase-border bg-supabase-input/30 p-5">
                                <p class="text-[9px] font-bold uppercase tracking-wider text-supabase-muted">Valid Until</p>
                                <p class="mt-3 text-xs font-bold uppercase tracking-wider text-white">
                                    {{ $tenant->expired_at ? $tenant->expired_at->format('d M Y') : 'No expiry set' }}
                                </p>
                                @if($tenant->expired_at)
                                    <p class="mt-1 text-[10px] font-bold uppercase tracking-wider text-supabase-muted">{{ $tenant->getSubscriptionDaysLeft() }} days remaining</p>
                                @endif
                            </div>
                        </div>

                        @if(($tenant->plan?->features))
                            <div class="rounded-xl border border-supabase-border bg-supabase-dark/40 p-5">
                                <p class="mb-4 text-[9px] font-bold uppercase tracking-wider text-supabase-muted">Included Features</p>
                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                    @foreach($tenant->plan->features as $feature)
                                        <div class="flex items-center gap-3 text-xs font-bold text-slate-300">
                                            <span class="flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full bg-supabase-accent/10 text-supabase-accent">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                            </span>
                                            <span>{{ $feature }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-[10px] font-bold uppercase tracking-wider text-white">Upgrade Options</h4>
                                <a href="{{ route('public.pricing') }}" class="text-[9px] font-bold uppercase tracking-wider text-supabase-accent hover:text-white">View public pricing</a>
                            </div>

                            @if($upgradePlans->isNotEmpty())
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    @foreach($upgradePlans as $plan)
                                        <div class="rounded-xl border border-supabase-border bg-supabase-dark/40 p-5">
                                            <div class="flex items-start justify-between gap-4">
                                                <div>
                                                    <h5 class="text-sm font-bold uppercase tracking-normal text-white">{{ $plan->name }}</h5>
                                                    <p class="mt-1 text-[10px] font-bold text-supabase-muted">{{ $plan->description }}</p>
                                                </div>
                                                <p class="whitespace-nowrap text-xs font-bold text-supabase-accent">Rp {{ number_format($plan->price, 0, ',', '.') }}</p>
                                            </div>
                                            <p class="mt-4 text-[10px] font-bold uppercase tracking-wider text-supabase-muted">Up to {{ $plan->max_channels }} active nodes</p>
                                            <form method="POST" action="{{ route('tenant.settings.request-upgrade') }}" class="mt-5">
                                                @csrf
                                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                                <button type="submit" class="sb-button-primary !h-9 !w-auto !rounded-md !py-0 px-5 !text-[10px] !shadow-none">Request Upgrade</button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="rounded-xl border border-supabase-border bg-supabase-dark/40 p-5 text-sm font-bold text-supabase-muted">
                                    You are already on the highest available package.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>

            <!-- Business Profile Section -->
            <section id="business-profile" class="space-y-8">
                <div class="bg-supabase-surface border border-supabase-border rounded-2xl overflow-hidden">
                    <div class="px-8 py-6 border-b border-supabase-border bg-supabase-dark/30">
                        <h3 class="text-xs font-bold text-white uppercase tracking-wider">Core Identity</h3>
                    </div>
                    <form method="POST" action="{{ route('tenant.settings.update') }}" class="p-8 space-y-8">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Merchant Name</label>
                                <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required class="sb-input"/>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Business Email</label>
                                <input type="email" name="email" value="{{ old('email', $tenant->email) }}" required class="sb-input"/>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Phone / WhatsApp</label>
                                <input type="tel" name="phone" value="{{ old('phone', $tenant->phone) }}" class="sb-input"/>
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Official Website</label>
                                <input type="url" name="website" value="{{ old('website', $tenant->website) }}" class="sb-input"/>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Merchant Description</label>
                            <textarea name="description" rows="3" class="sb-input resize-none">{{ old('description', $tenant->description) }}</textarea>
                        </div>

                        <!-- Webhook & Callback -->
                        <div class="pt-8 border-t border-supabase-border space-y-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Android Webhook URL</label>
                                    <input type="url" name="webhook_url" value="{{ old('webhook_url', $tenant->webhook_url) }}" class="sb-input" placeholder="https://yourdomain.com/hook"/>
                                    <p class="text-[8px] text-supabase-muted uppercase font-bold tracking-wider">Sync destination for the Android app</p>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Backend Callback URL</label>
                                    <input type="url" name="callback_url" value="{{ old('callback_url', $tenant->callback_url) }}" class="sb-input" placeholder="https://yourbackend.com/callback"/>
                                    <p class="text-[8px] text-supabase-muted uppercase font-bold tracking-wider">Confirmed payment notification target</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4 p-4 bg-supabase-input/50 border border-supabase-border rounded-xl">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="webhook_enabled" value="1" {{ old('webhook_enabled', $tenant->webhook_enabled) ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-11 h-6 bg-supabase-dark border border-supabase-border rounded-full peer peer-checked:bg-supabase-accent transition-colors"></div>
                                    <div class="absolute left-1 top-1 w-4 h-4 bg-supabase-muted rounded-full peer-checked:translate-x-5 peer-checked:bg-supabase-dark transition-transform"></div>
                                </label>
                                <div>
                                    <p class="text-[10px] font-bold text-white uppercase tracking-wider leading-none">Webhook Pipeline</p>
                                    <p class="text-[8px] text-supabase-muted uppercase font-bold tracking-normal mt-1">Status: {{ $tenant->webhook_enabled ? 'Operational' : 'Paused' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="pt-8 border-t border-supabase-border flex justify-end">
                            <button type="submit" class="sb-button-primary !w-auto px-12">Commit Changes</button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- API Keys Section -->
            <section id="api-keys" class="space-y-8">
                <div class="bg-supabase-surface border border-supabase-border rounded-2xl overflow-hidden">
                    <div class="px-8 py-6 border-b border-supabase-border bg-supabase-dark/30">
                        <h3 class="text-xs font-bold text-white uppercase tracking-wider">Security Credentials</h3>
                    </div>
                    <div class="p-8 space-y-8">
                        <div class="space-y-6">
                            <!-- Production API Key -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Production Master Key</label>
                                    <form method="POST" action="{{ route('tenant.settings.regenerate-api-key') }}" id="regenProdForm">
                                        @csrf
                                        <input type="hidden" name="mode" value="production">
                                        <button type="button" onclick="confirmRegen('regenProdForm')" class="text-[8px] font-bold text-red-500 uppercase tracking-wider hover:underline">Regenerate</button>
                                    </form>
                                </div>
                                <div class="flex space-x-2">
                                    <input type="password" id="prodKey" value="{{ $tenant->api_key_production }}" readonly class="sb-input font-mono flex-1 bg-supabase-dark/50 border-supabase-border/50 text-supabase-muted"/>
                                    <button onclick="toggleMask('prodKey', this)" class="sb-button-secondary !w-auto !px-3"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                                    <button onclick="copyToClip('prodKey')" class="sb-button-secondary !w-auto !px-3"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg></button>
                                </div>
                            </div>

                            <div class="border-t border-supabase-border/50"></div>

                            <!-- Webhook Secret -->
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Webhook Signing Secret</label>
                                    <form method="POST" action="{{ route('tenant.settings.regenerate-webhook-secret') }}" id="regenSecretForm">
                                        @csrf
                                        <button type="button" onclick="confirmRegen('regenSecretForm')" class="text-[8px] font-bold text-red-500 uppercase tracking-wider hover:underline">Regenerate</button>
                                    </form>
                                </div>
                                <div class="flex space-x-2">
                                    <input type="password" id="webhookSec" value="{{ $tenant->webhook_secret }}" readonly class="sb-input font-mono flex-1 bg-supabase-dark/50 border-supabase-border/50 text-supabase-muted"/>
                                    <button onclick="toggleMask('webhookSec', this)" class="sb-button-secondary !w-auto !px-3"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                                    <button onclick="copyToClip('webhookSec')" class="sb-button-secondary !w-auto !px-3"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg></button>
                                </div>
                            </div>
                        </div>

                        <div class="bg-amber-500/10 border border-amber-500/20 rounded-2xl p-6 flex items-start space-x-4">
                            <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            <div>
                                <p class="text-[10px] font-bold text-white uppercase tracking-wider mb-1">Security Advisory</p>
                                <p class="text-[8px] text-supabase-muted uppercase font-bold leading-relaxed">Regenerating keys will cause an immediate outage for any systems using the current credentials. Act with caution.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Two-Factor Authentication Section -->
            <section id="two-factor-auth" class="space-y-8">
                <div class="bg-supabase-surface border border-supabase-border rounded-2xl overflow-hidden">
                    <div class="px-8 py-6 border-b border-supabase-border bg-supabase-dark/30">
                        <h3 class="text-xs font-bold text-white uppercase tracking-wider">{{ __('Dua-Faktor Autentikasi (2FA)') }}</h3>
                    </div>
                    <div class="p-8 space-y-8">
                        @php
                            $user = auth()->user();
                            $qrCodeSvg = '';
                            if ($user->two_factor_secret && !$user->two_factor_enabled) {
                                $qrUrl = \App\Services\TwoFactorService::getQrCodeUrl($user->email, $user->two_factor_secret);
                                $qrCodeSvg = \App\Services\TwoFactorService::generateQrCodeSvg($qrUrl);
                            }
                        @endphp

                        @if ($user->two_factor_enabled)
                            <!-- 2FA Enabled State -->
                            <div class="flex items-start space-x-6">
                                <div class="w-12 h-12 rounded-2xl bg-green-500/10 border border-green-500/20 flex items-center justify-center text-green-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="text-sm font-bold text-white uppercase tracking-wider">{{ __('Dua-Faktor Autentikasi Aktif') }}</h4>
                                    <p class="text-xs text-supabase-muted leading-relaxed">{{ __('Akun Anda terlindungi dengan keamanan tambahan menggunakan aplikasi autentikator (Google Authenticator, Microsoft Authenticator, dll.).') }}</p>
                                </div>
                            </div>

                            <div class="border-t border-supabase-border/50 pt-8 space-y-6">
                                <h4 class="text-[10px] font-bold text-white uppercase tracking-wider text-red-500">{{ __('Nonaktifkan 2FA') }}</h4>
                                <form method="POST" action="{{ route('tenant.settings.2fa.disable') }}" class="space-y-4 max-w-md">
                                    @csrf
                                    <div class="space-y-2">
                                        <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">{{ __('Masukkan Kata Sandi Konfirmasi') }}</label>
                                        <input type="password" name="password" required class="sb-input" placeholder="{{ __('Kata sandi akun Anda') }}"/>
                                    </div>
                                    <button type="submit" class="sb-button-secondary !bg-red-500/10 hover:!bg-red-500/20 !border-red-500/20 !text-red-500 !w-auto px-6 py-2.5 rounded-lg text-xs font-bold transition-all">
                                        {{ __('Nonaktifkan 2FA') }}
                                    </button>
                                </form>
                            </div>
                        @elseif ($user->two_factor_secret)
                            <!-- 2FA Pending Confirmation State -->
                            <div class="space-y-6">
                                <div class="flex items-start space-x-4">
                                    <div class="w-10 h-10 rounded-xl bg-supabase-accent/10 flex items-center justify-center text-supabase-accent">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                        </svg>
                                    </div>
                                    <div class="space-y-1">
                                        <h4 class="text-xs font-bold text-white uppercase tracking-wider">{{ __('Scan Barcode Authenticator') }}</h4>
                                        <p class="text-[10px] text-supabase-muted uppercase font-bold leading-relaxed">{{ __('Pindai QR Code di bawah menggunakan aplikasi autentikator seperti Google Authenticator atau Microsoft Authenticator.') }}</p>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row items-center md:items-start gap-8 bg-supabase-dark/40 p-6 rounded-2xl border border-supabase-border/50">
                                    <div class="bg-white p-3 rounded-xl shadow-xl flex items-center justify-center">
                                        {!! $qrCodeSvg !!}
                                    </div>
                                    <div class="flex-1 space-y-4">
                                        <div class="space-y-2">
                                            <p class="text-[9px] font-bold text-supabase-muted uppercase tracking-wider">{{ __('Kunci Rahasia 2FA (Manual Key)') }}</p>
                                            <code class="block bg-supabase-dark text-supabase-accent px-4 py-2.5 rounded-xl border border-supabase-border font-mono text-xs select-all text-center md:text-left">{{ $user->two_factor_secret }}</code>
                                        </div>
                                        <p class="text-[9px] text-supabase-muted uppercase font-bold leading-relaxed">{{ __('Jika tidak bisa scan, masukkan kunci rahasia di atas secara manual pada aplikasi autentikator Anda.') }}</p>
                                    </div>
                                </div>

                                <div class="border-t border-supabase-border/50 pt-8 space-y-6">
                                    <h4 class="text-xs font-bold text-white uppercase tracking-wider">{{ __('Masukkan Kode Verifikasi') }}</h4>
                                    <form method="POST" action="{{ route('tenant.settings.2fa.confirm') }}" class="space-y-4 max-w-sm">
                                        @csrf
                                        <div class="space-y-2">
                                            <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">{{ __('Kode Autentikator 6-Digit') }}</label>
                                            <input type="text" name="code" required class="sb-input text-center font-mono tracking-[0.5em] text-lg max-w-[200px]" placeholder="000000" maxlength="6" autocomplete="off"/>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <button type="submit" class="sb-button-primary !w-auto px-8 py-2.5 text-xs font-bold uppercase tracking-wider">
                                                {{ __('Verifikasi & Aktifkan') }}
                                            </button>
                                            <button type="submit" form="regen2fa" class="text-[10px] font-bold text-supabase-muted hover:text-white uppercase tracking-wider transition-colors">
                                                {{ __('Generate Ulang QR') }}
                                            </button>
                                        </div>
                                    </form>
                                    <form method="POST" action="{{ route('tenant.settings.2fa.generate') }}" id="regen2fa" class="hidden">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        @else
                            <!-- 2FA Disabled State -->
                            <div class="flex items-start space-x-6">
                                <div class="w-12 h-12 rounded-2xl bg-supabase-input border border-supabase-border flex items-center justify-center text-supabase-muted">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                </div>
                                <div class="space-y-1 flex-1">
                                    <h4 class="text-sm font-bold text-white uppercase tracking-wider">{{ __('Dua-Faktor Autentikasi Nonaktif') }}</h4>
                                    <p class="text-xs text-supabase-muted leading-relaxed">{{ __('Tingkatkan keamanan akun Anda dengan mengaktifkan fitur 2FA. Setiap kali Anda masuk, Anda akan diminta memasukkan kode verifikasi dari aplikasi Authenticator.') }}</p>
                                </div>
                            </div>

                            <div class="border-t border-supabase-border/50 pt-8">
                                <form method="POST" action="{{ route('tenant.settings.2fa.generate') }}">
                                    @csrf
                                    <button type="submit" class="sb-button-primary !w-auto px-12">
                                        {{ __('Aktifkan 2FA') }}
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </section>

            <!-- Mobile Sync Section -->
            <section id="mobile-sync" class="space-y-8">
                <div class="bg-supabase-surface border border-supabase-border rounded-2xl overflow-hidden">
                    <div class="px-8 py-6 border-b border-supabase-border bg-supabase-dark/30">
                        <h3 class="text-xs font-bold text-white uppercase tracking-wider">Android Client Configuration</h3>
                    </div>
                    <div class="p-8 space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="p-6 bg-supabase-input/30 border border-supabase-border rounded-2xl">
                                <p class="text-[8px] font-bold text-supabase-muted uppercase tracking-wider mb-2">Sync Identity (Email)</p>
                                <p class="text-sm font-bold text-white font-mono uppercase tracking-normal">{{ $tenant->email }}</p>
                            </div>
                            <div class="p-6 bg-supabase-input/30 border border-supabase-border rounded-2xl">
                                <p class="text-[8px] font-bold text-supabase-muted uppercase tracking-wider mb-2">Sync Password</p>
                                <p class="text-xs font-bold text-supabase-accent uppercase tracking-wider italic">Managed via Dashboard Password</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <h4 class="text-[10px] font-bold text-white uppercase tracking-wider flex items-center">
                                <span class="w-1 h-1 bg-supabase-accent rounded-full mr-3"></span>
                                Setup Protocol
                            </h4>
                            <div class="grid grid-cols-1 gap-3">
                                @foreach([
                                    '1. Deploy APK to dedicated notification node.',
                                    '2. Authenticate using master dashboard email.',
                                    '3. Initialize Notification Listener service.',
                                    '4. Map monitored financial applications (e.g. BCA, DANA).'
                                ] as $step)
                                    <div class="px-4 py-3 bg-supabase-dark/50 border border-supabase-border/50 rounded-xl text-[10px] font-bold text-supabase-muted uppercase tracking-wider">
                                        {{ $step }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<x-confirm-modal />

@push('scripts')
<script>
function scrollToSection(id) {
    const el = document.getElementById(id);
    if (el) {
        window.scrollTo({
            top: el.offsetTop - 100,
            behavior: 'smooth'
        });
    }
}

function toggleMask(id, btn) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        btn.classList.add('text-supabase-accent');
    } else {
        input.type = 'password';
        btn.classList.remove('text-supabase-accent');
    }
}

function copyToClip(id) {
    const input = document.getElementById(id);
    const originalType = input.type;
    input.type = 'text';
    input.select();
    document.execCommand('copy');
    input.type = originalType;
    alert('Copied to system registry.');
}

function confirmRegen(formId) {
    showConfirmModal({
        type: 'danger',
        title: 'Regenerate Credential?',
        message: 'A physical system update will be required for all connected clients. Proceed?',
        confirmText: 'Execute Regeneration',
        onConfirm: () => document.getElementById(formId).submit()
    });
}
</script>
@endpush
@endsection
