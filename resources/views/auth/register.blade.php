<x-guest-layout>
    <!-- Title Text -->
    <div class="mb-10 text-center lg:text-left">
        <h2 class="text-3xl font-bold text-white mb-3">{{ __('Daftar Akun') }}</h2>
        <p class="text-supabase-muted">{{ __('Mulai otomatisasi pencocokan invoice pembayaran secara gratis sekarang juga.') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div class="space-y-2">
            <label for="name" class="block text-sm font-medium text-slate-300">{{ __('Nama Lengkap') }}</label>
            <input 
                id="name" 
                type="text" 
                name="name" 
                value="{{ old('name') }}" 
                required 
                autofocus 
                autocomplete="name"
                class="sb-input @error('name') border-red-500/50 @enderror"
                placeholder="Developer Cekbayar"
            />
            @error('name')
                <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-slate-300">{{ __('Alamat Email') }}</label>
            <input 
                id="email" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autocomplete="username"
                class="sb-input @error('email') border-red-500/50 @enderror"
                placeholder="admin@cekbayar.com"
            />
            @error('email')
                <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <label for="password" class="block text-sm font-medium text-slate-300">{{ __('Kata Sandi') }}</label>
            <input 
                id="password" 
                type="password" 
                name="password" 
                required 
                autocomplete="new-password"
                class="sb-input @error('password') border-red-500/50 @enderror"
                placeholder="••••••••"
            />
            @error('password')
                <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="space-y-2">
            <label for="password_confirmation" class="block text-sm font-medium text-slate-300">{{ __('Konfirmasi Kata Sandi') }}</label>
            <input 
                id="password_confirmation" 
                type="password" 
                name="password_confirmation" 
                required 
                autocomplete="new-password"
                class="sb-input @error('password_confirmation') border-red-500/50 @enderror"
                placeholder="••••••••"
            />
            @error('password_confirmation')
                <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Terms and Conditions Agreement -->
        <div class="flex items-start">
            <input 
                id="terms" 
                type="checkbox" 
                name="terms" 
                required 
                class="w-4 h-4 mt-1 bg-supabase-input border-supabase-border text-supabase-accent rounded focus:ring-supabase-accent cursor-pointer"
            />
            <label for="terms" class="ml-2 text-xs text-supabase-muted cursor-pointer hover:text-slate-300 transition-colors">
                {{ __('Saya menyetujui') }} 
                <a href="{{ localeRoute('public.terms') }}" target="_blank" class="text-supabase-accent hover:underline">{{ __('Syarat & Ketentuan') }}</a> 
                {{ __('dan') }} 
                <a href="{{ localeRoute('public.privacy') }}" target="_blank" class="text-supabase-accent hover:underline">{{ __('Kebijakan Privasi') }}</a>.
            </label>
        </div>

        <!-- Submit & Login Link -->
        <div class="flex items-center justify-between pt-2">
            <a href="{{ route('login', ['locale' => app()->getLocale()]) }}" class="text-sm text-supabase-accent hover:underline">
                {{ __('Sudah punya akun?') }}
            </a>
            
            <button 
                type="submit"
                class="sb-button-primary w-auto px-8"
            >
                {{ __('Daftar') }}
            </button>
        </div>
    </form>

    <!-- Footer -->
    <div class="mt-12 text-center text-xs text-supabase-muted">
        <p>&copy; {{ date('Y') }} Cekbayar Platform. {{ __('Hak Cipta Dilindungi.') }}</p>
    </div>
</x-guest-layout>
