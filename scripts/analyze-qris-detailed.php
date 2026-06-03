<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Invoice;

$invoice = Invoice::find(4);

echo "=== DETAILED QRIS ANALYSIS ===" . PHP_EOL . PHP_EOL;

echo "QRIS String:" . PHP_EOL;
echo $invoice->qris_string . PHP_EOL;
echo PHP_EOL;

echo "Length: " . strlen($invoice->qris_string) . " chars" . PHP_EOL;
echo PHP_EOL;

// Find all field 54 occurrences
echo "Searching for field 54 (Amount)..." . PHP_EOL;
$pos = 0;
while (($pos = strpos($invoice->qris_string, '54', $pos)) !== false) {
    $field = substr($invoice->qris_string, $pos, 2);
    if ($field === '54') {
        $length = substr($invoice->qris_string, $pos + 2, 2);
        $lengthInt = intval($length);
        $value = substr($invoice->qris_string, $pos + 4, $lengthInt);
        
        echo "  Found at position $pos: 54" . $length . $value . PHP_EOL;
        
        // Check if this is amount field (should be after currency)
        $before = substr($invoice->qris_string, max(0, $pos - 10), 10);
        echo "    Context before: ...{$before}" . PHP_EOL;
        
        // Try to determine if this is transaction amount
        if (is_numeric($value) && strlen($value) <= 12) {
            echo "    → Possible amount field: Rp " . number_format($value, 0, ',', '.') . PHP_EOL;
        } else {
            echo "    → Not amount (contains text or too long)" . PHP_EOL;
        }
    }
    $pos += 2;
}

echo PHP_EOL;

// Find currency field
echo "Currency field (53):" . PHP_EOL;
$pos53 = strpos($invoice->qris_string, '5303');
if ($pos53 !== false) {
    $currencyField = substr($invoice->qris_string, $pos53, 7);
    echo "  " . $currencyField . " at position $pos53" . PHP_EOL;
    
    // Check what's after currency
    $afterCurrency = substr($invoice->qris_string, $pos53 + 7, 20);
    echo "  After currency: " . $afterCurrency . PHP_EOL;
}

echo PHP_EOL;

// Find country field
echo "Country field (58):" . PHP_EOL;
$pos58 = strpos($invoice->qris_string, '5802');
if ($pos58 !== false) {
    $countryField = substr($invoice->qris_string, $pos58, 8);
    echo "  " . $countryField . " at position $pos58" . PHP_EOL;
    
    // Check what's before country
    $beforeCountry = substr($invoice->qris_string, max(0, $pos58 - 20), 20);
    echo "  Before country: " . $beforeCountry . PHP_EOL;
}

echo PHP_EOL;

echo "Expected amount: " . $invoice->unique_amount . PHP_EOL;
echo "Expected field: 54045518 (tag=54, length=04, value=5518)" . PHP_EOL;
echo PHP_EOL;

// Check if amount is embedded anywhere
if (strpos($invoice->qris_string, '5518') !== false) {
    echo "✅ Amount value '5518' found in QRIS string!" . PHP_EOL;
    $pos5518 = strpos($invoice->qris_string, '5518');
    $context = substr($invoice->qris_string, max(0, $pos5518 - 10), 25);
    echo "  Context: ..." . $context . "..." . PHP_EOL;
} else {
    echo "❌ Amount value '5518' NOT found in QRIS string!" . PHP_EOL;
}
