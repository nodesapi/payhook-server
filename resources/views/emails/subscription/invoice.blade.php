<x-mail::message>
# Halo {{ $tenant->name }},

Terima kasih telah memilih Payhook. Berikut adalah rincian tagihan untuk langganan Anda.

<x-mail::panel>
**No Invoice:** {{ $invoice->invoice_number }}<br>
**Nominal Bayar:** Rp {{ number_format($invoice->unique_amount, 0, ',', '.') }}<br>
**Batas Pembayaran:** {{ $invoice->expires_at->format('d M Y H:i') }}
</x-mail::panel>

<x-mail::button :url="route('invoices.show', $invoice->invoice_number)">
Lihat & Bayar Invoice
</x-mail::button>

Pembayaran Anda akan dideteksi secara otomatis oleh sistem kami. Segera setelah pembayaran berhasil, akun Anda akan langsung aktif.

Terima kasih,<br>
Tim Payhook
</x-mail::message>
