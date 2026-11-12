@props(['items'])
@if ($items->total() > 0)
    @php
        $currentPage = $items->currentPage();
        $lastPage = $items->lastPage();
        $firstVisiblePage = max(1, $currentPage - 2);
        $lastVisiblePage = min($lastPage, $currentPage + 2);
        $pageLinks = $items->getUrlRange($firstVisiblePage, $lastVisiblePage);
        $preservedQuery = collect(request()->query())
            ->reject(fn ($value, $key) => $key === 'per_page' || $key === 'page' || str_ends_with($key, '_page'))
            ->all();
    @endphp
    <nav class="pagination-wrap" aria-label="{{ __('admin.pagination.label') }}">
        <div class="pagination-info">
            <span class="pagination-summary">{{ __('admin.pagination.showing', ['from' => $items->firstItem(), 'to' => $items->lastItem(), 'total' => $items->total()]) }}</span>
            <span class="pagination-current">{{ __('admin.pagination.page', ['current' => $currentPage, 'last' => $lastPage]) }}</span>
        </div>
        <div class="pagination-controls">
            @if ($items->onFirstPage())
                <span class="pagination-link pagination-edge disabled" aria-disabled="true" aria-label="{{ __('admin.pagination.first') }}"><x-icon name="chevron" class="icon edge-first"/><x-icon name="chevron" class="icon edge-first"/></span>
                <span class="pagination-link disabled" aria-disabled="true"><x-icon name="chevron" class="icon pagination-prev-icon"/> {{ __('admin.common.previous') }}</span>
            @else
                <a class="pagination-link pagination-edge" href="{{ $items->url(1) }}" aria-label="{{ __('admin.pagination.first') }}"><x-icon name="chevron" class="icon edge-first"/><x-icon name="chevron" class="icon edge-first"/></a>
                <a class="pagination-link" href="{{ $items->previousPageUrl() }}" rel="prev"><x-icon name="chevron" class="icon pagination-prev-icon"/> {{ __('admin.common.previous') }}</a>
            @endif
            @if ($firstVisiblePage > 1)
                <a class="pagination-link page-number" href="{{ $items->url(1) }}">1</a>
                @if ($firstVisiblePage > 2)<span class="pagination-ellipsis" aria-hidden="true">…</span>@endif
            @endif
            @foreach ($pageLinks as $page => $url)
                @if ($page === $currentPage)
                    <span class="pagination-link page-number is-current" aria-current="page">{{ $page }}</span>
                @else
                    <a class="pagination-link page-number" href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach
            @if ($lastVisiblePage < $lastPage)
                @if ($lastVisiblePage < $lastPage - 1)<span class="pagination-ellipsis" aria-hidden="true">…</span>@endif
                <a class="pagination-link page-number" href="{{ $items->url($lastPage) }}">{{ $lastPage }}</a>
            @endif
            @if ($items->hasMorePages())
                <a class="pagination-link" href="{{ $items->nextPageUrl() }}" rel="next">{{ __('admin.common.next') }} <x-icon name="chevron" class="icon pagination-next-icon"/></a>
                <a class="pagination-link pagination-edge" href="{{ $items->url($lastPage) }}" aria-label="{{ __('admin.pagination.last') }}"><x-icon name="chevron" class="icon edge-last"/><x-icon name="chevron" class="icon edge-last"/></a>
            @else
                <span class="pagination-link disabled" aria-disabled="true">{{ __('admin.common.next') }} <x-icon name="chevron" class="icon pagination-next-icon"/></span>
                <span class="pagination-link pagination-edge disabled" aria-disabled="true" aria-label="{{ __('admin.pagination.last') }}"><x-icon name="chevron" class="icon edge-last"/><x-icon name="chevron" class="icon edge-last"/></span>
            @endif
        </div>
        <form class="pagination-size-form" method="GET" action="{{ url()->current() }}">
            @foreach ($preservedQuery as $key => $value)
                @if (is_scalar($value))
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @elseif (is_array($value))
                    @foreach ($value as $nestedKey => $nestedValue)
                        @if (is_scalar($nestedValue))<input type="hidden" name="{{ $key }}[{{ $nestedKey }}]" value="{{ $nestedValue }}">@endif
                    @endforeach
                @endif
            @endforeach
            <label for="per-page-{{ $items->getPageName() }}">{{ __('admin.pagination.rows') }}</label>
            <select id="per-page-{{ $items->getPageName() }}" name="per_page" onchange="this.form.requestSubmit()">
                @foreach ([10, 25, 50, 100] as $size)<option value="{{ $size }}" @selected($items->perPage() === $size)>{{ $size }}</option>@endforeach
            </select>
            <button type="submit" class="button button-secondary button-small">{{ __('admin.pagination.apply') }}</button>
        </form>
    </nav>
@endif
