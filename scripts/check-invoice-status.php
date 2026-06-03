<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Invoice;
use App\Models\PaymentLog;

echo "=== CHECKING INVOICE STATUS ===" . PHP_EOL . PHP_EOL;

$invoice = Invoice::find(5);

if (!$invoice) {
    echo "❌ Invoice not found!" . PHP_EOL;
    exit;
}

echo "Invoice: " . $invoice->invoice_number . PHP_EOL;
echo "Customer: " . $invoice->customer_name . PHP_EOL;
echo "Amount: Rp " . number_format($invoice->unique_amount, 0, ',', '.') . PHP_EOL;
echo "Status: " . strtoupper($invoice->status) . PHP_EOL;
echo "Created: " . $invoice->created_at->format('Y-m-d H:i:s') . PHP_EOL;

if ($invoice->paid_at) {
    echo "Paid at: " . $invoice->paid_at->format('Y-m-d H:i:s') . PHP_EOL;
}

echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "PAYMENT LOGS (Webhook History):" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

$logs = PaymentLog::where('invoice_id', $invoice->id)
    ->orWhere('amount', $invoice->unique_amount)
    ->latest()
    ->get();

if ($logs->isEmpty()) {
    echo "❌ No payment logs found!" . PHP_EOL;
    echo PHP_EOL;
    echo "Ini berarti:" . PHP_EOL;
    echo "  → PayHook BELUM mengirim webhook" . PHP_EOL;
    echo "  → Atau PayHook belum detect notifikasi pembayaran" . PHP_EOL;
    echo PHP_EOL;
} else {
    foreach ($logs as $log) {
        echo "Log ID: " . $log->id . PHP_EOL;
        echo "  Amount: Rp " . number_format($log->amount, 0, ',', '.') . PHP_EOL;
        echo "  Status: " . $log->status . PHP_EOL;
        echo "  Source: " . ($log->source ?? 'N/A') . PHP_EOL;
        echo "  Invoice: " . ($log->invoice_id ? "Matched (ID: {$log->invoice_id})" : "Not matched") . PHP_EOL;
        echo "  Created: " . $log->created_at->format('Y-m-d H:i:s') . PHP_EOL;
        
        if ($log->notification_text) {
            echo "  Notification: " . $log->notification_text . PHP_EOL;
        }
        
        if ($log->webhook_response) {
            echo "  Response: " . $log->webhook_response . PHP_EOL;
        }
        
        echo PHP_EOL;
    }
}

echo str_repeat("=", 60) . PHP_EOL;
echo "DIAGNOSIS:" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

if ($invoice->status === 'pending' && $logs->isEmpty()) {
    echo "❌ Status: PENDING (belum terbayar)" . PHP_EOL;
    echo "❌ Webhook: TIDAK ADA (PayHook tidak kirim data)" . PHP_EOL;
    echo PHP_EOL;
    
    echo "KEMUNGKINAN MASALAH:" . PHP_EOL;
    echo "  1. PayHook app BELUM diinstall di HP Android" . PHP_EOL;
    echo "  2. PayHook BELUM dikonfigurasi (webhook URL + token)" . PHP_EOL;
    echo "  3. Notification permission BELUM granted" . PHP_EOL;
    echo "  4. Monitored apps (DANA/BCA) BELUM dicentang" . PHP_EOL;
    echo "  5. Notifikasi pembayaran tidak terdeteksi PayHook" . PHP_EOL;
    echo PHP_EOL;
    
} elseif ($invoice->status === 'pending' && !$logs->isEmpty()) {
    echo "⚠️  Status: PENDING (belum auto-confirm)" . PHP_EOL;
    echo "✅ Webhook: ADA (PayHook sudah kirim)" . PHP_EOL;
    echo PHP_EOL;
    
    echo "KEMUNGKINAN MASALAH:" . PHP_EOL;
    echo "  → Amount di webhook tidak match dengan unique_amount" . PHP_EOL;
    echo "  → Check payment log details di atas" . PHP_EOL;
    echo PHP_EOL;
    
} elseif ($invoice->status === 'paid') {
    echo "✅ Status: PAID (LUNAS)" . PHP_EOL;
    echo "✅ Webhook: BERHASIL" . PHP_EOL;
    echo "✅ Auto-confirm: WORKING" . PHP_EOL;
    echo PHP_EOL;
}

echo str_repeat("=", 60) . PHP_EOL;
echo "NEXT STEPS:" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

if ($invoice->status === 'pending') {
    echo "Karena pembayaran sudah dilakukan tapi belum terdeteksi," . PHP_EOL;
    echo "mari setup PayHook di HP Android:" . PHP_EOL;
    echo PHP_EOL;
    
    echo "STEP 1: Install PayHook APK" . PHP_EOL;
    echo "  File: payhook/app/build/outputs/apk/debug/PayHook-20260401-0315.apk" . PHP_EOL;
    echo "  → Transfer ke HP → Install" . PHP_EOL;
    echo PHP_EOL;
    
    echo "STEP 2: Grant Notification Permission" . PHP_EOL;
    echo "  → PayHook app → Allow notifications" . PHP_EOL;
    echo "  → Settings → Apps → PayHook → Notifications → Enable" . PHP_EOL;
    echo PHP_EOL;
    
    echo "STEP 3: Add Webhook" . PHP_EOL;
    echo "  → PayHook app → '+' Add Webhook" . PHP_EOL;
    echo "  → URL: http://192.168.3.105:8000/api/webhook" . PHP_EOL;
    echo "  → Bearer Token: 1fec9da1fc93b70be4174aad239745ced5fae0e1c8ead0253ce2c6f289cbd091" . PHP_EOL;
    echo "  → Save → Status: Active ✅" . PHP_EOL;
    echo PHP_EOL;
    
    echo "STEP 4: Enable Bank Monitoring" . PHP_EOL;
    echo "  → PayHook → Settings → Monitored Apps" . PHP_EOL;
    echo "  → Centang: BCA mobile, DANA, GoPay, dll" . PHP_EOL;
    echo PHP_EOL;
    
    echo "STEP 5: Manual Confirm Invoice (untuk testing)" . PHP_EOL;
    echo "  Atau test kirim notifikasi lagi untuk trigger webhook" . PHP_EOL;
    echo PHP_EOL;
}

echo "View invoice: http://192.168.3.105:8000/invoices/5" . PHP_EOL;
