@extends('layouts.admin')

@section('title', 'Edit Master Payment Channel')

@section('content')

<!-- Page Header -->
<div class="mb-12">
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.master-channels.index') }}" class="p-3 bg-supabase-surface border border-supabase-border rounded-xl text-supabase-muted hover:text-white transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <div>
            <h1 class="text-4xl font-black text-white tracking-tight uppercase">Edit <span class="text-supabase-accent">Channel</span></h1>
            <p class="text-supabase-muted mt-2">Modify existing global payment channel.</p>
        </div>
    </div>
</div>

<!-- Form Container -->
<div class="w-full">
    <form action="{{ route('admin.master-channels.update', $channel->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 space-y-8 shadow-2xl">
            <h3 class="text-xs font-black text-white uppercase tracking-[0.2em] mb-6 flex items-center">
                <span class="w-1.5 h-1.5 bg-supabase-accent rounded-full mr-3 shadow-[0_0_10px_rgba(251,191,36,0.5)]"></span>
                Channel Properties
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Channel Name</label>
                    <input type="text" name="name" value="{{ old('name', $channel->name) }}" class="sb-input" required>
                    @error('name')<p class="mt-1 text-[10px] font-black text-red-500 uppercase tracking-widest">{{ $message }}</p>@enderror
                </div>
                
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Internal Code</label>
                    <input type="text" name="code" value="{{ old('code', $channel->code) }}" class="sb-input" required>
                    @error('code')<p class="mt-1 text-[10px] font-black text-red-500 uppercase tracking-widest">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Base Type</label>
                    <select name="type" class="sb-input bg-supabase-dark" required>
                        <option value="qris" {{ old('type', $channel->type) == 'qris' ? 'selected' : '' }}>QRIS</option>
                        <option value="ewallet" {{ old('type', $channel->type) == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                        <option value="virtual_account" {{ old('type', $channel->type) == 'virtual_account' ? 'selected' : '' }}>Virtual Account</option>
                        <option value="bank_transfer" {{ old('type', $channel->type) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer (Manual)</option>
                    </select>
                    @error('type')<p class="mt-1 text-[10px] font-black text-red-500 uppercase tracking-widest">{{ $message }}</p>@enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Logo / Icon</label>
                    @if($channel->logo_url)
                        <div class="mb-4 p-2 bg-white rounded-xl inline-block border border-supabase-border">
                            <img src="{{ $channel->logo_url }}" alt="Current Logo" class="h-12 object-contain">
                        </div>
                    @endif
                    <input type="file" name="logo" accept="image/*" class="sb-input !p-2 bg-supabase-dark">
                    <p class="text-[8px] text-supabase-muted uppercase font-bold tracking-tighter">Leave empty to keep current.</p>
                    @error('logo')<p class="mt-1 text-[10px] font-black text-red-500 uppercase tracking-widest">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="pt-4 border-t border-supabase-border flex items-center space-x-4">
                <div class="relative flex items-start">
                    <div class="flex h-6 items-center">
                        <input id="is_active" name="is_active" type="checkbox" class="h-4 w-4 rounded border-supabase-border bg-supabase-dark text-supabase-accent focus:ring-supabase-accent focus:ring-offset-supabase-dark" {{ old('is_active', $channel->is_active) ? 'checked' : '' }}>
                    </div>
                    <div class="ml-3 text-sm leading-6">
                        <label for="is_active" class="font-black text-white uppercase tracking-widest text-[10px]">Channel is Active</label>
                        <p class="text-[8px] text-supabase-muted uppercase font-bold tracking-tighter">Toggle global availability for this channel.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-6 pt-8">
            <a href="{{ route('admin.master-channels.index') }}" class="text-[10px] font-black text-supabase-muted uppercase tracking-widest hover:text-white transition-colors">Cancel</a>
            <button type="submit" class="sb-button-primary !w-auto !py-4 !px-16">
                Update Channel
            </button>
        </div>
    </form>
</div>
@endsection
