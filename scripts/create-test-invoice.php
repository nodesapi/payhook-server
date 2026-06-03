<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Invoice;

echo "=== CREATING TEST INVOICE ===" . PHP_EOL . PHP_EOL;

// Create real test invoice (not deleted)
$invoice = Invoice::create([
    'customer_name' => 'Test Payment - ' . date('H:i:s'),
    'customer_email' => 'test@payhook.com',
    'customer_phone' => '081234567890',
    'description' => 'Testing PayHook QRIS Static Payment Flow',
    'amount' => 5000, // Base amount Rp 5.000
]);

// Generate QRIS statis
$invoice->generateQris(2); // Dana template ID 2
$invoice->refresh();

echo "✅ INVOICE CREATED SUCCESSFULLY!" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "📝 Invoice Details:" . PHP_EOL;
echo "  Invoice Number: " . $invoice->invoice_number . PHP_EOL;
echo "  Customer: " . $invoice->customer_name . PHP_EOL;
echo "  Base Amount: Rp " . number_format($invoice->amount, 0, ',', '.') . PHP_EOL;
echo "  Unique Suffix: +" . $invoice->unique_suffix . PHP_EOL;
echo "  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" . PHP_EOL;
echo "  💰 TOTAL TO PAY: Rp " . number_format($invoice->unique_amount, 0, ',', '.') . PHP_EOL;
echo "  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" . PHP_EOL;
echo PHP_EOL;

echo "🎫 QRIS Info:" . PHP_EOL;
echo "  Template: " . $invoice->qrisTemplate->name . PHP_EOL;
echo "  QRIS Type: STATIC (no amount embedded) ✅" . PHP_EOL;
echo "  QRIS Length: " . strlen($invoice->qris_string) . " chars" . PHP_EOL;
echo PHP_EOL;

echo "🌐 View Invoice (with QR Code):" . PHP_EOL;
echo "  http://192.168.3.105:8000/invoices/" . $invoice->id . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "📱 CARA TESTING END-TO-END:" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "STEP 1: Buka invoice di browser" . PHP_EOL;
echo "  → http://192.168.3.105:8000/invoices/" . $invoice->id . PHP_EOL;
echo PHP_EOL;

echo "STEP 2: Scan QR Code dengan aplikasi DANA/BCA/GoPay" . PHP_EOL;
echo "  → Aplikasi akan minta 'Masukkan Nominal'" . PHP_EOL;
echo "  → Ini NORMAL karena QRIS statis tidak punya amount" . PHP_EOL;
echo PHP_EOL;

echo "STEP 3: Input nominal PERSIS:" . PHP_EOL;
echo "  → Rp " . number_format($invoice->unique_amount, 0, ',', '.') . PHP_EOL;
echo "  → (Base: " . number_format($invoice->amount, 0, ',', '.') . " + Suffix: " . $invoice->unique_suffix . ")" . PHP_EOL;
echo PHP_EOL;

echo "STEP 4: Bayar & tunggu notifikasi masuk ke HP" . PHP_EOL;
echo "  → Notifikasi: 'Transfer Rp " . number_format($invoice->unique_amount, 0, ',', '.') . " berhasil'" . PHP_EOL;
echo PHP_EOL;

echo "STEP 5: PayHook akan otomatis:" . PHP_EOL;
echo "  ✅ Baca notifikasi" . PHP_EOL;
echo "  ✅ Extract amount: " . $invoice->unique_amount . PHP_EOL;
echo "  ✅ Kirim webhook ke: http://192.168.3.105:8000/api/webhook" . PHP_EOL;
echo "  ✅ Laravel auto-match invoice by unique_amount" . PHP_EOL;
echo "  ✅ Status invoice berubah: pending → paid" . PHP_EOL;
echo PHP_EOL;

echo "STEP 6: Refresh halaman invoice" . PHP_EOL;
echo "  → Status akan berubah jadi 'LUNAS' ✅" . PHP_EOL;
echo "  → Tanggal pembayaran tercatat" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "⚠️  PENTING - SETUP PAYHOOK DULU:" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "1. Buka aplikasi PayHook di HP Android" . PHP_EOL;
echo PHP_EOL;

echo "2. Tambah Webhook:" . PHP_EOL;
echo "   • URL: http://192.168.3.105:8000/api/webhook" . PHP_EOL;
echo "   • Bearer Token: 1fec9da1fc93b70be4174aad239745ced5fae0e1c8ead0253ce2c6f289cbd091" . PHP_EOL;
echo "   • Status: Active ✅" . PHP_EOL;
echo PHP_EOL;

echo "3. Enable Bank Monitoring:" . PHP_EOL;
echo "   • Settings → Monitored Apps" . PHP_EOL;
echo "   • Centang: DANA, BCA, Mandiri, BRI, GoPay, dll" . PHP_EOL;
echo "   • Tanpa ini PayHook TIDAK akan baca notifikasi!" . PHP_EOL;
echo PHP_EOL;

echo "4. Test Notification Permission:" . PHP_EOL;
echo "   • Kirim test notification dari bank" . PHP_EOL;
echo "   • Cek Activity Log di PayHook" . PHP_EOL;
echo "   • Pastikan notifikasi terdeteksi" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "🎯 INVOICE READY FOR TESTING!" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
