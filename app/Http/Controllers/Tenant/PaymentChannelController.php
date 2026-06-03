<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\PaymentChannel;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentChannelController extends Controller
{
    private function getTenant()
    {
        $user = auth()->user();
        return Tenant::where('email', $user->email)->firstOrFail();
    }

    public function index()
    {
        $tenant = $this->getTenant();
        $channels = PaymentChannel::where('tenant_id', $tenant->id)
            ->withCount('transactions')
            ->latest()
            ->get();
        
        $stats = [
            'total' => $channels->count(),
            'active' => $channels->where('is_active', true)->count(),
            'qris' => $channels->where('channel_type', 'qris')->count(),
            'ewallet' => $channels->whereIn('channel_type', ['gopay', 'dana', 'ovo', 'linkaja', 'shopeepay'])->count(),
            'bank' => $channels->whereIn('channel_type', ['bank_transfer', 'virtual_account'])->count(),
        ];

        return view('tenant.payment-channels.index', compact('tenant', 'channels', 'stats'));
    }

    public function create()
    {
        $tenant = $this->getTenant();
        
        $masterChannels = \App\Models\MasterPaymentChannel::where('is_active', true)->orderBy('name')->get();

        return view('tenant.payment-channels.create', compact('tenant', 'masterChannels'));
    }

    public function store(Request $request)
    {
        $tenant = $this->getTenant();
        $plan = $tenant->plan;

        // Limit Enforcement
        if ($plan) {
            $currentCount = PaymentChannel::where('tenant_id', $tenant->id)->count();
            if ($currentCount >= $plan->max_channels) {
                return redirect()
                    ->route('tenant.payment-channels.index')
                    ->with('error', "Node Capacity Exceeded. Your plan limit is {$plan->max_channels} channels. Please upgrade your subscription matrix.");
            }
        }

        $validated = $request->validate([
            'master_code' => 'required|string',
            'channel_name' => 'required|string|max:255',
            'provider' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'merchant_id' => 'nullable|string|max:255',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'fee_percentage' => 'nullable|numeric|min:0|max:100',
            'fee_fixed' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $validated['tenant_id'] = $tenant->id;
        $validated['is_active'] = true;

        $masterChannel = \App\Models\MasterPaymentChannel::where('code', $validated['master_code'])->firstOrFail();
        $validated['channel_type'] = $masterChannel->type; // base type for logic
        $validated['provider'] = $masterChannel->code; // specific provider code
        unset($validated['master_code']);

        // Handle QR Code upload
        if ($request->hasFile('qr_code')) {
            $file = $request->file('qr_code');
            $path = $file->store('qr-codes', 'public');
            $validated['qr_code_path'] = $path;

            // Extract QRIS string from image
            if ($validated['channel_type'] === 'qris') {
                try {
                    $qrcode = new \Zxing\QrReader($file->getPathname());
                    $text = $qrcode->text();
                    if ($text) {
                        $validated['metadata'] = [
                            'qris_string' => $text
                        ];
                    }
                } catch (\Exception $e) {
                    // Fail silently, they can re-upload if it fails
                }
            }
        }

        PaymentChannel::create($validated);

        return redirect()
            ->route('tenant.payment-channels.index')
            ->with('success', 'Payment channel created successfully!');
    }

    public function edit(PaymentChannel $paymentChannel)
    {
        $tenant = $this->getTenant();
        
        // Ensure this channel belongs to the tenant
        if ($paymentChannel->tenant_id !== $tenant->id) {
            abort(403);
        }

        $masterChannels = \App\Models\MasterPaymentChannel::where('is_active', true)->orderBy('name')->get();

        return view('tenant.payment-channels.edit', compact('tenant', 'paymentChannel', 'masterChannels'));
    }

    public function update(Request $request, PaymentChannel $paymentChannel)
    {
        $tenant = $this->getTenant();
        
        if ($paymentChannel->tenant_id !== $tenant->id) {
            abort(403);
        }

        $validated = $request->validate([
            'channel_name' => 'required|string|max:255',
            'provider' => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'merchant_id' => 'nullable|string|max:255',
            'qr_code' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'fee_percentage' => 'nullable|numeric|min:0|max:100',
            'fee_fixed' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        // Handle QR Code upload
        if ($request->hasFile('qr_code')) {
            // Delete old QR code if exists
            if ($paymentChannel->qr_code_path) {
                Storage::disk('public')->delete($paymentChannel->qr_code_path);
            }
            $file = $request->file('qr_code');
            $path = $file->store('qr-codes', 'public');
            $validated['qr_code_path'] = $path;

            // Extract QRIS string from image
            if ($paymentChannel->channel_type === 'qris') {
                try {
                    $qrcode = new \Zxing\QrReader($file->getPathname());
                    $text = $qrcode->text();
                    if ($text) {
                        $metadata = is_array($paymentChannel->metadata) ? $paymentChannel->metadata : [];
                        $metadata['qris_string'] = $text;
                        $validated['metadata'] = $metadata;
                    }
                } catch (\Exception $e) {
                    // Fail silently
                }
            }
        }

        $paymentChannel->update($validated);

        return redirect()
            ->route('tenant.payment-channels.index')
            ->with('success', 'Payment channel updated successfully!');
    }

    public function destroy(PaymentChannel $paymentChannel)
    {
        $tenant = $this->getTenant();
        
        if ($paymentChannel->tenant_id !== $tenant->id) {
            abort(403);
        }

        // Delete QR code if exists
        if ($paymentChannel->qr_code_path) {
            Storage::disk('public')->delete($paymentChannel->qr_code_path);
        }

        $paymentChannel->delete();

        return redirect()
            ->route('tenant.payment-channels.index')
            ->with('success', 'Payment channel deleted successfully!');
    }

    public function toggle(PaymentChannel $paymentChannel)
    {
        $tenant = $this->getTenant();
        
        if ($paymentChannel->tenant_id !== $tenant->id) {
            abort(403);
        }

        $paymentChannel->is_active = !$paymentChannel->is_active;
        $paymentChannel->save();

        $status = $paymentChannel->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "Payment channel {$status} successfully!");
    }
}
