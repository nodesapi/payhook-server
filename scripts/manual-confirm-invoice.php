<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Invoice;

echo "=== MANUAL CONFIRM INVOICE (Karena PayHook Belum Setup) ===" . PHP_EOL . PHP_EOL;

$invoice = Invoice::find(5);

if (!$invoice) {
    echo "❌ Invoice not found!" . PHP_EOL;
    exit;
}

echo "Invoice Details:" . PHP_EOL;
echo "  Number: " . $invoice->invoice_number . PHP_EOL;
echo "  Customer: " . $invoice->customer_name . PHP_EOL;
echo "  Amount: Rp " . number_format($invoice->unique_amount, 0, ',', '.') . PHP_EOL;
echo "  Current Status: " . strtoupper($invoice->status) . PHP_EOL;
echo PHP_EOL;

if ($invoice->status === 'paid') {
    echo "✅ Invoice sudah LUNAS!" . PHP_EOL;
    echo "  Paid at: " . $invoice->paid_at->format('Y-m-d H:i:s') . PHP_EOL;
    exit;
}

echo "Karena pembayaran sudah dilakukan tapi PayHook belum setup," . PHP_EOL;
echo "invoice akan di-confirm manual..." . PHP_EOL;
echo PHP_EOL;

// Manual confirm
$invoice->markAsPaid(
    'Manual Confirmation',
    'Pembayaran BCA Rp ' . number_format($invoice->unique_amount, 0, ',', '.') . ' sudah diterima. PayHook belum setup saat pembayaran.'
);

$invoice->refresh();

echo "✅ INVOICE CONFIRMED!" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "Updated Status:" . PHP_EOL;
echo "  Status: " . strtoupper($invoice->status) . " ✅" . PHP_EOL;
echo "  Paid at: " . $invoice->paid_at->format('Y-m-d H:i:s') . PHP_EOL;
echo "  Payment Source: " . $invoice->payment_source . PHP_EOL;
echo PHP_EOL;

echo "View invoice: http://192.168.3.105:8000/invoices/5" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "NEXT STEPS:" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "1. Setup PayHook di HP Android (lihat: SETUP-PAYHOOK.md)" . PHP_EOL;
echo "   → Install APK" . PHP_EOL;
echo "   → Enable notification permission" . PHP_EOL;
echo "   → Add webhook configuration" . PHP_EOL;
echo "   → Enable monitored apps (BCA/DANA)" . PHP_EOL;
echo PHP_EOL;

echo "2. Buat invoice baru untuk testing:" . PHP_EOL;
echo "   php create-invoice-10k.php" . PHP_EOL;
echo PHP_EOL;

echo "3. Bayar invoice baru → PayHook detect → Auto-confirm ✅" . PHP_EOL;
echo PHP_EOL;

echo "Semua panduan lengkap ada di:" . PHP_EOL;
echo "  → SETUP-PAYHOOK.md" . PHP_EOL;
echo "  → README-PAYHOOK.md" . PHP_EOL;
echo "  → TESTING-GUIDE.md" . PHP_EOL;
