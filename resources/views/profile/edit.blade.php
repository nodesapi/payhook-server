@php
    $profileLayout = auth()->user()?->is_admin ? 'layouts.admin' : 'layouts.tenant';
@endphp

@extends($profileLayout)

@section('page-title', 'Account Profile')

@section('content')
<div class="w-full max-w-none space-y-8">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-black text-white tracking-tight uppercase">Account <span class="text-supabase-accent">Profile</span></h1>
            <p class="mt-1 text-sm text-supabase-muted">Manage your account identity and security credentials.</p>
        </div>
        <div class="flex items-center gap-3 rounded-lg border border-supabase-border bg-supabase-surface px-4 py-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-lg border border-supabase-border bg-supabase-input text-sm font-black text-supabase-accent">
                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-white">{{ $user->name }}</p>
                <p class="truncate text-[10px] font-bold uppercase tracking-widest text-supabase-muted">{{ $user->email }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <section class="rounded-lg border border-supabase-border bg-supabase-surface p-6 shadow-2xl">
            <div class="mb-6">
                <h2 class="text-lg font-black uppercase tracking-tight text-white">Profile Information</h2>
                <p class="mt-1 text-sm text-supabase-muted">Update your name and account email address.</p>
            </div>

            <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
                @csrf
                @method('patch')

                <div>
                    <label for="name" class="mb-2 block text-[10px] font-black uppercase tracking-widest text-supabase-muted">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" class="sb-input !rounded-lg">
                    @error('name')
                        <p class="mt-2 text-[10px] font-bold uppercase tracking-wider text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="mb-2 block text-[10px] font-black uppercase tracking-widest text-supabase-muted">Email</label>
                    <input id="email" type="email" value="{{ $user->email }}" readonly class="sb-input !rounded-lg cursor-not-allowed bg-supabase-dark/50 text-supabase-muted">
                    <p class="mt-2 text-[10px] font-bold uppercase tracking-wider text-supabase-muted">Email is locked for account identity.</p>
                </div>

                <div class="flex items-center gap-4 pt-1">
                    <button type="submit" class="sb-button-primary !h-9 !w-auto !rounded-md !py-0 px-5 !text-[11px] !shadow-none">Save Profile</button>
                    @if (session('status') === 'profile-updated')
                        <p class="text-xs font-bold text-green-500">Saved.</p>
                    @endif
                </div>
            </form>
        </section>

        <section class="rounded-lg border border-supabase-border bg-supabase-surface p-6 shadow-2xl">
            <div class="mb-6">
                <h2 class="text-lg font-black uppercase tracking-tight text-white">Update Password</h2>
                <p class="mt-1 text-sm text-supabase-muted">Use a strong password to keep admin access secure.</p>
            </div>

            <form method="post" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                @method('put')

                <div>
                    <label for="update_password_current_password" class="mb-2 block text-[10px] font-black uppercase tracking-widest text-supabase-muted">Current Password</label>
                    <div class="relative">
                        <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" class="sb-input !rounded-lg pr-11" data-password-toggle>
                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-supabase-muted transition-colors hover:text-supabase-accent" data-toggle-password="update_password_current_password" aria-label="Show current password">
                            <svg class="h-4 w-4 password-eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <svg class="hidden h-4 w-4 password-eye-closed" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.584 10.587A2 2 0 0012 14a2 2 0 001.414-.586M9.88 4.24A9.968 9.968 0 0112 4c4.478 0 8.268 2.943 9.542 7a9.978 9.978 0 01-2.01 3.345M6.61 6.61A9.97 9.97 0 002.458 12a9.956 9.956 0 004.593 5.197A9.96 9.96 0 0012 20a9.97 9.97 0 004.39-1.014"></path>
                            </svg>
                        </button>
                    </div>
                    @if($errors->updatePassword->has('current_password'))
                        <p class="mt-2 text-[10px] font-bold uppercase tracking-wider text-red-500">{{ $errors->updatePassword->first('current_password') }}</p>
                    @endif
                </div>

                <div>
                    <label for="update_password_password" class="mb-2 block text-[10px] font-black uppercase tracking-widest text-supabase-muted">New Password</label>
                    <div class="relative">
                        <input id="update_password_password" name="password" type="password" autocomplete="new-password" class="sb-input !rounded-lg pr-11" data-password-toggle>
                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-supabase-muted transition-colors hover:text-supabase-accent" data-toggle-password="update_password_password" aria-label="Show new password">
                            <svg class="h-4 w-4 password-eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <svg class="hidden h-4 w-4 password-eye-closed" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.584 10.587A2 2 0 0012 14a2 2 0 001.414-.586M9.88 4.24A9.968 9.968 0 0112 4c4.478 0 8.268 2.943 9.542 7a9.978 9.978 0 01-2.01 3.345M6.61 6.61A9.97 9.97 0 002.458 12a9.956 9.956 0 004.593 5.197A9.96 9.96 0 0012 20a9.97 9.97 0 004.39-1.014"></path>
                            </svg>
                        </button>
                    </div>
                    @if($errors->updatePassword->has('password'))
                        <p class="mt-2 text-[10px] font-bold uppercase tracking-wider text-red-500">{{ $errors->updatePassword->first('password') }}</p>
                    @endif
                </div>

                <div>
                    <label for="update_password_password_confirmation" class="mb-2 block text-[10px] font-black uppercase tracking-widest text-supabase-muted">Confirm Password</label>
                    <div class="relative">
                        <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="sb-input !rounded-lg pr-11" data-password-toggle>
                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-supabase-muted transition-colors hover:text-supabase-accent" data-toggle-password="update_password_password_confirmation" aria-label="Show password confirmation">
                            <svg class="h-4 w-4 password-eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <svg class="hidden h-4 w-4 password-eye-closed" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.584 10.587A2 2 0 0012 14a2 2 0 001.414-.586M9.88 4.24A9.968 9.968 0 0112 4c4.478 0 8.268 2.943 9.542 7a9.978 9.978 0 01-2.01 3.345M6.61 6.61A9.97 9.97 0 002.458 12a9.956 9.956 0 004.593 5.197A9.96 9.96 0 0012 20a9.97 9.97 0 004.39-1.014"></path>
                            </svg>
                        </button>
                    </div>
                    @if($errors->updatePassword->has('password_confirmation'))
                        <p class="mt-2 text-[10px] font-bold uppercase tracking-wider text-red-500">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                    @endif
                </div>

                <div class="flex items-center gap-4 pt-1">
                    <button type="submit" class="sb-button-primary !h-9 !w-auto !rounded-md !py-0 px-5 !text-[11px] !shadow-none">Update Password</button>
                    @if (session('status') === 'password-updated')
                        <p class="text-xs font-bold text-green-500">Saved.</p>
                    @endif
                </div>
            </form>
        </section>
    </div>
</div>

@push('scripts')
<script>
    document.querySelectorAll('[data-toggle-password]').forEach((button) => {
        button.addEventListener('click', () => {
            const input = document.getElementById(button.dataset.togglePassword);
            if (!input) return;

            const showPassword = input.type === 'password';
            input.type = showPassword ? 'text' : 'password';
            button.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
            button.querySelector('.password-eye-open')?.classList.toggle('hidden', showPassword);
            button.querySelector('.password-eye-closed')?.classList.toggle('hidden', !showPassword);
        });
    });
</script>
@endpush
@endsection
