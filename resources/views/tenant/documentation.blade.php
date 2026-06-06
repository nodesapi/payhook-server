@extends('layouts.tenant')

@section('title', 'Developer Portal')

@section('content')

<!-- Page Header -->
<div class="mb-12">
    <h1 class="text-4xl font-bold text-white tracking-normal uppercase">Developer <span class="text-supabase-accent">Portal</span></h1>
    <p class="text-supabase-muted mt-2 font-mono text-xs tracking-wider">v1.0.0-STABLE | API INTEGRATION PROTOCOLS</p>
</div>

<!-- Architecture Overview -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
    @foreach([
        ['Authentication', 'Bearer Token', 'All requests must include a secure Authorization header with your secret API key.', 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
        ['Security', 'HMAC SHA256', 'Payload integrity is guaranteed via X-Webhook-Signature verification.', 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
        ['Compliance', 'RESTful API', 'Standardized JSON payloads for seamless cross-platform synchronization.', 'M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z']
    ] as [$title, $subtitle, $desc, $path])
    <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 relative overflow-hidden group">
        <div class="p-3 bg-supabase-accent/10 text-supabase-accent w-fit rounded-xl mb-6">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $path }}"></path></svg>
        </div>
        <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-2">{{ $title }}</h3>
        <p class="text-xl font-bold text-supabase-accent mb-4">{{ $subtitle }}</p>
        <p class="text-[10px] text-supabase-muted font-bold uppercase leading-relaxed tracking-normal">{{ $desc }}</p>
    </div>
    @endforeach
</div>

