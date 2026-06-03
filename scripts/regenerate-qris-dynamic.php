<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Invoice;

echo "=== REGENERATE QRIS WITH AMOUNT EMBEDDED ===" . PHP_EOL . PHP_EOL;

// Get invoice 4
$invoice = Invoice::find(4);

if (!$invoice) {
    echo "❌ Invoice not found!" . PHP_EOL;
    exit;
}

echo "Current Invoice:" . PHP_EOL;
echo "  Invoice Number: " . $invoice->invoice_number . PHP_EOL;
echo "  Customer: " . $invoice->customer_name . PHP_EOL;
echo "  Unique Amount: Rp " . number_format($invoice->unique_amount, 0, ',', '.') . PHP_EOL;
echo PHP_EOL;

echo "Old QRIS:" . PHP_EOL;
echo "  Length: " . strlen($invoice->qris_string) . " chars" . PHP_EOL;
echo "  Has field 54: " . (strpos($invoice->qris_string, '540') !== false && strpos($invoice->qris_string, '5405') === false ? 'NO (Static)' : 'YES (Dynamic)') . PHP_EOL;
echo PHP_EOL;

// Regenerate QRIS with amount
echo "Regenerating QRIS with amount embedded..." . PHP_EOL;
$invoice->generateQris($invoice->qris_template_id);
$invoice->refresh();

echo "✅ QRIS regenerated!" . PHP_EOL;
echo PHP_EOL;

echo "New QRIS:" . PHP_EOL;
echo "  Length: " . strlen($invoice->qris_string) . " chars" . PHP_EOL;

// Check for amount field
$amountPos = strpos($invoice->qris_string, '5405');
if ($amountPos !== false) {
    $amountField = substr($invoice->qris_string, $amountPos, 10);
    echo "  Field 54 (Amount): " . $amountField . " ✅" . PHP_EOL;
    
    // Extract amount value
    $length = intval(substr($amountField, 2, 2));
    $amountValue = intval(substr($amountField, 4, $length));
    echo "  Embedded Amount: Rp " . number_format($amountValue, 0, ',', '.') . PHP_EOL;
    
    if ($amountValue == $invoice->unique_amount) {
        echo "  ✅ Amount MATCH!" . PHP_EOL;
    } else {
        echo "  ⚠️  Amount MISMATCH! Expected: " . $invoice->unique_amount . PHP_EOL;
    }
} else {
    echo "  ⚠️  No amount field (still static)" . PHP_EOL;
}

echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "TEST QR CODE:" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "1. Buka invoice di browser:" . PHP_EOL;
echo "   http://192.168.3.105:8000/invoices/4" . PHP_EOL;
echo PHP_EOL;

echo "2. Scan QR Code dengan DANA/BCA/GoPay" . PHP_EOL;
echo "   → Nominal Rp " . number_format($invoice->unique_amount, 0, ',', '.') . " SUDAH MUNCUL OTOMATIS ✅" . PHP_EOL;
echo "   → Customer TIDAK perlu input manual" . PHP_EOL;
echo PHP_EOL;

echo "3. Klik 'Bayar' → Notifikasi masuk" . PHP_EOL;
echo "   → PayHook detect → Auto-confirm ✅" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "⚠️  CATATAN:" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "Jika masih gagal scan dengan BCA mobile:" . PHP_EOL;
echo "  1. QRIS DANA mungkin expired → Generate fresh dari app DANA" . PHP_EOL;
echo "  2. BCA mobile tidak support QRIS DANA → Pakai e-wallet DANA/GoPay" . PHP_EOL;
echo "  3. Alternatif: Pakai Bank Transfer BCA (sudah di-setup)" . PHP_EOL;
echo PHP_EOL;
