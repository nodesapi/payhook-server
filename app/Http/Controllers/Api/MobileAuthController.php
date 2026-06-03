<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MobileAuthController extends Controller
{
    /**
     * Mobile app login - return tenant credentials
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Find user by email
        $user = User::where('email', $request->email)->first();

        // Validate user exists
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['Email tidak terdaftar.'],
            ]);
        }

        // Validate password
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        // Find tenant with same email
        $tenant = Tenant::where('email', $request->email)->first();

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda belum terdaftar sebagai merchant. Hubungi administrator.',
            ], 404);
        }

        // Check if tenant is active
        if (!$tenant->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun merchant Anda sedang tidak aktif. Hubungi administrator.',
            ], 403);
        }

        // Return tenant credentials for mobile app
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'email' => $tenant->email,
                'webhook_url' => $tenant->webhook_url ?? '',
                'api_key' => $tenant->api_key_production ?? '',
                'webhook_secret' => $tenant->webhook_secret ?? '',
                'mode' => $tenant->mode,
                'is_active' => $tenant->is_active,
            ],
        ], 200);
    }

    /**
     * Verify credentials (untuk re-sync data)
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $tenant = Tenant::where('email', $request->email)->first();

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'email' => $tenant->email,
                'webhook_url' => $tenant->webhook_url ?? '',
                'api_key' => $tenant->api_key_production ?? '',
                'webhook_secret' => $tenant->webhook_secret ?? '',
                'mode' => $tenant->mode,
                'is_active' => $tenant->is_active,
            ],
        ], 200);
    }
}
