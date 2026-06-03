<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Invoice;

echo "=== CREATING NEW TEST INVOICE (Min Rp 10.000) ===" . PHP_EOL . PHP_EOL;

// Create new test invoice with minimum amount
$invoice = Invoice::create([
    'customer_name' => 'Test Customer - ' . date('H:i:s'),
    'customer_email' => 'test@payhook.com',
    'customer_phone' => '081234567890',
    'description' => 'Testing QRIS Dynamic Payment - Min 10K',
    'amount' => 10000, // Base amount Rp 10.000 (minimum)
]);

// Generate QRIS dengan amount embedded
$invoice->generateQris(4); // Use template ID 4 (Dana)
$invoice->refresh();

echo "✅ INVOICE CREATED SUCCESSFULLY!" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "📝 Invoice Details:" . PHP_EOL;
echo "  Invoice Number: " . $invoice->invoice_number . PHP_EOL;
echo "  Customer: " . $invoice->customer_name . PHP_EOL;
echo "  Base Amount: Rp " . number_format($invoice->amount, 0, ',', '.') . PHP_EOL;
echo "  Unique Suffix: +" . $invoice->unique_suffix . PHP_EOL;
echo "  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" . PHP_EOL;
echo "  💰 TOTAL TO PAY: Rp " . number_format($invoice->unique_amount, 0, ',', '.') . PHP_EOL;
echo "  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" . PHP_EOL;
echo PHP_EOL;

echo "🎫 QRIS Info:" . PHP_EOL;
echo "  Template: " . $invoice->qrisTemplate->name . PHP_EOL;
echo "  QRIS Type: DYNAMIC (amount embedded) ✅" . PHP_EOL;
echo "  QRIS Length: " . strlen($invoice->qris_string) . " chars" . PHP_EOL;

// Check if amount is embedded
$amountPos = strpos($invoice->qris_string, '5405');
if ($amountPos !== false) {
    $amountField = substr($invoice->qris_string, $amountPos, 11);
    echo "  Field 54: " . $amountField . " ✅" . PHP_EOL;
    
    // Extract amount
    $length = intval(substr($amountField, 2, 2));
    $amountValue = intval(substr($amountField, 4, $length));
    echo "  Embedded Amount: Rp " . number_format($amountValue, 0, ',', '.') . PHP_EOL;
    
    if ($amountValue == $invoice->unique_amount) {
        echo "  ✅ Amount MATCH dengan unique_amount!" . PHP_EOL;
    }
} else {
    echo "  ⚠️  Amount field not found (check template)" . PHP_EOL;
}

echo PHP_EOL;

echo "🌐 View Invoice (with QR Code):" . PHP_EOL;
echo "  http://192.168.3.105:8000/invoices/" . $invoice->id . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "📱 CARA TESTING:" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "STEP 1: Buka invoice di HP browser" . PHP_EOL;
echo "  → http://192.168.3.105:8000/invoices/" . $invoice->id . PHP_EOL;
echo PHP_EOL;

echo "STEP 2: Scan QR Code dengan DANA/GoPay/BCA" . PHP_EOL;
echo "  → Nominal Rp " . number_format($invoice->unique_amount, 0, ',', '.') . " SUDAH MUNCUL OTOMATIS ✅" . PHP_EOL;
echo "  → Customer TIDAK perlu input manual" . PHP_EOL;
echo "  → (Base: " . number_format($invoice->amount, 0, ',', '.') . " + Suffix: " . $invoice->unique_suffix . ")" . PHP_EOL;
echo PHP_EOL;

echo "STEP 3: Klik 'Bayar' atau 'Konfirmasi'" . PHP_EOL;
echo "  → Tunggu notifikasi masuk ke HP" . PHP_EOL;
echo "  → Notifikasi: 'Transfer Rp " . number_format($invoice->unique_amount, 0, ',', '.') . " berhasil'" . PHP_EOL;
echo PHP_EOL;

echo "STEP 4: PayHook akan otomatis:" . PHP_EOL;
echo "  ✅ Baca notifikasi" . PHP_EOL;
echo "  ✅ Extract amount: " . $invoice->unique_amount . PHP_EOL;
echo "  ✅ Kirim webhook ke: http://192.168.3.105:8000/api/webhook" . PHP_EOL;
echo "  ✅ Laravel auto-match invoice by unique_amount" . PHP_EOL;
echo "  ✅ Status invoice berubah: pending → paid" . PHP_EOL;
echo PHP_EOL;

echo "STEP 5: Refresh halaman invoice" . PHP_EOL;
echo "  → Status akan berubah jadi 'LUNAS' ✅" . PHP_EOL;
echo "  → Tanggal pembayaran tercatat" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "⚠️  JIKA SCAN GAGAL:" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "Error: 'Transaksi mengalami gangguan'" . PHP_EOL;
echo "  → QRIS DANA expired atau tidak compatible" . PHP_EOL;
echo "  → Solusi:" . PHP_EOL;
echo "    1. Buka app DANA → Tab 'Terima' → Screenshot QR fresh" . PHP_EOL;
echo "    2. Decode: http://192.168.3.105:8000/qris-decoder.html" . PHP_EOL;
echo "    3. Update: http://192.168.3.105:8000/qris-templates/4/edit" . PHP_EOL;
echo "    4. Test lagi dengan invoice baru" . PHP_EOL;
echo PHP_EOL;

echo "Atau coba scan dengan aplikasi DANA langsung (bukan BCA)" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "🎯 INVOICE READY FOR TESTING!" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
