<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    /**
     * Display the 2FA challenge login page.
     */
    public function showChallenge()
    {
        if (!session()->has('auth.2fa.user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor');
    }

    /**
     * Verify the 2FA challenge login code.
     */
    public function verifyChallenge(Request $request)
    {
        if (!session()->has('auth.2fa.user_id')) {
            return redirect()->route('login');
        }

        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $userId = session('auth.2fa.user_id');
        $user = \App\Models\User::findOrFail($userId);

        if (TwoFactorService::verifyCode($user->two_factor_secret, $request->code)) {
            // Authenticate user
            Auth::loginUsingId($user->id, session('auth.2fa.remember', false));
            
            // Clear session 2FA variables
            session()->forget(['auth.2fa.user_id', 'auth.2fa.remember']);
            
            $request->session()->regenerate();

            $defaultRoute = $user->is_admin 
                ? route('admin.dashboard', absolute: false)
                : route('tenant.dashboard', absolute: false);

            return redirect()->intended($defaultRoute);
        }

        throw ValidationException::withMessages([
            'code' => [__('Kode autentikasi salah atau kadaluarsa.')],
        ]);
    }

    /**
     * Generate 2FA secret for the tenant user.
     */
    public function generate(Request $request)
    {
        $user = Auth::user();

        if ($user->two_factor_enabled) {
            return back()->withErrors(['2fa' => __('2FA sudah aktif.')]);
        }

        $secret = TwoFactorService::generateSecretKey();
        $user->two_factor_secret = $secret;
        $user->save();

        return back()->with('2fa_setup', true);
    }

    /**
     * Confirm and activate 2FA for the tenant user.
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        if (!$user->two_factor_secret) {
            return back()->withErrors(['code' => __('Rahasia 2FA tidak ditemukan. Harap generate ulang.')]);
        }

        if (TwoFactorService::verifyCode($user->two_factor_secret, $request->code)) {
            $user->two_factor_enabled = true;
            $user->save();

            return back()->with('success', __('Dua-Faktor Autentikasi (2FA) berhasil diaktifkan.'));
        }

        throw ValidationException::withMessages([
            'code' => [__('Kode verifikasi salah.')],
        ]);
    }

    /**
     * Disable 2FA for the tenant user.
     */
    public function disable(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password',
        ]);

        $user = Auth::user();
        $user->two_factor_enabled = false;
        $user->two_factor_secret = null;
        $user->save();

        return back()->with('success', __('Dua-Faktor Autentikasi (2FA) berhasil dinonaktifkan.'));
    }
}
