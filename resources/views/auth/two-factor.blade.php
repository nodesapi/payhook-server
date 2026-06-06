@extends('layouts.guest')

@section('content')
    <div>
        <h2 class="text-2xl font-bold text-white tracking-normal uppercase mb-2">
            Verification <span class="text-supabase-accent">Required</span>
        </h2>
        <p class="text-supabase-muted text-xs leading-relaxed mb-6">
            {{ __('Akun Anda dilindungi oleh Dua-Faktor Autentikasi. Silakan masukkan kode verifikasi 6 digit dari aplikasi autentikator Anda.') }}
        </p>

        <form method="POST" action="{{ route('login.two-factor') }}" class="space-y-6">
            @csrf

            <!-- Verification Code -->
            <div class="space-y-2">
                <label for="code" class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">{{ __('Kode Autentikator 6-Digit') }}</label>
                <input id="code" class="sb-input text-center font-mono tracking-[0.5em] text-xl py-4 rounded-2xl" 
                       type="text" 
                       name="code" 
                       required 
                       autofocus 
                       maxlength="6"
                       placeholder="000000"
                       autocomplete="one-time-code" />
                @error('code')
                    <p class="text-xs text-red-500 font-bold uppercase tracking-wide mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between pt-2">
                <a class="text-xs font-bold text-supabase-muted hover:text-white uppercase tracking-wider transition-colors" href="{{ route('login') }}">
                    {{ __('Kembali ke Login') }}
                </a>

                <button type="submit" class="sb-button-primary !w-auto px-8">
                    {{ __('Verifikasi') }}
                </button>
            </div>
        </form>
    </div>
@endsection
