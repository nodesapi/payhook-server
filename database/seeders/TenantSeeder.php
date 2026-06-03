<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Tenant;
use App\Models\QrisTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or update tenant user
        $user = User::updateOrCreate(
            ['email' => 'merchant@test.local'],
            [
                'name' => 'Test Merchant',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Create or update test tenant
        $tenant = Tenant::updateOrCreate(
            ['email' => 'merchant@test.local'],
            [
                'name' => 'Test Merchant',
                'webhook_url' => 'http://192.168.3.105:8000/api/payment-webhook',
                'webhook_secret' => 'test_webhook_secret_123',
                'is_active' => true,
                'mode' => 'sandbox',
                'settings' => json_encode([
                    'notification_email' => 'merchant@test.local',
                    'auto_confirm_payment' => true,
                ]),
            ]
        );

        // Generate API keys if not exist
        if (empty($tenant->api_key_sandbox)) {
            $tenant->api_key_sandbox = 'sk_test_' . \Illuminate\Support\Str::random(40);
        }
        if (empty($tenant->api_key_production)) {
            $tenant->api_key_production = 'sk_live_' . \Illuminate\Support\Str::random(40);
        }
        $tenant->save();

        // Create QRIS Template for GoPay Merchant (from previous successful test)
        $gopayQrisString = '00020101021126670016ID.CO.SHOPEE.WWW01189360091200003461540214952400000181830303UME51440014ID.CO.QRIS.WWW0215ID20232791044830303UME5204481253033605802ID5910SOLEH DEDI6013KOTA SUKABUMI61054315162390117ID.CO.SHOPEE.WWW20700205950480303UME0420240628172430430061106082024062863041C87';
        
        QrisTemplate::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'name' => 'GoPay Merchant',
            ],
            [
                'type' => 'ewallet',
                'qris_string' => $gopayQrisString,
                'account_name' => 'SOLEH DEDI',
                'account_number' => null,
                'is_active' => true,
            ]
        );

        $this->command->info('✅ Test tenant user and data created/updated successfully!');
        $this->command->info('👤 User: Test Merchant');
        $this->command->info('📧 Email: merchant@test.local');
        $this->command->info('🔑 Password: password');
        $this->command->info('🔐 API Key (Production): ' . $tenant->api_key_production);
        $this->command->info('🧪 API Key (Sandbox): ' . $tenant->api_key_sandbox);
        $this->command->info('🔒 Webhook Secret: test_webhook_secret_123');
        $this->command->info('💳 QRIS: GoPay Merchant (active)');
    }
}
