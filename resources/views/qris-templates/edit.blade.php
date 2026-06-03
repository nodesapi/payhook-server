@extends('layouts.app')

@section('title', 'Edit QRIS Template')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('qris-templates.index') }}" class="text-blue-600 hover:underline">← Back</a>
        <h1 class="text-2xl font-bold mt-2">Edit QRIS Template</h1>
    </div>

    <form action="{{ route('qris-templates.update', $template) }}" method="POST" class="bg-white rounded-lg border p-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Name *</label>
            <input type="text" name="name" value="{{ old('name', $template->name) }}" 
                   placeholder="e.g. BCA QRIS, DANA Personal"
                   class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500"
                   required>
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Type *</label>
            <select name="type" 
                    class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500"
                    required>
                <option value="bank" {{ old('type', $template->type) === 'bank' ? 'selected' : '' }}>Bank</option>
                <option value="ewallet" {{ old('type', $template->type) === 'ewallet' ? 'selected' : '' }}>E-Wallet</option>
            </select>
            @error('type')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">QRIS String *</label>
            <textarea name="qris_string" rows="8" 
                      placeholder="Paste QRIS string dari decoder..."
                      class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500 font-mono text-sm"
                      required>{{ old('qris_string', $template->qris_string) }}</textarea>
            <p class="text-xs text-gray-500 mt-1">Format: 00020101021126... (biasanya 200+ karakter)</p>
            @error('qris_string')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Account Name</label>
            <input type="text" name="account_name" value="{{ old('account_name', $template->account_name) }}" 
                   placeholder="e.g. WAHYU SUHANDI"
                   class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500">
            @error('account_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium mb-2">Account Number</label>
            <input type="text" name="account_number" value="{{ old('account_number', $template->account_number) }}" 
                   placeholder="e.g. 6801467137"
                   class="w-full px-3 py-2 border rounded focus:ring-2 focus:ring-blue-500">
            @error('account_number')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                Update Template
            </button>
            <a href="{{ route('qris-templates.index') }}" 
               class="bg-gray-200 text-gray-700 px-6 py-2 rounded hover:bg-gray-300">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
