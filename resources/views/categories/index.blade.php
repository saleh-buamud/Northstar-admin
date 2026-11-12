@extends('layouts.admin')

@section('page-title', __('admin.categories.title'))

@section('content')
    <section class="page-intro page-intro-actions">
        <div><span class="eyebrow">{{ __('admin.nav.workspace') }}</span><h2>{{ __('admin.categories.title') }}</h2><p>{{ __('admin.categories.subtitle') }}</p></div>
        @can('products.create')<a class="button button-primary" href="{{ route('categories.create') }}"><x-icon name="plus" class="icon"/>{{ __('admin.categories.new') }}</a>@endcan
    </section>
    <section class="panel table-panel">
        <form class="filter-bar" action="{{ route('categories.index') }}" method="GET">
            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
            <label class="search-control"><x-icon name="search" class="icon"/><input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('admin.categories.search_placeholder') }}" aria-label="{{ __('admin.common.search') }}"></label>
            <label class="sr-only" for="category-status">{{ __('admin.common.status') }}</label>
            <select class="filter-select" id="category-status" name="status"><option value="">{{ __('admin.categories.filter_status') }}</option><option value="active" @selected(request('status') === 'active')>{{ __('admin.products.active') }}</option><option value="inactive" @selected(request('status') === 'inactive')>{{ __('admin.products.inactive') }}</option></select>
            <button type="submit" class="button button-secondary">{{ __('admin.common.search') }}</button>
            @if (request()->hasAny(['q', 'status']))<a class="button button-ghost" href="{{ route('categories.index') }}">{{ __('admin.common.clear') }}</a>@endif
        </form>
        @if ($categories->isEmpty())
            <x-empty-state :title="request()->hasAny(['q', 'status']) ? __('admin.categories.no_results') : __('admin.categories.empty_title')" :hint="request()->hasAny(['q', 'status']) ? __('admin.empty.hint') : __('admin.categories.empty_hint')" icon="layers"/>
            @can('products.create')
                <div class="empty-state-action"><a class="button button-primary button-small" href="{{ route('categories.create') }}"><x-icon name="plus" class="icon"/>{{ __('admin.categories.new') }}</a></div>
            @endcan
        @else
            <div class="table-scroll"><table class="data-table">
                <thead><tr><th>{{ __('admin.categories.category') }}</th><th>{{ __('admin.common.description') }}</th><th>{{ __('admin.categories.products') }}</th><th>{{ __('admin.common.status') }}</th><th>{{ __('admin.common.created') }}</th><th>{{ __('admin.common.actions') }}</th></tr></thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td><span class="table-name-with-icon"><span class="table-icon"><x-icon name="layers" class="icon"/></span><strong>{{ $category->name }}</strong></span></td>
                            <td class="muted-cell">{{ $category->description ?: __('admin.common.no_description') }}</td>
                            <td>{{ number_format($category->products_count) }}</td>
                            <td><x-status-badge :status="$category->status">{{ __('admin.products.'.$category->status) }}</x-status-badge></td>
                            <td>{{ $category->created_at->translatedFormat('M j, Y') }}</td>
                            <td><div class="row-actions">
                                @can('products.edit')<a class="icon-button table-action" href="{{ route('categories.edit', $category) }}" aria-label="{{ __('admin.common.edit') }}" title="{{ __('admin.common.edit') }}"><x-icon name="edit" class="icon"/></a>@endcan
                                @can('products.delete')@if ($category->products_count === 0)<form action="{{ route('categories.destroy', $category) }}" method="POST" data-confirm="{{ __('admin.confirm.delete_category') }}">@csrf @method('DELETE')<button class="icon-button table-action danger-action" type="submit" aria-label="{{ __('admin.common.delete') }}" title="{{ __('admin.common.delete') }}"><x-icon name="trash" class="icon"/></button></form>@endif @endcan
                            </div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table></div>
            <div class="table-footer"><x-pagination :items="$categories"/></div>
        @endif
    </section>
@endsection
