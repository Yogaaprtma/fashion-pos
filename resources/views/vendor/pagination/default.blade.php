@if ($paginator->hasPages())
<nav class="pagination-nav" role="navigation" aria-label="{{ __('Pagination Navigation') }}">
    <div class="pagination-info">
        {{ __('Menampilkan') }}
        <strong>{{ $paginator->firstItem() }}</strong>
        {{ __('hingga') }}
        <strong>{{ $paginator->lastItem() }}</strong>
        {{ __('dari total') }}
        <strong>{{ $paginator->total() }}</strong>
        {{ __('data') }}
    </div>
    <ul class="pagination-list">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="pagination-item disabled" aria-disabled="true">
                <span class="pagination-link pagination-prev">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </span>
            </li>
        @else
            <li class="pagination-item">
                <a class="pagination-link pagination-prev" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="pagination-item disabled" aria-disabled="true"><span class="pagination-link pagination-dots">···</span></li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="pagination-item active" aria-current="page"><span class="pagination-link">{{ $page }}</span></li>
                    @else
                        <li class="pagination-item"><a class="pagination-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="pagination-item">
                <a class="pagination-link pagination-next" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </li>
        @else
            <li class="pagination-item disabled" aria-disabled="true">
                <span class="pagination-link pagination-next">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </span>
            </li>
        @endif
    </ul>
</nav>
@endif
