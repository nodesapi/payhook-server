# FIX PAYHOOK NOTIFICATION SERVICE

## Problem
PayHook Activity Logs kosong meskipun notifikasi GoPay Merchant sudah masuk.

## Root Cause
NotificationListenerService tidak running atau permission belum fully granted.

---

## SOLUTION 1: ADB Fix (Recommended)

### Step 1: Enable USB Debugging di HP
1. **Settings** → **About Phone** → Tap **"Build Number"** 7x
2. **Settings** → **System** → **Developer Options** → **USB Debugging ON**
3. Hubungkan HP ke laptop via USB
4. Popup "Allow USB Debugging" → **Allow**

### Step 2: Grant Notification Permission via ADB
```bash
# Check apakah ADB detect HP
adb devices

# Grant notification listener permission secara paksa
adb shell cmd notification allow_listener com.payhook.app/com.payhook.app.service.PaymentNotificationService

# Restart notification service
adb shell cmd notification disallow_listener com.payhook.app/com.payhook.app.service.PaymentNotificationService
adb shell cmd notification allow_listener com.payhook.app/com.payhook.app.service.PaymentNotificationService
```

### Step 3: Test
1. Biarkan PayHook terbuka
2. Create invoice baru & bayar
3. Check Activity Logs

---

## SOLUTION 2: Manual Settings Fix

### Step 1: Revoke & Re-grant Permission
1. **Settings** → **Apps** → **PayHook** → **Force Stop**
2. **Settings** → **Apps** → **Special Access** → **Notification Access**
3. **Toggle OFF** PayHook
4. **Restart HP**
5. **Settings** → **Apps** → **Special Access** → **Notification Access**
6. **Toggle ON** PayHook → Confirm "Allow"

### Step 2: Disable Battery Optimization
1. **Settings** → **Apps** → **PayHook** → **Battery**
2. Set to **"Unrestricted"** atau **"Don't optimize"**

### Step 3: Keep App Alive
1. **Settings** → **Apps** → **PayHook** → **Permissions**
2. Pastikan semua permission granted
3. Buka PayHook → **Lock app** di recent apps (pin icon)

---

## SOLUTION 3: Rebuild APK dengan Debug Logging

Jika masih tidak work, rebuild APK dengan logging lebih verbose.

### Edit: PaymentNotificationService.kt
Tambahkan logging di setiap step untuk debug.

---

## SOLUTION 4: Manual Confirmation (Temporary)

Sambil fix PayHook, gunakan manual confirmation dulu:

```bash
# Check invoice yang pending
php artisan tinker
>>> App\Models\Invoice::where('status', 'pending')->get(['id', 'invoice_number', 'unique_amount'])

# Confirm manual (ganti ID)
>>> $invoice = App\Models\Invoice::find(12);
>>> $invoice->markAsPaid('GoPay Merchant Manual', 'Pembayaran Rp 11.323 diterima');
>>> exit
```

---

## Testing Checklist

After applying fixes:

✅ Notification access ON di Settings
✅ Battery optimization disabled untuk PayHook
✅ PayHook app terbuka dan tidak di-kill Android
✅ GoPay Merchant enabled & toggle hijau
✅ Webhook configured dengan URL & token benar
✅ Laptop & HP di WiFi yang sama (192.168.3.x)
✅ Laravel dev server running (php artisan serve)

Test:
1. Create invoice baru: `php create-invoice-10k.php`
2. Buka di HP browser
3. Scan & bayar via GoPay Merchant
4. Check PayHook Activity Logs → harus muncul!
5. Check Laravel terminal → POST /api/webhook request
6. Refresh invoice page → status PAID

---

## Alternative: Use Android Logcat

Debug real-time kenapa notification tidak ditangkap:

```bash
# Monitor PayHook logs
adb logcat | grep -i "payhook\|payment"

# Monitor notification events
adb logcat | grep -i "notification"
```

Jalankan saat test bayar untuk lihat error messages.
