<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\QrisTemplate;

echo "=== UPDATE QRIS DENGAN QR FRESH DARI DANA ===" . PHP_EOL . PHP_EOL;

// ============================================
// 🔧 PASTE QRIS STRING BARU DI SINI:
// ============================================
$newQrisString = "";
// Contoh: "00020101021240540011ID.DANA.WWW..."

// ============================================

if (empty($newQrisString)) {
    echo "⚠️  QRIS STRING BELUM DIISI!" . PHP_EOL . PHP_EOL;
    
    echo "CARA MENGISI:" . PHP_EOL;
    echo str_repeat("-", 60) . PHP_EOL;
    echo PHP_EOL;
    
    echo "STEP 1: Buka Aplikasi DANA di HP" . PHP_EOL;
    echo "  → Tab 'Terima'" . PHP_EOL;
    echo "  → Pilih 'Terima Uang' atau 'Tunjukkan QR'" . PHP_EOL;
    echo "  → QR code akan muncul (ini QRIS statis Anda)" . PHP_EOL;
    echo PHP_EOL;
    
    echo "STEP 2: Screenshot QR Code Tersebut" . PHP_EOL;
    echo "  → Screenshot/foto QR code" . PHP_EOL;
    echo "  → Kirim ke PC (WhatsApp/Email)" . PHP_EOL;
    echo PHP_EOL;
    
    echo "STEP 3: Decode QR Code" . PHP_EOL;
    echo "  → Buka browser: http://192.168.3.105:8000/qris-decoder.html" . PHP_EOL;
    echo "  → Tab 'Upload Image'" . PHP_EOL;
    echo "  → Pilih file screenshot QR" . PHP_EOL;
    echo "  → Tunggu decode selesai" . PHP_EOL;
    echo "  → Copy string yang muncul (panjang ~180-200 chars)" . PHP_EOL;
    echo PHP_EOL;
    
    echo "STEP 4: Paste ke Script Ini" . PHP_EOL;
    echo "  → Edit file: update-fresh-qris.php" . PHP_EOL;
    echo "  → Cari baris: \$newQrisString = \"\";" . PHP_EOL;
    echo "  → Paste string hasil decode di dalam tanda kutip" . PHP_EOL;
    echo "  → Save file" . PHP_EOL;
    echo PHP_EOL;
    
    echo "STEP 5: Jalankan Script" . PHP_EOL;
    echo "  → php update-fresh-qris.php" . PHP_EOL;
    echo "  → Template akan terupdate otomatis" . PHP_EOL;
    echo PHP_EOL;
    
    echo str_repeat("=", 60) . PHP_EOL;
    echo "ALTERNATIF: Input Manual via Web" . PHP_EOL;
    echo str_repeat("=", 60) . PHP_EOL;
    echo PHP_EOL;
    echo "Bisa juga update via browser:" . PHP_EOL;
    echo "  1. Buka: http://192.168.3.105:8000/qris-templates/2/edit" . PHP_EOL;
    echo "  2. Paste QRIS string baru" . PHP_EOL;
    echo "  3. Klik 'Update'" . PHP_EOL;
    echo PHP_EOL;
    
    exit;
}

echo "New QRIS String:" . PHP_EOL;
echo $newQrisString . PHP_EOL;
echo PHP_EOL;

// Validate
echo "Validating new QRIS..." . PHP_EOL;
$length = strlen($newQrisString);
echo "  Length: $length chars" . PHP_EOL;

if ($length < 100) {
    echo "  ❌ Too short! QRIS should be ~180-200 chars" . PHP_EOL;
    exit;
}

$hasStart = substr($newQrisString, 0, 14) === '00020101021240';
echo "  EMV Start: " . ($hasStart ? '✅' : '❌') . PHP_EOL;

$hasCRC = preg_match('/6304[0-9A-F]{4}$/', $newQrisString);
echo "  CRC format: " . ($hasCRC ? '✅' : '❌') . PHP_EOL;

$hasDana = strpos($newQrisString, 'ID.DANA.WWW') !== false;
echo "  DANA identifier: " . ($hasDana ? '✅' : '❌') . PHP_EOL;

if (!$hasStart || !$hasCRC || !$hasDana) {
    echo PHP_EOL;
    echo "❌ QRIS validation failed! Please check the string." . PHP_EOL;
    exit;
}

echo PHP_EOL;
echo "✅ Validation passed!" . PHP_EOL;
echo PHP_EOL;

// Update database
$template = QrisTemplate::find(2);

if (!$template) {
    echo "❌ Template ID 2 not found!" . PHP_EOL;
    exit;
}

echo "Updating template..." . PHP_EOL;
$template->update([
    'qris_string' => $newQrisString,
    'updated_at' => now(),
]);

echo "✅ Template updated successfully!" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "NEXT: Test Payment" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "1. Create new invoice:" . PHP_EOL;
echo "   php create-test-invoice.php" . PHP_EOL;
echo PHP_EOL;

echo "2. Scan QR code dengan DANA" . PHP_EOL;
echo "   → Harus berhasil (tidak error lagi)" . PHP_EOL;
echo "   → App minta input nominal" . PHP_EOL;
echo PHP_EOL;

echo "3. Input nominal unik → Bayar" . PHP_EOL;
echo "   → Notifikasi masuk → PayHook detect" . PHP_EOL;
echo "   → Invoice auto-confirm ✅" . PHP_EOL;
echo PHP_EOL;
