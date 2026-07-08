<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    private function getTenant()
    {
        $user = auth()->user();
        return Tenant::where('email', $user->email)->firstOrFail();
    }

    public function index()
    {
        $tenant = $this->getTenant()->load('plan');
        $currentPrice = $tenant->plan?->price ?? 0;
        $upgradePlans = Plan::where('is_active', true)
            ->when($tenant->plan_id, fn ($query) => $query->where('price', '>', $currentPrice))
            ->orderBy('price')
            ->get();
        $pendingUpgradeRequest = $tenant->getUpgradeRequestDetails();

        return view('tenant.settings', compact('tenant', 'upgradePlans', 'pendingUpgradeRequest'));
    }

    public function update(Request $request)
    {
        $tenant = $this->getTenant();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url',
            'description' => 'nullable|string',
            'webhook_url' => 'nullable|url',
            'webhook_enabled' => 'boolean',
            'callback_url' => 'nullable|url',
        ]);

        $tenant->update($validated);

        return back()->with('success', 'Settings updated successfully!');
    }

    public function regenerateApiKey(Request $request)
    {
        $tenant = $this->getTenant();

        $mode = $request->input('mode', 'sandbox');

        if ($mode === 'sandbox') {
            $tenant->api_key_sandbox = 'sk_test_' . Str::random(40);
        } else {
            $tenant->api_key_production = 'sk_live_' . Str::random(40);
        }

        $tenant->save();

        return back()->with('success', ucfirst($mode) . ' API key regenerated successfully!');
    }

    public function regenerateWebhookSecret()
    {
        $tenant = $this->getTenant();
        $tenant->webhook_secret = Str::random(32);
        $tenant->save();

        return back()->with('success', 'Webhook secret regenerated successfully!');
    }

    public function requestUpgrade(Request $request)
    {
        $tenant = $this->getTenant()->load('plan');

        $validated = $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
        ]);

        $plan = Plan::where('is_active', true)->findOrFail($validated['plan_id']);

        if ($tenant->plan && $plan->price <= $tenant->plan->price) {
            return back()->withErrors(['plan_id' => 'Please select a higher plan to upgrade.']);
        }

        $settings = $tenant->settings ?? [];
        $settings['upgrade_request'] = array_merge($tenant->getUpgradeQuoteForPlan($plan), [
            'plan_id' => $plan->id,
            'plan_name' => $plan->name,
            'requested_at' => now()->toDateTimeString(),
            'status' => 'pending',
        ]);

        $tenant->forceFill(['settings' => $settings])->save();

        return back()->with('success', 'Upgrade request submitted. Admin will review your package upgrade.');
    }
}
