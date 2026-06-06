@extends('layouts.admin')

@section('page-title', 'Tenant Registry')

@section('content')
<div class="space-y-12">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
        <div>
            <h1 class="text-4xl font-bold text-white tracking-normal uppercase">Tenants</h1>
            <p class="text-supabase-muted mt-2">Manage your merchant tenants and their system access.</p>
        </div>
        <a href="{{ route('admin.tenants.create') }}" class="sb-button-primary !w-auto">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add New Tenant
        </a>
    </div>

    <!-- Tenants Table -->
    <div class="bg-supabase-surface border border-supabase-border rounded-2xl overflow-hidden shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-supabase-dark/50 text-[10px] uppercase tracking-wider font-bold text-supabase-muted">
                    <tr>
                        <th class="px-8 py-5 text-left">Tenant Info</th>
                        <th class="px-8 py-5 text-left">System Status</th>
                        <th class="px-8 py-5 text-left">Subscription</th>
                        <th class="px-8 py-5 text-left">Metrics</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-supabase-border">
                    @forelse($tenants as $tenant)
                        <tr class="group hover:bg-white/[0.02] transition-all duration-200">
                            <td class="px-8 py-6">
                                <div class="flex items-center space-x-4">
                                    <div class="w-10 h-10 rounded-xl bg-supabase-input border border-supabase-border flex items-center justify-center text-supabase-accent font-bold group-hover:scale-110 transition-transform">
                                        {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-white leading-tight mb-1">{{ $tenant->name }}</p>
                                        <p class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">{{ $tenant->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex flex-col space-y-2">
                                    @if($tenant->is_active)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-green-500/10 text-green-500 text-[10px] font-bold uppercase tracking-wider w-fit">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-red-500/10 text-red-500 text-[10px] font-bold uppercase tracking-wider w-fit">
                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-2"></span>
                                            Suspended
                                        </span>
                                    @endif
                                    <span class="px-2 py-0.5 bg-supabase-input border border-supabase-border text-supabase-muted text-[8px] font-bold uppercase tracking-wider rounded w-fit">
                                        {{ $tenant->mode }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                @php
                                    $isExpired = $tenant->expired_at && $tenant->expired_at->isPast();
                                @endphp
                                <div class="space-y-1">
                                    <p class="text-[10px] font-bold uppercase tracking-wider {{ $isExpired ? 'text-red-500' : 'text-white' }}">
                                        {{ $tenant->expired_at ? $tenant->expired_at->format('d M Y') : 'Life-time' }}
                                    </p>
                                    @if($isExpired)
                                        <p class="text-[8px] text-red-500/70 font-bold uppercase tracking-normal">Subscription Terminated</p>
                                    @else
                                        <p class="text-[8px] text-supabase-muted font-bold uppercase tracking-normal">Valid Access Node</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-6 text-xs">
                                <div class="space-y-1">
                                    <p class="text-white font-bold leading-none">{{ $tenant->invoices_count }} <span class="text-[10px] text-supabase-muted uppercase font-bold ml-1">Invoices</span></p>
                                    <p class="text-supabase-muted font-bold text-[10px] uppercase tracking-wider">{{ $tenant->payment_channels_count ?? $tenant->qris_templates_count }} <span class="ml-1">Channels</span></p>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center justify-end space-x-3">
                                    <button onclick="showExtendModal({{ $tenant->id }}, '{{ $tenant->name }}')" class="p-2 bg-supabase-accent/10 border border-supabase-accent/20 rounded-lg text-supabase-accent hover:bg-supabase-accent hover:text-supabase-dark transition-all shadow-sm" title="Extend Subscription">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </button>

                                    <a href="{{ route('admin.tenants.edit', $tenant) }}" class="p-2 bg-supabase-input border border-supabase-border rounded-lg text-supabase-muted hover:text-white hover:border-white/30 transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    
                                    @if($tenant->is_active)
                                        <button onclick="showConfirmModal({
                                            type: 'warning',
                                            title: 'Suspend Tenant',
                                            message: 'Are you sure you want to suspend {{ $tenant->name }}?',
                                            confirmText: 'Suspend Access',
                                            onConfirm: () => document.getElementById('suspend-form-{{ $tenant->id }}').submit()
                                        })" class="p-2 bg-amber-500/10 border border-amber-500/20 rounded-lg text-amber-500 hover:bg-amber-500 hover:text-white transition-all shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </button>
                                        <form id="suspend-form-{{ $tenant->id }}" method="POST" action="{{ route('admin.tenants.suspend', $tenant) }}" class="hidden">@csrf</form>
                                    @else
                                        <button onclick="showConfirmModal({
                                            type: 'info',
                                            title: 'Activate Tenant',
                                            message: 'Activate {{ $tenant->name }}?',
                                            confirmText: 'Grant Access',
                                            onConfirm: () => document.getElementById('activate-form-{{ $tenant->id }}').submit()
                                        })" class="p-2 bg-green-500/10 border border-green-500/20 rounded-lg text-green-500 hover:bg-green-500 hover:text-white transition-all shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </button>
                                        <form id="activate-form-{{ $tenant->id }}" method="POST" action="{{ route('admin.tenants.activate', $tenant) }}" class="hidden">@csrf</form>
                                    @endif

                                    <button onclick="showConfirmModal({
                                        type: 'danger',
                                        title: 'Terminate Tenant',
                                        message: 'Critical: Permanently delete {{ $tenant->name }}?',
                                        confirmText: 'Terminate',
                                        onConfirm: () => document.getElementById('delete-form-{{ $tenant->id }}').submit()
                                    })" class="p-2 bg-red-500/10 border border-red-500/20 rounded-lg text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                    <form id="delete-form-{{ $tenant->id }}" method="POST" action="{{ route('admin.tenants.destroy', $tenant) }}" class="hidden">@csrf @method('DELETE')</form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-24 text-center">
                                <h3 class="text-xl font-bold text-white uppercase tracking-normal mb-2">No Tenants Registered</h3>
                                <a href="{{ route('admin.tenants.create') }}" class="sb-button-primary !w-auto">Onboard Merchant</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($tenants->hasPages())
        <div class="mt-8">
            {{ $tenants->links() }}
        </div>
    @endif
