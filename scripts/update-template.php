<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Update template dengan QRIS tanpa nominal (static QR)
$newQris = '00020101021240540011ID.DANA.WWW01189360091531469500590213WAHYU SUHANDI5204482953033605802ID5913WAHYU SUHANDI6015Kota Tangerang 61051279062460804DMCT9934000200012428101201202510145948279963041D6E';

DB::table('qris_templates')->where('id', 2)->update([
    'qris_string' => $newQris
]);

echo "✅ Template updated with static QR (no amount field)\n";
echo "QRIS length: " . strlen($newQris) . " chars\n";
