<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\QrisTemplate;

// QRIS statis DANA (194 chars, tanpa field 54)
$staticDanaQris = "00020101021240540011ID.DANA.WWW01189360091531469500590213WAHYU SUHANDI5204482953033605802ID5913WAHYU SUHANDI6015Kota Tangerang 61051279062460804DMCT9934000200012428101201202510145948279963041D6E";

echo "=== UPDATING QRIS TEMPLATE TO STATIC ===" . PHP_EOL . PHP_EOL;

$template = QrisTemplate::find(2);

if ($template) {
    echo "Current template:" . PHP_EOL;
    echo "  Name: " . $template->name . PHP_EOL;
    echo "  Length: " . strlen($template->qris_string) . " chars" . PHP_EOL;
    echo "  Has field 54: " . (strpos($template->qris_string, '5405') !== false ? 'YES ❌' : 'NO ✅') . PHP_EOL;
    echo PHP_EOL;

    // Check if already static
    if (strlen($staticDanaQris) === strlen($template->qris_string) && 
        strpos($template->qris_string, '5405') === false) {
        echo "✅ Template is already STATIC (no field 54)!" . PHP_EOL;
        echo "No update needed." . PHP_EOL;
    } else {
        $template->update([
            'qris_string' => $staticDanaQris
        ]);

        echo "✅ Template updated to STATIC QRIS!" . PHP_EOL;
        echo "  New length: " . strlen($staticDanaQris) . " chars" . PHP_EOL;
        echo "  Field 54 (amount): NOT PRESENT ✅" . PHP_EOL;
        echo PHP_EOL;
        echo "✅ KONSEP PAYHOOK:" . PHP_EOL;
        echo "  1. Customer scan QRIS statis" . PHP_EOL;
        echo "  2. App minta input nominal manual" . PHP_EOL;
        echo "  3. Customer input nominal unik (misal Rp 10.123)" . PHP_EOL;
        echo "  4. Notifikasi masuk → PayHook detect → webhook" . PHP_EOL;
        echo "  5. Laravel auto-match by unique_amount ✅" . PHP_EOL;
    }
} else {
    echo "❌ Template ID 2 not found!" . PHP_EOL;
}

echo PHP_EOL;
echo "=== VERIFYING STATIC QRIS STRUCTURE ===" . PHP_EOL;
echo "Expected sequence: ...5303360 5802ID... (no field 54 between)" . PHP_EOL;
$pos53 = strpos($staticDanaQris, '5303360');
$pos58 = strpos($staticDanaQris, '5802ID');
if ($pos53 !== false && $pos58 !== false) {
    $between = substr($staticDanaQris, $pos53 + 7, $pos58 - $pos53 - 7);
    echo "Between field 53 and 58: '$between'" . PHP_EOL;
    if (empty($between)) {
        echo "✅ CORRECT: No field 54 (amount) present!" . PHP_EOL;
    } else {
        echo "⚠️  Something found between fields: $between" . PHP_EOL;
    }
}
