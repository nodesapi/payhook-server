@extends('layouts.admin')

@section('title', 'Master Payment Channels')

@section('content')

<!-- Page Header -->
<div class="mb-12">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-4xl font-bold text-white tracking-normal uppercase">Master <span class="text-supabase-accent">Channels</span></h1>
            <p class="text-supabase-muted mt-2">Manage global payment methods and visual assets for all tenants.</p>
        </div>
        <a href="{{ route('admin.master-channels.create') }}" class="sb-button-primary !w-auto !py-3 !px-8">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add New Channel
        </a>
    </div>
</div>

<!-- Success Message -->
@if(session('success'))
    <div class="mb-8 bg-green-500/10 border border-green-500/20 text-green-500 px-6 py-4 rounded-2xl flex items-center shadow-lg">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="text-xs font-bold uppercase tracking-wider">{{ session('success') }}</span>
    </div>
@endif

@if($channels->count() > 0)
    <div class="bg-supabase-surface border border-supabase-border rounded-3xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-supabase-dark border-b border-supabase-border">
                    <tr>
                        <th class="px-6 py-5 text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Channel Name</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Code</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Base Type</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-supabase-muted uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-5 text-[10px] font-bold text-supabase-muted uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-supabase-border/50">
                    @foreach($channels as $channel)
                        <tr class="hover:bg-supabase-dark/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center overflow-hidden border border-supabase-border p-1">
                                        @if($channel->logo_url)
                                            <img src="{{ $channel->logo_url }}" class="w-full h-full object-contain" alt="{{ $channel->name }}">
                                        @else
                                            <span class="text-xs font-bold text-supabase-dark">{{ substr($channel->name, 0, 2) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-white uppercase tracking-normal">{{ $channel->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold text-supabase-muted tracking-wider">{{ $channel->code }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-supabase-accent/10 text-supabase-accent border border-supabase-accent/20 rounded-lg text-[10px] font-bold uppercase tracking-wider">
                                    {{ str_replace('_', ' ', $channel->type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($channel->is_active)
                                    <span class="px-3 py-1 bg-green-500/10 text-green-500 border border-green-500/20 rounded-lg text-[10px] font-bold uppercase tracking-wider">Active</span>
                                @else
                                    <span class="px-3 py-1 bg-supabase-muted/10 text-supabase-muted border border-supabase-muted/20 rounded-lg text-[10px] font-bold uppercase tracking-wider">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-4">
                                    <a href="{{ route('admin.master-channels.edit', $channel->id) }}" class="text-[10px] font-bold text-supabase-accent hover:text-white uppercase tracking-wider transition-colors">Edit</a>
                                    <form action="{{ route('admin.master-channels.destroy', $channel->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this channel?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-[10px] font-bold text-red-500 hover:text-white uppercase tracking-wider transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <!-- Empty State -->
    <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-20 text-center relative overflow-hidden shadow-2xl">
        <div class="absolute inset-0 opacity-5 bg-[radial-gradient(#fbbf24_1px,transparent_1px)] [background-size:24px_24px]"></div>
        <div class="relative z-10 max-w-md mx-auto">
            <div class="w-24 h-24 bg-supabase-input border border-supabase-border rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-2xl">
                <svg class="w-12 h-12 text-supabase-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </div>
            <h3 class="text-2xl font-bold text-white uppercase tracking-normal mb-4">No Master Channels</h3>
            <p class="text-supabase-muted text-xs font-bold uppercase tracking-wider leading-relaxed mb-10">Add your first master channel to allow tenants to configure it.</p>
            <a href="{{ route('admin.master-channels.create') }}" class="sb-button-primary !w-auto !py-4 !px-12">Add New Channel</a>
        </div>
    </div>
@endif

@endsection
