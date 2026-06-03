<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BankAccount;
use App\Models\Invoice;

echo "=== SETUP BANK TRANSFER SEBAGAI METODE UTAMA ===" . PHP_EOL . PHP_EOL;

// Check or create BCA account
$bca = BankAccount::where('bank_name', 'BCA')->first();

if (!$bca) {
    echo "Creating BCA bank account..." . PHP_EOL;
    $bca = BankAccount::create([
        'bank_name' => 'BCA',
        'account_number' => '1234567890', // GANTI dengan nomor rekening asli!
        'account_name' => 'WAHYU SUHANDI', // GANTI dengan nama asli!
        'is_active' => true,
    ]);
    echo "✅ BCA account created!" . PHP_EOL;
} else {
    echo "✅ BCA account already exists!" . PHP_EOL;
}

echo PHP_EOL;
echo "Bank Account Info:" . PHP_EOL;
echo "  Bank: " . $bca->bank_name . PHP_EOL;
echo "  Account Number: " . $bca->account_number . PHP_EOL;
echo "  Account Name: " . $bca->account_name . PHP_EOL;
echo PHP_EOL;

echo "⚠️  IMPORTANT: Edit nomor rekening!" . PHP_EOL;
echo "   php artisan tinker" . PHP_EOL;
echo "   >>> \$bca = App\\Models\\BankAccount::first();" . PHP_EOL;
echo "   >>> \$bca->update(['account_number' => 'NOMOR_ASLI', 'account_name' => 'NAMA_ASLI']);" . PHP_EOL;
echo PHP_EOL;

// Update existing invoices to use bank transfer
echo str_repeat("=", 60) . PHP_EOL;
echo "Updating invoices to use bank transfer..." . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

$invoices = Invoice::where('status', 'pending')
    ->whereNull('bank_account_id')
    ->get();

if ($invoices->isEmpty()) {
    echo "No invoices to update." . PHP_EOL;
} else {
    foreach ($invoices as $invoice) {
        $invoice->update(['bank_account_id' => $bca->id]);
        echo "✅ Invoice {$invoice->invoice_number} → Bank transfer enabled" . PHP_EOL;
    }
}

echo PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo "SISTEM BANK TRANSFER SUDAH AKTIF! ✅" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "KEUNTUNGAN BANK TRANSFER:" . PHP_EOL;
echo "  ✅ Tidak ada masalah QRIS expired" . PHP_EOL;
echo "  ✅ Tidak perlu generate QR berulang" . PHP_EOL;
echo "  ✅ Customer familiar (transfer bank biasa)" . PHP_EOL;
echo "  ✅ Bisa dari ATM, mobile, internet banking" . PHP_EOL;
echo "  ✅ Tetap auto-confirm via PayHook" . PHP_EOL;
echo "  ✅ Lebih reliable & stable" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "CARA KERJA:" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "1. Invoice menampilkan info rekening BCA + nominal unik" . PHP_EOL;
echo "2. Customer transfer dari BCA mobile/ATM" . PHP_EOL;
echo "3. Notifikasi masuk ke HP Anda" . PHP_EOL;
echo "4. PayHook baca notifikasi → kirim webhook" . PHP_EOL;
echo "5. Laravel match by amount → Auto-confirm ✅" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "TEST SEKARANG:" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "1. Buat invoice baru:" . PHP_EOL;
echo "   php create-invoice-10k.php" . PHP_EOL;
echo PHP_EOL;

echo "2. Buka invoice di browser" . PHP_EOL;
echo "   → Akan tampil info rekening BCA" . PHP_EOL;
echo "   → QRIS sebagai alternatif (jika ada)" . PHP_EOL;
echo PHP_EOL;

echo "3. Transfer dari BCA mobile ke rekening Anda" . PHP_EOL;
echo "   → Nominal persis sesuai yang tertera" . PHP_EOL;
echo PHP_EOL;

echo "4. Tunggu notifikasi → PayHook detect → Auto-confirm ✅" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "SOLUSI FINAL UNTUK MASALAH QRIS EXPIRED!" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
