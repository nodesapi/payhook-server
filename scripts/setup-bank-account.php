<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BankAccount;

echo "=== SETUP REKENING BANK ===" . PHP_EOL . PHP_EOL;

// Check if already exists
$existing = BankAccount::where('bank_name', 'BCA')->first();

if ($existing) {
    echo "⚠️  BCA account already exists:" . PHP_EOL;
    echo "  Bank: " . $existing->bank_name . PHP_EOL;
    echo "  Account: " . $existing->account_number . PHP_EOL;
    echo "  Name: " . $existing->account_name . PHP_EOL;
    echo PHP_EOL;
    echo "Skip creating...use existing." . PHP_EOL;
    exit;
}

echo "Masukkan data rekening BCA Anda:" . PHP_EOL;
echo str_repeat("-", 60) . PHP_EOL;
echo PHP_EOL;

// For demo / example:
$accountNumber = readline("Nomor Rekening BCA: ");
$accountName = readline("Nama di Rekening: ");

if (empty($accountNumber) || empty($accountName)) {
    echo PHP_EOL;
    echo "❌ Data tidak lengkap! Silakan jalankan ulang." . PHP_EOL;
    exit;
}

$bankAccount = BankAccount::create([
    'bank_name' => 'BCA',
    'account_number' => $accountNumber,
    'account_name' => strtoupper($accountName),
    'is_active' => true,
]);

echo PHP_EOL;
echo "✅ Rekening BCA berhasil ditambahkan!" . PHP_EOL;
echo PHP_EOL;

echo "Bank Account Details:" . PHP_EOL;
echo "  ID: " . $bankAccount->id . PHP_EOL;
echo "  Bank: " . $bankAccount->bank_name . PHP_EOL;
echo "  Account: " . $bankAccount->account_number . PHP_EOL;
echo "  Name: " . $bankAccount->account_name . PHP_EOL;
echo "  Status: " . ($bankAccount->is_active ? 'Active ✅' : 'Inactive') . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "NEXT STEPS:" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "1. Update Invoice Model untuk support bank transfer" . PHP_EOL;
echo "2. Tambah dropdown pilih 'QRIS' atau 'Bank Transfer'" . PHP_EOL;
echo "3. Invoice akan tamp ilkan rekening BCA + nominal unik" . PHP_EOL;
echo "4. Customer transfer manual → notifikasi → auto-confirm ✅" . PHP_EOL;
echo PHP_EOL;

echo "Atau test manual:" . PHP_EOL;
echo "  → Transfer ke: " . $accountNumber . " (". $accountName . ")" . PHP_EOL;
echo "  → Nominal: Rp 5.518 (sesuai invoice)" . PHP_EOL;
echo "  → PayHook akan baca notifikasi SMS BCA" . PHP_EOL;
echo "  → Auto-match & confirm invoice ✅" . PHP_EOL;
echo PHP_EOL;
