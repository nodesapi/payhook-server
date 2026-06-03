<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\QrisTemplate;
use App\Services\QrisService;

$template = QrisTemplate::find(2);

echo "=== CURRENT DATABASE TEMPLATE ===" . PHP_EOL;
echo "Name: " . $template->name . PHP_EOL;
echo "QRIS String:" . PHP_EOL;
echo $template->qris_string . PHP_EOL;
echo PHP_EOL;
echo "Length: " . strlen($template->qris_string) . " chars" . PHP_EOL;
echo PHP_EOL;

echo "=== EXPECTED FORMAT (from user) ===" . PHP_EOL;
$expected = "00020101021240540011ID.DANA.WWW01189360091531469500590213WAHYU SUHANDI5204482953033605405100005802ID5913WAHYU SUHANDI6015Kota Tangerang 61051279062460804DMCT993400020001242810120120251014778617396304A35F";
echo $expected . PHP_EOL;
echo PHP_EOL;
echo "Length: " . strlen($expected) . " chars" . PHP_EOL;
echo PHP_EOL;

echo "=== TESTING INJECTION WITH AMOUNT 10000 ===" . PHP_EOL;
$service = new QrisService();
try {
    $injected = $service->injectAmount($template->qris_string, 10000);
    echo "Result:" . PHP_EOL;
    echo $injected . PHP_EOL;
    echo PHP_EOL;
    echo "Length: " . strlen($injected) . " chars" . PHP_EOL;
    echo PHP_EOL;
    
    // Compare
    echo "=== COMPARISON ===" . PHP_EOL;
    if (strlen($injected) !== strlen($expected)) {
        echo "❌ Length MISMATCH: Laravel=" . strlen($injected) . " vs Expected=" . strlen($expected) . PHP_EOL;
    } else {
        echo "✅ Length MATCH: " . strlen($injected) . " chars" . PHP_EOL;
    }
    
    // Compare field by field
    echo PHP_EOL;
    echo "Field 54 in Laravel: " . substr($injected, strpos($injected, '5405'), 10) . PHP_EOL;
    echo "Field 54 in Expected: " . substr($expected, strpos($expected, '5405'), 10) . PHP_EOL;
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
