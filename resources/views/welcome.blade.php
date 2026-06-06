<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('Cekbayar | Premium Payment Bridge') }}</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-supabase-dark text-slate-300 font-sans antialiased">
        <div class="relative min-h-screen flex flex-col overflow-hidden">
            {{-- Background Ornament --}}
            <div class="absolute inset-0 z-0">
                <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-supabase-accent/5 blur-[120px] rounded-full animate-pulse"></div>
                <div class="absolute bottom-0 right-1/4 w-[500px] h-[500px] bg-blue-500/5 blur-[120px] rounded-full animate-pulse"></div>
                <div class="absolute inset-0 opacity-[0.03] bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
            </div>

            <x-public-nav />

            <main class="relative z-10 flex-1">
                {{-- Hero Section --}}
                <section class="max-w-7xl mx-auto px-6 pt-16 pb-20 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                    {{-- Hero Left --}}
                    <div class="lg:col-span-7 space-y-6 text-left">
                        <div class="inline-flex items-center space-x-2 px-3 py-1 bg-supabase-accent/10 border border-supabase-accent/20 rounded-full mb-2">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-supabase-accent opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-supabase-accent"></span>
                            </span>
                            <span class="text-[10px] font-bold text-supabase-accent uppercase tracking-wider">{{ __('Gateway Pembayaran Instan Rp 0 Fee') }}</span>
                        </div>

                        <h1 class="text-3xl md:text-5xl font-bold text-white tracking-normal leading-tight">
                            {{ __('Konfirmasi Pembayaran') }} <br>
                            <span class="text-supabase-accent">{{ __('Otomatis dari HP Anda') }}</span>.
                        </h1>
                        
                        <p class="max-w-xl text-base md:text-lg text-slate-400 font-medium tracking-normal leading-relaxed">
                            {{ __('Membaca notifikasi transfer masuk dari bank & e-wallet di HP Android Anda secara real-time, lalu meneruskannya sebagai JSON webhook langsung ke server Anda. Tanpa potongan biaya, tanpa perantara, 100% langsung masuk ke rekening pribadi Anda.') }}
                        </p>

                        <div class="flex flex-col sm:flex-row items-center gap-4 pt-4">
                            <a href="{{ route('register') }}" class="w-full sm:w-auto text-center px-10 py-4 bg-supabase-accent text-supabase-dark font-bold text-xs uppercase tracking-wider rounded-2xl hover:scale-105 transition-all shadow-xl shadow-supabase-accent/15">
                                {{ __('Mulai Secara Gratis') }}
                            </a>
                            <a href="#how-it-works" class="w-full sm:w-auto text-center px-10 py-4 border border-supabase-border rounded-2xl text-white font-bold text-xs uppercase tracking-wider hover:bg-white/5 transition-all">
                                {{ __('Lihat Protokol') }}
                            </a>
                        </div>

                        {{-- Stats --}}
                        <div class="pt-12 grid grid-cols-2 md:grid-cols-4 gap-6 border-t border-supabase-border/40 max-w-2xl">
                            @foreach([
                                ['Rp 0', 'Biaya Transaksi'],
                                ['< 100ms', 'Latensi Webhook'],
                                ['AES-256', 'Vault Terenkripsi'],
                                ['99.9%', 'Uptime Sinkronisasi']
                            ] as [$val, $label])
                                <div>
                                    <p class="text-2xl font-bold text-white tracking-normal mb-1">{{ __($val) }}</p>
                                    <p class="text-[9px] font-bold text-supabase-muted uppercase tracking-wider opacity-60">{{ __($label) }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Hero Right (Mockup Alerts Container) --}}
                    <div class="lg:col-span-5 relative">
                        <div class="absolute inset-0 bg-supabase-accent/10 blur-[100px] rounded-full"></div>
                        <div class="relative bg-supabase-surface border border-supabase-border rounded-[40px] p-6 space-y-4 shadow-2xl">
                            
                            {{-- Alert 1: BCA --}}
                            <div class="bg-supabase-dark border border-supabase-border rounded-[24px] p-4 flex items-center justify-between hover:border-supabase-accent/30 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-blue-500/10 text-blue-400 rounded-xl flex items-center justify-center font-bold text-xs">
                                        BCA
                                    </div>
                                    <div>
                                        <h4 class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">{{ __('Uang Transfer Masuk') }}</h4>
                                        <p class="text-sm font-bold text-white">Rp 150.472</p>
                                        <p class="text-[9px] text-supabase-muted font-bold">Pengirim: DARMA RIDUAN</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 bg-green-500/10 text-green-400 text-[8px] font-bold uppercase tracking-wider rounded-full">
                                    {{ __('Berhasil') }}
                                </span>
                            </div>

                            {{-- Alert 2: DANA --}}
                            <div class="bg-supabase-dark border border-supabase-border rounded-[24px] p-4 flex items-center justify-between hover:border-supabase-accent/30 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-supabase-accent/10 text-supabase-accent rounded-xl flex items-center justify-center font-bold text-xs">
                                        DANA
                                    </div>
                                    <div>
                                        <h4 class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">{{ __('Pembayaran QRIS') }}</h4>
                                        <p class="text-sm font-bold text-white">Rp 300.000</p>
                                        <p class="text-[9px] text-supabase-muted font-bold">Pengirim: AMINAH</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 bg-green-500/10 text-green-400 text-[8px] font-bold uppercase tracking-wider rounded-full">
                                    {{ __('Berhasil') }}
                                </span>
                            </div>

                            {{-- Alert 3: Webhook Relay --}}
                            <div class="bg-supabase-dark border border-supabase-border rounded-[24px] p-4 flex items-center justify-between hover:border-supabase-accent/30 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 bg-purple-500/10 text-purple-400 rounded-xl flex items-center justify-center font-bold">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">{{ __('Callback Webhook') }}</h4>
                                        <p class="text-[10px] font-mono text-purple-400">event: payment.confirmed</p>
                                        <p class="text-[9px] text-supabase-muted font-bold">{{ __('Latensi') }}: 42ms</p>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 bg-green-500/10 text-green-400 text-[8px] font-bold uppercase tracking-wider rounded-full">
                                    {{ __('HMAC OK') }}
                                </span>
                            </div>

                        </div>
                    </div>
                </section>

                {{-- Features Section --}}
                <section id="features" class="py-32 px-6 lg:px-12 bg-supabase-dark/50 relative border-t border-supabase-border/30">
                    <div class="max-w-7xl mx-auto">
                        <div class="mb-16 text-center">
                            <h2 class="text-xs font-bold text-supabase-accent uppercase tracking-[0.4em] mb-4">{{ __('Fitur Unggulan') }}</h2>
                            <h3 class="text-3xl md:text-4xl font-bold text-white tracking-normal">{{ __('Dirancang untuk') }} <span class="text-supabase-accent">{{ __('Keandalan') }}</span>.</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            @foreach([
                                [
                                    'title' => 'Tanpa Potongan Biaya (Rp 0 Fee)',
                                    'desc' => '100% uang pembayaran dari pelanggan langsung masuk ke rekening bank atau e-wallet Anda tanpa potongan sepeser pun.',
                                    'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'
                                ],
                                [
                                    'title' => 'Multi-Bank & E-Wallet Sync',
                                    'desc' => 'Hubungkan beberapa akun perbankan dan dompet digital sekaligus (BCA, Mandiri, DANA, GoPay, OVO, Superbank) untuk dipantau secara terpusat.',
                                    'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'
                                ],
                                [
                                    'title' => 'Konfirmasi Instan (< 100ms)',
                                    'desc' => 'Mengirimkan payload callback JSON ke server Anda dalam hitungan milidetik setelah dana masuk terdeteksi di HP Anda.',
                                    'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'
                                ],
                                [
                                    'title' => 'Keamanan Kriptografi',
                                    'desc' => 'Transmisi aman terlindungi token otentikasi Bearer, API Key, dan HTTPS terenkripsi untuk mencegah modifikasi data.',
                                    'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
                                ],
                                [
                                    'title' => 'Bebas SDK (Standard JSON)',
                                    'desc' => 'Dapat langsung diintegrasikan dengan bahasa pemrograman apa pun yang mendukung HTTP POST endpoint (Laravel, PHP, Node.js, Python, Go).',
                                    'icon' => 'M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'
                                ],
                                [
                                    'title' => 'Log Aktivitas Detail',
                                    'desc' => 'Lihat riwayat notifikasi, payload data transaksi, status respons server tujuan, dan penanganan error secara real-time.',
                                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'
                                ]
                            ] as $feature)
                            <div class="bg-supabase-surface border border-supabase-border rounded-[32px] p-10 hover:border-supabase-accent/30 transition-all group">
                                <div class="w-14 h-14 bg-supabase-accent/10 text-supabase-accent rounded-2xl flex items-center justify-center mb-8 group-hover:scale-110 transition-transform">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}"></path></svg>
                                </div>
                                <h4 class="text-xl font-bold text-white tracking-normal mb-4">{{ __($feature['title']) }}</h4>
                                <p class="text-sm text-slate-400 font-medium leading-relaxed">{{ __($feature['desc']) }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </section>

                {{-- How it works --}}
                <section id="how-it-works" class="py-32 px-6 lg:px-12 relative overflow-hidden border-t border-supabase-border/30">
                    <div class="max-w-7xl mx-auto">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-24 items-center">
                            
                            {{-- How Left --}}
                            <div class="space-y-12">
                                <div>
                                    <h2 class="text-xs font-bold text-supabase-accent uppercase tracking-[0.4em] mb-4">{{ __('Protokol') }}</h2>
                                    <h3 class="text-2xl md:text-4xl font-bold text-white tracking-normal leading-none">
                                        {{ __('Tiga Langkah Mudah') }}.
                                    </h3>
                                </div>
                                
                                <div class="space-y-8">
                                    @foreach([
                                        ['01', 'Instalasi & Hubungkan', 'Pasang aplikasi Android Cekbayar di HP Anda, kemudian berikan izin akses notifikasi agar aplikasi dapat berjalan di latar belakang.'],
                                        ['02', 'Konfigurasi Webhook', 'Masukkan URL webhook server Anda ke dalam aplikasi Cekbayar beserta token otentikasi (Bearer/API Key) untuk keamanan.'],
                                        ['03', 'Mulai Sinkronisasi', 'Setiap kali ada transfer masuk di HP Anda, Cekbayar akan mendeteksinya dan mengirim data JSON webhook langsung ke server Anda secara instan.']
                                    ] as [$step, $title, $desc])
                                    <div class="flex items-start space-x-6 group">
                                        <span class="text-2xl font-bold text-supabase-accent/30 font-mono group-hover:text-supabase-accent transition-colors">{{ $step }}</span>
                                        <div>
                                            <h5 class="text-sm font-bold text-white tracking-wide mb-1.5">{{ __($title) }}</h5>
                                            <p class="text-xs md:text-sm text-slate-400 font-medium leading-relaxed">{{ __($desc) }}</p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- How Right (Payload Code View) --}}
                            <div class="relative">
                                <div class="absolute inset-0 bg-supabase-accent/10 blur-[100px] rounded-full"></div>
                                <div class="relative bg-supabase-surface border border-supabase-border rounded-[40px] p-4 shadow-2xl">
                                    <div class="bg-supabase-dark border border-supabase-border rounded-[32px] p-8 font-mono text-[10px] text-supabase-accent overflow-hidden">
                                        <div class="flex items-center space-x-2 mb-6 border-b border-supabase-border pb-4">
                                            <div class="w-3 h-3 rounded-full bg-red-500/20"></div>
                                            <div class="w-3 h-3 rounded-full bg-amber-500/20"></div>
                                            <div class="w-3 h-3 rounded-full bg-green-500/20"></div>
                                            <span class="ml-4 text-supabase-muted opacity-50 uppercase tracking-wider">webhook_payload.json</span>
                                        </div>
