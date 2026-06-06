@extends('layouts.admin')

@section('page-title', 'System Config')

@section('content')

<!-- Page Header -->
<div class="mb-12">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-4xl font-black text-white tracking-tight uppercase">System <span class="text-supabase-accent">Config</span></h1>
            <p class="text-supabase-muted mt-2">Manage SMTP email and platform master billing channels.</p>
        </div>
    </div>
</div>

<div class="flex flex-col lg:flex-row gap-8">
    <!-- TABS NAV -->
    <div class="w-full lg:w-64 flex-shrink-0">
        <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-4 shadow-2xl sticky top-24">
            <ul class="space-y-2">
                <li>
                    <button onclick="switchTab('billing')" id="tab-btn-billing" class="w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all bg-supabase-accent text-supabase-dark shadow-lg shadow-supabase-accent/20">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Master Billing
                        </div>
                    </button>
                </li>
                <li>
                    <button onclick="switchTab('email')" id="tab-btn-email" class="w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all text-supabase-muted hover:bg-supabase-dark hover:text-white">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            SMTP Email
                        </div>
                    </button>
                </li>
            </ul>
        </div>
    </div>

    <!-- TABS CONTENT -->
    <div class="flex-1 min-w-0">
        
        <!-- TAB 1: MASTER BILLING -->
        <div id="tab-content-billing" class="block space-y-8">
            @if(!$masterTenant)
            <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 shadow-2xl relative overflow-hidden">
                <div class="absolute inset-0 opacity-5 bg-[radial-gradient(#fbbf24_1px,transparent_1px)] [background-size:24px_24px]"></div>
                <div class="relative z-10">
                    <h3 class="text-xs font-black text-white uppercase tracking-[0.2em] mb-2 flex items-center">
                        <span class="w-2 h-2 bg-supabase-accent rounded-full mr-3 animate-pulse"></span>
                        Initialize Master Billing
                    </h3>
                    <p class="text-xs font-bold text-supabase-muted uppercase tracking-widest mb-8">You don't have a Master Tenant. Provide a password to auto-generate the finance@cekbayar.com account.</p>

                    <form action="{{ route('admin.system-config.initialize') }}" method="POST" class="space-y-6 max-w-md">
                        @csrf
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Master Email</label>
                            <input type="text" value="finance@cekbayar.com" class="sb-input bg-supabase-dark/50 cursor-not-allowed text-supabase-muted" readonly>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Mobile App Password</label>
                            <input type="password" name="password" class="sb-input" placeholder="Min 8 characters" required>
                            <p class="text-[8px] text-supabase-muted uppercase font-bold tracking-tighter">You will use this to login to the Android Relay App.</p>
                        </div>
                        <button type="submit" class="sb-button-primary !w-full">Initialize Account</button>
                    </form>
                </div>
            </div>
            @else
            
            <!-- QRIS SECTION -->
            <div class="bg-supabase-surface border border-supabase-border rounded-3xl overflow-hidden shadow-2xl">
                <div class="p-6 border-b border-supabase-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-xs font-black text-white uppercase tracking-[0.2em] flex items-center">
                            <span class="w-1.5 h-1.5 bg-supabase-accent rounded-full mr-3 shadow-[0_0_10px_rgba(251,191,36,0.5)]"></span>
                            Platform QRIS
                        </h3>
                        <p class="text-[10px] text-supabase-muted uppercase font-bold tracking-tighter mt-1">QRIS used for subscription automated billing.</p>
                    </div>
                    <button onclick="toggleModal('modalAddQris')" class="sb-button-primary !w-auto !py-3 !px-6 !text-[10px]">
                        + Add QRIS
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-supabase-dark border-b border-supabase-border">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black text-supabase-muted uppercase tracking-widest">QRIS Name</th>
                                <th class="px-6 py-4 text-[10px] font-black text-supabase-muted uppercase tracking-widest">Image</th>
                                <th class="px-6 py-4 text-[10px] font-black text-supabase-muted uppercase tracking-widest text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-supabase-border/50">
                            @forelse($masterTenant->qrisTemplates as $qris)
                            <tr class="hover:bg-supabase-dark/50 transition-colors">
                                <td class="px-6 py-4"><span class="text-sm font-black text-white uppercase tracking-tight">{{ $qris->name }}</span></td>
                                <td class="px-6 py-4">
                                    <div class="w-16 h-16 bg-white p-1 rounded-xl">
                                        <img src="{{ Storage::url($qris->image_path) }}" class="w-full h-full object-contain rounded-lg">
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('admin.system-config.qris.destroy', $qris->id) }}" method="POST" onsubmit="return confirm('Delete this QRIS?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-[10px] font-black text-red-500 hover:text-white uppercase tracking-widest transition-colors">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <p class="text-[10px] font-black text-supabase-muted uppercase tracking-widest">No QRIS templates found.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- BANK SECTION -->
            <div class="bg-supabase-surface border border-supabase-border rounded-3xl overflow-hidden shadow-2xl">
                <div class="p-6 border-b border-supabase-border flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h3 class="text-xs font-black text-white uppercase tracking-[0.2em] flex items-center">
                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-3 shadow-[0_0_10px_rgba(59,130,246,0.5)]"></span>
                            Platform Bank Accounts
                        </h3>
                        <p class="text-[10px] text-supabase-muted uppercase font-bold tracking-tighter mt-1">Manual bank transfer options.</p>
                    </div>
                    <button onclick="toggleModal('modalAddBank')" class="sb-button-primary !w-auto !py-3 !px-6 !text-[10px] !bg-blue-500 !text-white !shadow-blue-500/20">
                        + Add Bank
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-supabase-dark border-b border-supabase-border">
                            <tr>
                                <th class="px-6 py-4 text-[10px] font-black text-supabase-muted uppercase tracking-widest">Bank Name</th>
                                <th class="px-6 py-4 text-[10px] font-black text-supabase-muted uppercase tracking-widest">Account Number</th>
                                <th class="px-6 py-4 text-[10px] font-black text-supabase-muted uppercase tracking-widest">Account Name</th>
                                <th class="px-6 py-4 text-[10px] font-black text-supabase-muted uppercase tracking-widest text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-supabase-border/50">
                            @php $bankAccounts = \App\Models\BankAccount::where('tenant_id', $masterTenant->id)->get(); @endphp
                            @forelse($bankAccounts as $bank)
                            <tr class="hover:bg-supabase-dark/50 transition-colors">
                                <td class="px-6 py-4"><span class="text-sm font-black text-white uppercase tracking-tight">{{ $bank->bank_name }}</span></td>
                                <td class="px-6 py-4"><span class="text-xs font-bold text-supabase-muted">{{ $bank->account_number }}</span></td>
                                <td class="px-6 py-4"><span class="text-xs font-bold text-supabase-muted">{{ $bank->account_name }}</span></td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('admin.system-config.bank.destroy', $bank->id) }}" method="POST" onsubmit="return confirm('Delete this Bank Account?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-[10px] font-black text-red-500 hover:text-white uppercase tracking-widest transition-colors">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <p class="text-[10px] font-black text-supabase-muted uppercase tracking-widest">No Bank accounts found.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @endif
        </div>

        <!-- TAB 2: EMAIL SMTP -->
        <div id="tab-content-email" class="hidden">
            <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 shadow-2xl">
                <h3 class="text-xs font-black text-white uppercase tracking-[0.2em] mb-2 flex items-center">
                    <span class="w-1.5 h-1.5 bg-supabase-accent rounded-full mr-3 shadow-[0_0_10px_rgba(251,191,36,0.5)]"></span>
                    SMTP Configuration
                </h3>
                <p class="text-[10px] text-supabase-muted uppercase font-bold tracking-widest mb-8">This writes directly to the .env file.</p>

                <form action="{{ route('admin.system-config.smtp') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">MAIL_HOST</label>
                            <input type="text" name="MAIL_HOST" class="sb-input" value="{{ $mailConfig['MAIL_HOST'] }}" required>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">MAIL_PORT</label>
                            <input type="text" name="MAIL_PORT" class="sb-input" value="{{ $mailConfig['MAIL_PORT'] }}" required>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">MAIL_USERNAME</label>
                        <input type="text" name="MAIL_USERNAME" class="sb-input" value="{{ $mailConfig['MAIL_USERNAME'] }}" required>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">MAIL_PASSWORD</label>
                        <input type="password" name="MAIL_PASSWORD" class="sb-input" placeholder="Leave blank to keep unchanged">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">MAIL_ENCRYPTION</label>
                            <input type="text" name="MAIL_ENCRYPTION" class="sb-input" value="{{ $mailConfig['MAIL_ENCRYPTION'] }}" placeholder="ssl / tls">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">MAIL_FROM_ADDRESS</label>
                            <input type="email" name="MAIL_FROM_ADDRESS" class="sb-input" value="{{ $mailConfig['MAIL_FROM_ADDRESS'] }}" required>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-supabase-border flex justify-end">
                        <button type="submit" class="sb-button-primary !w-auto !py-4 !px-12">Save SMTP Config</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<!-- Modal Add QRIS -->
<div id="modalAddQris" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300 pointer-events-none">
    <div class="pointer-events-auto bg-supabase-surface border border-supabase-border rounded-3xl w-full max-w-2xl overflow-hidden shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)] transform scale-95 transition-transform duration-300">
        <form action="{{ route('admin.system-config.qris') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 border-b border-supabase-border flex justify-between items-center bg-supabase-dark/80 backdrop-blur-md">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-supabase-accent/10 flex items-center justify-center border border-supabase-accent/20">
                        <svg class="w-4 h-4 text-supabase-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <h3 class="text-sm font-black text-white uppercase tracking-[0.2em]">Add Platform QRIS</h3>
                </div>
                <button type="button" onclick="toggleModal('modalAddQris')" class="p-2 rounded-xl bg-supabase-dark border border-supabase-border text-supabase-muted hover:text-white hover:border-white/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-8 space-y-8 bg-gradient-to-b from-supabase-surface to-supabase-dark/30">
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-supabase-accent uppercase tracking-[0.2em]">1. Select QRIS Provider</label>
                    @if($qrisChannels->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($qrisChannels as $channel)
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="name" value="{{ $channel->name }}" class="peer sr-only" required>
                                    <div class="p-4 border border-supabase-border rounded-2xl bg-supabase-dark peer-checked:border-supabase-accent peer-checked:bg-supabase-accent/5 group-hover:border-supabase-border/80 transition-all flex flex-col items-center justify-center h-28 text-center gap-3 relative overflow-hidden">
                                        <div class="absolute inset-0 bg-gradient-to-b from-white/5 to-transparent opacity-0 peer-checked:opacity-100"></div>
                                        @if($channel->logo_url)
                                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center p-1 shadow-inner relative z-10">
                                                <img src="{{ $channel->logo_url }}" class="max-h-full max-w-full object-contain" alt="{{ $channel->name }}">
                                            </div>
                                        @else
                                            <div class="w-12 h-12 bg-supabase-surface rounded-xl flex items-center justify-center border border-supabase-border relative z-10">
                                                <span class="text-xs font-black text-white">{{ substr($channel->name, 0, 2) }}</span>
                                            </div>
                                        @endif
                                        <span class="text-[9px] font-black text-supabase-muted uppercase tracking-widest relative z-10 group-hover:text-white transition-colors peer-checked:text-white">{{ $channel->name }}</span>
                                    </div>
                                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-supabase-accent rounded-full text-supabase-dark flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-all shadow-[0_0_15px_rgba(251,191,36,0.4)] scale-50 peer-checked:scale-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 bg-red-500/10 border border-red-500/20 rounded-2xl flex flex-col items-center justify-center text-center">
                            <div class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <p class="text-xs font-black text-white uppercase tracking-widest">No QRIS Master Channels</p>
                            <p class="text-[10px] text-supabase-muted mt-2 font-bold uppercase tracking-widest">Please add them in Master Channels first.</p>
                        </div>
                    @endif
                </div>
                <div class="space-y-4 pt-6 border-t border-supabase-border/50">
                    <label class="block text-[10px] font-black text-supabase-accent uppercase tracking-[0.2em]">2. Upload QRIS Image</label>
                    <div class="relative border-2 border-dashed border-supabase-border hover:border-supabase-accent/50 rounded-2xl bg-supabase-dark/50 p-8 text-center transition-colors group cursor-pointer overflow-hidden">
                        <input type="file" name="qris_image" id="qrisFileInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*" required>
                        <div id="qrisUploadState" class="flex flex-col items-center justify-center space-y-3 relative z-0">
                            <div class="w-12 h-12 bg-supabase-surface rounded-full flex items-center justify-center border border-supabase-border group-hover:bg-supabase-accent/10 group-hover:border-supabase-accent/30 transition-all">
                                <svg class="w-6 h-6 text-supabase-muted group-hover:text-supabase-accent transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs font-black text-white uppercase tracking-widest">Click to browse or drag image here</p>
                                <p class="text-[10px] text-supabase-muted mt-1 font-bold uppercase tracking-widest">PNG, JPG up to 2MB</p>
                            </div>
                        </div>
                        <img id="qrisImagePreview" src="" class="hidden absolute inset-0 w-full h-full object-contain p-2 bg-supabase-dark z-20 pointer-events-none">
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-supabase-border bg-supabase-dark flex justify-end items-center space-x-6">
                <button type="button" onclick="toggleModal('modalAddQris')" class="text-[10px] font-black text-supabase-muted uppercase tracking-[0.2em] hover:text-white transition-colors">Cancel</button>
                <button type="submit" class="sb-button-primary !w-auto !py-3 !px-10 !text-[10px]">
                    Save QRIS Template
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Add Bank -->
<div id="modalAddBank" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 opacity-0 transition-opacity duration-300 pointer-events-none">
    <div class="pointer-events-auto bg-supabase-surface border border-supabase-border rounded-3xl w-full max-w-2xl overflow-hidden shadow-[0_25px_50px_-12px_rgba(0,0,0,0.5)] transform scale-95 transition-transform duration-300">
        <form action="{{ route('admin.system-config.bank') }}" method="POST">
            @csrf
            <div class="p-6 border-b border-supabase-border flex justify-between items-center bg-supabase-dark/80 backdrop-blur-md">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-blue-500/10 flex items-center justify-center border border-blue-500/20">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </div>
                    <h3 class="text-sm font-black text-white uppercase tracking-[0.2em]">Add Platform Bank</h3>
                </div>
                <button type="button" onclick="toggleModal('modalAddBank')" class="p-2 rounded-xl bg-supabase-dark border border-supabase-border text-supabase-muted hover:text-white hover:border-white/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-8 space-y-8 bg-gradient-to-b from-supabase-surface to-supabase-dark/30">
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-blue-500 uppercase tracking-[0.2em]">1. Select Bank Provider</label>
                    @if($bankChannels->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($bankChannels as $channel)
                                <label class="relative cursor-pointer group">
                                    <input type="radio" name="bank_name" value="{{ $channel->name }}" class="peer sr-only" required>
                                    <div class="p-4 border border-supabase-border rounded-2xl bg-supabase-dark peer-checked:border-blue-500 peer-checked:bg-blue-500/5 group-hover:border-supabase-border/80 transition-all flex flex-col items-center justify-center h-28 text-center gap-3 relative overflow-hidden">
                                        <div class="absolute inset-0 bg-gradient-to-b from-white/5 to-transparent opacity-0 peer-checked:opacity-100"></div>
                                        @if($channel->logo_url)
                                            <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center p-1 shadow-inner relative z-10">
                                                <img src="{{ $channel->logo_url }}" class="max-h-full max-w-full object-contain" alt="{{ $channel->name }}">
                                            </div>
                                        @else
                                            <div class="w-12 h-12 bg-supabase-surface rounded-xl flex items-center justify-center border border-supabase-border relative z-10">
                                                <span class="text-xs font-black text-white uppercase">{{ substr($channel->name, 0, 3) }}</span>
                                            </div>
                                        @endif
                                        <span class="text-[9px] font-black text-supabase-muted uppercase tracking-widest relative z-10 group-hover:text-white transition-colors peer-checked:text-white">{{ $channel->name }}</span>
                                    </div>
                                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-blue-500 rounded-full text-white flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-all shadow-[0_0_15px_rgba(59,130,246,0.4)] scale-50 peer-checked:scale-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 bg-red-500/10 border border-red-500/20 rounded-2xl flex flex-col items-center justify-center text-center">
                            <div class="w-12 h-12 bg-red-500/20 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <p class="text-xs font-black text-white uppercase tracking-widest">No Bank Master Channels</p>
                            <p class="text-[10px] text-supabase-muted mt-2 font-bold uppercase tracking-widest">Please add them in Master Channels first.</p>
                        </div>
                    @endif
                </div>
                <div class="space-y-4 pt-6 border-t border-supabase-border/50">
                    <label class="block text-[10px] font-black text-blue-500 uppercase tracking-[0.2em]">2. Account Details</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Account Number</label>
                            <input type="text" name="account_number" class="sb-input" placeholder="e.g. 1234567890" required>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black text-supabase-muted uppercase tracking-widest">Account Name</label>
                            <input type="text" name="account_name" class="sb-input" placeholder="e.g. PT Cekbayar Teknologi" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-supabase-border bg-supabase-dark flex justify-end items-center space-x-6">
                <button type="button" onclick="toggleModal('modalAddBank')" class="text-[10px] font-black text-supabase-muted uppercase tracking-[0.2em] hover:text-white transition-colors">Cancel</button>
                <button type="submit" class="sb-button-primary !w-auto !py-3 !px-10 !text-[10px] !bg-blue-500 !text-white !shadow-blue-500/20">
                    Save Bank Account
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function switchTab(tabName) {
        const tabs = ['billing', 'email'];
        
        tabs.forEach(t => {
            const btn = document.getElementById('tab-btn-' + t);
            const content = document.getElementById('tab-content-' + t);
            
            if (t === tabName) {
                btn.className = 'w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all bg-supabase-accent text-supabase-dark shadow-lg shadow-supabase-accent/20';
                content.classList.remove('hidden');
                content.classList.add('block', 'animate-fade-in');
            } else {
                btn.className = 'w-full text-left px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all text-supabase-muted hover:bg-supabase-dark hover:text-white';
                content.classList.add('hidden');
                content.classList.remove('block', 'animate-fade-in');
            }
        });
    }

    function toggleModal(modalID) {
        const modal = document.getElementById(modalID);
        const modalContent = modal.querySelector('div');
        
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            // Small delay for transition
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95', 'translate-y-4');
                modalContent.classList.add('scale-100', 'translate-y-0');
            }, 10);
        } else {
            modal.classList.add('opacity-0');
            modalContent.classList.remove('scale-100', 'translate-y-0');
            modalContent.classList.add('scale-95', 'translate-y-4');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    }

    // QRIS Image Preview Logic
    const qrisInput = document.getElementById('qrisFileInput');
    const qrisPreview = document.getElementById('qrisImagePreview');
    const qrisUploadState = document.getElementById('qrisUploadState');

    if(qrisInput) {
        qrisInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if(file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    qrisPreview.src = e.target.result;
                    qrisPreview.classList.remove('hidden');
                    qrisUploadState.style.opacity = '0';
                }
                reader.readAsDataURL(file);
            }
        });
    }
</script>
<style>
    .animate-fade-in { animation: fadeIn 0.3s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endpush
@endsection
