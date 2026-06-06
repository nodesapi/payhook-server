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
            @if(session('success'))
                <div class="p-4 bg-green-500/10 border border-green-500/20 rounded-xl mb-4">
                    <p class="text-xs font-bold text-green-500 uppercase tracking-widest">{{ session('success') }}</p>
                </div>
            @endif

            @if(isset($tenant) && $tenant->payment_proof_path)
                <div class="p-4 bg-supabase-surface border border-supabase-border rounded-xl mb-4">
                    <div class="flex items-center justify-center space-x-2 text-green-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Bukti Terunggah</span>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('tenant.kyc.upload-payment') }}" enctype="multipart/form-data" class="bg-supabase-surface border border-supabase-border rounded-2xl p-6 mb-4">
                @csrf
                <label class="block text-xs font-black text-white uppercase tracking-widest mb-3 text-center">
                    {{ isset($tenant) && $tenant->payment_proof_path ? 'Ganti Bukti Pembayaran' : 'Upload Bukti Pembayaran' }}
                </label>
                
                <div class="flex justify-center px-4 pt-4 pb-4 border-2 border-supabase-border border-dashed rounded-xl hover:border-yellow-500/50 transition-colors">
                    <div class="space-y-1 text-center">
                        <svg id="payment-icon" class="mx-auto h-8 w-8 text-supabase-muted transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        <div class="flex text-sm text-slate-400 justify-center mt-2">
                            <label for="payment_proof" class="relative cursor-pointer rounded-md font-bold text-yellow-500 hover:text-white focus-within:outline-none transition-colors">
                                <span id="payment-btn-text" class="uppercase text-[10px] tracking-widest">Pilih File Foto</span>
                                <input id="payment_proof" name="payment_proof" type="file" class="sr-only" required accept="image/*"
                                    onchange="
                                        const file = this.files[0];
                                        if(file) {
                                            document.getElementById('payment-filename').textContent = file.name;
                                            document.getElementById('payment-filename').classList.remove('hidden');
                                            document.getElementById('payment-icon').classList.remove('text-supabase-muted');
                                            document.getElementById('payment-icon').classList.add('text-yellow-500');
                                            document.getElementById('payment-btn-text').textContent = 'GANTI FILE';
                                        }
                                    ">
                            </label>
                        </div>
                        <p id="payment-filename" class="text-[10px] font-bold text-white mt-2 hidden"></p>
                    </div>
                </div>
                @error('payment_proof') <p class="text-[10px] text-red-400 mt-2 font-bold text-center uppercase">{{ $message }}</p> @enderror

                <button type="submit" class="w-full mt-4 flex justify-center items-center py-3 px-6 rounded-xl shadow-lg shadow-yellow-500/20 text-xs font-bold transition-all active:scale-95 border border-transparent uppercase tracking-widest" style="background-color: #eab308; color: #000000;">
                    Kirim Bukti
                </button>
            </form>
            
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
