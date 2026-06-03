<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get DANA template
$template = DB::table('qris_templates')->where('id', 2)->first();

echo "=== DANA QRIS Analysis ===\n\n";
echo "QRIS String:\n{$template->qris_string}\n\n";

// Check if has amount field (54XX)
if (preg_match('/54(\d{2})(\d+)/', $template->qris_string, $match)) {
    echo "✅ Has amount field: 54{$match[1]}{$match[2]}\n";
    echo "   Amount: {$match[2]}\n";
} else {
    echo "❌ NO amount field found (Static QR)\n";
}

// Check country code
if (strpos($template->qris_string, '5802ID') !== false) {
    echo "✅ Has country code: 5802ID\n";
} else {
    echo "❌ NO country code found\n";
}

// Check CRC (last 4 chars should be 6304XXXX)
$lastPart = substr($template->qris_string, -8);
echo "\nLast 8 chars: {$lastPart}\n";

if (substr($template->qris_string, -8, 4) === '6304') {
    echo "✅ Has CRC field (6304)\n";
    $crc = substr($template->qris_string, -4);
    echo "   CRC value: {$crc}\n";
} else {
    echo "❌ CRC format unexpected\n";
}
