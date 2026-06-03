@extends('layouts.admin')

@section('page-title', 'Configure Plan Matrix')

@section('content')
<div class="w-full mx-auto">
    <!-- Header -->
    <div class="mb-12">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.plans.index') }}" class="p-3 bg-supabase-surface border border-supabase-border rounded-xl text-supabase-muted hover:text-white transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-4xl font-black text-white tracking-tight uppercase">Modify <span class="text-supabase-accent">Plan Matrix</span></h1>
                <p class="text-supabase-muted mt-2">Adjust operational constraints for the <span class="text-white">{{ $plan->name }}</span> tier.</p>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-8 bg-red-500/10 border border-red-500/20 text-red-500 px-6 py-4 rounded-2xl shadow-lg">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="text-xs font-black uppercase tracking-widest">Protocol Errors Detected</span>
            </div>
            <ul class="text-[10px] font-bold uppercase tracking-wider space-y-1 ml-8">
                @foreach($errors->all() as $error)
                    <li>&bull; {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.plans.update', $plan) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Core Parameters -->
            <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 space-y-8 shadow-2xl">
                <h3 class="text-xs font-black text-white uppercase tracking-[0.2em] flex items-center">
                    <span class="w-1.5 h-1.5 bg-supabase-accent rounded-full mr-3 shadow-[0_0_10px_rgba(251,191,36,0.5)]"></span>
                    Core Identity
                </h3>

                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-supabase-muted uppercase tracking-widest">Tier Name</label>
                        <input type="text" name="name" value="{{ old('name', $plan->name) }}" required class="sb-input">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-supabase-muted uppercase tracking-widest">Description</label>
                        <textarea name="description" rows="4" class="sb-input resize-none">{{ old('description', $plan->description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Operational Constraints -->
            <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 space-y-8 shadow-2xl">
                <h3 class="text-xs font-black text-white uppercase tracking-[0.2em] flex items-center">
                    <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-3 shadow-[0_0_10px_rgba(59,130,246,0.5)]"></span>
                    Operational Parameters
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-supabase-muted uppercase tracking-widest">Initialization Fee (IDR)</label>
                        <input type="number" name="price" value="{{ old('price', $plan->price) }}" min="0" required class="sb-input">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-supabase-muted uppercase tracking-widest">Temporal Validity (Days)</label>
                        <input type="number" name="duration_days" value="{{ old('duration_days', $plan->duration_days) }}" min="1" required class="sb-input">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black text-supabase-muted uppercase tracking-widest">Max Distribution Nodes</label>
                        <input type="number" name="max_channels" value="{{ old('max_channels', $plan->max_channels) }}" min="1" required class="sb-input">
                    </div>
                    <div class="space-y-2 flex flex-col justify-end pb-2 relative">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-supabase-dark border border-supabase-border rounded-full peer peer-checked:bg-supabase-accent transition-colors"></div>
                            <div class="absolute left-1 top-1 w-4 h-4 bg-supabase-muted rounded-full peer-checked:translate-x-5 peer-checked:bg-supabase-dark transition-transform"></div>
                            <span class="text-[10px] font-black text-white uppercase tracking-widest">Active Matrix</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end space-x-6 pt-8 border-t border-supabase-border">
            <a href="{{ route('admin.plans.index') }}" class="text-[10px] font-black text-supabase-muted uppercase tracking-widest hover:text-white transition-colors">Discard Updates</a>
            <button type="submit" class="sb-button-primary !w-auto !py-4 !px-16">
                Commit Matrix Updates
            </button>
        </div>
    </form>
</div>
@endsection
