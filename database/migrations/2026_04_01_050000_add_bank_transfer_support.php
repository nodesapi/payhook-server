<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name'); // BCA, Mandiri, BRI, etc
            $table->string('account_number');
            $table->string('account_name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add bank_account_id to invoices
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('bank_account_id')->nullable()->after('qris_template_id');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('bank_account_id');
        });
        
        Schema::dropIfExists('bank_accounts');
    }
};
