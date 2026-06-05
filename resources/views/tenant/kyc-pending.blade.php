<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Verifikasi | Cekbayar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-supabase-dark text-slate-300 font-['Inter'] antialiased h-full flex items-center justify-center p-6">
    <div class="max-w-md w-full text-center">
        <!-- Icon -->
        <div class="mb-8 inline-flex items-center justify-center">
            <div class="w-24 h-24 bg-yellow-500/10 rounded-3xl flex items-center justify-center border border-yellow-500/20">
                <svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Message -->
        <h1 class="text-3xl font-black text-white mb-4 uppercase tracking-tight">SEDANG <span class="text-yellow-500">DIPROSES</span></h1>
        <p class="text-supabase-muted mb-8 leading-relaxed text-sm font-bold">
            Pengajuan langganan dan verifikasi identitas (KYC) Anda sedang ditinjau oleh tim kami. Silakan selesaikan pembayaran paket secara manual jika belum melakukannya.
        </p>

        <!-- Actions -->
        <div class="space-y-4">
            <a href="https://wa.me/628123456789" target="_blank" class="block w-full sb-button-primary">
                Konfirmasi Pembayaran
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
