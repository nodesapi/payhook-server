<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('Kebijakan Privasi | Cekbayar') }}</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-supabase-dark text-slate-300 font-sans antialiased">
        <div class="relative min-h-screen flex flex-col overflow-hidden">
            {{-- Background Ornament --}}
            <div class="absolute inset-0 z-0">
                <div class="absolute top-0 right-1/4 w-[500px] h-[500px] bg-blue-500/5 blur-[120px] rounded-full"></div>
                <div class="absolute inset-0 opacity-[0.03] bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
            </div>

            <x-public-nav />

            <main class="relative z-10 flex-1 px-6 py-24">
                <div class="max-w-4xl mx-auto bg-supabase-surface border border-supabase-border rounded-[40px] p-8 md:p-16 shadow-2xl">
                    <h1 class="text-4xl md:text-5xl font-bold text-white tracking-normal mb-8">
                        {{ __('Kebijakan Privasi') }}
                    </h1>
                    
                    <p class="text-xs text-supabase-muted uppercase tracking-wider font-bold mb-8 border-b border-supabase-border pb-4">
                        {{ __('Terakhir diperbarui: 25 Mei 2026') }}
                    </p>

                    <div class="space-y-8 text-sm text-slate-300 font-medium leading-relaxed">
                        <section class="space-y-4">
                            <h2 class="text-lg font-bold text-white uppercase tracking-wider">{{ __('1. Komitmen Privasi Cekbayar') }}</h2>
                            <p>{{ __('Kami di Cekbayar sangat menghargai privasi data keuangan Anda. Model bisnis kami didasarkan pada penyediaan layanan jembatan data terenkripsi langsung dari HP Anda ke server Anda. Kami tidak memiliki akses atas dana Anda dan tidak menyimpan data kredensial finansial sensitif.') }}</p>
                        </section>

                        <section class="space-y-4">
                            <h2 class="text-lg font-bold text-white uppercase tracking-wider">{{ __('2. Data yang Diproses') }}</h2>
                            <p>{{ __('Untuk menjalankan fungsinya, aplikasi Android Cekbayar membaca isi notifikasi sistem yang dikirimkan oleh aplikasi perbankan atau dompet digital pilihan Anda. Data ini dibaca secara lokal di perangkat Anda, diekstrak isinya (nominal, nama pengirim, nama bank), kemudian dikirimkan secara instan ke URL webhook yang Anda daftarkan.') }}</p>
                        </section>

                        <section class="space-y-4">
                            <h2 class="text-lg font-bold text-white uppercase tracking-wider">{{ __('3. Penyimpanan Data') }}</h2>
                            <p>{{ __('Cekbayar hanya menyimpan log pengiriman callback webhook (seperti status kode respon, tanggal pengiriman, dan nama bank) untuk keperluan pelacakan masalah (debugging) di sisi pengguna. Kami tidak menyimpan salinan raw payload data di luar masa retensi logs debugging.') }}</p>
                        </section>

                        <section class="space-y-4">
                            <h2 class="text-lg font-bold text-white uppercase tracking-wider">{{ __('4. Keamanan Informasi') }}</h2>
                            <p>{{ __('Semua transmisi data antara aplikasi Android, server Cekbayar, dan server endpoint Anda wajib menggunakan protokol HTTPS terenkripsi. Kami juga menyertakan penandatanganan payload digital (digital signature) menggunakan SHA-256 HMAC untuk memastikan integritas data yang dikirimkan.') }}</p>
                        </section>

                        <section class="space-y-4">
                            <h2 class="text-lg font-bold text-white uppercase tracking-wider">{{ __('5. Perubahan Kebijakan') }}</h2>
                            <p>{{ __('Kami dapat memperbarui Kebijakan Privasi ini sewaktu-waktu. Setiap perubahan akan diberitahukan dengan memperbarui tanggal "Terakhir diperbarui" di bagian atas halaman ini. Anda disarankan untuk meninjau Kebijakan Privasi ini secara berkala.') }}</p>
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
                    <span class="text-xl font-bold text-white tracking-normal">Cek<span class="text-supabase-accent">bayar</span></span>
                </div>
                <p class="text-[9px] font-bold text-supabase-muted uppercase tracking-wider">
                    {{ __('© 2026 Cekbayar Infrastructure. Tanpa penahanan dana. Semua hak cipta dilindungi.') }}
                </p>
                <div class="flex space-x-8 mt-6 md:mt-0">
                    <a href="{{ localeRoute('public.privacy') }}" class="text-[9px] font-bold text-supabase-muted hover:text-white uppercase tracking-wider transition-colors">{{ __('Kebijakan Privasi') }}</a>
                    <a href="{{ localeRoute('public.terms') }}" class="text-[9px] font-bold text-supabase-muted hover:text-white uppercase tracking-wider transition-colors">{{ __('Syarat & Ketentuan') }}</a>
                </div>
            </footer>
        </div>
    </body>
</html>
