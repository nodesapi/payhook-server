<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Documentation') | {{ config('app.name', 'Cekbayar') }}</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        
        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Inter', sans-serif; }
            .docs-sidebar::-webkit-scrollbar { width: 2px; }
            .docs-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
            .prose h1, .prose h2, .prose h3 { letter-spacing: -0.025em; text-transform: uppercase; }
            .prose pre { border: 1px solid rgba(255,255,255,0.1); background: #0a0a0a !important; }
        </style>
    </head>
    <body class="bg-supabase-dark text-slate-300 antialiased selection:bg-supabase-accent selection:text-supabase-dark">
        <div class="min-h-screen flex flex-col relative overflow-hidden">
            {{-- Background Ornament --}}
            <div class="absolute inset-0 z-0 pointer-events-none">
                <div class="absolute top-0 left-0 w-[800px] h-[800px] bg-supabase-accent/5 blur-[150px] rounded-full -translate-x-1/2 -translate-y-1/2"></div>
                <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-blue-500/5 blur-[120px] rounded-full translate-x-1/4 translate-y-1/4"></div>
            </div>

            <x-public-nav />

            <div class="flex-1 w-full flex relative z-10">
                <!-- Sidebar -->
                <aside class="hidden lg:block w-80 flex-shrink-0 border-r border-supabase-border/50 overflow-y-auto docs-sidebar sticky top-20 h-[calc(100vh-5rem)] bg-supabase-dark/20 backdrop-blur-sm">
                    <nav class="p-10 space-y-12">
                        <div>
                            <h3 class="text-[10px] font-bold text-white uppercase tracking-wider mb-6 flex items-center">
                                <span class="w-1 h-1 bg-supabase-accent rounded-full mr-3 animate-pulse"></span>
                                Introduction
                            </h3>
                            <ul class="space-y-4">
                                <li><a href="#getting-started" class="text-xs font-bold text-supabase-muted hover:text-supabase-accent transition-colors flex items-center group uppercase tracking-wider"><span class="w-1.5 h-1.5 rounded-full border border-supabase-muted mr-4 group-hover:border-supabase-accent transition-colors"></span>Getting Started</a></li>
                                <li><a href="#architecture" class="text-xs font-bold text-supabase-muted hover:text-supabase-accent transition-colors flex items-center group uppercase tracking-wider"><span class="w-1.5 h-1.5 rounded-full border border-supabase-muted mr-4 group-hover:border-supabase-accent transition-colors"></span>Architecture</a></li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-[10px] font-bold text-white uppercase tracking-wider mb-6 flex items-center">
                                <span class="w-1 h-1 bg-blue-500 rounded-full mr-3"></span>
                                Integration
                            </h3>
                            <ul class="space-y-4">
                                <li><a href="#android-setup" class="text-xs font-bold text-supabase-muted hover:text-supabase-accent transition-colors flex items-center group uppercase tracking-wider"><span class="w-1.5 h-1.5 rounded-full border border-supabase-muted mr-4 group-hover:border-supabase-accent transition-colors"></span>Android Setup</a></li>
                                <li><a href="#authentication" class="text-xs font-bold text-supabase-muted hover:text-supabase-accent transition-colors flex items-center group uppercase tracking-wider"><span class="w-1.5 h-1.5 rounded-full border border-supabase-muted mr-4 group-hover:border-supabase-accent transition-colors"></span>Security</a></li>
                            </ul>
                        </div>
                        <div>
                            <h3 class="text-[10px] font-bold text-white uppercase tracking-wider mb-6 flex items-center">
                                <span class="w-1 h-1 bg-green-500 rounded-full mr-3"></span>
                                API Engine
                            </h3>
                            <ul class="space-y-4">
                                <li><a href="#create-invoice" class="text-xs font-bold text-supabase-muted hover:text-supabase-accent transition-colors flex items-center group uppercase tracking-wider"><span class="w-1.5 h-1.5 rounded-full border border-supabase-muted mr-4 group-hover:border-supabase-accent transition-colors"></span>Create Invoice</a></li>
                                <li><a href="#receiving-webhook" class="text-xs font-bold text-supabase-muted hover:text-supabase-accent transition-colors flex items-center group uppercase tracking-wider"><span class="w-1.5 h-1.5 rounded-full border border-supabase-muted mr-4 group-hover:border-supabase-accent transition-colors"></span>Webhooks</a></li>
                            </ul>
                        </div>
                    </nav>
                </aside>

                <!-- Main Content -->
                <main class="flex-1 min-w-0 p-8 lg:p-24 overflow-y-auto">
                    <div class="max-w-4xl mx-auto">
                        @yield('content')
                    </div>
                </main>
                
                <!-- Table of Contents (Desktop) -->
                <aside class="hidden xl:block w-72 flex-shrink-0 p-10 sticky top-20 h-[calc(100vh-5rem)]">
                    <h3 class="text-[10px] font-bold text-white uppercase tracking-wider mb-6">In this block</h3>
                    <nav class="space-y-4 text-[10px] font-bold text-supabase-muted uppercase tracking-[0.1em]">
                        @yield('toc')
                    </nav>
                </aside>
            </div>
        </div>
    </body>
</html>
