<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; padding: 40px; color: #333; }
        .invoice-header { display: flex; justify-content: space-between; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 3px solid #0d6efd; }
        .company-info h1 { color: #0d6efd; margin-bottom: 10px; }
        .invoice-meta { text-align: right; }
        .invoice-meta h2 { color: #666; font-size: 1.2rem; }
        .details-section { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .detail-box { flex: 1; }
        .detail-box h3 { color: #666; font-size: 0.9rem; margin-bottom: 10px; text-transform: uppercase; }
        .detail-box p { margin: 5px 0; }
        .description { margin-bottom: 30px; padding: 20px; background-color: #f8f9fa; border-radius: 5px; }
        .amount-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .amount-table td { padding: 15px; border-bottom: 1px solid #dee2e6; }
        .amount-table .label { color: #666; }
        .amount-table .value { text-align: right; font-weight: bold; }
        .total-row { background-color: #0d6efd; color: white; }
        .total-row td { font-size: 1.5rem; border: none; }
        .payment-instructions { margin-top: 40px; padding: 20px; background-color: #fff3cd; border-left: 4px solid #ffc107; }
        .payment-instructions h3 { color: #856404; margin-bottom: 10px; }
        .status-badge { display: inline-block; padding: 5px 15px; border-radius: 3px; font-weight: bold; margin-top: 10px; }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-paid { background-color: #28a745; color: white; }
        @media print { body { padding: 20px; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="invoice-header">
        <div class="company-info">
            <h1>INVOICE</h1>
            <p>Perusahaan Anda</p>
            <p>Alamat Perusahaan</p>
            <p>Telp: 0123456789</p>
        </div>
        <div class="invoice-meta">
            <h2>{{ $invoice->invoice_number }}</h2>
            <p><strong>Tanggal:</strong> {{ $invoice->created_at->format('d F Y') }}</p>
            <span class="status-badge {{ $invoice->status === 'paid' ? 'status-paid' : 'status-pending' }}">
                {{ strtoupper($invoice->status) }}
            </span>
        </div>
    </div>

    <div class="details-section">
        <div class="detail-box">
            <h3>Kepada:</h3>
            <p><strong>{{ $invoice->customer_name }}</strong></p>
            @if($invoice->customer_email)
                <p>{{ $invoice->customer_email }}</p>
            @endif
            @if($invoice->customer_phone)
                <p>{{ $invoice->customer_phone }}</p>
            @endif
        </div>
        <div class="detail-box" style="text-align: right;">
            <h3>Berlaku Hingga:</h3>
            <p><strong>{{ $invoice->expires_at->format('d F Y') }}</strong></p>
            <p>{{ $invoice->expires_at->format('H:i') }} WIB</p>
        </div>
    </div>

    @if($invoice->description)
    <div class="description">
        <h3 style="margin-bottom: 10px; color: #666;">Deskripsi:</h3>
        <p>{{ $invoice->description }}</p>
    </div>
    @endif

    <table class="amount-table">
        <tr>
            <td class="label">Nominal Asli</td>
            <td class="value">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Angka Unik</td>
            <td class="value">+ {{ $invoice->unique_suffix }}</td>
        </tr>
        <tr class="total-row">
            <td>TOTAL YANG HARUS DIBAYAR</td>
            <td style="text-align: right;">Rp {{ number_format($invoice->unique_amount, 0, ',', '.') }}</td>
        </tr>
    </table>

    @if($invoice->status === 'pending')
    <div class="payment-instructions">
        <h3>Instruksi Pembayaran</h3>
        <ol style="margin-left: 20px; margin-top: 10px;">
            <li>Transfer ke rekening: <strong>[NAMA BANK - NO. REKENING - ATAS NAMA]</strong></li>
            <li>Nominal yang ditransfer: <strong>Rp {{ number_format($invoice->unique_amount, 0, ',', '.') }}</strong></li>
            <li>Pembayaran akan dikonfirmasi otomatis</li>
            <li>Berlaku hingga: <strong>{{ $invoice->expires_at->format('d F Y H:i') }} WIB</strong></li>
        </ol>
    </div>
    @endif

    @if($invoice->status === 'paid')
    <div class="payment-instructions" style="background-color: #d4edda; border-left-color: #28a745;">
        <h3 style="color: #155724;">✓ Pembayaran Telah Diterima</h3>
        <p><strong>Tanggal Bayar:</strong> {{ $invoice->paid_at->format('d F Y H:i') }} WIB</p>
        <p><strong>Sumber:</strong> {{ $invoice->payment_source }}</p>
        <p style="margin-top: 10px;">Terima kasih atas pembayaran Anda!</p>
    </div>
    @endif

    <div class="no-print" style="margin-top: 40px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 30px; font-size: 16px; cursor: pointer; background-color: #0d6efd; color: white; border: none; border-radius: 5px;">
            Cetak Invoice
        </button>
    </div>
</body>
</html>
