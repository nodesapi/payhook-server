<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            
            // Channel Info
            $table->enum('channel_type', [
                'qris',
                'gopay',
                'dana',
                'ovo',
                'linkaja',
                'shopeepay',
                'bank_transfer',
                'virtual_account'
            ]);
            $table->string('channel_name'); // Custom name: "BCA Virtual Account", "QRIS Toko A"
            $table->string('provider')->nullable(); // BCA, Mandiri, BRI, etc for bank
            
            // Account Details
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->string('merchant_id')->nullable(); // For e-wallet
            
            // QR Code
            $table->string('qr_code_path')->nullable(); // Storage path
            
            // Settings
            $table->boolean('is_active')->default(true);
            $table->decimal('fee_percentage', 5, 2)->default(0); // Fee %
            $table->decimal('fee_fixed', 15, 2)->default(0); // Fixed fee
            $table->text('description')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable(); // Additional settings
            
            $table->timestamps();
            
            // Indexes
            $table->index(['tenant_id', 'is_active']);
            $table->index('channel_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_channels');
    }
};
