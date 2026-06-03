<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        if ($user->two_factor_enabled) {
            $userId = $user->id;
            $remember = $request->boolean('remember');

            Auth::guard('web')->logout();

            session([
                'auth.2fa.user_id' => $userId,
                'auth.2fa.remember' => $remember,
            ]);

            return redirect()->route('login.two-factor', ['locale' => app()->getLocale()]);
        }

        $request->session()->regenerate();

        // Redirect based on user role
        $defaultRoute = $user->is_admin 
            ? route('admin.dashboard', absolute: false)
            : route('tenant.dashboard', absolute: false);

        return redirect()->intended($defaultRoute);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
