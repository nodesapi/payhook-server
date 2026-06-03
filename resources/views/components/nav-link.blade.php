@props(['active', 'icon'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-2.5 text-sm font-bold rounded-xl transition-all duration-200 bg-supabase-accent text-supabase-dark shadow-lg shadow-supabase-accent/10'
            : 'flex items-center px-4 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 text-supabase-muted hover:bg-supabase-surface hover:text-white group';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <svg class="w-5 h-5 mr-3 {{ ($active ?? false) ? 'text-supabase-dark' : 'text-supabase-muted group-hover:text-supabase-accent transition-colors' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"></path>
        </svg>
    @endif
    {{ $slot }}
</a>
