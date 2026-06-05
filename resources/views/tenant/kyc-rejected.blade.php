<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KYC Ditolak | Cekbayar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-supabase-dark text-slate-300 font-['Inter'] antialiased h-full flex items-center justify-center p-6">
    <div class="max-w-md w-full text-center">
        <!-- Icon -->
        <div class="mb-8 inline-flex items-center justify-center">
            <div class="w-24 h-24 bg-red-500/10 rounded-3xl flex items-center justify-center border border-red-500/20">
                <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
        </div>

        <!-- Message -->
        <h1 class="text-3xl font-black text-white mb-4 uppercase tracking-tight">DATA <span class="text-red-500">DITOLAK</span></h1>
        
        <div class="mt-4 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-left mb-8">
            <p class="text-xs font-black text-red-400 uppercase tracking-widest mb-1">Alasan Penolakan:</p>
            <p class="text-sm text-slate-300">{{ \App\Models\Tenant::where('email', auth()->user()->email)->first()->kyc_reject_reason ?? 'Dokumen KTP tidak valid atau buram.' }}</p>
        </div>

        <!-- Actions -->
        <div class="space-y-4">
            <a href="{{ route('tenant.setup') }}" class="block w-full sb-button-primary">
                Perbaiki Data
            </a>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block w-full text-xs font-black text-supabase-muted hover:text-white transition-colors uppercase tracking-[0.2em] py-4">
                    Logout
                </button>
            </form>
        </div>
        
        <p class="mt-12 text-[8px] text-supabase-muted uppercase tracking-[0.3em]">Cekbayar Platform v1.0</p>
    </div>
</body>
</html>
