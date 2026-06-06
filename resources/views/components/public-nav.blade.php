<nav class="relative z-50 px-6 lg:px-12 h-24 flex items-center justify-between border-b border-supabase-border/50 backdrop-blur-md bg-supabase-dark/30 sticky top-0">
    <div class="flex items-center space-x-3">
        <a href="{{ localeRoute('home') }}" class="flex items-center space-x-3 group">
            <div class="w-10 h-10 bg-supabase-accent group-hover:scale-105 rounded-xl flex items-center justify-center transition-all shadow-lg shadow-supabase-accent/20">
                <svg class="w-6 h-6 text-supabase-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            </div>
            <span class="text-2xl font-bold text-white tracking-normal">Cek<span class="text-supabase-accent">bayar</span></span>
        </a>
    </div>

    <div class="hidden lg:flex items-center space-x-12">
        <a href="{{ app()->getLocale() == 'en' ? '/en#features' : '/#features' }}" class="text-[10px] font-bold text-supabase-muted hover:text-white uppercase tracking-wider transition-colors">{{ __('Fitur') }}</a>
        <a href="{{ app()->getLocale() == 'en' ? '/en#how-it-works' : '/#how-it-works' }}" class="text-[10px] font-bold text-supabase-muted hover:text-white uppercase tracking-wider transition-colors">{{ __('Protokol') }}</a>
        <a href="{{ localeRoute('public.pricing') }}" class="text-[10px] font-bold {{ request()->routeIs('*.public.pricing') || request()->routeIs('public.pricing') ? 'text-supabase-accent' : 'text-supabase-muted' }} hover:text-white uppercase tracking-wider transition-colors">{{ __('Harga') }}</a>
        <a href="{{ localeRoute('public.docs') }}" class="text-[10px] font-bold {{ request()->routeIs('*.public.docs') || request()->routeIs('public.docs') ? 'text-supabase-accent' : 'text-supabase-muted' }} hover:text-white uppercase tracking-wider transition-colors">{{ __('Dokumentasi') }}</a>
    </div>

    <div class="flex items-center space-x-4 md:space-x-8">
        {{-- Language Selector --}}
        @php
            $currentRouteName = request()->route() ? request()->route()->getName() : 'home';
            $isEn = app()->getLocale() === 'en';
            if ($isEn) {
                $targetRouteName = str_replace('en.', '', $currentRouteName ?: 'home');
                $targetUrl = \Illuminate\Support\Facades\Route::has($targetRouteName) ? route($targetRouteName) : '/';
            } else {
                $targetRouteName = 'en.' . ($currentRouteName ?: 'home');
                $targetUrl = \Illuminate\Support\Facades\Route::has($targetRouteName) ? route($targetRouteName) : '/en';
            }
        @endphp
        <a href="{{ $targetUrl }}" class="flex items-center space-x-1.5 px-3 py-1.5 rounded-lg border border-supabase-border bg-supabase-surface/50 hover:bg-supabase-border text-white text-[10px] font-bold tracking-wider transition-all hover:scale-105">
            @if($isEn)
                <span>🇮🇩</span> <span class="hidden md:inline text-[9px]">ID</span>
            @else
                <span>🇬🇧</span> <span class="hidden md:inline text-[9px]">EN</span>
            @endif
        </a>

        @auth
            <a href="{{ auth()->user()->is_admin ? route('admin.dashboard') : route('tenant.dashboard') }}" 
               class="px-6 py-2 bg-supabase-accent text-supabase-dark text-[10px] font-bold tracking-wider rounded-lg hover:scale-105 transition-transform shadow-lg shadow-supabase-accent/20">
                {{ __('Dasbor') }} &rarr;
            </a>
        @else
            <a href="{{ route('login', ['locale' => app()->getLocale()]) }}" class="text-[10px] font-bold text-supabase-muted hover:text-white tracking-wider transition-colors">{{ __('Masuk') }}</a>
            <a href="{{ route('register', ['locale' => app()->getLocale()]) }}" class="px-6 py-2 bg-supabase-accent text-supabase-dark text-[10px] font-bold tracking-wider rounded-lg hover:scale-105 transition-transform shadow-lg shadow-supabase-accent/20">{{ __('Mulai') }}</a>
        @endauth
    </div>
</nav>
