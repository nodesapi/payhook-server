@extends('layouts.app')

@section('title', 'QRIS Templates')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">QRIS Templates</h1>
        <div class="space-x-2">
            <a href="{{ route('qris-templates.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                + Add Template
            </a>
            <a href="/qris-decoder.html" target="_blank" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                📷 Scan QR Code
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($templates->isEmpty())
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
            <p class="font-bold">Belum ada QRIS template!</p>
            <p class="mt-2">Silakan scan QR code BCA/DANA Anda terlebih dahulu menggunakan tool decoder, lalu tambahkan template.</p>
            <a href="/qris-decoder.html" target="_blank" class="text-blue-600 underline">Scan QR Code →</a>
        </div>
    @else
        <div class="grid gap-4">
            @foreach($templates as $template)
            <div class="border rounded-lg p-4 {{ $template->is_active ? 'bg-white' : 'bg-gray-100' }}">
                <div class="flex justify-between items-start">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-semibold">{{ $template->name }}</h3>
                            <span class="px-2 py-1 text-xs rounded {{ $template->type === 'bank' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $template->type }}
                            </span>
                            @if($template->is_active)
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Active</span>
                            @endif
                        </div>
                        
                        @if($template->account_name || $template->account_number)
                        <div class="mt-2 text-sm text-gray-600">
                            @if($template->account_name)
                                <div>{{ $template->account_name }}</div>
                            @endif
                            @if($template->account_number)
                                <div>{{ $template->account_number }}</div>
                            @endif
                        </div>
                        @endif

                        <div class="mt-2 text-xs text-gray-400">
                            QRIS Length: {{ strlen($template->qris_string) }} chars
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('qris-templates.edit', $template) }}" 
                           class="px-3 py-1 text-sm bg-blue-500 text-white rounded hover:bg-blue-600">
                            Edit
                        </a>
                        <a href="{{ route('qris-templates.test', $template) }}" 
                           class="px-3 py-1 text-sm bg-gray-200 rounded hover:bg-gray-300">
                            Test
                        </a>
                        <form action="{{ route('qris-templates.destroy', $template) }}" 
                              method="POST" 
                              onsubmit="return confirm('Yakin ingin menghapus template ini?')"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1 text-sm bg-red-500 text-white rounded hover:bg-red-600">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
