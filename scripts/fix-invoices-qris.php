<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Invoice;
use App\Models\QrisTemplate;

echo "=== FIXING INVOICES WITHOUT QRIS TEMPLATE ===" . PHP_EOL . PHP_EOL;

// Find invoices without QRIS
$invoicesWithoutQris = Invoice::whereNull('qris_template_id')
    ->orWhereNull('qris_string')
    ->get();

echo "Found " . $invoicesWithoutQris->count() . " invoices without QRIS" . PHP_EOL;
echo PHP_EOL;

if ($invoicesWithoutQris->isEmpty()) {
    echo "✅ All invoices already have QRIS!" . PHP_EOL;
    exit;
}

// Get active QRIS template
$template = QrisTemplate::active()->first();

if (!$template) {
    echo "❌ No active QRIS template found!" . PHP_EOL;
    echo "Please create a QRIS template first." . PHP_EOL;
    exit;
}

echo "Will assign QRIS template:" . PHP_EOL;
echo "  ID: " . $template->id . PHP_EOL;
echo "  Name: " . $template->name . PHP_EOL;
echo "  Type: " . $template->type . PHP_EOL;
echo PHP_EOL;

foreach ($invoicesWithoutQris as $invoice) {
    echo "Processing Invoice: " . $invoice->invoice_number . PHP_EOL;
    
    // Assign QRIS template and string
    $invoice->update([
        'qris_template_id' => $template->id,
        'qris_string' => $template->qris_string, // Static QRIS
    ]);
    
    echo "  ✅ QRIS assigned (static, amount: Rp " . number_format($invoice->unique_amount, 0, ',', '.') . ")" . PHP_EOL;
}

echo PHP_EOL;
echo "✅ ALL INVOICES FIXED!" . PHP_EOL;
echo PHP_EOL;

echo "Test invoice:" . PHP_EOL;
echo "  http://192.168.3.105:8000/invoices/4" . PHP_EOL;
echo PHP_EOL;
