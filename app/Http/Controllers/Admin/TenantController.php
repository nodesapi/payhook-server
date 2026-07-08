<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tenants = Tenant::with('plan')
            ->withCount(['invoices', 'qrisTemplates'])
            ->latest()
            ->paginate(20);

        return view('admin.tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $plans = Plan::where('is_active', true)->get();
        return view('admin.tenants.create', compact('plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plan_id'        => 'nullable|exists:plans,id',
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:tenants,email',
            'webhook_url'    => 'required|url',
            'webhook_secret' => 'nullable|string|max:255',
            'password'       => 'required|string|min:8|confirmed',
        ]);

        $validated['slug']      = Str::slug($validated['name']);
        $validated['is_active'] = true;
        $validated['mode']      = 'sandbox';

        // Set expiry if plan selected
        if (!empty($validated['plan_id'])) {
            $plan = Plan::find($validated['plan_id']);
            $validated['expired_at'] = now()->addDays($plan->duration_days);
        }

        // Auto-generate webhook secret if not provided
        if (empty($validated['webhook_secret'])) {
            $validated['webhook_secret'] = 'whsec_' . Str::random(32);
        }

        $tenant = Tenant::create($validated);

        // Auto-create user account dengan email & password yang sama
        if (! User::where('email', $validated['email'])->exists()) {
            User::create([
                'name'              => $validated['name'],
                'email'             => $validated['email'],
                'password'          => Hash::make($validated['password']),
                'email_verified_at' => now(),
            ]);
        }

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant berhasil dibuat! API Key: ' . $tenant->api_key_sandbox);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant)
    {
        $tenant->load(['invoices' => function($q) {
            $q->latest()->take(10);
        }, 'qrisTemplates']);

        return view('admin.tenants.show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tenant $tenant)
    {
        $plans = Plan::where('is_active', true)->get();
        $tenant->load('plan');
        $pendingUpgradeRequest = $tenant->getUpgradeRequestDetails();

        return view('admin.tenants.edit', compact('tenant', 'plans', 'pendingUpgradeRequest'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'plan_id'        => 'nullable|exists:plans,id',
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:tenants,email,' . $tenant->id,
            'webhook_url'    => 'required|url',
            'webhook_secret' => 'nullable|string|max:255',
            'callback_url'   => 'nullable|url',
            'is_active'      => 'boolean',
            'password'       => 'nullable|string|min:8|confirmed',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $tenant->update($validated);

        // Update user password jika diisi
        if (! empty($validated['password'])) {
            $user = User::where('email', $tenant->email)->first();
            if ($user) {
                $user->update(['password' => Hash::make($validated['password'])]);
            }
        }

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()->route('admin.tenants.index')
            ->with('success', 'Tenant deleted successfully!');
    }

    /**
     * Activate tenant
     */
    public function activate(Tenant $tenant)
    {
        $tenant->activate();

        return back()->with('success', 'Tenant activated successfully!');
    }

    /**
     * Suspend tenant
     */
    public function suspend(Tenant $tenant)
    {
        $tenant->suspend();

        return back()->with('success', 'Tenant suspended successfully!');
    }

    /**
     * Regenerate API key
     */
    public function regenerateKey(Tenant $tenant)
    {
        $newKey = $tenant->mode === 'production' 
            ? $tenant->generateProductionKey()
            : 'sk_test_' . Str::random(40);

        if ($tenant->mode === 'sandbox') {
            $tenant->update(['api_key_sandbox' => $newKey]);
        } else {
            $tenant->update(['api_key_production' => $newKey]);
        }

        return back()->with('success', 'API Key regenerated: ' . $newKey);
    }

    /**
     * Extend tenant subscription
     */
    public function extend(Request $request, Tenant $tenant)
    {
        $request->validate([
            'months' => 'required|integer|min:1|max:24',
        ]);

        $months = $request->months;
        $currentExpiry = $tenant->expired_at && $tenant->expired_at->isFuture() 
            ? $tenant->expired_at 
            : now();

        $tenant->update([
            'expired_at' => $currentExpiry->addMonths($months),
            'is_active' => true // Auto-reactivate if it was expired
        ]);

        return back()->with('success', "Subscription extended by {$months} month(s) for {$tenant->name}!");
    }

    /**
     * Approve KYC
     */
    public function approveKyc(Tenant $tenant)
    {
        $tenant->update([
            'kyc_status' => 'VERIFIED',
            'is_active' => true,
        ]);
        
        // Also extend subscription based on plan if expired_at is null or past
        if ($tenant->plan && (!$tenant->expired_at || $tenant->expired_at->isPast())) {
            $tenant->update([
                'expired_at' => now()->addDays($tenant->plan->duration_days)
            ]);
        }

        return back()->with('success', "KYC for {$tenant->name} approved successfully!");
    }

    /**
     * Reject KYC
     */
    public function rejectKyc(Request $request, Tenant $tenant)
    {
        $request->validate([
            'kyc_reject_reason' => 'required|string|max:1000',
        ]);

        $tenant->update([
            'kyc_status' => 'REJECTED',
            'kyc_reject_reason' => $request->kyc_reject_reason,
            'is_active' => false,
        ]);

        return back()->with('success', "KYC for {$tenant->name} rejected.");
    }

    public function approveUpgrade(Tenant $tenant)
    {
        $upgradeRequest = $tenant->getUpgradeRequestDetails();

        if (!$upgradeRequest || data_get($upgradeRequest, 'status') !== 'pending') {
            return back()->with('error', 'No pending upgrade request found for this tenant.');
        }

        $targetPlan = Plan::find(data_get($upgradeRequest, 'plan_id'));

        if (!$targetPlan) {
            return back()->with('error', 'Requested upgrade plan is no longer available.');
        }

        $settings = $tenant->settings ?? [];
        $settings['upgrade_request'] = array_merge($upgradeRequest, [
            'status' => 'approved',
            'approved_at' => now()->toDateTimeString(),
            'approved_by' => auth()->id(),
        ]);

        $updates = [
            'plan_id' => $targetPlan->id,
            'settings' => $settings,
        ];

        if (!$tenant->expired_at || $tenant->expired_at->isPast()) {
            $updates['expired_at'] = now()->addDays($targetPlan->duration_days);
        }

        if ($tenant->kyc_status === 'VERIFIED') {
            $updates['is_active'] = true;
        }

        $tenant->update($updates);

        return back()->with('success', "Upgrade to {$targetPlan->name} approved for {$tenant->name}.");
    }

    public function rejectUpgrade(Tenant $tenant)
    {
        $upgradeRequest = $tenant->getUpgradeRequestDetails();

        if (!$upgradeRequest || data_get($upgradeRequest, 'status') !== 'pending') {
            return back()->with('error', 'No pending upgrade request found for this tenant.');
        }

        $settings = $tenant->settings ?? [];
        $settings['upgrade_request'] = array_merge($upgradeRequest, [
            'status' => 'rejected',
            'rejected_at' => now()->toDateTimeString(),
            'rejected_by' => auth()->id(),
        ]);

        $tenant->update([
            'settings' => $settings,
        ]);

        return back()->with('success', "Upgrade request for {$tenant->name} has been rejected.");
    }
}
