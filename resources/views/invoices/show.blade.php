@extends('layouts.app')

@section('title', 'Detail Invoice - ' . $invoice->invoice_number)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card invoice-card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-receipt"></i> {{ $invoice->invoice_number }}</h4>
                @if($invoice->status === 'pending')
                    <span class="badge bg-warning text-dark">
                        <i class="bi bi-clock"></i> Menunggu Pembayaran
                    </span>
                @elseif($invoice->status === 'paid')
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle"></i> LUNAS
                    </span>
                @elseif($invoice->status === 'expired')
                    <span class="badge bg-secondary">
                        <i class="bi bi-x-circle"></i> Expired
                    </span>
                @else
                    <span class="badge bg-danger">
                        <i class="bi bi-x-circle"></i> Dibatalkan
                    </span>
                @endif
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Customer</h6>
                        <p class="mb-1"><strong>{{ $invoice->customer_name }}</strong></p>
                        @if($invoice->customer_email)
                            <p class="mb-1"><i class="bi bi-envelope"></i> {{ $invoice->customer_email }}</p>
                        @endif
                        @if($invoice->customer_phone)
                            <p class="mb-0"><i class="bi bi-phone"></i> {{ $invoice->customer_phone }}</p>
                        @endif
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h6 class="text-muted mb-2">Tanggal</h6>
                        <p class="mb-1"><strong>{{ $invoice->created_at->format('d F Y') }}</strong></p>
                        <p class="mb-0">{{ $invoice->created_at->format('H:i') }} WIB</p>
                    </div>
                </div>

                <hr>

                @if($invoice->description)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Deskripsi</h6>
                    <p>{{ $invoice->description }}</p>
                </div>
                @endif

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Nominal Asli</h6>
                        <h4>Rp {{ number_format($invoice->amount, 0, ',', '.') }}</h4>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-2">Angka Unik</h6>
                        <h4 class="text-primary">+ {{ $invoice->unique_suffix }}</h4>
                    </div>
                </div>

                <div class="alert alert-primary mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total yang harus dibayar:</h6>
                            <small class="text-muted">Transfer ke rekening Anda dengan nominal ini</small>
                        </div>
                        <div class="text-end">
                            <h2 class="mb-0">Rp {{ number_format($invoice->unique_amount, 0, ',', '.') }}</h2>
                        </div>
                    </div>
                </div>

                @if($invoice->status === 'pending')
                
                <!-- Bank Transfer Payment (Primary Method) -->
                @if($invoice->bankAccount)
                <div class="card mb-4 border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-bank"></i> Pembayaran via Transfer Bank</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-success text-center mb-4">
                            <h6 class="mb-2"><i class="bi bi-cash-stack"></i> <strong>Total Pembayaran:</strong></h6>
                            <h2 class="text-success mb-2 fw-bold">Rp {{ number_format($invoice->unique_amount, 0, ',', '.') }}</h2>
                            <small class="text-muted d-block">
                                <i class="bi bi-key"></i> Nominal dengan kode unik ({{ number_format($invoice->amount, 0, ',', '.') }} + {{ $invoice->unique_suffix }})
                            </small>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card border-secondary">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-3">Transfer ke Rekening:</h6>
                                        <div class="mb-2">
                                            <small class="text-muted">Bank</small>
                                            <h5 class="mb-0 fw-bold">{{ $invoice->bankAccount->bank_name }}</h5>
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Nomor Rekening</small>
                                            <h5 class="mb-0 fw-bold">{{ $invoice->bankAccount->account_number }}</h5>
                                        </div>
                                        <div>
                                            <small class="text-muted">Atas Nama</small>
                                            <h5 class="mb-0 fw-bold">{{ $invoice->bankAccount->account_name }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info">
                                    <h6 class="mb-2"><i class="bi bi-info-circle"></i> <strong>Cara Bayar:</strong></h6>
                                    <ol class="mb-0 ps-3 small">
                                        <li>Buka <strong>{{ $invoice->bankAccount->bank_name }} mobile</strong> atau ATM</li>
                                        <li>Pilih <strong>Transfer</strong> → Transfer Antar Rekening</li>
                                        <li>Input nominal: <strong class="text-danger">Rp {{ number_format($invoice->unique_amount, 0, ',', '.') }}</strong></li>
                                        <li>Isi <strong>Berita</strong>: <code class="small">{{ $invoice->invoice_number }}</code></li>
                                        <li>Konfirmasi transfer ✅</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-warning mb-3">
                            <small><i class="bi bi-exclamation-triangle"></i> <strong>PENTING:</strong> Transfer dengan nominal <strong>PERSIS Rp {{ number_format($invoice->unique_amount, 0, ',', '.') }}</strong> agar sistem otomatis mendeteksi pembayaran. Jangan bulatkan!</small>
                        </div>

                        <div class="text-muted small">
                            <p class="mb-1"><i class="bi bi-check-circle text-success"></i> Pembayaran otomatis terkonfirmasi dalam 1-2 menit setelah transfer</p>
                            <p class="mb-0"><i class="bi bi-shield-check text-primary"></i> Tidak ada biaya tambahan - Aman & Terpercaya</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- QRIS Payment (Alternative) -->
                @if($qrSvg && $invoice->qrisTemplate)
                <div class="card mb-4 border-secondary">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="bi bi-qr-code"></i> Alternatif: Pembayaran via QRIS</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning small mb-3">
                            <i class="bi bi-exclamation-triangle"></i> <strong>Catatan:</strong> QRIS mungkin expired. Jika tidak bisa scan, gunakan <strong>Transfer Bank</strong> di atas.
                        </div>

                        <!-- QR Code -->
                        <div class="text-center mb-3">
                            <div class="d-inline-block p-3 bg-white border border-2 rounded">
                                {!! $qrSvg !!}
                            </div>
                        </div>

                        <!-- Nominal -->
                        <div class="alert alert-light text-center mb-0">
                            <small class="text-muted d-block mb-1">Total Pembayaran:</small>
                            <h5 class="text-dark mb-0">Rp {{ number_format($invoice->unique_amount, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
                @endif

                @endif

                @if($invoice->status === 'paid')
                <div class="alert alert-success">
                    <h6><i class="bi bi-check-circle"></i> Pembayaran Diterima</h6>
                    <p class="mb-1"><strong>Tanggal:</strong> {{ $invoice->paid_at->format('d F Y H:i') }} WIB</p>
                    <p class="mb-1"><strong>Sumber:</strong> {{ $invoice->payment_source }}</p>
                    @if($invoice->payment_notification_text)
                        <p class="mb-0"><strong>Detail:</strong> {{ $invoice->payment_notification_text }}</p>
                    @endif
                </div>
                @endif

                @if($invoice->status === 'pending')
                <div class="alert alert-warning">
                    <p class="mb-0">
                        <i class="bi bi-clock"></i>
                        <strong>Berlaku hingga:</strong> {{ $invoice->expires_at->format('d F Y H:i') }} WIB
                        ({{ $invoice->expires_at->diffForHumans() }})
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="bi bi-gear"></i> Aksi</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('invoices.print', $invoice) }}" 
                       class="btn btn-outline-primary" 
                       target="_blank">
                        <i class="bi bi-printer"></i> Cetak Invoice
                    </a>

                    <a href="{{ route('invoices.pdf', $invoice) }}" 
                       class="btn btn-outline-success">
                        <i class="bi bi-file-earmark-pdf"></i> Download PDF
                    </a>

                    @if($invoice->status === 'pending')
                        <form method="POST" action="{{ route('invoices.cancel', $invoice) }}" 
                              onsubmit="return confirm('Yakin ingin membatalkan invoice ini?')">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-x-circle"></i> Batalkan Invoice
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>

        @if($invoice->status === 'pending')
        <div class="card mt-3">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Instruksi</h6>
            </div>
            <div class="card-body">
                <ol class="mb-0 ps-3">
                    <li>Kirim invoice ini ke customer</li>
                    <li>Minta customer transfer ke rekening Anda</li>
                    <li>Cekbayar akan otomatis mendeteksi pembayaran</li>
                    <li>Status akan berubah menjadi "LUNAS"</li>
                </ol>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
