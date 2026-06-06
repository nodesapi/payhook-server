@extends('layouts.admin')

@section('page-title', 'Delivery Logs')

@section('content')
@php
    $currentSort = $sort ?? request('sort', 'created_at');
    $currentDirection = $direction ?? request('direction', 'desc');
    $sortUrl = function (string $column) use ($currentSort, $currentDirection) {
        return route('admin.webhook-logs.index', array_merge(
            request()->except('page'),
            [
                'sort' => $column,
                'direction' => $currentSort === $column && $currentDirection === 'asc' ? 'desc' : 'asc',
            ]
        ));
    };
    $sortLabel = fn (string $column) => $currentSort === $column ? ($currentDirection === 'asc' ? 'Asc' : 'Desc') : 'Sort';
@endphp

<div class="max-w-full min-w-0 space-y-8 overflow-x-hidden">
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-normal uppercase">Webhook <span class="text-supabase-accent">Logs</span></h1>
            <p class="text-supabase-muted mt-1 text-sm">Real-time payment notification bridge history.</p>
        </div>
        <div class="flex items-center px-4 py-2 bg-supabase-surface border border-supabase-border rounded-xl">
            <span class="text-xs font-bold text-white uppercase tracking-wider">{{ number_format($logs->total()) }} <span class="text-supabase-muted ml-1 font-medium">Total Events</span></span>
        </div>
    </div>

    <div class="bg-supabase-surface border border-supabase-border rounded-lg p-4 shadow-xl">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="hidden" name="sort" value="{{ $currentSort }}">
            <input type="hidden" name="direction" value="{{ $currentDirection }}">

            <div class="flex-1 min-w-[240px]">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search source, package, title..."
                       class="sb-input !rounded-lg !py-2">
            </div>
            <div class="w-full sm:w-44">
                <select name="status" class="sb-input !rounded-lg !py-2 bg-supabase-dark">
                    <option value="">All Statuses</option>
                    <option value="matched" @selected(request('status') === 'matched')>Matched</option>
                    <option value="unmatched" @selected(request('status') === 'unmatched')>Unmatched</option>
                    <option value="failed" @selected(request('status') === 'failed')>Failed</option>
                    <option value="duplicate" @selected(request('status') === 'duplicate')>Duplicate</option>
                </select>
            </div>
            <div class="w-full sm:w-32">
                <select name="per_page" class="sb-input !rounded-lg !py-2 bg-supabase-dark" onchange="this.form.submit()">
                    @foreach([10, 25, 50, 100] as $size)
                        <option value="{{ $size }}" @selected((int) request('per_page', $perPage ?? 25) === $size)>{{ $size }} rows</option>
                    @endforeach
                </select>
            </div>
            <div class="flex h-9 items-center gap-2">
                <button type="submit" class="sb-button-primary !h-8 !w-auto !rounded-md !py-0 px-5 !text-[9px] !shadow-none">Filter</button>
                @if(request()->anyFilled(['search', 'status']) || request()->hasAny(['sort', 'direction', 'per_page']))
                    <a href="{{ route('admin.webhook-logs.index') }}" class="sb-button-secondary !h-9 !w-auto !rounded-md !py-0 px-4">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <div class="max-w-full min-w-0 bg-supabase-surface border border-supabase-border rounded-2xl overflow-hidden shadow-2xl">
        <div class="w-full max-w-full overflow-x-auto">
            <table class="min-w-[840px] w-full table-fixed text-sm">
                <colgroup>
                    <col class="w-[128px]">
                    <col class="w-[160px]">
                    <col class="w-[280px]">
                    <col class="w-[128px]">
                    <col class="w-[128px]">
                    <col class="w-[148px]">
                    <col class="w-[64px]">
                </colgroup>
                <thead class="bg-supabase-dark/50 text-[10px] uppercase tracking-wider font-bold text-supabase-muted border-b border-supabase-border">
                    <tr>
                        @foreach([
                            'created_at' => ['label' => 'Timestamp', 'align' => 'text-left'],
                            'source' => ['label' => 'Origin', 'align' => 'text-left'],
                            'notification_title' => ['label' => 'Notification Payload', 'align' => 'text-left'],
                            'amount' => ['label' => 'Amount', 'align' => 'text-right'],
                            'tenant' => ['label' => 'Tenant', 'align' => 'text-left'],
                            'status' => ['label' => 'Logic State', 'align' => 'text-left'],
                        ] as $column => $heading)
                            <th class="{{ $heading['align'] }} px-4 py-3">
                                <a href="{{ $sortUrl($column) }}" class="inline-flex items-center gap-1.5 text-supabase-muted transition-colors hover:text-white" title="{{ $sortLabel($column) }}">
                                    <span>{{ $heading['label'] }}</span>
                                    <svg class="h-3 w-3 {{ $currentSort === $column ? 'text-supabase-accent' : 'text-supabase-muted/50' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        @if($currentSort === $column && $currentDirection === 'asc')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        @endif
                                    </svg>
                                </a>
                            </th>
                        @endforeach
                        <th class="px-4 py-3">
                            <span class="sr-only">Action</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-supabase-border">
                @forelse($logs as $log)
                    @php
                        $statusMap = [
                            'matched' => ['bg-green-500/10 text-green-500', 'Matched'],
                            'unmatched' => ['bg-amber-500/10 text-amber-500', 'Unmatched'],
                            'failed' => ['bg-red-500/10 text-red-500', 'Failed'],
                            'duplicate' => ['bg-supabase-muted/10 text-supabase-muted', 'Duplicate'],
                        ];
                        [$badgeCls, $badgeLbl] = $statusMap[$log->status] ?? ['bg-supabase-muted/10 text-supabase-muted', ucfirst($log->status ?? '-')];
                    @endphp
                    <tr class="group hover:bg-white/[0.02] transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-white">{{ $log->created_at->format('d M Y') }}</span>
                                <span class="text-[10px] text-supabase-muted uppercase tracking-normal">{{ $log->created_at->format('H:i:s') }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span class="block truncate text-xs font-bold text-white group-hover:text-supabase-accent transition-colors">{{ strtoupper($log->source ?? 'Unknown') }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-bold text-white text-xs truncate">{{ $log->notification_title ?: '-' }}</div>
                            <div class="text-supabase-muted text-[10px] truncate">{{ $log->notification_text ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <span class="font-mono font-bold text-white text-xs">
                                {{ $log->amount ? 'Rp ' . number_format($log->amount, 0, ',', '.') : '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="block truncate text-[10px] font-bold text-supabase-muted uppercase tracking-wider">{{ $log->tenant?->name ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex px-2 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider {{ $badgeCls }}">
                                {{ $badgeLbl }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <a href="{{ route('admin.webhook-logs.show', $log->id) }}"
                               class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-supabase-border bg-supabase-dark text-supabase-accent transition-colors hover:border-supabase-accent/60 hover:bg-supabase-accent hover:text-supabase-dark"
                               aria-label="View log detail"
                               title="View detail">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-14 h-14 bg-supabase-input border border-supabase-border rounded-2xl flex items-center justify-center text-supabase-muted mb-5">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <h3 class="text-base font-bold text-white uppercase tracking-normal">No Events Logged</h3>
                                <p class="text-supabase-muted text-sm mt-2 italic">Connect an Android device to start receiving payment hooks.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 flex flex-col items-center justify-between gap-4 text-xs text-supabase-muted sm:flex-row">
        <p>
            Showing <span class="font-bold text-white">{{ number_format($logs->firstItem() ?? 0) }}</span>
            to <span class="font-bold text-white">{{ number_format($logs->lastItem() ?? 0) }}</span>
            of <span class="font-bold text-white">{{ number_format($logs->total()) }}</span> logs
        </p>
        @if($logs->hasPages())
            {{ $logs->links('vendor.pagination.supabase') }}
        @endif
    </div>
</div>
@endsection
