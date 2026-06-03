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
        Schema::create('qris_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // BCA, DANA, GoPay, etc
            $table->string('type'); // bank, ewallet
            $table->text('qris_string'); // Raw QRIS string from QR code
            $table->string('account_name')->nullable();
            $table->string('account_number')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qris_templates');
    }
};
