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
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('qris_template_id')->nullable()->constrained()->onDelete('set null');
            $table->text('qris_string')->nullable(); // Generated dynamic QRIS
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['qris_template_id']);
            $table->dropColumn(['qris_template_id', 'qris_string']);
        });
    }
};