</div>

<!-- Extend Modal -->
<div id="extend-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-6">
    <div class="bg-supabase-surface border border-supabase-border rounded-3xl p-8 max-w-md w-full shadow-2xl">
        <h3 class="text-xl font-bold text-white uppercase tracking-normal mb-2">Extend <span class="text-supabase-accent">Subscription</span></h3>
        <p class="text-supabase-muted text-[10px] font-bold uppercase mb-6" id="extend-tenant-name"></p>
        
        <form id="extend-form" method="POST" action="" class="space-y-6">
            @csrf
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Extension Period</label>
                <select name="months" class="sb-input bg-supabase-dark" required>
                    <option value="1">1 Month</option>
                    <option value="3">3 Months</option>
                    <option value="6">6 Months</option>
                    <option value="12">12 Months (Pro)</option>
                </select>
            </div>
            <div class="flex items-center justify-end space-x-4 pt-4">
                <button type="button" onclick="hideExtendModal()" class="text-[10px] font-bold text-supabase-muted uppercase tracking-wider">Abort</button>
                <button type="submit" class="sb-button-primary !w-auto !py-2.5 !px-8">Update Registry</button>
            </div>
        </form>
    </div>
</div>

<x-confirm-modal />

@push('scripts')
<script>
function showExtendModal(id, name) {
    const modal = document.getElementById('extend-modal');
    const form = document.getElementById('extend-form');
    const nameEl = document.getElementById('extend-tenant-name');
    
    nameEl.textContent = 'Modifying Node: ' + name;
    form.action = '/admin/tenants/' + id + '/extend';
    modal.classList.remove('hidden');
}
function hideExtendModal() {
    document.getElementById('extend-modal').classList.add('hidden');
}
</script>
@endpush

@endsection
