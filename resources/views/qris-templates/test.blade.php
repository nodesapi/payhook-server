@extends('layouts.app')

@section('title', 'Test QRIS - ' . $qrisTemplate->name)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('qris-templates.index') }}" class="text-blue-600 hover:underline">← Back</a>
        <h1 class="text-2xl font-bold mt-2">Test QRIS Template</h1>
        <p class="text-gray-600">{{ $qrisTemplate->name }}</p>
    </div>

    <div class="bg-white rounded-lg border p-6 mb-6">
        <form method="GET" action="{{ route('qris-templates.test', $qrisTemplate) }}" class="mb-6">
            <div class="flex gap-2">
                <div class="flex-1">
                    <label class="block text-sm font-medium mb-2">Test Amount</label>
                    <input type="number" name="amount" value="{{ $amount }}" 
                           class="w-full px-3 py-2 border rounded"
                           min="1000" step="0.01">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">
                        Generate
                    </button>
                </div>
            </div>
        </form>

        <div class="text-center">
            <div class="inline-block p-4 bg-white border-2 border-gray-200 rounded">
                {!! $qrSvg !!}
            </div>
            <div class="mt-4 text-lg font-bold">
                Rp {{ number_format($amount, 2, ',', '.') }}
            </div>
            <p class="text-sm text-gray-500">Scan QR ini dengan app e-wallet untuk test</p>
        </div>

        <div class="mt-6">
            <label class="block text-sm font-medium mb-2">Dynamic QRIS String:</label>
            <textarea readonly rows="4" 
                      class="w-full px-3 py-2 border rounded bg-gray-50 font-mono text-xs"
            >{{ $dynamicQris }}</textarea>
        </div>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded p-4">
        <p class="font-medium">💡 Cara Test:</p>
        <ol class="list-decimal list-inside mt-2 space-y-1 text-sm">
            <li>Scan QR code di atas dengan app e-wallet (DANA/GoPay/dll)</li>
            <li>Pastikan nominal yang muncul sesuai: Rp {{ number_format($amount, 2, ',', '.') }}</li>
            <li>Jangan bayar! Cukup cek nominal sudah sesuai.</li>
            <li>Jika nominal berbeda atau error, QRIS string tidak valid.</li>
        </ol>
    </div>
</div>
@endsection
