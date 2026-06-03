<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$invoice = \App\Models\Invoice::where('invoice_number', 'INV-20260331-RQYQG6')->first();

echo "📊 INVOICE STATUS CHECK\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Invoice Number  : " . $invoice->invoice_number . "\n";
echo "Customer        : " . $invoice->customer_name . "\n";
echo "Amount          : Rp " . number_format($invoice->unique_amount, 0, ',', '.') . "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ STATUS       : " . strtoupper($invoice->status) . "\n";
echo "💰 Paid At      : " . ($invoice->paid_at ? $invoice->paid_at->format('d M Y H:i:s') : '-') . "\n";
echo "📱 Source       : " . ($invoice->payment_source ?? '-') . "\n";
echo "📝 Notification : " . ($invoice->payment_notification_text ?? '-') . "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Check payment log
$log = \App\Models\PaymentLog::where('invoice_id', $invoice->id)->latest()->first();
if ($log) {
    echo "\n📋 PAYMENT LOG:\n";
    echo "Status          : " . $log->status . "\n";
    echo "Amount          : Rp " . number_format($log->amount, 0, ',', '.') . "\n";
    echo "Source          : " . $log->source . "\n";
    echo "Notes           : " . $log->notes . "\n";
}
