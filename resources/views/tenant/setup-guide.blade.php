@extends('layouts.admin')

@section('page-title', 'Setup Guide')

@section('content')
<div class="w-full mx-auto">
    <div class="bg-white rounded-xl border border-slate-200 p-8">
        <h1 class="text-3xl font-bold text-slate-900 mb-2">Cekbayar Setup Guide</h1>
        <p class="text-slate-600 mb-8">Panduan lengkap untuk mengintegrasikan Cekbayar payment gateway dengan sistem Anda.</p>

        <!-- Step 1: Download APK -->
        <div class="mb-8 pb-8 border-b border-slate-200">
            <div class="flex items-start">
                <div class="flex-shrink-0 w-10 h-10 bg-indigo-600 text-white rounded-lg flex items-center justify-center font-bold">
                    1
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Download Cekbayar APK</h3>
                    <p class="text-slate-600 mb-4">Download aplikasi Cekbayar untuk Android yang akan membaca notifikasi pembayaran secara otomatis.</p>
                    
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 2a1 1 0 000 2h2a1 1 0 100-2H8z"></path>
                                    <path d="M3 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v6h-4.586l1.293-1.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L10.414 13H15v3a2 2 0 01-2 2H5a2 2 0 01-2-2V5zM15 11h2a1 1 0 110 2h-2v-2z"></path>
                                </svg>
                                <div class="ml-3">
                                    <p class="font-medium text-slate-900">Cekbayar.apk</p>
                                    <p class="text-sm text-slate-500">Latest version</p>
                                </div>
                            </div>
                            <a href="{{ route('tenant.download-apk') }}" class="px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700">
                                Download
                            </a>
                        </div>
                    </div>

                    <div class="mt-4 bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <p class="text-sm text-amber-800">
                            <strong>⚠️ Persyaratan:</strong>
                        </p>
                        <ul class="mt-2 text-sm text-amber-800 list-disc list-inside space-y-1">
                            <li>Android 7.0 (Nougat) atau lebih baru</li>
                            <li>HP harus connect ke WiFi yang sama dengan server</li>
                            <li>Minimal 10MB storage tersedia</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Install APK -->
        <div class="mb-8 pb-8 border-b border-slate-200">
            <div class="flex items-start">
                <div class="flex-shrink-0 w-10 h-10 bg-indigo-600 text-white rounded-lg flex items-center justify-center font-bold">
                    2
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Install Aplikasi</h3>
                    <p class="text-slate-600 mb-4">Ikuti langkah-langkah berikut untuk menginstall Cekbayar di HP Android:</p>
                    
                    <ol class="space-y-3 text-slate-700">
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center text-xs font-medium mr-3">1</span>
                            <span>Transfer file <code class="px-2 py-0.5 bg-slate-100 rounded text-sm">Cekbayar.apk</code> ke HP Android (via USB, Bluetooth, atau download langsung)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center text-xs font-medium mr-3">2</span>
                            <span>Buka file APK → klik "Install"</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center text-xs font-medium mr-3">3</span>
                            <span>Jika muncul warning "Install blocked" → buka <strong>Settings → Security → Allow from unknown sources</strong> → aktifkan untuk Chrome/File Manager</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center text-xs font-medium mr-3">4</span>
                            <span>Selesai! Buka aplikasi Cekbayar</span>
                        </li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Step 2A: Developer Mode for Multi Device -->
        <div class="mb-8 pb-8 border-b border-slate-200">
            <div class="flex items-start">
                <div class="flex-shrink-0 w-10 h-10 bg-amber-500 text-white rounded-lg flex items-center justify-center font-bold">
                    2A
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Developer Mode untuk Beberapa HP Relay</h3>
                    <p class="text-slate-600 mb-4">
                        Jika memakai beberapa HP sebagai node Cekbayar, lakukan checklist ini di setiap HP. Developer Mode tidak wajib untuk membaca notifikasi, tapi sangat membantu untuk HP dedicated yang dipakai 24/7, terutama saat install APK via kabel, testing, dan maintenance.
                    </p>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                            <p class="text-sm font-semibold text-slate-900 mb-3">Cara aktifkan Developer Mode:</p>
                            <ol class="text-sm text-slate-700 space-y-2 list-decimal list-inside">
                                <li>Buka <strong>Settings</strong> di HP Android</li>
                                <li>Masuk ke <strong>About phone / Tentang ponsel</strong></li>
                                <li>Tap <strong>Build number / Nomor bentukan</strong> sebanyak 7 kali</li>
                                <li>Masukkan PIN/pola jika diminta</li>
                                <li>Buka <strong>System / Additional settings</strong> lalu masuk <strong>Developer options</strong></li>
                            </ol>
                        </div>

                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                            <p class="text-sm font-semibold text-amber-900 mb-3">Setting yang disarankan:</p>
                            <ul class="text-sm text-amber-800 space-y-2 list-disc list-inside">
                                <li><strong>Stay awake:</strong> ON jika HP relay selalu tersambung charger</li>
                                <li><strong>USB debugging:</strong> opsional, hanya untuk install APK/troubleshooting via kabel</li>
                                <li><strong>Background process limit:</strong> biarkan Standard limit</li>
                                <li><strong>Don't keep activities:</strong> pastikan OFF</li>
                            </ul>
                        </div>
                    </div>

                    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800">
                            <strong>Checklist per HP:</strong> install Cekbayar, login tenant yang benar, aktifkan Notification Access, pilih app bank/e-wallet yang ada di HP tersebut, matikan battery restriction, aktifkan autostart jika tersedia, lalu uji satu transaksi kecil.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Grant Permissions -->
        <div class="mb-8 pb-8 border-b border-slate-200">
            <div class="flex items-start">
                <div class="flex-shrink-0 w-10 h-10 bg-indigo-600 text-white rounded-lg flex items-center justify-center font-bold">
                    3
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Grant Notification Permission</h3>
                    <p class="text-slate-600 mb-4"><strong>PENTING:</strong> Cekbayar memerlukan akses notifikasi untuk membaca pembayaran masuk.</p>
                    
                    <ol class="space-y-3 text-slate-700">
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center text-xs font-medium mr-3">1</span>
                            <span>Buka Cekbayar → popup "Notification Access" akan muncul otomatis</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center text-xs font-medium mr-3">2</span>
                            <span>Klik <strong>"Grant Permission"</strong></span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center text-xs font-medium mr-3">3</span>
                            <span>Cari <strong>"Cekbayar"</strong> di list → toggle <strong>ON</strong> (hijau)</span>
                        </li>
                        <li class="flex items-start">
                            <span class="flex-shrink-0 w-6 h-6 bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center text-xs font-medium mr-3">4</span>
                            <span>Confirm "Allow notification access" → kembali ke app</span>
                        </li>
                    </ol>

                    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800">
                            <strong>💡 Jika tidak muncul otomatis:</strong><br>
                            Settings → Apps → Special access → Notification access → Cekbayar → Toggle ON
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 4: Configure Webhook -->
        <div class="mb-8 pb-8 border-b border-slate-200">
            <div class="flex items-start">
                <div class="flex-shrink-0 w-10 h-10 bg-indigo-600 text-white rounded-lg flex items-center justify-center font-bold">
                    4
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Configure Webhook</h3>
                    <p class="text-slate-600 mb-4">Setup webhook endpoint untuk menerima notifikasi pembayaran.</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Webhook URL</label>
                            <code class="block px-3 py-2 bg-slate-50 border border-slate-200 rounded text-sm">{{ $tenant->webhook_url ?? 'http://your-server.com/api/webhook' }}</code>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Bearer Token</label>
                            <code class="block px-3 py-2 bg-slate-50 border border-slate-200 rounded text-sm font-mono break-all">{{ $tenant->getActiveApiKey() ?? 'your-api-key-here' }}</code>
                        </div>

                        <div class="bg-slate-50 border border-slate-200 rounded-lg p-4">
                            <p class="text-sm font-medium text-slate-900 mb-2">Cara input di Cekbayar:</p>
                            <ol class="text-sm text-slate-700 space-y-1 list-decimal list-inside">
                                <li>Buka tab <strong>"Webhooks"</strong></li>
                                <li>Klik <strong>"+ Add Webhook"</strong></li>
                                <li>Name: <strong>Server Test</strong></li>
                                <li>URL: paste URL di atas</li>
                                <li>Bearer Token: paste token di atas</li>
                                <li>Toggle <strong>ON</strong> (aktif)</li>
                                <li>Klik <strong>"Test Webhook"</strong> → harus 200 OK</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 5: Add Monitored Apps -->
        <div class="mb-8 pb-8 border-b border-slate-200">
            <div class="flex items-start">
                <div class="flex-shrink-0 w-10 h-10 bg-indigo-600 text-white rounded-lg flex items-center justify-center font-bold">
                    5
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Enable Payment Apps</h3>
                    <p class="text-slate-600 mb-4">Pilih aplikasi pembayaran yang akan dimonitor oleh Cekbayar.</p>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <div class="border border-slate-200 rounded-lg p-3 text-center">
                            <div class="text-2xl mb-1">💳</div>
                            <p class="text-sm font-medium text-slate-900">GoPay Merchant</p>
                        </div>
                        <div class="border border-slate-200 rounded-lg p-3 text-center">
                            <div class="text-2xl mb-1">💰</div>
                            <p class="text-sm font-medium text-slate-900">DANA Bisnis</p>
                        </div>
                        <div class="border border-slate-200 rounded-lg p-3 text-center">
                            <div class="text-2xl mb-1">🏦</div>
                            <p class="text-sm font-medium text-slate-900">BCA Mobile</p>
                        </div>
                        <div class="border border-slate-200 rounded-lg p-3 text-center">
                            <div class="text-2xl mb-1">🏧</div>
                            <p class="text-sm font-medium text-slate-900">Mandiri Mobile</p>
                        </div>
                        <div class="border border-slate-200 rounded-lg p-3 text-center">
                            <div class="text-2xl mb-1">💵</div>
                            <p class="text-sm font-medium text-slate-900">BRI Mobile</p>
                        </div>
                        <div class="border border-slate-200 rounded-lg p-3 text-center">
                            <div class="text-2xl mb-1">🎯</div>
                            <p class="text-sm font-medium text-slate-900">OVO/ShopeePay</p>
                        </div>
                    </div>

                    <div class="mt-4 bg-slate-50 border border-slate-200 rounded-lg p-4">
                        <p class="text-sm font-medium text-slate-900 mb-2">Cara enable di Cekbayar:</p>
                        <ol class="text-sm text-slate-700 space-y-1 list-decimal list-inside">
                            <li>Buka tab <strong>"Monitored Apps"</strong></li>
                            <li>Klik <strong>"+ Add App"</strong></li>
                            <li>Cari aplikasi (contoh: "GoPay Merchant")</li>
                            <li>Klik app → toggle <strong>ON</strong></li>
                            <li>Ulangi untuk app lain yang diperlukan</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 6: Test Payment -->
        <div class="mb-8">
            <div class="flex items-start">
                <div class="flex-shrink-0 w-10 h-10 bg-emerald-600 text-white rounded-lg flex items-center justify-center font-bold">
                    6
                </div>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">Test Payment Flow</h3>
                    <p class="text-slate-600 mb-4">Verifikasi semua setup dengan melakukan test payment.</p>
                    
                    <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4">
                        <p class="text-sm font-medium text-emerald-900 mb-3">✅ Testing Checklist:</p>
                        <ol class="text-sm text-emerald-800 space-y-2 list-decimal list-inside">
                            <li>Kembali ke <a href="{{ route('tenant.dashboard') }}" class="font-medium underline">Tenant Dashboard</a></li>
                            <li>Gunakan form <strong>"Test Payment"</strong> untuk create invoice</li>
                            <li>Buka invoice di browser HP (pastikan HP & laptop di WiFi sama)</li>
                            <li>Scan QR Code dengan GoPay Merchant / DANA Bisnis</li>
                            <li>Nominal harus muncul OTOMATIS</li>
                            <li>Bayar invoice → tunggu notifikasi masuk</li>
                            <li>Cekbayar auto-detect → kirim webhook ke server</li>
                            <li>Invoice status berubah <strong>PAID</strong> ✅</li>
                            <li>Cek Cekbayar → Tab "Activity Logs" → harus ada entry SUCCESS</li>
                        </ol>
                    </div>

                    <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800">
                            <strong>💡 Troubleshooting:</strong>
                        </p>
                        <ul class="mt-2 text-sm text-blue-800 list-disc list-inside space-y-1">
                            <li>Activity Log kosong → Cek notification permission granted</li>
                            <li>Webhook failed → Test connection via "Test Webhook" button</li>
                            <li>QR tidak scan → Upload QRIS Merchant yang valid</li>
                            <li>HP tidak connect → Pastikan HP & laptop di WiFi sama</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="pt-6 border-t border-slate-200">
            <a href="{{ route('tenant.dashboard') }}" class="inline-flex items-center px-4 py-2 border border-slate-300 text-slate-700 font-medium rounded-lg hover:bg-slate-50">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection
