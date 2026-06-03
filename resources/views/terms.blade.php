<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('Syarat & Ketentuan | Cekbayar') }}</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-supabase-dark text-slate-300 font-sans antialiased">
        <div class="relative min-h-screen flex flex-col overflow-hidden">
            {{-- Background Ornament --}}
            <div class="absolute inset-0 z-0">
                <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-supabase-accent/5 blur-[120px] rounded-full"></div>
                <div class="absolute inset-0 opacity-[0.03] bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
            </div>

            <x-public-nav />

            <main class="relative z-10 flex-1 px-6 py-24">
                <div class="max-w-4xl mx-auto bg-supabase-surface border border-supabase-border rounded-[40px] p-8 md:p-16 shadow-2xl">
                    <h1 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-8">
                        {{ __('Syarat & Ketentuan') }}
                    </h1>
                    
                    <p class="text-xs text-supabase-muted uppercase tracking-widest font-black mb-8 border-b border-supabase-border pb-4">
                        {{ __('Terakhir diperbarui: 25 Mei 2026') }}
                    </p>

                    <div class="space-y-8 text-sm text-slate-300 font-medium leading-relaxed">
                        <section class="space-y-4">
                            <h2 class="text-lg font-black text-white uppercase tracking-wider">{{ __('1. Penerimaan Ketentuan') }}</h2>
                            <p>{{ __('Dengan mengakses dan menggunakan platform Cekbayar, Anda menyetujui untuk terikat oleh Syarat & Ketentuan ini. Jika Anda tidak menyetujui salah satu bagian dari ketentuan ini, Anda tidak diperkenankan menggunakan layanan kami.') }}</p>
                        </section>

                        <section class="space-y-4">
                            <h2 class="text-lg font-black text-white uppercase tracking-wider">{{ __('2. Deskripsi Layanan') }}</h2>
                            <p>{{ __('Cekbayar menyediakan jembatan data (bridge) yang meneruskan push notification transaksi masuk dari aplikasi perbankan atau dompet digital Anda di perangkat Android ke server tujuan Anda via webhook JSON. Cekbayar tidak bertindak sebagai lembaga keuangan, tidak memotong biaya transaksi, dan tidak menampung dana pengguna.') }}</p>
                        </section>

                        <section class="space-y-4">
                            <h2 class="text-lg font-black text-white uppercase tracking-wider">{{ __('3. Tanggung Jawab Akun & Perangkat') }}</h2>
                            <p>{{ __('Anda bertanggung jawab penuh untuk menjaga keamanan akses perangkat Android Anda yang menjalankan aplikasi Cekbayar, kerahasiaan token otentikasi webhook, dan endpoint server Anda. Segala penyalahgunaan akses yang disebabkan kelalaian pengguna adalah tanggung jawab pribadi Anda.') }}</p>
                        </section>

                        <section class="space-y-4">
                            <h2 class="text-lg font-black text-white uppercase tracking-wider">{{ __('4. Batasan Tanggung Jawab') }}</h2>
                            <p>{{ __('Layanan disediakan "sebagaimana adanya" tanpa jaminan ketersediaan mutlak. Cekbayar tidak bertanggung jawab atas kerugian materiil atau imateriil yang disebabkan oleh kegagalan sistem pengiriman notifikasi dari bank, gangguan internet pada perangkat pengguna, kesalahan logika pencocokan di server pengguna, atau perubahan kebijakan dari aplikasi pihak ketiga.') }}</p>
                        </section>

                        <section class="space-y-4">
                            <h2 class="text-lg font-black text-white uppercase tracking-wider">{{ __('5. Penggunaan yang Diperbolehkan') }}</h2>
                            <p>{{ __('Anda setuju untuk menggunakan layanan ini hanya untuk tujuan sah sesuai dengan hukum yang berlaku di Republik Indonesia. Penggunaan platform untuk aktivitas ilegal, penipuan, atau pencucian uang akan berakibat pada penangguhan akun secara permanen.') }}</p>
                        </section>
                    </div>
                </div>
            </main>

            {{-- Footer --}}
            <footer class="relative z-10 px-12 py-12 flex flex-col md:flex-row items-center justify-between border-t border-supabase-border/30 bg-supabase-dark/50">
                <div class="flex items-center space-x-3 mb-6 md:mb-0">
                    <div class="w-8 h-8 bg-supabase-accent rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-supabase-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <span class="text-xl font-black text-white tracking-tighter">Cek<span class="text-supabase-accent">bayar</span></span>
                </div>
                <p class="text-[9px] font-black text-supabase-muted uppercase tracking-widest">
                    {{ __('© 2026 Cekbayar Infrastructure. Tanpa penahanan dana. Semua hak cipta dilindungi.') }}
                </p>
                <div class="flex space-x-8 mt-6 md:mt-0">
                    <a href="{{ localeRoute('public.privacy') }}" class="text-[9px] font-black text-supabase-muted hover:text-white uppercase tracking-widest transition-colors">{{ __('Kebijakan Privasi') }}</a>
                    <a href="{{ localeRoute('public.terms') }}" class="text-[9px] font-black text-supabase-muted hover:text-white uppercase tracking-widest transition-colors">{{ __('Syarat & Ketentuan') }}</a>
                </div>
            </footer>
        </div>
    </body>
</html>
