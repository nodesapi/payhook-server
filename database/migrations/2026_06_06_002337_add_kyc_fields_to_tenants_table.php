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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('kyc_status')->default('PENDING')->after('plan_id')->comment('PENDING, VERIFIED, REJECTED');
            $table->string('ktp_name')->nullable()->after('kyc_status');
            $table->string('ktp_number')->nullable()->after('ktp_name');
            $table->string('ktp_image_path')->nullable()->after('ktp_number');
            $table->text('kyc_reject_reason')->nullable()->after('ktp_image_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'kyc_status',
                'ktp_name',
                'ktp_number',
                'ktp_image_path',
                'kyc_reject_reason'
            ]);
        });
    }
};
