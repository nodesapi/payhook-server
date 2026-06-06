@extends('layouts.tenant')

@section('content')

<!-- Page Header -->
<div class="mb-12">
    <h1 class="text-4xl font-bold text-white tracking-normal uppercase">API <span class="text-supabase-accent">Playground</span></h1>
    <p class="text-supabase-muted mt-2">Test your integration logic and payload delivery in a sandboxed environment.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left Column: Credentials & Connection -->
    <div class="lg:col-span-1 space-y-8">
        <!-- API Credentials -->
        <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4">
                <span class="px-3 py-1 bg-supabase-accent/10 text-supabase-accent text-[8px] font-bold uppercase tracking-wider border border-supabase-accent/20 rounded-full">Secure Node</span>
            </div>
            
            <h2 class="text-xs font-bold text-white uppercase tracking-wider mb-6 flex items-center">
                <span class="w-1.5 h-1.5 bg-supabase-accent rounded-full mr-3 shadow-[0_0_10px_rgba(251,191,36,0.5)]"></span>
                Node Identity
            </h2>

            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Secret API Key</label>
                    <div class="relative group">
                        <input type="password" value="{{ $tenant->api_key_production }}" readonly id="apiKey" 
                               class="sb-input !pr-12 font-mono text-xs">
                        <button onclick="toggleVisibility('apiKey', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-supabase-muted hover:text-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Webhook Endpoint</label>
                    <input type="text" value="{{ $tenant->webhook_url ?? 'NODE_NOT_CONFIGURED' }}" readonly 
                           class="sb-input font-mono text-xs {{ $tenant->webhook_url ? 'text-white' : 'text-red-500' }}">
                </div>
            </div>

            <div class="mt-8 p-4 bg-supabase-dark border border-supabase-border rounded-2xl flex items-start space-x-3">
                <div class="p-2 bg-blue-500/10 text-blue-500 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <p class="text-[9px] text-supabase-muted font-bold uppercase leading-relaxed tracking-normal">Security Alert: Rotate keys immediately if compromised via the <a href="{{ route('tenant.settings') }}" class="text-supabase-accent underline">Control Panel</a>.</p>
            </div>
        </div>

        <!-- Android Sync Status -->
        <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 shadow-2xl overflow-hidden group">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-xs font-bold text-white uppercase tracking-wider">Android Bridge</h2>
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 rounded-full {{ $androidAppConnected ? 'bg-green-500 animate-pulse' : 'bg-supabase-muted' }}"></div>
                    <span class="text-[8px] font-bold uppercase text-white tracking-wider">{{ $androidAppConnected ? 'Active' : 'Offline' }}</span>
                </div>
            </div>

            <div class="space-y-4">
                <div class="p-4 bg-supabase-dark border border-supabase-border rounded-2xl group-hover:border-supabase-accent/30 transition-colors">
                    <p class="text-[8px] font-bold text-supabase-muted uppercase tracking-wider mb-1">Latest Pulse</p>
                    <p class="text-xs font-bold text-white uppercase">{{ $latestPaymentLog ? $latestPaymentLog->created_at->diffForHumans() : 'No activity logged' }}</p>
                </div>
                @if(!$androidAppConnected)
                    <div class="p-6 bg-amber-500/5 border border-amber-500/20 rounded-2xl">
                        <p class="text-[9px] text-amber-500 font-bold uppercase tracking-wider mb-2 italic">Bridge Required</p>
                        <p class="text-[10px] text-supabase-muted font-bold uppercase leading-relaxed">Download the APK from settings to begin notification capture.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column: Testing Tools -->
    <div class="lg:col-span-2 space-y-8">
        <!-- Transaction Simulator -->
        <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 shadow-2xl relative overflow-hidden">
            <h2 class="text-xs font-bold text-white uppercase tracking-wider mb-8 flex items-center">
                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-3 shadow-[0_0_10px_rgba(34,197,94,0.5)]"></span>
                Transaction Simulator
            </h2>

            <form id="createTransactionForm" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @csrf
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Target Channel</label>
                        <select name="payment_channel_id" required class="sb-input bg-supabase-dark">
                            @foreach($channels as $channel)
                                <option value="{{ $channel->id }}">{{ $channel->channel_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Magnitude (Rp)</label>
                        <div class="relative">
                            <span class="absolute text-supabase-muted font-bold text-xs z-10" style="left: 16px; top: 50%; transform: translateY(-50%);">Rp</span>
                            <input type="text" id="amountDisplay" placeholder="100.000" class="sb-input font-mono font-bold tracking-wider relative z-0 w-full" style="padding-left: 45px !important;" required oninput="formatRupiah(this)">
                            <input type="hidden" name="amount" id="amountActual">
                        </div>
                    </div>
                </div>
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Entity Identity</label>
                        <input type="text" name="customer_name" placeholder="John Doe" class="sb-input" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">External Reference</label>
                        <input type="text" name="external_id" placeholder="TEST-ORDER-001" class="sb-input">
                    </div>
                </div>
                <div class="md:col-span-2 pt-4">
                    <button type="submit" class="sb-button-primary !w-full !py-4 uppercase font-bold tracking-wider">
                        Execute Payload Generation
                    </button>
                </div>
            </form>

            <div id="transactionResult" class="mt-8 hidden animate-in fade-in slide-in-from-top-4 duration-500">
                <div class="border-t border-supabase-border pt-8" id="transactionResultContent"></div>
            </div>
        </div>

        <!-- External Webhook Test -->
        <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 shadow-2xl">
            <h2 class="text-xs font-bold text-white uppercase tracking-wider mb-8 flex items-center">
                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-3 shadow-[0_0_10px_rgba(59,130,246,0.5)]"></span>
                Webhook Callback Tester
            </h2>

            <form id="testWebhookForm" class="space-y-8">
                @csrf
                <div class="space-y-4">
                    <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Recipient Endpoint</label>
                    <div class="flex flex-col md:flex-row gap-4">
                        <input type="url" name="webhook_url" id="testWebhookUrl" placeholder="https://webhook.site/..." class="sb-input flex-1" required>
                        <button type="button" onclick="window.open('https://webhook.site', '_blank')" class="sb-button-secondary !w-auto !px-8 text-[10px] uppercase font-bold whitespace-nowrap">Open Hook Site</button>
                    </div>
                    <p class="text-[8px] text-supabase-muted font-bold uppercase tracking-normal">HMAC SHA256 signatures will be automatically computed for the payload.</p>
                </div>

                <button type="submit" class="sb-button-secondary !w-full !py-4 !bg-blue-500/10 !border-blue-500/20 !text-blue-500 hover:!bg-blue-500 hover:!text-white font-bold uppercase tracking-wider">
                    Transmit Diagnostic Payload
                </button>
            </form>

            <div id="webhookResult" class="mt-8 hidden border-t border-supabase-border pt-8">
                <div id="webhookResultContent"></div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleVisibility(id, btn) {
    const el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
}

function formatRupiah(input) {
    let value = input.value.replace(/[^,\d]/g, '').toString();
    let split = value.split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);
    
    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    
    rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    input.value = rupiah;
    
    // Set actual hidden value
    document.getElementById('amountActual').value = value;
}

document.getElementById('createTransactionForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = e.target.querySelector('button');
    const originalText = btn.textContent;
    btn.textContent = 'GENERATING...';
    btn.disabled = true;

    try {
        const res = await fetch('{{ route("tenant.api-playground.create-transaction") }}', {
            method: 'POST',
            body: new FormData(e.target)
        });
        const data = await res.json();
        
        const content = document.getElementById('transactionResultContent');
        document.getElementById('transactionResult').classList.remove('hidden');
        
        if (data.success) {
            let paymentInstruction = '';
            
            if (data.transaction.channel_type === 'qris' && data.transaction.qr_svg) {
                paymentInstruction = `
                    <div class="mt-4 p-6 bg-white rounded-2xl flex flex-col items-center justify-center w-full overflow-hidden shadow-inner">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" alt="QRIS" class="h-10 sm:h-16 mb-6 object-contain">
                        <div class="w-full max-w-[300px] sm:max-w-[350px] aspect-square flex items-center justify-center [&>svg]:w-full [&>svg]:h-full [&>svg]:max-w-full">
                            ${data.transaction.qr_svg}
                        </div>
                        <p class="mt-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Scan to pay</p>
                        <p class="text-lg font-bold text-gray-900 mt-1">Rp ${parseFloat(data.transaction.unique_amount).toLocaleString('id-ID')}</p>
                    </div>
                `;
            } else if (data.transaction.account_number) {
                paymentInstruction = `
                    <div class="mt-4 p-4 bg-supabase-surface border border-supabase-border rounded-xl">
                        <p class="text-[8px] font-bold text-supabase-muted uppercase mb-1">Transfer Destination</p>
                        <p class="text-sm font-bold text-white">${data.transaction.provider} - ${data.transaction.account_number}</p>
                        <p class="text-xs text-supabase-muted">${data.transaction.account_name}</p>
                        <div class="mt-3 p-2 bg-supabase-dark rounded">
                            <p class="text-[10px] text-supabase-muted uppercase mb-1">Exact Amount to Transfer</p>
                            <p class="text-lg font-bold text-supabase-accent">Rp ${parseFloat(data.transaction.unique_amount).toLocaleString('id-ID')}</p>
                        </div>
                    </div>
                `;
            }

            content.innerHTML = `
                <div class="p-6 bg-supabase-dark border border-supabase-border rounded-2xl space-y-4">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-bold text-supabase-accent uppercase tracking-wider">Generation Successful</p>
                        <p class="text-[10px] font-mono text-supabase-muted">${data.transaction.id}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-supabase-surface border border-supabase-border rounded-xl">
                            <p class="text-[8px] font-bold text-supabase-muted uppercase mb-1">Magnitude</p>
                            <p class="text-xs font-bold text-white">Rp ${parseFloat(data.transaction.amount).toLocaleString('id-ID')}</p>
                        </div>
                        <div class="p-4 bg-supabase-surface border border-supabase-border rounded-xl">
                            <p class="text-[8px] font-bold text-supabase-muted uppercase mb-1">Status</p>
                            <p id="playgroundInvoiceStatus" class="text-xs font-bold text-amber-500 uppercase">${data.transaction.status}</p>
                        </div>
                    </div>
                    <div id="paymentInstructionWrapper">
                        ${paymentInstruction}
                    </div>
                </div>
            `;

            if (data.transaction.status === 'pending') {
                if (window.invoicePollInterval) {
                    clearInterval(window.invoicePollInterval);
                }
                window.invoicePollInterval = setInterval(async () => {
                    try {
                        const checkRes = await fetch(`/tenant/api-playground/invoice-status/${data.transaction.invoice_id}`);
                        const checkData = await checkRes.json();
                        if (checkData.success && checkData.invoice.status !== 'pending') {
                            clearInterval(window.invoicePollInterval);
                            const statusEl = document.getElementById('playgroundInvoiceStatus');
                            if (statusEl) {
                                statusEl.innerText = checkData.invoice.status;
                                if (checkData.invoice.status === 'paid' || checkData.invoice.status === 'success') {
                                    statusEl.className = 'text-xs font-bold text-green-500 uppercase';
                                    const wrapper = document.getElementById('paymentInstructionWrapper');
                                    if (wrapper) {
                                        wrapper.innerHTML = `
                                            <div class="mt-4 p-8 bg-green-500/10 border border-green-500/20 rounded-xl flex flex-col items-center justify-center text-center">
                                                <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mb-4 shadow-[0_0_20px_rgba(34,197,94,0.4)]">
                                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                                <h3 class="text-sm font-bold text-green-500 uppercase tracking-wider mb-1">Pembayaran Diterima</h3>
                                                <p class="text-[10px] text-supabase-muted uppercase font-bold tracking-wider">Dana telah diverifikasi oleh sistem Node.</p>
                                            </div>
                                        `;
                                    }
                                } else {
                                    statusEl.className = 'text-xs font-bold text-red-500 uppercase';
                                }
                            }
                        }
                    } catch (e) {
                        console.error('Polling error', e);
                    }
                }, 3000);
            }
        } else {
            content.innerHTML = `<div class="p-4 bg-red-500/10 border border-red-500/20 text-red-500 text-xs font-bold uppercase tracking-wider rounded-xl">Generation Failed: ${data.message}</div>`;
        }
    } finally {
        btn.textContent = originalText;
        btn.disabled = false;
    }
});

document.getElementById('testWebhookForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = e.target.querySelector('button');
    btn.textContent = 'TRANSMITTING...';
    btn.disabled = true;

    try {
        const res = await fetch('{{ route("tenant.api-playground.test-webhook") }}', {
            method: 'POST',
            body: new FormData(e.target)
        });
        const data = await res.json();
        
        const content = document.getElementById('webhookResultContent');
        document.getElementById('webhookResult').classList.remove('hidden');
        
        content.innerHTML = `
            <div class="space-y-4">
                <div class="p-4 bg-supabase-dark border border-supabase-border rounded-2xl">
                    <p class="text-[8px] font-bold text-supabase-muted uppercase mb-2">Endpoint Response</p>
                    <pre class="text-xs font-mono text-supabase-accent overflow-x-auto">${JSON.stringify(data, null, 2)}</pre>
                </div>
            </div>
        `;
    } finally {
        btn.textContent = 'Transmit Diagnostic Payload';
        btn.disabled = false;
    }
});
</script>
@endpush

@endsection