<!-- Integration Guide -->
<div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 mb-12 shadow-2xl overflow-hidden relative">
    <div class="absolute top-0 right-0 p-8 opacity-5">
        <svg class="w-48 h-48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
    </div>
    <div class="relative z-10">
        <h2 class="text-xs font-bold text-white uppercase tracking-wider mb-8 flex items-center">
            <span class="w-1.5 h-1.5 bg-supabase-accent rounded-full mr-3"></span>
            Quick Integration Sequence
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach([
                ['01', 'Configure', 'Define your webhook endpoint in the secure settings module.'],
                ['02', 'Bridge', 'Install and authorize the Android notification capture node.'],
                ['03', 'Simulate', 'Utilize the API Playground to transmit diagnostic payloads.'],
                ['04', 'Deploy', 'Finalize your logic and transition to production traffic.']
            ] as [$step, $label, $text])
            <div class="space-y-4">
                <span class="text-4xl font-bold text-supabase-accent/20 font-mono">{{ $step }}</span>
                <h4 class="text-[10px] font-bold text-white uppercase tracking-wider">{{ $label }}</h4>
                <p class="text-[10px] text-supabase-muted font-bold uppercase tracking-normal leading-relaxed">{{ $text }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Android Relay Device Guide -->
<div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 mb-12 shadow-2xl overflow-hidden relative">
    <div class="absolute top-0 right-0 p-8 opacity-5">
        <svg class="w-48 h-48" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
    </div>
    <div class="relative z-10">
        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between mb-8">
            <div>
                <h2 class="text-xs font-bold text-white uppercase tracking-wider flex items-center">
                    <span class="w-1.5 h-1.5 bg-supabase-accent rounded-full mr-3"></span>
                    Multi-HP Android Relay
                </h2>
                <p class="mt-3 max-w-3xl text-[10px] text-supabase-muted font-bold uppercase tracking-normal leading-relaxed">
                    Jika memakai beberapa HP sebagai relay notifikasi, ulangi setup berikut di setiap HP. Developer Mode tidak wajib untuk membaca notifikasi, tetapi disarankan untuk HP dedicated agar proses testing, instalasi, dan maintenance lebih stabil.
                </p>
            </div>
            <div class="rounded-xl border border-supabase-accent/20 bg-supabase-accent/10 px-4 py-3 text-supabase-accent">
                <p class="text-[9px] font-bold uppercase tracking-wider">Per HP Relay</p>
                <p class="mt-1 text-xs font-bold">1 HP = 1 listener node</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="rounded-2xl border border-supabase-border bg-supabase-dark/50 p-6">
                <p class="text-[10px] font-bold text-white uppercase tracking-wider mb-4">Aktifkan Developer Mode</p>
                <ol class="space-y-3 text-[10px] font-bold text-supabase-muted uppercase tracking-normal leading-relaxed">
                    <li><span class="text-supabase-accent">1.</span> Buka Settings di HP Android.</li>
                    <li><span class="text-supabase-accent">2.</span> Masuk ke About phone / Tentang ponsel.</li>
                    <li><span class="text-supabase-accent">3.</span> Tap Build number / Nomor bentukan sebanyak 7 kali.</li>
                    <li><span class="text-supabase-accent">4.</span> Masukkan PIN/pola jika diminta.</li>
                    <li><span class="text-supabase-accent">5.</span> Buka System / Additional settings, lalu masuk Developer options.</li>
                </ol>
            </div>

            <div class="rounded-2xl border border-supabase-border bg-supabase-dark/50 p-6">
                <p class="text-[10px] font-bold text-white uppercase tracking-wider mb-4">Setting yang Disarankan</p>
                <div class="space-y-3">
                    @foreach([
                        ['Stay awake', 'ON saat HP dipakai sebagai node dan selalu tersambung charger.'],
                        ['USB debugging', 'Opsional, aktifkan hanya saat install APK via kabel atau troubleshooting.'],
                        ['Background process limit', 'Biarkan Standard limit. Jangan pilih No background processes.'],
                        ['Don\'t keep activities', 'Pastikan OFF supaya aplikasi tidak dipaksa tutup.']
                    ] as [$label, $desc])
                        <div class="rounded-xl border border-supabase-border/60 bg-supabase-input/40 p-4">
                            <p class="text-[10px] font-bold text-white uppercase tracking-wider">{{ $label }}</p>
                            <p class="mt-1 text-[9px] font-bold text-supabase-muted uppercase tracking-normal leading-relaxed">{{ $desc }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl border border-supabase-border bg-supabase-dark/50 p-6">
                <p class="text-[10px] font-bold text-white uppercase tracking-wider mb-4">Checklist Tiap HP</p>
                <ul class="space-y-3 text-[10px] font-bold text-supabase-muted uppercase tracking-normal leading-relaxed">
                    <li class="flex gap-3"><span class="text-supabase-accent">01</span><span>Install APK Cekbayar dan login ke tenant yang benar.</span></li>
                    <li class="flex gap-3"><span class="text-supabase-accent">02</span><span>Aktifkan Notification Access untuk Cekbayar.</span></li>
                    <li class="flex gap-3"><span class="text-supabase-accent">03</span><span>Pilih aplikasi bank/e-wallet yang memang ada di HP tersebut.</span></li>
                    <li class="flex gap-3"><span class="text-supabase-accent">04</span><span>Matikan battery restriction dan aktifkan autostart jika tersedia.</span></li>
                    <li class="flex gap-3"><span class="text-supabase-accent">05</span><span>Pastikan aplikasi bank/e-wallet menampilkan notifikasi transaksi masuk.</span></li>
                    <li class="flex gap-3"><span class="text-supabase-accent">06</span><span>Uji satu transaksi kecil lalu cek Activity Logs dan webhook response.</span></li>
                </ul>
            </div>
        </div>

        <div class="mt-6 rounded-2xl border border-amber-500/20 bg-amber-500/10 p-5">
            <p class="text-[10px] font-bold text-amber-400 uppercase tracking-wider mb-2">Catatan Penting</p>
            <p class="text-[10px] font-bold text-supabase-muted uppercase tracking-normal leading-relaxed">
                Developer Mode harus diaktifkan satu per satu di setiap HP relay. Untuk Xiaomi, OPPO, Vivo, Realme, dan beberapa Samsung, tetap lakukan pengaturan Battery: No restriction / Unrestricted, Autostart: ON, dan kunci aplikasi di Recent Apps jika tersedia. Developer Mode saja tidak cukup jika sistem masih mematikan aplikasi background.
            </p>
        </div>
    </div>
</div>

<!-- Payload Diagnostics -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
    <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 shadow-2xl">
        <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-6">Protocol Headers</h3>
        <div class="bg-supabase-dark border border-supabase-border rounded-2xl p-6 font-mono text-[10px] text-supabase-accent space-y-2 uppercase">
            <p><span class="text-supabase-muted">Authorization:</span> Bearer YOUR_API_KEY</p>
            <p><span class="text-supabase-muted">X-Webhook-Signature:</span> HMAC_SHA256(RAW_JSON, SECRET)</p>
            <p><span class="text-supabase-muted">Content-Type:</span> application/json</p>
            <p><span class="text-supabase-muted">Accept:</span> application/json</p>
        </div>
    </div>
    <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 shadow-2xl">
        <h3 class="text-xs font-bold text-white uppercase tracking-wider mb-6">Callback Schema</h3>
        <div class="bg-supabase-dark border border-supabase-border rounded-2xl p-6 font-mono text-[10px] text-supabase-accent overflow-x-auto">
<pre>{
  "event": "payment.success",
  "transaction_id": "PHK-ABC123",
  "external_id": "ORDER-99",
  "magnitude": 50000,
  "net_settlement": 49500,
  "fee": 500,
  "status": "CONFIRMED",
  "timestamp": "2026-04-02 14:30:00"
}</pre>
        </div>
    </div>
</div>

<!-- Support -->
<div class="p-8 bg-supabase-accent/10 border border-supabase-accent/20 rounded-3xl flex flex-col md:flex-row items-center justify-between gap-6">
    <div class="flex items-center space-x-6">
        <div class="w-12 h-12 bg-supabase-accent text-supabase-dark rounded-xl flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
        </div>
        <div>
            <h4 class="text-xs font-bold text-white uppercase tracking-wider leading-none">Developer Support Node</h4>
            <p class="text-[10px] text-supabase-muted font-bold uppercase tracking-normal mt-1">Need help with complex integration scenarios? Access the community or read full docs.</p>
        </div>
    </div>
    <a href="{{ route('tenant.api-playground') }}" class="sb-button-primary !w-auto !py-3 !px-12">Initialize Sandbox</a>
</div>

@endsection
