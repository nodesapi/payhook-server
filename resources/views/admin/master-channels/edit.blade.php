@extends('layouts.admin')

@section('title', 'Edit Master Payment Channel')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Edit Master Payment Channel</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.master-channels.update', $channel->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Channel Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $channel->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Internal Code</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $channel->code) }}" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Channel Type</label>
                                <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="qris" {{ old('type', $channel->type) == 'qris' ? 'selected' : '' }}>QRIS</option>
                                    <option value="ewallet" {{ old('type', $channel->type) == 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
                                    <option value="virtual_account" {{ old('type', $channel->type) == 'virtual_account' ? 'selected' : '' }}>Virtual Account</option>
                                    <option value="bank_transfer" {{ old('type', $channel->type) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer (Manual)</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="logo" class="form-label">Logo / Icon</label>
                                @if($channel->logo_url)
                                    <div class="mb-2">
                                        <img src="{{ $channel->logo_url }}" alt="Current Logo" class="img-thumbnail" style="max-height: 80px;">
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('logo') is-invalid @enderror" id="logo" name="logo" accept="image/*">
                                <small class="text-muted">Leave empty to keep current logo</small>
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', $channel->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Channel is Active</label>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.master-channels.index') }}" class="btn btn-light me-2">Cancel</a>
                            <button type="submit" class="btn bg-gradient-primary">Update Channel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
