<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title', 'Admin') | Cekbayar Control</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .sidebar-scrollbar::-webkit-scrollbar { width: 4px; }
        .sidebar-scrollbar::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
    </style>
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.classList.add('light');
        } else {
            document.documentElement.classList.remove('light');
        }
    </script>
</head>
<body class="bg-supabase-dark text-slate-300 font-sans antialiased min-h-screen">

    <!-- Overlay for mobile (admin) -->
    <div id="sidebarOverlay" class="lg:hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-30 hidden transition-all duration-300"></div>
    
    <!-- Sidebar -->
    <aside id="sidebar" class="fixed left-0 top-0 h-screen w-72 flex flex-col z-40 bg-supabase-dark border-r border-supabase-border transition-all duration-300" style="display:none;">

        <!-- Logo Area -->
        <div class="p-8 border-b border-supabase-border bg-supabase-surface/20">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-supabase-accent rounded-xl flex items-center justify-center shadow-lg shadow-supabase-accent/20">
                    <svg class="w-6 h-6 text-supabase-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-white font-black text-xl uppercase tracking-tighter leading-none">Cek<span class="text-supabase-accent">bayar</span></div>
                    <div class="mt-1 inline-flex px-1.5 py-0.5 bg-supabase-accent text-supabase-dark text-[8px] font-black uppercase tracking-widest rounded">Admin Central</div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-8 overflow-y-auto sidebar-scrollbar space-y-8">
            <div>
                <h3 class="px-4 text-[10px] font-bold text-supabase-muted uppercase tracking-[0.2em] mb-4">Core Management</h3>
                <div class="space-y-1">
                    <x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')" icon="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        System Overview
                    </x-nav-link>

                    <x-nav-link href="{{ route('admin.tenants.index') }}" :active="request()->routeIs('admin.tenants.*')" icon="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                        Tenant Registry
                    </x-nav-link>

                    <x-nav-link href="{{ route('admin.webhook-logs.index') }}" :active="request()->routeIs('admin.webhook-logs.*')" icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        Webhook Logs
                    </x-nav-link>

                    <x-nav-link href="{{ route('admin.plans.index') }}" :active="request()->routeIs('admin.plans.*')" icon="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                        Subscription Plans
                    </x-nav-link>

                    <x-nav-link href="{{ route('admin.master-channels.index') }}" :active="request()->routeIs('admin.master-channels.*')" icon="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                        Master Channels
                    </x-nav-link>

                    <x-nav-link href="{{ route('admin.system-config.index') }}" :active="request()->routeIs('admin.system-config.*')" icon="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                        System Config
                    </x-nav-link>
                </div>
            </div>

            <div>
                <h3 class="px-4 text-[10px] font-bold text-supabase-muted uppercase tracking-[0.2em] mb-4">Switch Panel</h3>
                <div class="space-y-1">
                    <x-nav-link href="{{ route('profile.edit') }}" :active="request()->routeIs('profile.*')" icon="M5.121 17.804A9 9 0 1118.88 6.196M15 11a3 3 0 11-6 0 3 3 0 016 0zm-6.5 6.5a5 5 0 017 0">
                        Account Profile
                    </x-nav-link>

                    <x-nav-link href="{{ route('tenant.dashboard') }}" :active="false" icon="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                        Merchant View
                    </x-nav-link>
                </div>
            </div>
        </nav>

        <!-- User Info -->
        <div class="p-6 border-t border-supabase-border bg-supabase-surface/30">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-supabase-input border border-supabase-border flex items-center justify-center text-supabase-accent font-bold">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-bold text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</div>
                    <div class="text-[10px] text-supabase-muted uppercase tracking-wider truncate">Master Admin</div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                    @csrf
                    <button type="submit" class="p-2 text-supabase-muted hover:text-red-400 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="lg:ml-72 min-h-screen flex flex-col min-w-0 overflow-x-hidden">
        <!-- Top Nav -->
        <header class="h-16 border-b border-supabase-border flex items-center px-4 lg:px-8 justify-between sticky top-0 bg-supabase-dark/80 backdrop-blur-md z-20 min-w-0">
            <div class="flex items-center gap-2 lg:gap-3 text-ellipsis overflow-hidden">
                <!-- Hamburger (mobile) stays on the left, next to title -->
                <button id="mobileSidebarToggle" class="lg:hidden text-white p-2" aria-label="Open menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <div class="text-xs lg:text-sm font-bold text-white uppercase tracking-wider lg:tracking-[0.2em] whitespace-nowrap">
                    @yield('page-title', 'Dashboard')
                </div>
            </div>

            <!-- Online status stays on the right -->
            <div class="flex items-center space-x-2 lg:space-x-4">
                <button id="themeToggleBtn" class="p-2 text-supabase-muted hover:text-white transition-all active:scale-95 flex items-center justify-center" aria-label="Toggle theme">
                    <!-- Sun Icon (shows in light mode to switch to dark) -->
                    <svg id="themeToggleSun" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M14 12a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <!-- Moon Icon (shows in dark mode to switch to light) -->
                    <svg id="themeToggleMoon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>

                <div class="flex items-center px-2 py-0.5 lg:px-3 lg:py-1 bg-supabase-surface border border-supabase-border rounded-full whitespace-nowrap">
                    <div class="w-1 h-1 lg:w-2 lg:h-2 rounded-full bg-supabase-accent mr-1 lg:mr-2 animate-pulse"></div>
                    <span class="text-[8px] lg:text-[10px] font-bold uppercase tracking-wider lg:tracking-widest text-slate-300">Admin Control Online</span>
                </div>
            </div>
        </header>

        <div class="flex-1 min-w-0 max-w-full p-6 sm:p-8 lg:p-12">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mb-8 bg-green-500/10 border border-green-500/20 text-green-500 px-6 py-4 rounded-2xl flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="text-sm font-bold">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-8 bg-red-500/10 border border-red-500/20 text-red-500 px-6 py-4 rounded-2xl flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="text-sm font-bold">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
        
        <footer class="p-8 border-t border-supabase-border text-center">
            <p class="text-[10px] text-supabase-muted uppercase tracking-[0.3em]">Cekbayar Admin &copy; 2026 - Master Node Control</p>
        </footer>
    </main>

    @stack('scripts')

    <!-- Mobile sidebar control (admin) -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
        const closeSidebar = document.getElementById('closeSidebar');

        function isMobile() {
            return window.matchMedia && window.matchMedia('(max-width: 1023px)').matches;
        }

        function openSidebar() {
            if (!sidebar) return;
            sidebar.style.display = '';
            sidebar.classList.add('-translate-x-0');
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay && sidebarOverlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebarFunc() {
            if (!sidebar) return;
            sidebar.classList.add('-translate-x-full');
            sidebarOverlay && sidebarOverlay.classList.add('hidden');
            sidebar.style.display = 'none';
            document.body.style.overflow = '';
        }

        // Initial state + keep in sync on resize
        function applyMobileSidebarState() {
            if (!sidebar) return;
            if (isMobile()) {
                sidebar.style.display = 'none';
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('-translate-x-0');
            } else {
                sidebar.style.display = '';
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.remove('-translate-x-0');
            }
        }

        applyMobileSidebarState();
        if (window.matchMedia) {
            const mq = window.matchMedia('(max-width: 1023px)');
            if (mq.addEventListener) mq.addEventListener('change', applyMobileSidebarState);
            else if (mq.addListener) mq.addListener(applyMobileSidebarState);
        }

        if (mobileSidebarToggle) mobileSidebarToggle.addEventListener('click', () => {
            if (isMobile()) openSidebar();
        });
        if (closeSidebar) closeSidebar.addEventListener('click', closeSidebarFunc);
        if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebarFunc);

        // Theme Toggle Logic
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('themeToggleBtn');
            const sunIcon = document.getElementById('themeToggleSun');
            const moonIcon = document.getElementById('themeToggleMoon');
            
            function updateThemeUI() {
                const isLight = document.documentElement.classList.contains('light');
                if (isLight) {
                    sunIcon.classList.add('hidden');
                    moonIcon.classList.remove('hidden');
                } else {
                    sunIcon.classList.remove('hidden');
                    moonIcon.classList.add('hidden');
                }
            }

            updateThemeUI();

            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    if (document.documentElement.classList.contains('light')) {
                        document.documentElement.classList.remove('light');
                        localStorage.setItem('theme', 'dark');
                    } else {
                        document.documentElement.classList.add('light');
                        localStorage.setItem('theme', 'light');
                    }
                    updateThemeUI();
                });
            }
        });
    </script>
</body>
</html>
