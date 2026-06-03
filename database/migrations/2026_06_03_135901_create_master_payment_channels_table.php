<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_payment_channels', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. qris, dana, bca_va
            $table->string('name'); // e.g. QRIS, DANA, BCA Virtual Account
            $table->string('type'); // e.g. qris, ewallet, virtual_account, bank_transfer
            $table->string('logo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_payment_channels');
    }
};
