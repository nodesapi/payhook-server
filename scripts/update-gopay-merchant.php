<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\QrisTemplate;

echo "\n";
echo "==============================================\n";
echo "  UPDATE GOPAY MERCHANT QRIS TEMPLATE\n";
echo "==============================================\n\n";

// Minta input QRIS string dari user
echo "Paste QRIS string GoPay Merchant kamu di bawah:\n";
echo "(Format harus dimulai dengan 00020101...)\n\n";
echo "QRIS String: ";

$qrisString = trim(fgets(STDIN));

if (empty($qrisString)) {
    echo "\n❌ ERROR: QRIS string tidak boleh kosong!\n\n";
    exit(1);
}

// Validasi format dasar
if (!str_starts_with($qrisString, '000201')) {
    echo "\n❌ ERROR: Format QRIS tidak valid!\n";
    echo "   QRIS harus dimulai dengan '000201'\n";
    echo "   Yang kamu input: " . substr($qrisString, 0, 20) . "...\n\n";
    exit(1);
}

// Validasi panjang (minimal 100 karakter untuk QRIS valid)
if (strlen($qrisString) < 100) {
    echo "\n❌ ERROR: QRIS string terlalu pendek!\n";
    echo "   Panjang: " . strlen($qrisString) . " karakter (minimal 100)\n\n";
    exit(1);
}

// Cek apakah punya CRC (4 karakter terakhir setelah 6304)
if (!str_contains($qrisString, '6304')) {
    echo "\n⚠️  WARNING: QRIS tidak ada field CRC (6304)!\n";
    echo "   QRIS mungkin tidak valid.\n";
}

echo "\n✅ Format QRIS terlihat valid!\n";
echo "   Panjang: " . strlen($qrisString) . " karakter\n";
echo "   Preview: " . substr($qrisString, 0, 50) . "...\n\n";

// Update atau create template
echo "Mengupdate database...\n";

$template = QrisTemplate::first();

if ($template) {
    $template->update([
        'qris_string' => $qrisString,
        'name' => 'GoPay Merchant QRIS',
        'type' => 'merchant',
        'is_active' => true
    ]);
    echo "✅ Template berhasil diupdate!\n";
} else {
    $template = QrisTemplate::create([
        'name' => 'GoPay Merchant QRIS',
        'type' => 'merchant',
        'qris_string' => $qrisString,
        'is_active' => true
    ]);
    echo "✅ Template baru berhasil dibuat!\n";
}

echo "\n";
echo "==============================================\n";
echo "  TEMPLATE INFO\n";
echo "==============================================\n";
echo "ID:           " . $template->id . "\n";
echo "Name:         " . $template->name . "\n";
echo "Length:       " . strlen($template->qris_string) . " chars\n";
echo "Status:       " . ($template->is_active ? 'ACTIVE ✅' : 'INACTIVE ❌') . "\n";
echo "Updated:      " . $template->updated_at->format('Y-m-d H:i:s') . "\n";
echo "\n";

echo "==============================================\n";
echo "  TEST INJECTION\n";
echo "==============================================\n\n";

// Test inject nominal
$testAmount = 15000; // Rp 15.000 untuk test
echo "Testing amount injection dengan Rp " . number_format($testAmount, 0, ',', '.') . "...\n";

try {
    $qrisService = app(\App\Services\QrisService::class);
    $injectedQris = $qrisService->injectAmount($template->qris_string, $testAmount);
    
    if ($injectedQris && $injectedQris !== $template->qris_string) {
        echo "✅ Amount injection BERHASIL!\n";
        echo "   Original length: " . strlen($template->qris_string) . " chars\n";
        echo "   Injected length: " . strlen($injectedQris) . " chars\n";
        
        // Cari field 54 di QRIS hasil injection
        if (preg_match('/5405(\d+)63/', $injectedQris, $matches)) {
            echo "   Field 54 found: 5405" . $matches[1] . "\n";
            echo "   Amount encoded: Rp " . number_format(intval($matches[1]) / 100, 0, ',', '.') . "\n";
        }
        echo "\n";
    } else {
        echo "⚠️  Amount injection GAGAL atau tidak ada perubahan!\n";
        echo "   Coba manual test di bawah.\n\n";
    }
} catch (Exception $e) {
    echo "❌ ERROR saat test injection: " . $e->getMessage() . "\n\n";
}

echo "==============================================\n";
echo "  NEXT STEPS - TESTING\n";
echo "==============================================\n\n";

echo "1. CREATE TEST INVOICE:\n";
echo "   php create-invoice-10k.php\n\n";

echo "2. BUKA INVOICE DI BROWSER:\n";
echo "   http://192.168.3.105:8000/invoices/{id}\n\n";

echo "3. SCAN QRIS dengan BCA Mobile atau app lain:\n";
echo "   - Nominal harus muncul OTOMATIS\n";
echo "   - Cek apakah nominal sesuai dengan invoice\n\n";

echo "4. BAYAR invoice:\n";
echo "   - Bayar via QRIS atau transfer bank\n";
echo "   - Cek notifikasi masuk di PayHook\n";
echo "   - Cek webhook log di Laravel\n\n";

echo "==============================================\n";
echo "  GOPAY MERCHANT BENEFITS\n";
echo "==============================================\n\n";

echo "✅ QRIS PERMANENT - Tidak expired seperti DANA Personal\n";
echo "✅ DYNAMIC AMOUNT - Nominal auto-inject saat scan\n";
echo "✅ UNIVERSAL - Bisa dibayar dari BCA, Mandiri, BRI, dll\n";
echo "✅ AUTO-CONFIRM - PayHook deteksi notifikasi → webhook\n";
echo "✅ NO MDR - GoPay Merchant biasanya gratis untuk receive\n\n";

echo "Good luck! 🚀\n\n";
