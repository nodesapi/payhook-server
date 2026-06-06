@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="font-weight-bolder mb-0">Platform Settings & Config</h2>
            <p class="text-sm text-muted">Kelola SMTP Email dan Saluran Pembayaran Platform (Master Tenant).</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success text-white">
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger text-white">
        {{ session('error') }}
    </div>
    @endif
    @if ($errors->any())
    <div class="alert alert-danger text-white">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row">
        <!-- TABS NAV -->
        <div class="col-md-3">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-3">
                    <ul class="nav nav-pills flex-column" id="settingsTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="billing-tab" data-bs-toggle="tab" href="#billing" role="tab">
                                <i class="fas fa-wallet me-2"></i> Master Billing (QRIS)
                            </a>
                        </li>
                        <li class="nav-item mt-1">
                            <a class="nav-link" id="email-tab" data-bs-toggle="tab" href="#email" role="tab">
                                <i class="fas fa-envelope me-2"></i> SMTP Email
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- TABS CONTENT -->
        <div class="col-md-9">
            <div class="tab-content" id="settingsTabContent">
                
                <!-- TAB 1: MASTER BILLING -->
                <div class="tab-pane fade show active" id="billing" role="tabpanel">
                    @if(!$masterTenant)
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom pb-0 pt-4">
                            <h5 class="mb-0 text-primary"><i class="fas fa-info-circle me-2"></i> Inisialisasi Master Billing</h5>
                            <p class="text-sm text-muted mt-2">Anda belum memiliki akun Master Tenant untuk menerima pembayaran langganan pengguna. Masukkan password di bawah ini untuk meng-otomatisasi pembuatan akun <b>finance@cekbayar.com</b>.</p>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.system-config.initialize') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Email Master</label>
                                    <input type="text" class="form-control" value="finance@cekbayar.com" readonly>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password Aplikasi Android</label>
                                    <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
                                    <small class="text-muted">Password ini akan Anda gunakan untuk login di aplikasi Android Relay Payhook.</small>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Buat Akun Billing</button>
                            </form>
                        </div>
                    </div>
                    @else
                    
                    <!-- KELOLA QRIS & BANK -->
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center pb-3 pt-4">
                            <div>
                                <h5 class="mb-0 text-primary"><i class="fas fa-qrcode me-2"></i> QRIS Platform</h5>
                                <p class="text-sm text-muted mt-1 mb-0">QRIS untuk tagihan langganan otomatis</p>
                            </div>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddQris">
                                + Tambah QRIS
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama QRIS</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Gambar</th>
                                            <th class="text-secondary opacity-7"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($masterTenant->qrisTemplates as $qris)
                                        <tr>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0 ps-3">{{ $qris->name }}</p>
                                            </td>
                                            <td>
                                                <img src="{{ Storage::url($qris->image_path) }}" alt="{{ $qris->name }}" class="img-fluid rounded" style="max-height: 50px;">
                                            </td>
                                            <td class="text-end pe-4">
                                                <form action="{{ route('admin.system-config.qris.destroy', $qris->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger mb-0" onclick="return confirm('Hapus QRIS ini?')"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4">
                                                <p class="text-sm text-muted mb-0">Belum ada QRIS platform. Silakan tambahkan.</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center pb-3 pt-4">
                            <div>
                                <h5 class="mb-0 text-primary"><i class="fas fa-university me-2"></i> Rekening Bank Platform</h5>
                                <p class="text-sm text-muted mt-1 mb-0">Opsi transfer bank manual (Dicek oleh Relay Android)</p>
                            </div>
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddBank">
                                + Tambah Bank
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama Bank</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No Rekening</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Atas Nama</th>
                                            <th class="text-secondary opacity-7"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $bankAccounts = \App\Models\BankAccount::where('tenant_id', $masterTenant->id)->get();
                                        @endphp
                                        @forelse($bankAccounts as $bank)
                                        <tr>
                                            <td>
                                                <p class="text-sm font-weight-bold mb-0 ps-3">{{ $bank->bank_name }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm mb-0">{{ $bank->account_number }}</p>
                                            </td>
                                            <td>
                                                <p class="text-sm mb-0">{{ $bank->account_name }}</p>
                                            </td>
                                            <td class="text-end pe-4">
                                                <form action="{{ route('admin.system-config.bank.destroy', $bank->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger mb-0" onclick="return confirm('Hapus Bank ini?')"><i class="fas fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">
                                                <p class="text-sm text-muted mb-0">Belum ada Rekening Bank. Silakan tambahkan.</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    @endif
                </div>

                <!-- TAB 2: EMAIL SMTP -->
                <div class="tab-pane fade" id="email" role="tabpanel">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white border-bottom pb-0 pt-4">
                            <h5 class="mb-0 text-primary"><i class="fas fa-envelope-open-text me-2"></i> Konfigurasi SMTP Email</h5>
                            <p class="text-sm text-muted mt-2">Email ini akan digunakan untuk mengirim *Invoice* pendaftaran dan Notifikasi Lunas ke pengguna. Data disimpan ke file <code>.env</code>.</p>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.system-config.smtp') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-8 mb-3">
                                        <label class="form-label">MAIL_HOST</label>
                                        <input type="text" name="MAIL_HOST" class="form-control" value="{{ $mailConfig['MAIL_HOST'] }}" required>
                                        <small class="text-muted">Contoh: mail.cekbayar.com</small>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">MAIL_PORT</label>
                                        <input type="text" name="MAIL_PORT" class="form-control" value="{{ $mailConfig['MAIL_PORT'] }}" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">MAIL_USERNAME</label>
                                    <input type="text" name="MAIL_USERNAME" class="form-control" value="{{ $mailConfig['MAIL_USERNAME'] }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">MAIL_PASSWORD</label>
                                    <input type="password" name="MAIL_PASSWORD" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password saat ini">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">MAIL_ENCRYPTION</label>
                                        <input type="text" name="MAIL_ENCRYPTION" class="form-control" value="{{ $mailConfig['MAIL_ENCRYPTION'] }}">
                                        <small class="text-muted">ssl / tls / kosong</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">MAIL_FROM_ADDRESS</label>
                                        <input type="email" name="MAIL_FROM_ADDRESS" class="form-control" value="{{ $mailConfig['MAIL_FROM_ADDRESS'] }}" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 mt-2">Simpan Konfigurasi SMTP</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Add QRIS -->
<div class="modal fade" id="modalAddQris" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.system-config.qris') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah QRIS Platform</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama QRIS</label>
                        <input type="text" class="form-control" name="name" placeholder="Misal: QRIS Utama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gambar QRIS</label>
                        <input type="file" class="form-control" name="qris_image" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan QRIS</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Add Bank -->
<div class="modal fade" id="modalAddBank" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('admin.system-config.bank') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Bank Platform</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Bank</label>
                        <input type="text" class="form-control" name="bank_name" placeholder="BCA / Mandiri / BNI" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No Rekening</label>
                        <input type="text" class="form-control" name="account_number" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Atas Nama</label>
                        <input type="text" class="form-control" name="account_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Bank</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
