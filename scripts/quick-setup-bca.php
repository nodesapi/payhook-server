<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BankAccount;

echo "=== QUICK SETUP: BCA BANK TRANSFER ===" . PHP_EOL . PHP_EOL;

// Create default BCA account (you can edit later)
$bankAccount =BankAccount::create([
    'bank_name' => 'BCA',
    'account_number' => '1234567890', // ← GANTI dengan nomor rekening BCA Anda
    'account_name' => 'WAHYU SUHANDI', // ← GANTI dengan nama di rekening
    'is_active' => true,
]);

echo "✅ Rekening BCA ditambahkan!" . PHP_EOL;
echo PHP_EOL;

echo "Bank info:" . PHP_EOL;
echo "  Bank: BCA" . PHP_EOL;
echo "  No. Rek: 1234567890" . PHP_EOL;
echo "  Nama: WAHYU SUHANDI" . PHP_EOL;
echo PHP_EOL;

echo "⚠️  PENTING: Edit nomor rekening di database!" . PHP_EOL;
echo "  → http://192.168.3.105:8000 (buat halaman bank accounts)" . PHP_EOL;
echo "  → Atau edit langsung database: bank_accounts table" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "CARA PAKAI:" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "1. Invoice akan menampilkan:" . PHP_EOL;
echo "   ┌─────────────────────────────────────┐" . PHP_EOL;
echo "   │ Transfer ke Rekening BCA:          │" . PHP_EOL;
echo "   │ Nomor: 1234567890                  │" . PHP_EOL;
echo "   │ Nama: WAHYU SUHANDI                │" . PHP_EOL;
echo "   │                                     │" . PHP_EOL;
echo "   │ Nominal: Rp 5.518                  │" . PHP_EOL;
echo "   │ (sudah termasuk kode unik)         │" . PHP_EOL;
echo "   └─────────────────────────────────────┘" . PHP_EOL;
echo PHP_EOL;

echo "2. Customer transfer dari BCA mobile/ATM" . PHP_EOL;
echo "   → Transfer Rp 5.518 persis" . PHP_EOL;
echo PHP_EOL;

echo "3. Notifikasi SMS masuk ke HP:" . PHP_EOL;
echo '   "Transfer Rp5.518 ke REK 1234567890 berhasil"' . PHP_EOL;
echo PHP_EOL;

echo "4. PayHook baca SMS → kirim webhook" . PHP_EOL;
echo "   → Laravel match by amount 5518.00" . PHP_EOL;
echo "   → Invoice auto-confirmed ✅" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "LEBIH MUDAH dari QRIS!" . PHP_EOL;
echo "- Tidak perlu scan QR" . PHP_EOL;
echo "- Langsung transfer dari BCA mobile" . PHP_EOL;
echo "- Tetap auto-confirm sama seperti QRIS" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
