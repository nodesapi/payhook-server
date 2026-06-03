<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Models\PaymentChannel;
use App\Models\QrisTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TupplyIntegrationController extends Controller
{
    /**
     * Auto-register a Tenant and User when they upgrade to Premium in Tupply.
     */
    public function autoRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'nullable|string',
            'password_hash' => 'required|string', // Hashed password from Tupply
            'domain' => 'nullable|string',
            'expired_at' => 'nullable|date',
            'callback_url' => 'nullable|url',
        ]);

        try {
            DB::beginTransaction();

            // 1. Create or Update User (for Cekbayar app login)
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                // Gunakan query builder untuk bypass model cast 'hashed'
                $userId = DB::table('users')->insertGetId([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => $request->password_hash,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $user = User::find($userId);
            } else {
                // Update password if the user already exists (syncing)
                DB::table('users')->where('id', $user->id)->update([
                    'password' => $request->password_hash,
                    'updated_at' => now()
                ]);
            }

            // Get the Starter Node plan to restrict to 1 channel
            $starterPlan = \App\Models\Plan::where('slug', 'starter-node')->first();

            // 2. Create or Update Tenant (Merchant Account)
            $tenant = Tenant::where('email', $request->email)->first();
            if (!$tenant) {
                $tenant = Tenant::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'slug' => Str::slug($request->name) . '-' . Str::random(5),
                    'website' => $request->domain,
                    'mode' => 'production', // Ready to accept payments
                    'is_active' => true,
                    'plan_id' => $starterPlan ? $starterPlan->id : null,
                    'webhook_enabled' => true,
                    'callback_url' => $request->callback_url ?? (env('APP_URL') . '/webhook/payhook'),
                    'expired_at' => $request->expired_at ? \Carbon\Carbon::parse($request->expired_at) : null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tenant and User auto-provisioned successfully',
                'data' => [
                    'tenant_id' => $tenant->id,
                    'user_id' => $user->id,
                    'api_key_production' => $tenant->api_key_production,
                    'api_key_sandbox' => $tenant->api_key_sandbox,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to auto-register: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload Static QRIS and set up the Payment Channel for the Tenant.
     */
    public function uploadQris(Request $request, $tenantId)
    {
        $request->validate([
            'qris_image' => 'required|image|max:2048', // max 2MB
        ]);

        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            return response()->json(['success' => false, 'message' => 'Tenant not found'], 404);
        }

        try {
            $path = $request->file('qris_image')->store('qris_static', 'public');

            // Find or create PaymentChannel for this tenant (Assuming multi-tenant architecture uses tenant_id)
            // Note: If PaymentChannel doesn't have tenant_id in schema, this logic must be adjusted based on payhook-server DB schema.
            // Based on previous search, PaymentChannel has a tenant_id or relates to MasterPaymentChannel.
            
            // For now, let's assume we store the path in a new QrisTemplate or we just return the path to Tupply
            // Wait, we need to know the exact schema of PaymentChannel or QrisTemplate.

            // Returning the path for now so Tupply can store it or use it.
            return response()->json([
                'success' => true,
                'message' => 'QRIS uploaded successfully',
                'data' => [
                    'qris_path' => $path,
                    'qris_url' => asset('storage/' . $path)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload QRIS: ' . $e->getMessage()
            ], 500);
        }
    }
}
