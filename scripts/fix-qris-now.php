<?php

echo "============================================================" . PHP_EOL;
echo "PANDUAN GENERATE QRIS DANA FRESH (Tidak Expired)" . PHP_EOL;
echo "============================================================" . PHP_EOL;
echo PHP_EOL;

echo "PROBLEM: QRIS DANA Expired" . PHP_EOL;
echo "  → Invoice 5 (tadi berhasil) sekarang gagal" . PHP_EOL;
echo "  → Invoice baru juga gagal" . PHP_EOL;
echo "  → Error: 'Transaksi mengalami gangguan'" . PHP_EOL;
echo PHP_EOL;

echo "PENYEBAB:" . PHP_EOL;
echo "  → QRIS DANA punya masa aktif (30 menit - 1 jam)" . PHP_EOL;
echo "  → Setelah expired, perlu generate ulang" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "CARA FIX - GENERATE QRIS FRESH (5 Menit):" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "STEP 1: Buka Aplikasi DANA di HP" . PHP_EOL;
echo "  → Login ke akun DANA" . PHP_EOL;
echo "  → Pastikan akun sudah verifikasi KTP" . PHP_EOL;
echo PHP_EOL;

echo "STEP 2: Generate QR Fresh" . PHP_EOL;
echo "  → Tap tab 'Terima' (di bawah)" . PHP_EOL;
echo "  → Pilih 'Terima Uang' atau 'Tunjukkan QR'" . PHP_EOL;
echo "  → SCREENSHOT QR code yang muncul" . PHP_EOL;
echo PHP_EOL;

echo "STEP 3: Kirim Screenshot ke PC" . PHP_EOL;
echo "  → Via WhatsApp (kirim ke diri sendiri)" . PHP_EOL;
echo "  → Atau via email/USB cable" . PHP_EOL;
echo PHP_EOL;

echo "STEP 4: Decode QR di Browser PC" . PHP_EOL;
echo "  → Buka: http://192.168.3.105:8000/qris-decoder.html" . PHP_EOL;
echo "  → Tab 'Upload Image'" . PHP_EOL;
echo "  → Upload screenshot QR" . PHP_EOL;
echo "  → Tunggu decode selesai (1-2 detik)" . PHP_EOL;
echo "  → COPY QRIS string hasil decode" . PHP_EOL;
echo "     (panjang ~190-200 chars)" . PHP_EOL;
echo PHP_EOL;

echo "STEP 5: Update Template di Laravel" . PHP_EOL;
echo "  → Buka: http://192.168.3.105:8000/qris-templates/4/edit" . PHP_EOL;
echo "  → HAPUS QRIS string lama" . PHP_EOL;
echo "  → PASTE QRIS string baru (dari decoder)" . PHP_EOL;
echo "  → Klik 'Update'" . PHP_EOL;
echo PHP_EOL;

echo "STEP 6: Test Invoice Baru" . PHP_EOL;
echo "  → php create-invoice-10k.php" . PHP_EOL;
echo "  → Scan QR code → Harus berhasil! ✅" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "CATATAN PENTING:" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "✅ Upload QR HANYA SEKALI!" . PHP_EOL;
echo "   Setelah template updated, semua invoice otomatis pakai QR baru" . PHP_EOL;
echo PHP_EOL;

echo "✅ QRIS Fresh Tidak Expired Cepat" . PHP_EOL;
echo "   QR yang di-generate langsung dari DANA app biasanya lebih stabil" . PHP_EOL;
echo PHP_EOL;

echo "✅ Nominal Otomatis Beda Per Invoice" . PHP_EOL;
echo "   System inject amount otomatis (10.833, 10.844, dll)" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "ALTERNATIVE: Pakai Bank Transfer (Tidak Perlu QR)" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "Jika QRIS masih bermasalah, pakai transfer BCA:" . PHP_EOL;
echo "  → Customer transfer langsung ke rekening BCA" . PHP_EOL;
echo "  → Notifikasi masuk → PayHook detect" . PHP_EOL;
echo "  → Auto-confirm sama seperti QRIS ✅" . PHP_EOL;
echo "  → Lebih reliable, tidak ada masalah expired" . PHP_EOL;
echo PHP_EOL;

echo "Setup bank transfer sudah ready:" . PHP_EOL;
echo "  php setup-bank-transfer.php" . PHP_EOL;
echo PHP_EOL;

echo str_repeat("=", 60) . PHP_EOL;
echo "SILAKAN GENERATE QRIS FRESH SEKARANG!" . PHP_EOL;
echo str_repeat("=", 60) . PHP_EOL;
echo PHP_EOL;

echo "1. Buka DANA → Screenshot QR" . PHP_EOL;
echo "2. Upload ke: http://192.168.3.105:8000/qris-decoder.html" . PHP_EOL;
echo "3. Copy string hasil decode" . PHP_EOL;
echo "4. Update: http://192.168.3.105:8000/qris-templates/4/edit" . PHP_EOL;
echo "5. Test buat invoice baru ✅" . PHP_EOL;
