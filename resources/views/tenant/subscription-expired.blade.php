<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Subscription Expired | {{ config('app.name', 'Cekbayar') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-supabase-dark text-slate-300 font-['Inter'] antialiased h-full flex items-center justify-center p-6">
    
    <div class="max-w-md w-full text-center">
        <div class="mb-8 inline-flex items-center justify-center">
            <div class="w-20 h-20 bg-supabase-accent/10 rounded-3xl flex items-center justify-center border border-supabase-accent/20">
                <svg class="w-10 h-10 text-supabase-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <h1 class="text-3xl font-bold text-white mb-4 uppercase tracking-normal">Masa Berlaku <span class="text-supabase-accent">Habis</span></h1>
        <p class="text-supabase-muted mb-8 leading-relaxed">
            Maaf bro, masa aktif akun <strong>{{ $tenant->name }}</strong> sudah berakhir. Silakan lakukan perpanjangan untuk terus menggunakan layanan Cekbayar.
        </p>

        <div class="bg-supabase-surface border border-supabase-border rounded-2xl p-6 mb-8 text-left">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-supabase-muted uppercase tracking-wider">Status Akun</span>
                <span class="px-2 py-0.5 bg-red-500/10 text-red-500 text-[10px] font-bold rounded uppercase">Expired</span>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-supabase-border">
                    <span class="text-sm text-slate-400">Terakhir Aktif</span>
                    <span class="text-sm text-white font-medium">{{ $tenant->expired_at ? $tenant->expired_at->format('d M Y') : '-' }}</span>
                </div>
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm text-slate-400">Paket Terakhir</span>
                    <span class="text-sm text-white font-medium">Pro Monthly</span>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <a href="#" class="sb-button-primary">Perpanjang Sekarang</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs font-bold text-supabase-muted hover:text-white transition-colors uppercase tracking-wider">
                    Keluar dari Akun
                </button>
            </form>
        </div>

        <p class="mt-12 text-[10px] text-supabase-muted uppercase tracking-wider">Cekbayar Billing System v1.0</p>
    </div>

</body>
</html>
