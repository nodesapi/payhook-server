<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Cekbayar') }}</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body {
                font-family: 'Inter', sans-serif;
            }
        </style>
    </head>
    <body class="sb-layout">
        <div class="min-h-screen flex flex-col lg:flex-row w-full overflow-hidden">
            <!-- Left Side - Branding -->
            <div class="sb-branding-panel">
                <!-- Digital Pattern SVG -->
                <div class="absolute inset-0 opacity-[0.15] mix-blend-overlay">
                    <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <pattern id="tech-pattern" width="60" height="60" patternUnits="userSpaceOnUse">
                                <circle cx="2" cy="2" r="1" fill="#fbbf24" opacity="0.4"/>
                                <path d="M 60 0 L 0 0 0 60" fill="none" stroke="#fbbf24" stroke-width="0.2" opacity="0.2"/>
                                <path d="M 30 30 L 30 0 M 30 30 L 60 30" fill="none" stroke="#fbbf24" stroke-width="0.1" opacity="0.1"/>
                            </pattern>
                        </defs>
                        <rect width="100%" height="100%" fill="url(#tech-pattern)" />
                    </svg>
                </div>

                <div class="sb-grid-bg"></div>
                <div class="sb-glow-ornament"></div>
                <div class="absolute top-0 right-0 w-64 h-64 bg-supabase-accent/5 blur-[100px] rounded-full"></div>

                <!-- Technical Ornaments -->
                <div class="absolute bottom-24 right-12 opacity-20 hidden xl:block">
                    <svg width="200" height="200" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="100" cy="100" r="99.5" stroke="#fbbf24" stroke-dasharray="10 10"/>
                        <circle cx="100" cy="100" r="70" stroke="#fbbf24" stroke-width="0.5"/>
                        <path d="M100 0V200M0 100H200" stroke="#fbbf24" stroke-width="0.2"/>
                        <rect x="85" y="85" width="30" height="30" stroke="#fbbf24" stroke-width="2"/>
                    </svg>
                </div>
                
                <div class="relative z-10 max-w-lg w-full">
                    <a href="{{ localeRoute('home') }}" class="mb-12 inline-flex items-center space-x-4 group">
                        <div class="w-12 h-12 bg-supabase-accent rounded-xl flex items-center justify-center shadow-2xl shadow-supabase-accent/20 group-hover:scale-105 transition-transform">
                            <svg class="w-8 h-8 text-supabase-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h1 class="text-4xl font-black tracking-tighter text-white">Cek<span class="text-supabase-accent">bayar</span></h1>
                    </a>

                    <div class="space-y-8">
                        <h2 class="text-5xl font-extrabold text-white leading-[1.1] tracking-tight">
                            {!! __('Jembatan pembayaran <span class="text-supabase-accent">generasi baru</span> untuk developer.') !!}
                        </h2>
                        <p class="text-xl text-supabase-muted leading-relaxed">
                            {!! __('Integrasikan sekali, skala selamanya. Infrastruktur multi-tenant yang dirancang untuk <span class="text-white font-semibold">web modern.</span>') !!}
                        </p>
                        
                        <div class="pt-8 space-y-5">
                            <div class="flex items-center space-x-4 group">
                                <div class="w-2 h-2 rounded-full bg-supabase-accent shadow-[0_0_10px_rgba(251,191,36,0.5)]"></div>
                                <p class="text-supabase-muted group-hover:text-white transition-colors">{{ __('Callback Webhook Instan') }}</p>
                            </div>
                            <div class="flex items-center space-x-4 group">
                                <div class="w-2 h-2 rounded-full bg-supabase-accent shadow-[0_0_10px_rgba(251,191,36,0.5)]"></div>
                                <p class="text-supabase-muted group-hover:text-white transition-colors">{{ __('Arsitektur Multi-Tenant') }}</p>
                            </div>
                            <div class="flex items-center space-x-4 group">
                                <div class="w-2 h-2 rounded-full bg-supabase-accent shadow-[0_0_10px_rgba(251,191,36,0.5)]"></div>
                                <p class="text-supabase-muted group-hover:text-white transition-colors">{{ __('Dukungan QRIS Penuh') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer-like element for Branding Panel -->
                <div class="absolute bottom-12 left-12 right-12 flex justify-between items-center text-[10px] text-supabase-muted uppercase tracking-[0.2em] font-bold">
                    <span>{{ __('Infrastruktur Terdistribusi') }}</span>
                    <div class="h-[1px] flex-1 mx-8 bg-supabase-border opacity-50"></div>
                    <span>v2.0.4</span>
                </div>
            </div>

            <!-- Right Side - Content -->
            <div class="sb-form-panel">
                <div class="w-full max-w-[400px]">
                    <a href="{{ localeRoute('home') }}" class="lg:hidden mb-12 flex items-center space-x-3 group">
                        <div class="w-10 h-10 bg-supabase-accent rounded-lg flex items-center justify-center group-hover:scale-105 transition-transform shadow-lg shadow-supabase-accent/20">
                            <svg class="w-6 h-6 text-supabase-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h1 class="text-2xl font-black text-white tracking-tighter">Cek<span class="text-supabase-accent">bayar</span></h1>
                    </a>
                    
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
