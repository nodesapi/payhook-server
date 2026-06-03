@echo off
echo ====================================================
echo TEST WEBHOOK AUTO-CONFIRM PAYMENT
echo ====================================================
echo.
echo Invoice: INV-20260401-XO03QZ
echo Amount: Rp 10.833
echo.
echo Simulating PayHook webhook notification...
echo.

curl -X POST "http://192.168.3.105:8000/api/webhook" ^
  -H "Authorization: Bearer 1fec9da1fc93b70be4174aad239745ced5fae0e1c8ead0253ce2c6f289cbd091" ^
  -H "Content-Type: application/json" ^
  -d "{\"amount\":10833.00,\"source\":\"BCA mobile\",\"timestamp\":\"2026-04-01 04:55:00\",\"notification_text\":\"Transfer Rp 10.833 ke WAHYU SUHANDI berhasil. INV-20260401-XO03QZ\",\"raw_notification\":\"BCA mobile - Transfer berhasil\"}"

echo.
echo.
echo ====================================================
echo CHECK RESULT:
echo ====================================================
echo.
echo 1. Buka invoice: http://192.168.3.105:8000/invoices/5
echo.
echo 2. Status harus berubah: pending -^> LUNAS
echo.
echo 3. Tanggal pembayaran tercatat
echo.
echo 4. Detail notifikasi tersimpan
echo.
echo ====================================================
pause
