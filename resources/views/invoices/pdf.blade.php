<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .details { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        .total { background-color: #f0f0f0; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p>{{ $invoice->invoice_number }}</p>
    </div>
    
    <div class="details">
        <p><strong>Customer:</strong> {{ $invoice->customer_name }}</p>
        <p><strong>Date:</strong> {{ $invoice->created_at->format('d F Y') }}</p>
    </div>
    
    <table>
        <tr>
            <td>Nominal Asli</td>
            <td align="right">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Angka Unik</td>
            <td align="right">+ {{ $invoice->unique_suffix }}</td>
        </tr>
        <tr class="total">
            <td>TOTAL</td>
            <td align="right">Rp {{ number_format($invoice->unique_amount, 0, ',', '.') }}</td>
        </tr>
    </table>
</body>
</html>
