<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Tenant | Cekbayar</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23FBBF24'><path d='M13 10V3L4 14h7v7l9-11h-7z'/></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-supabase-dark text-slate-300 font-['Inter'] antialiased min-h-screen pt-12 pb-24 px-6">
    <div class="max-w-4xl w-full mx-auto">
        <!-- Header -->
        <div class="mb-10 flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-supabase-accent rounded-2xl flex items-center justify-center shadow-xl shadow-supabase-accent/20 mb-6 relative">
                <div class="absolute -inset-1 rounded-2xl blur bg-supabase-accent/30"></div>
                <svg class="w-8 h-8 text-supabase-dark relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <h1 class="text-3xl md:text-4xl font-black text-white mb-3 tracking-tight">SETUP <span class="text-supabase-accent">TENANT</span></h1>
            <p class="text-supabase-muted text-xs md:text-sm uppercase tracking-widest font-bold max-w-2xl">
                Lengkapi profil merchant, pilih paket langganan, dan unggah KYC untuk mulai menerima pembayaran.
            </p>
        </div>

        <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-6 md:p-10 shadow-2xl">
            <form method="POST" action="{{ route('tenant.setup.store') }}" enctype="multipart/form-data" class="space-y-10">
                @csrf

                <!-- Section 1: Merchant Profile -->
                <div>
                    <h2 class="text-xl font-bold text-white mb-5 flex items-center">
                        <span class="w-8 h-8 rounded-full bg-supabase-accent text-supabase-dark flex items-center justify-center text-sm mr-3 shadow-lg shadow-supabase-accent/20">1</span>
                        Profil Merchant
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-supabase-darker/50 p-6 rounded-2xl border border-supabase-border">
                        <div class="space-y-2">
                            <label for="name" class="block text-sm font-medium text-slate-300">Nama Toko/Bisnis <span class="text-red-500">*</span></label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required class="sb-input w-full bg-supabase-dark" placeholder="Cekbayar Store">
                            @error('name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label for="phone" class="block text-sm font-medium text-slate-300">Nomor WhatsApp</label>
                            <input id="phone" type="text" name="phone" value="{{ old('phone') }}" class="sb-input w-full bg-supabase-dark" placeholder="08123456789">
                            @error('phone') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2 md:col-span-2">
                            <label for="website" class="block text-sm font-medium text-slate-300">Website</label>
                            <input id="website" type="url" name="website" value="{{ old('website') }}" class="sb-input w-full bg-supabase-dark" placeholder="https://contoh.com">
                            @error('website') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Pilih Paket -->
                <div>
                    <h2 class="text-xl font-bold text-white mb-5 flex items-center">
                        <span class="w-8 h-8 rounded-full bg-supabase-accent text-supabase-dark flex items-center justify-center text-sm mr-3 shadow-lg shadow-supabase-accent/20">2</span>
                        Pilih Paket Langganan <span class="text-red-500 ml-1">*</span>
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        @foreach($plans as $plan)
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="plan_id" value="{{ $plan->id }}" class="peer sr-only" required>
                            <div class="p-6 rounded-2xl border-2 border-supabase-border bg-supabase-darker peer-checked:border-supabase-accent peer-checked:bg-supabase-accent/5 hover:border-slate-600 transition-all h-full flex flex-col relative overflow-hidden">
                                <div class="absolute top-0 right-0 p-3 opacity-0 peer-checked:opacity-100 transition-opacity">
                                    <svg class="w-6 h-6 text-supabase-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div class="font-bold text-white text-lg pr-6 min-h-[3.5rem]">{{ $plan->name }}</div>
                                <div class="text-supabase-accent font-black mb-4 flex items-baseline">
                                    <span class="text-sm mr-1">Rp</span>
                                    <span class="text-3xl tracking-tight">{{ number_format($plan->price, 0, ',', '.') }}</span>
                                </div>
                                <div class="text-sm text-slate-400 space-y-2 mt-auto">
                                    <div class="flex items-center"><svg class="w-4 h-4 mr-2 text-supabase-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg> Aktif {{ $plan->duration_days }} hari</div>
                                    <div class="flex items-center"><svg class="w-4 h-4 mr-2 text-supabase-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path></svg> {{ $plan->max_channels }} Channel</div>
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('plan_id') <p class="text-xs text-red-400 mt-2">{{ $message }}</p> @enderror
                </div>

                <!-- Section 3: KYC Data -->
                <div>
                    <h2 class="text-xl font-bold text-white mb-5 flex items-center">
                        <span class="w-8 h-8 rounded-full bg-supabase-accent text-supabase-dark flex items-center justify-center text-sm mr-3 shadow-lg shadow-supabase-accent/20">3</span>
                        Verifikasi Identitas (KYC)
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-supabase-darker/50 p-6 rounded-2xl border border-supabase-border">
                        <div class="space-y-2">
                            <label for="ktp_name" class="block text-sm font-medium text-slate-300">Nama Sesuai KTP <span class="text-red-500">*</span></label>
                            <input id="ktp_name" type="text" name="ktp_name" value="{{ old('ktp_name') }}" required class="sb-input w-full bg-supabase-dark" placeholder="NAMA LENGKAP KTP">
                            @error('ktp_name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label for="ktp_number" class="block text-sm font-medium text-slate-300">NIK KTP <span class="text-red-500">*</span></label>
                            <input id="ktp_number" type="text" name="ktp_number" value="{{ old('ktp_number') }}" required class="sb-input w-full bg-supabase-dark" placeholder="3201xxxxxxxxxxxx" maxlength="16">
                            @error('ktp_number') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2 md:col-span-2">
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

                <div class="pt-8 border-t border-supabase-border flex flex-col-reverse md:flex-row justify-between items-center gap-6">
                    <button type="button" onclick="document.getElementById('logout-form').submit();" class="w-full md:w-auto py-3 px-6 text-sm font-bold text-slate-400 hover:text-white uppercase tracking-widest transition-colors border border-transparent hover:border-slate-700 rounded-xl">
                        LOGOUT
                    </button>
                    <button type="submit" class="w-full md:flex-1 md:max-w-md flex justify-center items-center py-4 px-8 rounded-xl shadow-lg shadow-yellow-500/20 text-sm font-bold transition-all active:scale-95 border border-transparent" style="background-color: #eab308; color: #000000;">
                        Kirim Pengajuan
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
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
