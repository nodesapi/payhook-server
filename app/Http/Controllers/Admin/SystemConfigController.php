<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class SystemConfigController extends Controller
{
    public function index()
    {
        $masterTenant = Tenant::where('is_master', true)->with(['paymentChannels', 'qrisTemplates'])->first();
        
        $envPath = base_path('.env');
        $envContent = File::exists($envPath) ? File::get($envPath) : '';
        
        $mailConfig = [
            'MAIL_HOST' => env('MAIL_HOST', 'mail.cekbayar.com'),
            'MAIL_PORT' => env('MAIL_PORT', '465'),
            'MAIL_USERNAME' => env('MAIL_USERNAME', 'finance@cekbayar.com'),
            'MAIL_PASSWORD' => env('MAIL_PASSWORD', ''),
            'MAIL_ENCRYPTION' => env('MAIL_ENCRYPTION', 'ssl'),
            'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS', 'finance@cekbayar.com'),
        ];

        $bankChannels = \App\Models\MasterPaymentChannel::where('type', 'bank_transfer')->where('is_active', true)->get();
        $ewalletChannels = \App\Models\MasterPaymentChannel::where('type', 'ewallet')->where('is_active', true)->get();
        $qrisChannels = \App\Models\MasterPaymentChannel::where('type', 'qris')->where('is_active', true)->get();

        $bankAccounts = [];
        $ewalletAccounts = [];
        if ($masterTenant) {
            $allAccounts = \App\Models\BankAccount::where('tenant_id', $masterTenant->id)->get();
            foreach ($allAccounts as $acc) {
                if ($bankChannels->contains('name', $acc->bank_name)) {
                    $bankAccounts[] = $acc;
                } elseif ($ewalletChannels->contains('name', $acc->bank_name)) {
                    $ewalletAccounts[] = $acc;
                } else {
                    $bankAccounts[] = $acc; // default to bank if unknown
                }
            }
        }

        return view('admin.settings.index', compact('masterTenant', 'mailConfig', 'bankChannels', 'ewalletChannels', 'qrisChannels', 'bankAccounts', 'ewalletAccounts'));
    }

    public function initializeBilling(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8',
        ]);

        $email = 'finance@cekbayar.com';
        
        // Cek apakah user sudah ada
        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Payhook Finance',
                'email' => $email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(),
            ]);
        } else {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        // Cek apakah tenant sudah ada
        $tenant = Tenant::where('email', $email)->first();
        if (!$tenant) {
            $tenant = Tenant::create([
                'name' => 'Payhook Global Billing',
                'email' => $email,
                'slug' => 'payhook-finance',
                'is_master' => true,
                'is_active' => true,
                'kyc_status' => 'VERIFIED', // Auto bypass KYC
                'mode' => 'production',
            ]);
        } else {
            $tenant->update([
                'is_master' => true,
                'is_active' => true,
                'kyc_status' => 'VERIFIED',
            ]);
        }

        // Matikan master untuk tenant lain
        Tenant::where('id', '!=', $tenant->id)->update(['is_master' => false]);

        return back()->with('success', 'Akun Master Billing berhasil diinisialisasi! Gunakan email dan password tersebut untuk login ke Aplikasi Android Relay.');
    }

    public function updateSmtp(Request $request)
    {
        $validated = $request->validate([
            'MAIL_HOST' => 'required|string',
            'MAIL_PORT' => 'required|string',
            'MAIL_USERNAME' => 'required|string',
            'MAIL_PASSWORD' => 'nullable|string',
            'MAIL_ENCRYPTION' => 'nullable|string',
            'MAIL_FROM_ADDRESS' => 'required|email',
        ]);

        $envPath = base_path('.env');
        
        if (File::exists($envPath)) {
            $content = File::get($envPath);
            
            foreach ($validated as $key => $value) {
                // Ignore empty password if it's already set in env
                if ($key === 'MAIL_PASSWORD' && empty($value)) {
                    continue;
                }
                
                $value = preg_replace('/\s+/', '', $value); // hapus spasi
                $pattern = "/^" . $key . "=(.*)/m";
                $replacement = $key . "=" . $value;

                if (preg_match($pattern, $content)) {
                    $content = preg_replace($pattern, $replacement, $content);
                } else {
                    $content .= "\n" . $replacement;
                }
            }
            
            // Perbaiki kutipan untuk MAIL_FROM_NAME
            $patternFromName = "/^MAIL_FROM_NAME=(.*)/m";
            $appName = env('APP_NAME', 'Payhook');
            if (preg_match($patternFromName, $content)) {
                $content = preg_replace($patternFromName, 'MAIL_FROM_NAME="${APP_NAME} Finance"', $content);
            } else {
                $content .= "\nMAIL_FROM_NAME=\"\${APP_NAME} Finance\"";
            }

            File::put($envPath, $content);
            
            // Clear config cache
            Artisan::call('optimize:clear');
            
            return back()->with('success', 'Konfigurasi Email SMTP berhasil diperbarui!');
        }

        return back()->with('error', 'File .env tidak ditemukan.');
    }

    public function storeQris(Request $request)
    {
        $request->validate([
            'qris_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'name' => 'required|string|max:255',
        ]);

        $masterTenant = Tenant::where('is_master', true)->firstOrFail();

        $path = $request->file('qris_image')->store('qris-templates', 'public');

        \App\Models\QrisTemplate::create([
            'tenant_id' => $masterTenant->id,
            'name' => $request->name,
            'image_path' => $path,
            'is_active' => true,
        ]);

        // Auto create or update a QRIS payment channel
        $masterTenant->paymentChannels()->updateOrCreate(
            ['channel_type' => 'qris'],
            [
                'channel_name' => 'QRIS ' . $request->name,
                'provider' => 'qris',
                'is_active' => true,
                'fee_percentage' => 0,
                'fee_fixed' => 0,
            ]
        );

        return back()->with('success', 'QRIS Template berhasil ditambahkan.');
    }

    public function deleteQris($id)
    {
        $template = \App\Models\QrisTemplate::findOrFail($id);
        
        if ($template->image_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($template->image_path);
        }
        
        $template->delete();

        return back()->with('success', 'QRIS Template dihapus.');
    }

    public function storeBank(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
        ]);

        $masterTenant = Tenant::where('is_master', true)->firstOrFail();

        \App\Models\BankAccount::create([
            'tenant_id' => $masterTenant->id,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name' => $request->account_name,
            'is_active' => true,
        ]);

        // Auto create or update a Bank payment channel
        $masterTenant->paymentChannels()->updateOrCreate(
            ['channel_type' => 'bank_transfer', 'provider' => $request->bank_name],
            [
                'channel_name' => 'Transfer ' . $request->bank_name,
                'is_active' => true,
                'fee_percentage' => 0,
                'fee_fixed' => 0,
            ]
        );

        return back()->with('success', 'Rekening Bank berhasil ditambahkan.');
    }

    public function deleteBank($id)
    {
        $bank = \App\Models\BankAccount::findOrFail($id);
        $bank->delete();

        return back()->with('success', 'Rekening Bank dihapus.');
    }

    public function storeEwallet(Request $request)
    {
        $request->validate([
            'ewallet_name' => 'required|string|max:100',
            'phone_number' => 'required|string|max:50',
            'account_name' => 'required|string|max:100',
        ]);

        $masterTenant = Tenant::where('is_master', true)->firstOrFail();

        \App\Models\BankAccount::create([
            'tenant_id' => $masterTenant->id,
            'bank_name' => $request->ewallet_name,
            'account_number' => $request->phone_number,
            'account_name' => $request->account_name,
            'is_active' => true,
        ]);

        // Auto create or update an E-Wallet payment channel
        $masterTenant->paymentChannels()->updateOrCreate(
            ['channel_type' => 'ewallet', 'provider' => $request->ewallet_name],
            [
                'channel_name' => 'E-Wallet ' . $request->ewallet_name,
                'is_active' => true,
                'fee_percentage' => 0,
                'fee_fixed' => 0,
            ]
        );

        return back()->with('success', 'E-Wallet berhasil ditambahkan.');
    }
}
