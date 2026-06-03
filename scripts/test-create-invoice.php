<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$invoice = \App\Models\Invoice::create([
    'customer_name' => 'PT Test Indonesia',
    'customer_email' => 'test@example.com',
    'customer_phone' => '08123456789',
    'description' => 'Testing PayHook Integration',
    'amount' => 100000
]);

echo "✅ Invoice Created!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Invoice Number  : " . $invoice->invoice_number . "\n";
echo "Customer        : " . $invoice->customer_name . "\n";
echo "Amount          : Rp " . number_format($invoice->amount, 0, ',', '.') . "\n";
echo "Unique Suffix   : +" . $invoice->unique_suffix . "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "💰 TOTAL TO PAY : Rp " . number_format($invoice->unique_amount, 0, ',', '.') . "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Status          : " . $invoice->status . "\n";
echo "Expires At      : " . $invoice->expires_at->format('d M Y H:i') . "\n";
