<x-mail::message>
# Pembayaran Berhasil! 🎉

Halo {{ $tenant->name }},

Kami telah menerima pembayaran Anda untuk paket **{{ $planName }}**. Masa aktif langganan Anda telah diperpanjang.

Anda kini dapat menggunakan semua layanan Payhook dan API Relay tanpa hambatan.

<x-mail::button :url="route('dashboard')">
Masuk ke Dashboard
</x-mail::button>

Terima kasih telah mempercayakan transaksi bisnis Anda kepada Payhook.<br>
Tim Payhook
</x-mail::message>
