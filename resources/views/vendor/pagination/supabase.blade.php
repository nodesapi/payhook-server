@if ($paginator->hasPages())
    @php
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $pages = collect(range(1, $lastPage))
            ->filter(fn ($page) => $page === 1 || $page === $lastPage || abs($page - $currentPage) <= 1)
            ->values();
        $previousPage = null;
    @endphp

    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col items-center gap-3 sm:flex-row sm:justify-end">
        <div class="flex items-center gap-2">
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-supabase-border bg-supabase-dark/60 text-supabase-muted/50">
                    <span class="sr-only">{{ __('Previous') }}</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-supabase-border bg-supabase-dark text-slate-300 transition-colors hover:border-supabase-accent/60 hover:bg-supabase-accent hover:text-supabase-dark">
                    <span class="sr-only">{{ __('Previous') }}</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
            @endif

            <div class="hidden items-center gap-1 sm:flex">
                @foreach ($pages as $page)
                    @if (! is_null($previousPage) && $page - $previousPage > 1)
                        <span class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-supabase-border bg-supabase-dark/60 px-3 text-xs font-bold text-supabase-muted">...</span>
                    @endif

                    @if ($page === $currentPage)
                        <span aria-current="page" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-supabase-accent bg-supabase-accent px-3 text-xs font-black text-supabase-dark shadow-lg shadow-supabase-accent/10">{{ $page }}</span>
                    @else
                        <a href="{{ $paginator->url($page) }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-supabase-border bg-supabase-dark px-3 text-xs font-bold text-slate-300 transition-colors hover:border-supabase-accent/60 hover:text-supabase-accent">{{ $page }}</a>
                    @endif

                    @php $previousPage = $page; @endphp
                @endforeach
            </div>

            <div class="inline-flex items-center rounded-lg border border-supabase-border bg-supabase-dark px-3 py-2 text-xs font-bold text-slate-300 sm:hidden">
                {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </div>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-supabase-border bg-supabase-dark text-slate-300 transition-colors hover:border-supabase-accent/60 hover:bg-supabase-accent hover:text-supabase-dark">
                    <span class="sr-only">{{ __('Next') }}</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            @else
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-supabase-border bg-supabase-dark/60 text-supabase-muted/50">
                    <span class="sr-only">{{ __('Next') }}</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </span>
            @endif
        </div>
    </nav>
@endif
