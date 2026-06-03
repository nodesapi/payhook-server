<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MasterPaymentChannelController extends Controller
{
    public function index()
    {
        $channels = \App\Models\MasterPaymentChannel::orderBy('name')->get();
        return view('admin.master-channels.index', compact('channels'));
    }

    public function create()
    {
        return view('admin.master-channels.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:master_payment_channels',
            'name' => 'required|string|max:255',
            'type' => 'required|in:qris,ewallet,virtual_account,bank_transfer',
            'logo' => 'nullable|image|max:2048', // max 2MB
            'is_active' => 'boolean',
        ]);

        $data = [
            'code' => $validated['code'],
            'name' => $validated['name'],
            'type' => $validated['type'],
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('payment-logos', 'public');
        }

        \App\Models\MasterPaymentChannel::create($data);

        return redirect()->route('admin.master-channels.index')
            ->with('success', 'Master Payment Channel created successfully.');
    }

    public function edit($id)
    {
        $channel = \App\Models\MasterPaymentChannel::findOrFail($id);
        return view('admin.master-channels.edit', compact('channel'));
    }

    public function update(Request $request, $id)
    {
        $channel = \App\Models\MasterPaymentChannel::findOrFail($id);
        
        $validated = $request->validate([
            'code' => 'required|string|unique:master_payment_channels,code,' . $id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:qris,ewallet,virtual_account,bank_transfer',
            'logo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        $channel->code = $validated['code'];
        $channel->name = $validated['name'];
        $channel->type = $validated['type'];
        $channel->is_active = $request->has('is_active');

        if ($request->hasFile('logo')) {
            if ($channel->logo_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($channel->logo_path);
            }
            $channel->logo_path = $request->file('logo')->store('payment-logos', 'public');
        }

        $channel->save();

        return redirect()->route('admin.master-channels.index')
            ->with('success', 'Master Payment Channel updated successfully.');
    }

    public function destroy($id)
    {
        $channel = \App\Models\MasterPaymentChannel::findOrFail($id);
        if ($channel->logo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($channel->logo_path);
        }
        $channel->delete();

        return redirect()->route('admin.master-channels.index')
            ->with('success', 'Master Payment Channel deleted successfully.');
    }
}
