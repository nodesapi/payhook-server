<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_channel_id')->nullable()->constrained()->onDelete('set null');
            
            // Transaction Info
            $table->string('transaction_id')->unique(); // PHK-xxxxx
            $table->string('external_id')->nullable(); // Merchant's order ID
            
            // Amount
            $table->decimal('amount', 15, 2);
            $table->decimal('fee', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2); // amount - fee
            
            // Status
            $table->enum('status', [
                'pending',
                'processing',
                'success',
                'failed',
                'expired',
                'refund'
            ])->default('pending');
            
            // Customer Info
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            
            // Payment Details
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable(); // Bank ref, e-wallet trx id
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            
            // Webhook
            $table->boolean('webhook_sent')->default(false);
            $table->timestamp('webhook_sent_at')->nullable();
            $table->integer('webhook_attempts')->default(0);
            $table->text('webhook_response')->nullable();
            
            // Metadata
            $table->json('metadata')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['tenant_id', 'status']);
            $table->index('transaction_id');
            $table->index('external_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
