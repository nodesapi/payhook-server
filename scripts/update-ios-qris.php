<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\QrisTemplate;

echo "=== UPDATING QRIS TEMPLATE WITH iOS QRIS ===" . PHP_EOL . PHP_EOL;

// QRIS from iOS (CORRECT FORMAT - EMV static)
$iosQris = "00020101021240540011ID.DANA.WWW01189360091531469500590213WAHYU SUHANDI5204482953033605802ID5913WAHYU SUHANDI6015Kota Tangerang 61051279062460804DMCT99340002000124281012012026040192081071630416F3";

echo "iOS QRIS (CORRECT - EMV Static):" . PHP_EOL;
echo $iosQris . PHP_EOL;
echo PHP_EOL;

echo "Length: " . strlen($iosQris) . " chars" . PHP_EOL;

// Validate
$hasValidStart = substr($iosQris, 0, 14) === '00020101021240';
echo "Valid EMV start: " . ($hasValidStart ? '✅' : '❌') . PHP_EOL;

$hasCRC = preg_match('/6304[0-9A-F]{4}$/', $iosQris);
echo "Valid CRC format: " . ($hasCRC ? '✅' : '❌') . PHP_EOL;

$hasDana = strpos($iosQris, 'ID.DANA.WWW') !== false;
echo "Contains DANA: " . ($hasDana ? '✅' : '❌') . PHP_EOL;

// Check if static (no field 54)
$hasAmountField = strpos($iosQris, '5405') !== false;
echo "Has amount field (54): " . ($hasAmountField ? 'YES (dynamic)' : 'NO (static) ✅') . PHP_EOL;
echo PHP_EOL;

if (!$hasValidStart || !$hasCRC || !$hasDana) {
    echo "❌ QRIS validation failed!" . PHP_EOL;
    exit;
}

echo "✅ QRIS validation PASSED!" . PHP_EOL;
echo PHP_EOL;

// Update template
$template = QrisTemplate::find(4);

if (!$template) {
    echo "❌ Template not found!" . PHP_EOL;
    exit;
}

echo "Updating template ID 4 (DANA)..." . PHP_EOL;

$oldLength = strlen($template->qris_string);
$template->update(['qris_string' => $iosQris]);

echo "✅ Template updated!" . PHP_EOL;
echo PHP_EOL;

echo "Before: " . $oldLength . " chars" . PHP_EOL;
echo "After: " . strlen($iosQris) . " chars" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "PENJELASAN:" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "❌ DANA Android → Generate Dynamic URL:" . PHP_EOL;
echo "   https://qr.dana.id/v1/..." . PHP_EOL;
echo "   → Expired cepat (30-60 menit)" . PHP_EOL;
echo "   → Tidak bisa inject amount" . PHP_EOL;
echo "   → Tidak cocok untuk payment gateway" . PHP_EOL;
echo PHP_EOL;

echo "✅ DANA iOS → Generate QRIS EMV Static:" . PHP_EOL;
echo "   00020101021240540011ID.DANA.WWW..." . PHP_EOL;
echo "   → Static (tidak expired cepat)" . PHP_EOL;
echo "   → Bisa inject amount" . PHP_EOL;
echo "   → Perfect untuk payment gateway ✅" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "NEXT: TEST INVOICE BARU" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "1. Buat invoice baru:" . PHP_EOL;
echo "   php create-invoice-10k.php" . PHP_EOL;
echo PHP_EOL;

echo "2. Scan QR code dengan BCA mobile/DANA" . PHP_EOL;
echo "   → Nominal akan muncul otomatis ✅" . PHP_EOL;
echo "   → TIDAK expired cepat seperti sebelumnya" . PHP_EOL;
echo PHP_EOL;

echo "3. Bayar → Notifikasi → Auto-confirm ✅" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "KESIMPULAN:" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "✅ Pakai DANA di iOS untuk generate QRIS" . PHP_EOL;
echo "❌ Jangan pakai DANA di Android (generate URL, bukan QRIS)" . PHP_EOL;
echo PHP_EOL;

echo "Atau alternatif:" . PHP_EOL;
echo "  → BCA mobile QRIS merchant" . PHP_EOL;
echo "  → Bank transfer BCA (tanpa QR)" . PHP_EOL;
echo PHP_EOL;
