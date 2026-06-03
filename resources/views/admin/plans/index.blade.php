@extends('layouts.admin')

@section('page-title', 'Subscription Plans')

@section('content')
<div class="space-y-12">
    <!-- Header Area -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tight uppercase">Subscription <span class="text-supabase-accent">Plans</span></h1>
            <p class="text-supabase-muted mt-2">Manage pricing tiers, duration, and channel capacities.</p>
        </div>
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.plans.create') }}" class="sb-button-primary !w-auto">
                + Create Plan
            </a>
        </div>
    </div>

    <!-- Feedback -->
    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-500 px-6 py-4 rounded-2xl flex items-center shadow-lg animate-in fade-in slide-in-from-top-4 duration-500">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="text-[10px] font-black uppercase tracking-widest">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Plans Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($plans as $plan)
            <div class="bg-supabase-surface border border-supabase-border rounded-3xl overflow-hidden shadow-2xl hover:border-supabase-accent/30 transition-all group">
                <div class="p-8 space-y-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-2xl font-black text-white uppercase tracking-tighter">{{ $plan->name }}</h3>
                            <p class="text-[10px] font-black text-supabase-muted uppercase tracking-widest mt-1">{{ $plan->duration_days }} Days Validity</p>
                        </div>
                        <div class="flex items-center space-x-2 bg-supabase-dark border border-supabase-border px-3 py-1 rounded-full">
                            <div class="w-1.5 h-1.5 rounded-full {{ $plan->is_active ? 'bg-green-500 animate-pulse' : 'bg-supabase-muted' }}"></div>
                            <span class="text-[8px] font-black uppercase text-white tracking-widest">{{ $plan->is_active ? 'Active' : 'Draft' }}</span>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <p class="text-4xl font-black text-white leading-none">Rp {{ number_format($plan->price, 0, ',', '.') }}</p>
                        <p class="text-[10px] font-bold text-supabase-muted uppercase tracking-[0.2em]">One-time initialization</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 py-6 border-y border-supabase-border/50">
                        <div>
                            <p class="text-[8px] font-black text-supabase-muted uppercase tracking-widest mb-1">Channel Capacity</p>
                            <p class="text-sm font-black text-white">{{ $plan->max_channels }} <span class="text-[10px] text-supabase-muted ml-1 font-bold">Nodes</span></p>
                        </div>
                        <div class="text-right">
                            <p class="text-[8px] font-black text-supabase-muted uppercase tracking-widest mb-1">Current Usage</p>
                            <p class="text-sm font-black text-supabase-accent">{{ $plan->tenants_count ?? $plan->tenants()->count() }} <span class="text-[10px] text-supabase-muted ml-1 font-bold">Tenants</span></p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 pt-2">
                        <a href="{{ route('admin.plans.edit', $plan) }}" class="flex-1 sb-button-secondary !w-auto !py-3 !text-[10px] !font-black uppercase tracking-widest text-center">
                            Modify Matrix
                        </a>
                        <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Archive this plan matrix?')" class="flex-none">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-3 bg-red-500/5 border border-red-500/20 text-red-500 hover:bg-red-500 hover:text-white rounded-xl transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="lg:col-span-3 bg-supabase-surface border border-supabase-border rounded-3xl p-20 text-center relative overflow-hidden shadow-2xl">
                <div class="absolute inset-0 opacity-5 bg-[radial-gradient(#fbbf24_1px,transparent_1px)] [background-size:24px_24px]"></div>
                <div class="relative z-10 max-w-md mx-auto">
                    <div class="w-24 h-24 bg-supabase-input border border-supabase-border rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-2xl">
                        <svg class="w-12 h-12 text-supabase-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    </div>
                    <h3 class="text-2xl font-black text-white uppercase tracking-tight mb-4">No Subscription Models</h3>
                    <p class="text-supabase-muted text-xs font-bold uppercase tracking-widest leading-relaxed mb-10">Initialize your first subscription plan to start onboarding tenants into the Cekbayar ecosystem.</p>
                    <a href="{{ route('admin.plans.create') }}" class="sb-button-primary !w-auto !py-4 !px-12">Initialize First Plan</a>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
