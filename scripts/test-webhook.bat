@echo off
echo ====================================
echo Testing PayHook Webhook Manual
echo ====================================
echo.
echo Invoice: INV-20260401-KEIEIU
echo Amount: 5518.00
echo URL: http://192.168.3.105:8000/api/webhook
echo.

curl -X POST "http://192.168.3.105:8000/api/webhook" ^
  -H "Authorization: Bearer 1fec9da1fc93b70be4174aad239745ced5fae0e1c8ead0253ce2c6f289cbd091" ^
  -H "Content-Type: application/json" ^
  -d "{\"amount\":5518.00,\"source\":\"DANA\",\"timestamp\":\"2026-04-01 04:30:00\",\"notification_text\":\"Transfer Rp 5.518 berhasil ke WAHYU SUHANDI\",\"raw_notification\":\"Test Payment\"}"

echo.
echo ====================================
echo Check result:
echo http://192.168.3.105:8000/invoices/4
echo ====================================
pause
