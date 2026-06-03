@extends('layouts.app')

@section('title', 'Buat Invoice Baru')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Buat Invoice Baru</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('invoices.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Nama Customer <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            class="form-control @error('customer_name') is-invalid @enderror" 
                            id="customer_name" 
                            name="customer_name" 
                            value="{{ old('customer_name') }}"
                            required
                            placeholder="Contoh: PT Maju Jaya"
                        >
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="customer_email" class="form-label">Email Customer</label>
                        <input 
                            type="email" 
                            class="form-control @error('customer_email') is-invalid @enderror" 
                            id="customer_email" 
                            name="customer_email" 
                            value="{{ old('customer_email') }}"
                            placeholder="customer@email.com"
                        >
                        @error('customer_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="customer_phone" class="form-label">No. Telepon</label>
                        <input 
                            type="text" 
                            class="form-control @error('customer_phone') is-invalid @enderror" 
                            id="customer_phone" 
                            name="customer_phone" 
                            value="{{ old('customer_phone') }}"
                            placeholder="08123456789"
                        >
                        @error('customer_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea 
                            class="form-control @error('description') is-invalid @enderror" 
                            id="description" 
                            name="description" 
                            rows="3"
                            placeholder="Deskripsi invoice / item yang dibeli"
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Nominal (Rp) <span class="text-danger">*</span></label>
                        <input 
                            type="number" 
                            class="form-control @error('amount') is-invalid @enderror" 
                            id="amount" 
                            name="amount" 
                            value="{{ old('amount') }}"
                            required
                            min="1000"
                            step="1"
                            placeholder="100000"
                        >
                        <small class="text-muted">
                            Nominal asli. Sistem akan otomatis menambahkan angka unik 1-999.
                        </small>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if($qrisTemplates->isNotEmpty())
                    <div class="mb-3">
                        <label for="qris_template_id" class="form-label">QRIS Template (Opsional)</label>
                        <select 
                            class="form-select @error('qris_template_id') is-invalid @enderror" 
                            id="qris_template_id" 
                            name="qris_template_id"
                        >
                            <option value="">-- Tidak generate QRIS --</option>
                            @foreach($qrisTemplates as $template)
                            <option value="{{ $template->id }}" {{ old('qris_template_id') == $template->id ? 'selected' : '' }}>
                                {{ $template->name }} ({{ $template->account_name ?? $template->account_number }})
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">
                            Pilih template untuk auto-generate QRIS dengan nominal unik.
                        </small>
                        @error('qris_template_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @else
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        Belum ada QRIS template. 
                        <a href="{{ route('qris-templates.create') }}" target="_blank" class="alert-link">Tambah QRIS template →</a>
                    </div>
                    @endif

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Nominal Unik:</strong> Sistem akan otomatis menambahkan angka random (Rp 1-999) 
                        ke nominal asli untuk memudahkan identifikasi pembayaran.
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('invoices.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Buat Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
