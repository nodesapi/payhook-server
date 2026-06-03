<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\QrisTemplate;

echo "=== CHECKING QRIS VALIDITY ===" . PHP_EOL . PHP_EOL;

$template = QrisTemplate::find(2);

if (!$template) {
    echo "❌ Template not found!" . PHP_EOL;
    exit;
}

echo "Current QRIS Template:" . PHP_EOL;
echo "  Name: " . $template->name . PHP_EOL;
echo "  Account: " . $template->account_name . " (" . $template->account_number . ")" . PHP_EOL;
echo PHP_EOL;

echo "QRIS String:" . PHP_EOL;
echo $template->qris_string . PHP_EOL;
echo PHP_EOL;

echo "QRIS Analysis:" . PHP_EOL;
echo "  Length: " . strlen($template->qris_string) . " chars" . PHP_EOL;

// Check basic structure
$hasValidStart = substr($template->qris_string, 0, 14) === '00020101021240';
echo "  Valid EMV start: " . ($hasValidStart ? '✅' : '❌') . PHP_EOL;

$hasValidCRC = preg_match('/6304[0-9A-F]{4}$/', $template->qris_string);
echo "  Valid CRC format: " . ($hasValidCRC ? '✅' : '❌') . PHP_EOL;

$hasDana = strpos($template->qris_string, 'ID.DANA.WWW') !== false;
echo "  Contains DANA: " . ($hasDana ? '✅' : '❌') . PHP_EOL;

$hasCountry = strpos($template->qris_string, '5802ID') !== false;
echo "  Country code (ID): " . ($hasCountry ? '✅' : '❌') . PHP_EOL;

$hasCurrency = strpos($template->qris_string, '5303360') !== false;
echo "  Currency (IDR/360): " . ($hasCurrency ? '✅' : '❌') . PHP_EOL;

echo PHP_EOL;

// Parse merchant info
if (preg_match('/5913(.{0,25})6015/', $template->qris_string, $matches)) {
    echo "Merchant Name: " . $matches[1] . PHP_EOL;
}

if (preg_match('/6015(.{0,30})6105/', $template->qris_string, $matches)) {
    echo "Merchant City: " . $matches[1] . PHP_EOL;
}

echo PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo "⚠️  KEMUNGKINAN MASALAH:" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "1. QRIS DANA EXPIRED" . PHP_EOL;
echo "   → QRIS statis dari aplikasi DANA kadang punya masa aktif" . PHP_EOL;
echo "   → Solusi: Generate QR baru dari app DANA" . PHP_EOL;
echo PHP_EOL;

echo "2. QRIS INVALID/CORRUPT" . PHP_EOL;
echo "   → Saat copy-paste mungkin ada karakter hilang" . PHP_EOL;
echo "   → Solusi: Decode ulang dari QR image fresh" . PHP_EOL;
echo PHP_EOL;

echo "3. ACCOUNT DANA BERMASALAH" . PHP_EOL;
echo "   → Akun DANA belum verifikasi / limit habis" . PHP_EOL;
echo "   → Solusi: Cek status akun DANA" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "🔧 CARA FIX:" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "LANGKAH 1: Buka Aplikasi DANA" . PHP_EOL;
echo "  → Login ke akun DANA Anda" . PHP_EOL;
echo "  → Pastikan akun sudah verifikasi KTP" . PHP_EOL;
echo PHP_EOL;

echo "LANGKAH 2: Generate QR Fresh" . PHP_EOL;
echo "  → Tab 'Terima' → Pilih 'Terima Uang'" . PHP_EOL;
echo "  → Screenshot QR code yang muncul" . PHP_EOL;
echo "  → ATAU scan dengan tool decoder" . PHP_EOL;
echo PHP_EOL;

echo "LANGKAH 3: Decode QR Code" . PHP_EOL;
echo "  → Buka: http://192.168.3.105:8000/qris-decoder.html" . PHP_EOL;
echo "  → Upload screenshot QR / scan dengan camera" . PHP_EOL;
echo "  → Copy QRIS string yang muncul" . PHP_EOL;
echo PHP_EOL;

echo "LANGKAH 4: Update Template" . PHP_EOL;
echo "  → Edit file: update-fresh-qris.php" . PHP_EOL;
echo "  → Paste QRIS string baru" . PHP_EOL;
echo "  → Run: php update-fresh-qris.php" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "ALTERNATIF: Pakai Bank Transfer (Tanpa QRIS)" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "Jika QRIS masih bermasalah, pakai Bank Transfer:" . PHP_EOL;
echo "  1. Tampilkan nomor rekening bank (bukan QRIS)" . PHP_EOL;
echo "  2. Customer transfer manual dengan nominal unik" . PHP_EOL;
echo "  3. Notifikasi SMS masuk → PayHook detect" . PHP_EOL;
echo "  4. Webhook → Auto-confirm (sama seperti QRIS)" . PHP_EOL;
echo PHP_EOL;

echo "Rekening yang bisa dipakai:" . PHP_EOL;
echo "  - BCA, Mandiri, BRI, BNI (bank utama)" . PHP_EOL;
echo "  - Pastikan SMS banking aktif" . PHP_EOL;
echo "  - PayHook akan baca SMS notifikasi transfer" . PHP_EOL;
echo PHP_EOL;
