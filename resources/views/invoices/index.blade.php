@extends('layouts.app')

@section('title', 'Daftar Invoice')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="bi bi-receipt"></i> Daftar Invoice</h2>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Buat Invoice Baru
        </a>
    </div>
</div>

@if($invoices->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Belum ada invoice. <a href="{{ route('invoices.create') }}">Buat invoice pertama Anda</a>.
    </div>
@else
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Invoice</th>
                            <th>Customer</th>
                            <th>Nominal Asli</th>
                            <th>Nominal Unik</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                        <tr>
                            <td><strong>{{ $invoice->invoice_number }}</strong></td>
                            <td>
                                {{ $invoice->customer_name }}
                                @if($invoice->customer_email)
                                    <br><small class="text-muted">{{ $invoice->customer_email }}</small>
                                @endif
                            </td>
                            <td>Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                            <td>
                                <strong class="text-primary">
                                    Rp {{ number_format($invoice->unique_amount, 0, ',', '.') }}
                                </strong>
                                <br><small class="text-muted">(+{{ $invoice->unique_suffix }})</small>
                            </td>
                            <td>
                                @if($invoice->status === 'pending')
                                    <span class="badge bg-warning text-dark status-badge">
                                        <i class="bi bi-clock"></i> Pending
                                    </span>
                                @elseif($invoice->status === 'paid')
                                    <span class="badge bg-success status-badge">
                                        <i class="bi bi-check-circle"></i> Paid
                                    </span>
                                @elseif($invoice->status === 'expired')
                                    <span class="badge bg-secondary status-badge">
                                        <i class="bi bi-x-circle"></i> Expired
                                    </span>
                                @else
                                    <span class="badge bg-danger status-badge">
                                        <i class="bi bi-x-circle"></i> Cancelled
                                    </span>
                                @endif
                            </td>
                            <td>
                                {{ $invoice->created_at->format('d M Y') }}
                                <br><small class="text-muted">{{ $invoice->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $invoices->links() }}
    </div>
@endif
@endsection
