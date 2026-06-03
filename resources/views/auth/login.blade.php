<x-guest-layout>
    <!-- Welcome Text -->
    <div class="mb-10 text-center lg:text-left">
        <h2 class="text-3xl font-bold text-white mb-3">{{ __('Masuk') }}</h2>
        <p class="text-supabase-muted">{{ __('Masukkan email dan kata sandi Anda untuk mengakses dasbor') }}</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl">
            <p class="text-sm text-emerald-400 font-medium">{{ session('status') }}</p>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-slate-300">{{ __('Alamat Email') }}</label>
            <input 
                id="email" 
                type="email" 
                name="email" 
                value="{{ old('email') }}" 
                required 
                autofocus 
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
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-medium text-slate-300">{{ __('Kata Sandi') }}</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-supabase-muted hover:text-white transition-colors">
                        {{ __('Lupa kata sandi?') }}
                    </a>
                @endif
            </div>
            <input 
                id="password" 
                type="password" 
                name="password" 
                required 
                autocomplete="current-password"
                class="sb-input @error('password') border-red-500/50 @enderror"
                placeholder="••••••••"
            />
            @error('password')
                <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me & Register link -->
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <input 
                    id="remember_me" 
                    type="checkbox" 
                    name="remember"
                    class="w-4 h-4 bg-supabase-input border-supabase-border text-supabase-accent rounded focus:ring-supabase-accent cursor-pointer"
                >
                <label for="remember_me" class="ml-2 text-sm text-supabase-muted cursor-pointer hover:text-slate-300 transition-colors">{{ __('Ingat saya') }}</label>
            </div>
            
            <a href="{{ route('register', ['locale' => app()->getLocale()]) }}" class="text-sm text-supabase-accent hover:underline">
                {{ __('Daftar Akun') }}
            </a>
        </div>

        <!-- Submit Button -->
        <button 
            type="submit"
            class="sb-button-primary"
        >
            {{ __('Masuk') }}
        </button>
    </form>



    <!-- Footer -->
    <div class="mt-12 text-center text-xs text-supabase-muted">
        <p>&copy; {{ date('Y') }} Cekbayar Platform. {{ __('Hak Cipta Dilindungi.') }}</p>
    </div>
</x-guest-layout>
