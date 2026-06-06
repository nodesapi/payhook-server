<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Node Identity Error | Cekbayar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-supabase-dark text-slate-300 font-['Inter'] antialiased h-full flex items-center justify-center p-6">
    <div class="max-w-md w-full text-center">
        <!-- Icon -->
        <div class="mb-8 inline-flex items-center justify-center">
            <div class="w-24 h-24 bg-supabase-accent/10 rounded-3xl flex items-center justify-center border border-supabase-accent/20">
                <svg class="w-12 h-12 text-supabase-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
        </div>

        <!-- Message -->
        <h1 class="text-3xl font-bold text-white mb-4 uppercase tracking-normal">Identity <span class="text-supabase-accent">Orphaned</span></h1>
        <p class="text-supabase-muted mb-12 leading-relaxed font-bold uppercase text-[10px] tracking-wider">
            Your authentication node is not currently mapped to a verified merchant tenant. Access to terminal infrastructure is restricted.
        </p>

        <!-- Actions -->
        <div class="space-y-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sb-button-primary">Terminate Session</button>
            </form>
            
            <a href="mailto:admin@Cekbayar.local" class="block text-[10px] font-bold text-supabase-muted hover:text-white transition-colors uppercase tracking-wider">
                Request Node Mapping
            </a>
        </div>

        <!-- Debug Trace -->
        <div class="mt-12 p-6 bg-supabase-surface border border-supabase-border rounded-2xl text-left relative overflow-hidden">
            <div class="absolute inset-0 opacity-5 bg-[radial-gradient(#fbbf24_1px,transparent_1px)] [background-size:16px_16px]"></div>
            <div class="relative z-10">
                <p class="text-[8px] font-bold text-supabase-accent uppercase tracking-wider mb-2 flex items-center">
                    <span class="w-1 h-1 bg-supabase-accent rounded-full mr-2"></span>
                    Diagnostic Note
                </p>
                <p class="text-[10px] text-supabase-muted font-bold uppercase leading-tight">
                    Ensure your account email matches a registered tenant identity in the <span class="text-white">Admin Registry</span>.
                </p>
            </div>
        </div>
        
        <p class="mt-12 text-[8px] text-supabase-muted uppercase tracking-[0.3em]">Cekbayar Identity Node v1.0</p>
    </div>
</body>
</html>
