<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Invoice;

echo "=== COMPARING WORKING vs NOT WORKING QRIS ===" . PHP_EOL . PHP_EOL;

// Invoice 5 (yang berhasil)
$invoice5 = Invoice::find(5);

echo "Invoice 5 (WORKING - berhasil di BCA):" . PHP_EOL;
echo "  Invoice: " . $invoice5->invoice_number . PHP_EOL;
echo "  Amount: Rp " . number_format($invoice5->unique_amount, 0, ',', '.') . PHP_EOL;
echo "  QRIS Length: " . strlen($invoice5->qris_string) . " chars" . PHP_EOL;
echo PHP_EOL;

echo "QRIS String:" . PHP_EOL;
echo $invoice5->qris_string . PHP_EOL;
echo PHP_EOL;

// Check amount field
$amountPos5 = strpos($invoice5->qris_string, '5405');
if ($amountPos5 !== false) {
    $field5 = substr($invoice5->qris_string, $amountPos5, 11);
    echo "Field 54: " . $field5 . PHP_EOL;
}

// Check CRC
$crc5 = substr($invoice5->qris_string, -4);
echo "CRC: " . $crc5 . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

// Cari invoice terbaru
$latestInvoice = Invoice::latest()->where('id', '!=', 5)->first();

if ($latestInvoice) {
    echo "Invoice Terbaru (yang gagal scan):" . PHP_EOL;
    echo "  Invoice: " . $latestInvoice->invoice_number . PHP_EOL;
    echo "  Amount: Rp " . number_format($latestInvoice->unique_amount, 0, ',', '.') . PHP_EOL;
    echo "  QRIS Length: " . strlen($latestInvoice->qris_string) . " chars" . PHP_EOL;
    echo PHP_EOL;
    
    echo "QRIS String:" . PHP_EOL;
    echo $latestInvoice->qris_string . PHP_EOL;
    echo PHP_EOL;
    
    // Check amount field
    $amountPosLatest = strpos($latestInvoice->qris_string, '5405');
    if ($amountPosLatest !== false) {
        $fieldLatest = substr($latestInvoice->qris_string, $amountPosLatest, 11);
        echo "Field 54: " . $fieldLatest . PHP_EOL;
    }
    
    // Check CRC
    $crcLatest = substr($latestInvoice->qris_string, -4);
    echo "CRC: " . $crcLatest . PHP_EOL;
    echo PHP_EOL;
    
    echo str_repeat("=", 60) . PHP_EOL;
    echo "COMPARISON:" . PHP_EOL;
    echo str_repeat("=", 60) . PHP_EOL;
    echo PHP_EOL;
    
    if ($invoice5->qris_string === $latestInvoice->qris_string) {
        echo "⚠️  QRIS strings IDENTICAL (both using same base QR)" . PHP_EOL;
        echo "Ini normal kalau belum regenerate dengan amount berbeda" . PHP_EOL;
    } else {
        echo "✅ QRIS strings DIFFERENT (each has unique amount)" . PHP_EOL;
    }
    
    if (strlen($invoice5->qris_string) !== strlen($latestInvoice->qris_string)) {
        echo "⚠️  Length DIFFERENT:" . PHP_EOL;
        echo "    Working: " . strlen($invoice5->qris_string) . " chars" . PHP_EOL;
        echo "    Latest: " . strlen($latestInvoice->qris_string) . " chars" . PHP_EOL;
    } else {
        echo "✅ Length SAME: " . strlen($invoice5->qris_string) . " chars" . PHP_EOL;
    }
}

echo PHP_EOL;
echo "Kemungkinan masalah:" . PHP_EOL;
echo "1. QRIS template berubah/corrupt" . PHP_EOL;
echo "2. QRIS generation logic ada bug" . PHP_EOL;
echo "3. Invoice baru pakai template yang berbeda" . PHP_EOL;
echo PHP_EOL;

echo "Solusi: Pakai QRIS yang sama dengan invoice 5 (yang working)" . PHP_EOL;
