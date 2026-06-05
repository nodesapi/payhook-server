<x-guest-layout>
    <div class="max-w-md mx-auto">
        <!-- Icon Header -->
        <div class="mb-8 flex justify-center">
            <div class="relative">
                <div class="absolute -inset-1 rounded-full blur bg-yellow-500/20"></div>
                <div class="relative bg-[#1A1A1A] border border-gray-800 rounded-full p-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Title & Description -->
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-white mb-3 tracking-tight">Cek Email Anda</h2>
            <p class="text-gray-400 text-sm leading-relaxed">
                {{ __('Terima kasih telah mendaftar! Kami telah mengirimkan link verifikasi ke alamat email Anda. Silakan klik link tersebut untuk mulai menggunakan layanan kami.') }}
            </p>
        </div>

        <!-- Status Message -->
        @if (session('status') == 'verification-link-sent')
            <div class="mb-8 p-4 bg-green-500/10 border border-green-500/20 rounded-xl flex items-start space-x-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-green-400">
                    {{ __('Link verifikasi baru telah berhasil dikirim ulang ke email Anda.') }}
                </p>
            </div>
        @endif

        <!-- Actions -->
        <div class="space-y-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-black bg-yellow-500 hover:bg-yellow-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 focus:ring-offset-[#111111] transition-all duration-200">
                    {{ __('Kirim Ulang Email Verifikasi') }}
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-gray-700 rounded-xl shadow-sm text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-700 focus:ring-offset-[#111111] transition-all duration-200">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
        
        <!-- Spam Notice -->
        <div class="mt-8 text-center">
            <p class="text-xs text-gray-500">
                Tidak menemukan email? Coba periksa folder <span class="text-gray-400 font-medium">Spam</span> atau <span class="text-gray-400 font-medium">Promotions</span> Anda.
            </p>
        </div>
    </div>
</x-guest-layout>
