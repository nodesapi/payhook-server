<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BankAccount;
use App\Models\Invoice;

echo "=== SETUP BANK TRANSFER BCA ===" . PHP_EOL . PHP_EOL;

// Check if BCA already exists
$bca = BankAccount::where('bank_name', 'BCA')->first();

if ($bca) {
    echo "✅ BCA account already exists:" . PHP_EOL;
    echo "  Account Number: " . $bca->account_number . PHP_EOL;
    echo "  Account Name: " . $bca->account_name . PHP_EOL;
    echo PHP_EOL;
} else {
    echo "Creating BCA account (please edit later)..." . PHP_EOL;
    
    $bca = BankAccount::create([
        'bank_name' => 'BCA',
        'account_number' => '1234567890', // ← EDIT INI!
        'account_name' => 'WAHYU SUHANDI', // ← EDIT INI!
        'is_active' => true,
    ]);
    
    echo "✅ BCA account created!" . PHP_EOL;
    echo "  Account Number: " . $bca->account_number . PHP_EOL;
    echo "  Account Name: " . $bca->account_name . PHP_EOL;
    echo PHP_EOL;
    
    echo "⚠️  IMPORTANT: Edit nomor rekening di:" . PHP_EOL;
    echo "  → Database: bank_accounts table" . PHP_EOL;
    echo "  → File: fix-invoices-qris.php (update account_number & account_name)" . PHP_EOL;
    echo PHP_EOL;
}

echo str_repeat("=", 60) . PHP_EOL;
echo "CONVERT INVOICE TO BANK TRANSFER" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

// Get invoice 4
$invoice = Invoice::find(4);

if ($invoice) {
    echo "Invoice: " . $invoice->invoice_number . PHP_EOL;
    echo "  Customer: " . $invoice->customer_name . PHP_EOL;
    echo "  Amount: Rp " . number_format($invoice->unique_amount, 0, ',', '.') . PHP_EOL;
    echo PHP_EOL;
    
    // Update to use bank transfer
    $invoice->update([
        'bank_account_id' => $bca->id,
        // Keep QRIS as fallback, but prioritize bank transfer
    ]);
    
    echo "✅ Invoice updated to support Bank Transfer!" . PHP_EOL;
    echo PHP_EOL;
}

echo str_repeat("=", 60) . PHP_EOL;
echo "CARA BAYAR VIA BANK TRANSFER:" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "1. Customer buka:" . PHP_EOL;
echo "   http://192.168.3.105:8000/invoices/4" . PHP_EOL;
echo PHP_EOL;

echo "2. Invoice menampilkan:" . PHP_EOL;
echo "   ┌──────────────────────────────────────┐" . PHP_EOL;
echo "   │ Transfer ke Rekening BCA:           │" . PHP_EOL;
echo "   │ Nomor: " . $bca->account_number . "                  │" . PHP_EOL;
echo "   │ Nama: " . str_pad($bca->account_name, 26) . " │" . PHP_EOL;
echo "   │                                      │" . PHP_EOL;
if ($invoice) {
echo "   │ Nominal: Rp " . str_pad(number_format($invoice->unique_amount, 0, ',', '.'), 23, ' ', STR_PAD_LEFT) . " │" . PHP_EOL;
}
echo "   │ (sudah termasuk kode unik)          │" . PHP_EOL;
echo "   └──────────────────────────────────────┘" . PHP_EOL;
echo PHP_EOL;

echo "3. Customer transfer via BCA mobile/ATM:" . PHP_EOL;
if ($invoice) {
echo "   → Transfer Rp " . number_format($invoice->unique_amount, 0, ',', '.') . " persis" . PHP_EOL;
}
echo "   → Ke rekening: " . $bca->account_number . PHP_EOL;
echo "   → A/N: " . $bca->account_name . PHP_EOL;
echo PHP_EOL;

echo "4. Notifikasi SMS/push masuk ke HP:" . PHP_EOL;
if ($invoice) {
echo '   "Transfer Rp' . number_format($invoice->unique_amount, 0, ',', '.') . ' ke REK ' . $bca->account_number . ' berhasil"' . PHP_EOL;
}
echo PHP_EOL;

echo "5. PayHook baca notifikasi → kirim webhook" . PHP_EOL;
if ($invoice) {
echo "   → Laravel match by amount: " . $invoice->unique_amount . PHP_EOL;
}
echo "   → Invoice auto-confirmed ✅" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "KEUNTUNGAN BANK TRANSFER:" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "✅ Tidak perlu scan QR (no camera needed)" . PHP_EOL;
echo "✅ Customer familiar dengan transfer bank" . PHP_EOL;
echo "✅ Work 100% dengan BCA mobile" . PHP_EOL;
echo "✅ Tetap auto-confirm seperti QRIS" . PHP_EOL;
echo "✅ Lebih reliable (no QRIS compatibility issue)" . PHP_EOL;
echo PHP_EOL;
