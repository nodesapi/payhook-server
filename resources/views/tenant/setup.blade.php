<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Tenant | Cekbayar</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-supabase-dark text-slate-300 font-['Inter'] antialiased min-h-screen flex items-center justify-center p-6">
    <div class="max-w-2xl w-full">
        <!-- Header -->
        <div class="mb-10 text-center">
            <h1 class="text-3xl font-black text-white mb-3 tracking-tight">SETUP <span class="text-supabase-accent">TENANT</span></h1>
            <p class="text-supabase-muted text-sm uppercase tracking-widest font-bold">
                Lengkapi profil merchant, pilih paket langganan, dan unggah KYC untuk mulai menerima pembayaran.
            </p>
        </div>

        <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8">
            <form method="POST" action="{{ route('tenant.setup.store') }}" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- Section 1: Merchant Profile -->
                <div>
                    <h2 class="text-lg font-bold text-white mb-4 flex items-center">
                        <span class="w-6 h-6 rounded-full bg-supabase-accent text-supabase-dark flex items-center justify-center text-xs mr-3">1</span>
                        Profil Merchant
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-medium text-slate-300">Nama Toko/Bisnis <span class="text-red-500">*</span></label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required class="sb-input w-full" placeholder="Cekbayar Store">
                            @error('name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label for="phone" class="block text-sm font-medium text-slate-300">Nomor WhatsApp</label>
                            <input id="phone" type="text" name="phone" value="{{ old('phone') }}" class="sb-input w-full" placeholder="08123456789">
                            @error('phone') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label for="website" class="block text-sm font-medium text-slate-300">Website</label>
                            <input id="website" type="url" name="website" value="{{ old('website') }}" class="sb-input w-full" placeholder="https://contoh.com">
                            @error('website') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Pilih Paket -->
                <div>
                    <h2 class="text-lg font-bold text-white mb-4 flex items-center">
                        <span class="w-6 h-6 rounded-full bg-supabase-accent text-supabase-dark flex items-center justify-center text-xs mr-3">2</span>
                        Pilih Paket Langganan <span class="text-red-500 ml-1">*</span>
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($plans as $plan)
                        <label class="relative cursor-pointer">
                            <input type="radio" name="plan_id" value="{{ $plan->id }}" class="peer sr-only" required>
                            <div class="p-5 rounded-2xl border border-supabase-border bg-supabase-darker peer-checked:border-supabase-accent peer-checked:bg-supabase-accent/5 transition-all">
                                <div class="font-bold text-white mb-1">{{ $plan->name }}</div>
                                <div class="text-supabase-accent font-black text-xl mb-3">Rp {{ number_format($plan->price, 0, ',', '.') }}</div>
                                <div class="text-xs text-supabase-muted space-y-1">
                                    <p>• Masa aktif {{ $plan->duration_days }} hari</p>
                                    <p>• {{ $plan->max_channels }} Channel (Node)</p>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('plan_id') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Section 3: KYC Data -->
                <div>
                    <h2 class="text-lg font-bold text-white mb-4 flex items-center">
                        <span class="w-6 h-6 rounded-full bg-supabase-accent text-supabase-dark flex items-center justify-center text-xs mr-3">3</span>
                        Verifikasi Identitas (KYC)
                    </h2>
                    <div class="grid grid-cols-1 gap-6">
                        <div class="space-y-2">
                            <label for="ktp_name" class="block text-sm font-medium text-slate-300">Nama Sesuai KTP <span class="text-red-500">*</span></label>
                            <input id="ktp_name" type="text" name="ktp_name" value="{{ old('ktp_name') }}" required class="sb-input w-full" placeholder="NAMA LENGKAP KTP">
                            @error('ktp_name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label for="ktp_number" class="block text-sm font-medium text-slate-300">NIK KTP <span class="text-red-500">*</span></label>
                            <input id="ktp_number" type="text" name="ktp_number" value="{{ old('ktp_number') }}" required class="sb-input w-full" placeholder="3201xxxxxxxxxxxx" maxlength="16">
                            @error('ktp_number') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-300">Unggah Foto KTP <span class="text-red-500">*</span></label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-supabase-border border-dashed rounded-2xl hover:border-supabase-accent/50 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-supabase-muted" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-slate-400 justify-center">
                                        <label for="ktp_image" class="relative cursor-pointer rounded-md font-medium text-supabase-accent hover:text-white focus-within:outline-none">
                                            <span>Pilih File KTP</span>
                                            <input id="ktp_image" name="ktp_image" type="file" class="sr-only" required accept="image/*">
                                        </label>
                                    </div>
                                    <p class="text-xs text-supabase-muted">PNG, JPG, WEBP maksimal 2MB</p>
                                </div>
                            </div>
                            @error('ktp_image') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-supabase-border flex justify-end items-center gap-4">
                    <button type="button" onclick="document.getElementById('logout-form').submit();" class="text-sm font-bold text-supabase-muted hover:text-white uppercase tracking-widest transition-colors">
                        Logout
                    </button>
                    <button type="submit" class="sb-button-primary px-8">
                        Kirim Pengajuan
                    </button>
                </div>
            </form>
            
            <form method="POST" action="{{ route('logout') }}" id="logout-form" class="hidden">
                @csrf
            </form>
        </div>
    </div>
</body>
</html>
