@extends('layouts.admin')

@section('page-title', __('admin.products.title'))

@section('content')
    <section class="page-intro page-intro-actions">
        <div><span class="eyebrow">{{ __('admin.nav.workspace') }}</span><h2>{{ __('admin.products.title') }}</h2><p>{{ __('admin.products.subtitle') }}</p></div>
        <div class="intro-buttons">
            @can('products.create')<a class="button button-primary" href="{{ route('products.create') }}"><x-icon name="plus" class="icon"/>{{ __('admin.products.new') }}</a>@endcan
        </div>
    </section>
    <section class="panel table-panel">
        <form class="filter-bar" action="{{ route('products.index') }}" method="GET">
            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
            @if (request()->filled('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
            @if (request()->filled('direction'))<input type="hidden" name="direction" value="{{ request('direction') }}">@endif
            <label class="search-control"><x-icon name="search" class="icon"/><input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('admin.products.search_placeholder') }}" aria-label="{{ __('admin.common.search') }}"></label>
            <label class="sr-only" for="filter-category">{{ __('admin.products.category') }}</label>
            <select class="filter-select" id="filter-category" name="category_id"><option value="">{{ __('admin.products.filter_category') }}</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>@endforeach</select>
            <label class="sr-only" for="filter-status">{{ __('admin.common.status') }}</label>
            <select class="filter-select" id="filter-status" name="status"><option value="">{{ __('admin.products.filter_status') }}</option><option value="active" @selected(request('status') === 'active')>{{ __('admin.products.active') }}</option><option value="inactive" @selected(request('status') === 'inactive')>{{ __('admin.products.inactive') }}</option><option value="draft" @selected(request('status') === 'draft')>{{ __('admin.products.draft') }}</option></select>
            <button type="submit" class="button button-secondary">{{ __('admin.common.search') }}</button>
            @if (request()->hasAny(['q', 'category_id', 'status']))<a class="button button-ghost" href="{{ route('products.index') }}">{{ __('admin.common.clear') }}</a>@endif
        </form>
        @if ($products->isEmpty())
            <x-empty-state :title="request()->hasAny(['q', 'category_id', 'status']) ? __('admin.products.no_results') : __('admin.products.empty_title')" :hint="request()->hasAny(['q', 'category_id', 'status']) ? __('admin.empty.hint') : __('admin.products.empty_hint')" icon="box"/>
            @can('products.create')
                <div class="empty-state-action"><a class="button button-primary button-small" href="{{ route('products.create') }}"><x-icon name="plus" class="icon"/>{{ __('admin.products.new') }}</a></div>
            @endcan
        @else
            <div class="table-scroll">
                <table class="data-table">
                    <thead><tr>
                        <th><a class="sort-link" href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => $sort === 'name' && $direction === 'asc' ? 'desc' : 'asc'])) }}">{{ __('admin.products.product') }}</a></th>
                        <th><a class="sort-link" href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'sku', 'direction' => $sort === 'sku' && $direction === 'asc' ? 'desc' : 'asc'])) }}">{{ __('admin.products.sku') }}</a></th>
                        <th>{{ __('admin.products.category') }}</th>
                        <th><a class="sort-link" href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'price', 'direction' => $sort === 'price' && $direction === 'asc' ? 'desc' : 'asc'])) }}">{{ __('admin.common.price') }}</a></th>
                        <th><a class="sort-link" href="{{ route('products.index', array_merge(request()->query(), ['sort' => 'stock_quantity', 'direction' => $sort === 'stock_quantity' && $direction === 'asc' ? 'desc' : 'asc'])) }}">{{ __('admin.products.stock') }}</a></th>
                        <th>{{ __('admin.common.status') }}</th><th>{{ __('admin.common.actions') }}</th></tr></thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td><a class="table-product" href="{{ route('products.show', $product) }}"><span class="product-thumb">@if ($product->image_path)<span class="skeleton skeleton-image"></span><img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->image_path) }}" alt="" data-skeleton-image>@else<x-icon name="box" class="icon"/>@endif</span><span><strong>{{ $product->name }}</strong><small>{{ $product->created_at->translatedFormat('M j, Y') }}</small></span></a></td>
                                <td><span class="code-chip">{{ $product->sku }}</span></td>
                                <td>{{ $product->category->name }}</td>
                                <td class="numeric-cell">@if ($product->sale_price !== null)<strong>{{ number_format((float) $product->sale_price, 2) }}</strong> <small>{{ __('admin.common.currency') }}</small><br><del class="muted-cell">{{ number_format((float) $product->price, 2) }}</del>@else{{ number_format((float) $product->price, 2) }} <small>{{ __('admin.common.currency') }}</small>@endif</td>
                                <td><span class="{{ $product->stock_quantity <= 5 ? 'stock-low' : 'stock-normal' }}">{{ number_format($product->stock_quantity) }}</span></td>
                                <td><x-status-badge :status="$product->status">{{ __('admin.products.'.$product->status) }}</x-status-badge></td>
                                <td><div class="row-actions">
                                    <a class="icon-button table-action" href="{{ route('products.show', $product) }}" aria-label="{{ __('admin.common.view') }}" title="{{ __('admin.common.view') }}"><x-icon name="eye" class="icon"/></a>
                                    @can('products.edit')<a class="icon-button table-action" href="{{ route('products.edit', $product) }}" aria-label="{{ __('admin.common.edit') }}" title="{{ __('admin.common.edit') }}"><x-icon name="edit" class="icon"/></a>@endcan
                                    @can('products.delete')@if ($product->sales_count === 0)<form action="{{ route('products.destroy', $product) }}" method="POST" data-confirm="{{ __('admin.confirm.delete_product') }}">@csrf @method('DELETE')<button class="icon-button table-action danger-action" type="submit" aria-label="{{ __('admin.common.delete') }}" title="{{ __('admin.common.delete') }}"><x-icon name="trash" class="icon"/></button></form>@endif @endcan
                                </div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="table-footer"><x-pagination :items="$products"/></div>
        @endif
    </section>
@endsection