<pre class="animate-pulse">
{
  "event": "payment.confirmed",
  "node_id": "CB-RELAY-BCA",
  "payload": {
    "amount": 150472,
    "source": "BCA_MOBILE",
    "customer": "DARMA RIDUAN",
    "reference": "INV-2026-001"
  },
  "signature": "hmac_sha256_verified",
  "latency": "42ms"
}
</pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- CTA Section --}}
                <section class="py-32 px-6 border-t border-supabase-border/30">
                    <div class="max-w-5xl mx-auto bg-gradient-to-br from-supabase-accent to-amber-600 rounded-[48px] p-12 md:p-24 text-center relative overflow-hidden shadow-2xl shadow-supabase-accent/20">
                        <div class="absolute inset-0 opacity-10 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
                        <div class="relative z-10">
                            <h2 class="text-2xl md:text-4xl font-bold text-supabase-dark tracking-normal leading-none mb-8">
                                {{ __('Siap Menghubungkan Pembayaran Anda?') }}
                            </h2>
                            <p class="max-w-xl mx-auto text-supabase-dark/80 font-semibold tracking-normal mb-12">
                                {{ __('Mulai otomatisasi pencocokan invoice pembayaran secara gratis sekarang juga tanpa biaya komisi.') }}
                            </p>
                            <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                                <a href="{{ route('register') }}" class="w-full sm:w-auto px-12 py-5 bg-supabase-dark text-white rounded-2xl font-bold text-sm tracking-[0.3em] hover:scale-105 transition-transform shadow-2xl">
                                    {{ __('Mulai Gratis') }}
                                </a>
                                <a href="{{ localeRoute('public.pricing') }}" class="w-full sm:w-auto px-12 py-5 border-2 border-supabase-dark/20 text-supabase-dark rounded-2xl font-bold text-sm tracking-[0.3em] hover:bg-supabase-dark/5 transition-colors">
                                    {{ __('Lihat Harga') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
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
