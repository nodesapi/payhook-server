<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('amount', 12, 2);
            $table->string('source');
            $table->string('package_name');
            $table->string('notification_title')->nullable();
            $table->text('notification_text')->nullable();
            $table->timestamp('notification_timestamp')->nullable();
            $table->enum('status', ['matched', 'no_match', 'ambiguous'])->default('no_match');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index('amount');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_logs');
    }
};
