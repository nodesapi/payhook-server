<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ __('Harga | Cekbayar Premium') }}</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-supabase-dark text-slate-300 font-sans antialiased">
        <div class="relative min-h-screen flex flex-col overflow-hidden">
            {{-- Background Ornament --}}
            <div class="absolute inset-0 z-0">
                <div class="absolute top-0 right-1/4 w-[500px] h-[500px] bg-supabase-accent/5 blur-[120px] rounded-full"></div>
                <div class="absolute inset-0 opacity-[0.03] bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
            </div>

            <x-public-nav />

            {{-- Pricing Hero --}}
            <main class="relative z-10 flex-1 px-6 py-24">
                <div class="max-w-7xl mx-auto text-center mb-24">
                    <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight leading-tight mb-8">
                        {{ __('Pilih Kapasitas') }} <br><span class="text-supabase-accent">{{ __('Payload Anda') }}</span>.
                    </h1>
                    <p class="max-w-2xl mx-auto text-base md:text-lg text-supabase-muted font-bold tracking-tight leading-relaxed">
                        {{ __('Pilihan paket harga transparan sesuai kebutuhan bisnis Anda. Mulai dari developer independen hingga infrastruktur enterprise skala besar.') }}
                    </p>
                </div>

                {{-- Pricing Grid --}}
                <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($plans as $plan)
                        <div class="bg-supabase-surface border border-supabase-border rounded-[40px] p-10 flex flex-col relative overflow-hidden group hover:border-supabase-accent transition-all duration-500 {{ $loop->index === 1 ? 'scale-105 shadow-2xl shadow-supabase-accent/10 border-supabase-accent/50 z-10' : '' }}">
                            @if($loop->index === 1)
                                <div class="absolute top-8 right-8">
                                    <span class="px-3 py-1 bg-supabase-accent text-supabase-dark text-[8px] font-black uppercase tracking-widest rounded-full">{{ __('Paling Populer') }}</span>
                                </div>
                            @endif

                            <div class="mb-10">
                                <h3 class="text-xs font-black text-supabase-muted tracking-[0.3em] mb-4">{{ $plan->name }}</h3>
                                <div class="flex items-baseline space-x-2">
                                    <span class="text-3xl md:text-4xl font-black text-white tracking-tighter">Rp {{ number_format($plan->price, 0, ',', '.') }}</span>
                                    <span class="text-xs font-bold text-supabase-muted uppercase tracking-widest">/ {{ $plan->duration_days }} {{ __('Hari') }}</span>
                                </div>
                            </div>

                            <div class="space-y-6 mb-12 flex-1">
                                <div class="flex items-center space-x-4">
                                    <div class="w-5 h-5 bg-supabase-accent/10 text-supabase-accent rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <p class="text-[10px] font-black text-white tracking-widest">{{ __('Hingga') }} {{ $plan->max_channels }} {{ __('Node Aktif') }}</p>
                                </div>
                                
                                @php
                                    $defaultFeatures = [
                                        'Konfirmasi Otomatis',
                                        'Tanda Tangan Webhook HMAC',
                                        'Relay Notifikasi Android 24/7',
                                        'Log Audit Transaksi'
                                    ];
                                    $features = $plan->features ?? $defaultFeatures;
                                @endphp
                                
                                @foreach($features as $feature)
                                    <div class="flex items-center space-x-4">
                                        <div class="w-5 h-5 bg-white/5 text-supabase-muted rounded-full flex items-center justify-center flex-shrink-0">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <p class="text-[10px] font-bold text-supabase-muted tracking-widest">{{ __($feature) }}</p>
                                    </div>
                                @endforeach
                            </div>

                            <a href="{{ route('register', ['plan' => $plan->slug]) }}" 
                               class="w-full py-4 text-center text-[10px] font-black tracking-[0.2em] rounded-2xl transition-all {{ $loop->index === 1 ? 'bg-supabase-accent text-supabase-dark shadow-lg shadow-supabase-accent/20 hover:scale-[1.02]' : 'bg-supabase-input text-white hover:bg-supabase-border' }}">
                                {{ __('Mulai Hubungkan') }}
                            </a>
                        </div>
                    @endforeach
                </div>

                {{-- FAQ Section --}}
                <div class="max-w-3xl mx-auto mt-48 text-center">
                    <h2 class="text-2xl font-black text-white tracking-tighter mb-12">{{ __('Tanya Jawab Protokol') }}</h2>
                    <div class="space-y-8 text-left">
                        <div>
                            <h4 class="text-xs font-black text-white uppercase tracking-widest mb-2">{{ __('Bagaimana cara kerja sinkronisasi Cekbayar?') }}</h4>
                            <p class="text-[11px] text-supabase-muted font-bold tracking-tight leading-relaxed">
                                {{ __('Cekbayar mendeteksi push notifikasi dari aplikasi perbankan atau dompet digital Anda di perangkat Android khusus, kemudian mengirimkan payload data tersebut ke URL webhook server Anda secara real-time.') }}
                            </p>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-white uppercase tracking-widest mb-2">{{ __('Apakah dana saya aman?') }}</h4>
                            <p class="text-[11px] text-supabase-muted font-bold tracking-tight leading-relaxed">
                                {{ __('Ya, 100% aman. Cekbayar tidak pernah menyentuh, memotong, atau menyimpan dana Anda. Kami hanya menyinkronkan data notifikasi transaksi. Dana Anda tetap berada di rekening bank atau e-wallet pribadi Anda setiap saat.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </main>

            {{-- Footer --}}
            <footer class="relative z-10 px-12 py-12 flex flex-col md:flex-row items-center justify-between border-t border-supabase-border/30 bg-supabase-dark/50 mt-24">
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
