<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PaymentLog;
use App\Models\Invoice;

echo "\n=== PAYMENT LOGS (Last 10) ===\n\n";

$logs = PaymentLog::latest()->take(10)->get();

if ($logs->isEmpty()) {
    echo "❌ No payment logs found!\n";
    echo "   PayHook belum pernah kirim webhook.\n\n";
} else {
    foreach ($logs as $log) {
        echo "ID: {$log->id}\n";
        echo "Amount: Rp " . number_format($log->amount, 0, ',', '.') . "\n";
        echo "Source: {$log->source}\n";
        echo "Invoice: {$log->invoice_id}\n";
        echo "Time: {$log->created_at}\n";
        echo "---\n";
    }
}

echo "\n=== INVOICE #12 STATUS ===\n\n";

$invoice = Invoice::find(12);
if ($invoice) {
    echo "Invoice: {$invoice->invoice_number}\n";
    echo "Amount: Rp " . number_format($invoice->unique_amount, 0, ',', '.') . "\n";
    echo "Status: {$invoice->status}\n";
    echo "Paid At: " . ($invoice->paid_at ? $invoice->paid_at : 'Not paid') . "\n";
} else {
    echo "❌ Invoice #12 not found\n";
}

echo "\n";
