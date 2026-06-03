<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Invoice;
use App\Services\QrisService;

echo "=== TESTING STATIC QRIS WORKFLOW ===" . PHP_EOL . PHP_EOL;

// Create test invoice
$invoice = Invoice::create([
    'customer_name' => 'Test Customer',
    'customer_email' => 'test@example.com',
    'customer_phone' => '081234567890',
    'description' => 'Test Static QRIS Payment',
    'amount' => 10000,
]);

echo "✅ Invoice created:" . PHP_EOL;
echo "  Invoice Number: " . $invoice->invoice_number . PHP_EOL;
echo "  Base Amount: Rp " . number_format($invoice->amount, 0, ',', '.') . PHP_EOL;
echo "  Unique Suffix: " . $invoice->unique_suffix . PHP_EOL;
echo "  Unique Amount: Rp " . number_format($invoice->unique_amount, 0, ',', '.') . PHP_EOL;
echo PHP_EOL;

// Generate QRIS
$invoice->generateQris(2); // Dana template
$invoice->refresh();

echo "✅ QRIS Generated:" . PHP_EOL;
echo "  Template: " . $invoice->qrisTemplate->name . PHP_EOL;
echo "  QRIS Length: " . strlen($invoice->qris_string) . " chars" . PHP_EOL;
echo "  Has field 54 (amount): " . (strpos($invoice->qris_string, '5405') !== false ? 'YES ❌ (WRONG!)' : 'NO ✅ (CORRECT!)') . PHP_EOL;
echo PHP_EOL;

echo "=== KONSEP PEMBAYARAN ===" . PHP_EOL;
echo "1. Customer buka DANA/BCA/GoPay" . PHP_EOL;
echo "2. Scan QR code (QRIS STATIS - tanpa amount embedded)" . PHP_EOL;
echo "3. App minta 'Masukkan Nominal'" . PHP_EOL;
echo "4. Customer input: Rp " . number_format($invoice->unique_amount, 0, ',', '.') . PHP_EOL;
echo "5. Customer bayar → Notifikasi masuk ke HP" . PHP_EOL;
echo "6. PayHook baca notifikasi (amount: " . number_format($invoice->unique_amount, 0, ',', '.') . ")" . PHP_EOL;
echo "7. Webhook ke server: amount=" . $invoice->unique_amount . PHP_EOL;
echo "8. Laravel match invoice by unique_amount → Auto-confirm ✅" . PHP_EOL;
echo PHP_EOL;

echo "✅ View invoice: http://192.168.3.105:8000/invoices/" . $invoice->id . PHP_EOL;

// Cleanup
echo PHP_EOL;
echo "Deleting test invoice..." . PHP_EOL;
$invoice->delete();
echo "✅ Test completed!" . PHP_EOL;
