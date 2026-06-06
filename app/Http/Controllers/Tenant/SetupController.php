<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SetupController extends Controller
{
    public function create()
    {
        $user = auth()->user();
        $tenant = Tenant::where('email', $user->email)->first();

        if ($tenant && $tenant->kyc_status !== 'REJECTED') {
            return redirect()->route('dashboard');
        }

        $plans = Plan::where('is_active', true)->orderBy('price')->get();

        return view('tenant.setup', compact('plans', 'tenant'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $tenant = Tenant::where('email', $user->email)->first();

        if ($tenant && $tenant->kyc_status !== 'REJECTED') {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'plan_id' => 'required|exists:plans,id',
            'ktp_name' => 'required|string|max:255',
            'ktp_number' => 'required|string|max:20',
            'ktp_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $path = $request->file('ktp_image')->store('kyc-documents', 'public');

        if ($tenant) {
            $tenant->update([
                'name' => $validated['name'],
                'phone' => $validated['phone'] ?? null,
                'website' => $validated['website'] ?? null,
                'plan_id' => $validated['plan_id'],
                'kyc_status' => 'PENDING',
                'ktp_name' => $validated['ktp_name'],
                'ktp_number' => $validated['ktp_number'],
                'ktp_image_path' => $path,
            ]);
        } else {
            $tenant = Tenant::create([
                'name' => $validated['name'],
                'email' => $user->email,
                'phone' => $validated['phone'] ?? null,
                'website' => $validated['website'] ?? null,
                'slug' => Str::slug($validated['name']) . '-' . Str::random(5),
                'plan_id' => $validated['plan_id'],
                'kyc_status' => 'PENDING',
                'ktp_name' => $validated['ktp_name'],
                'ktp_number' => $validated['ktp_number'],
                'ktp_image_path' => $path,
                'is_active' => false,
                'mode' => 'production',
            ]);
        }

        return redirect()->route('tenant.kyc.pending')->with('success', 'Setup completed. Please wait for admin approval.');
    }

    public function pending()
    {
        $user = auth()->user();
        $tenant = Tenant::where('email', $user->email)->first();

        return view('tenant.kyc-pending', compact('tenant'));
    }

    public function uploadPaymentProof(Request $request)
    {
        $user = auth()->user();
        $tenant = Tenant::where('email', $user->email)->first();

        if (!$tenant) {
            return redirect()->route('tenant.setup');
        }

        $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $path = $request->file('payment_proof')->store('payment-proofs', 'public');

        $tenant->update([
            'payment_proof_path' => $path,
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah dan sedang ditinjau.');
    }
}
