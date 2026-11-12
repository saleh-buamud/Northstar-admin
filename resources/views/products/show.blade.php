@extends('layouts.admin')

@section('page-title', $product->name)

@section('content')
    <section class="page-intro page-intro-actions">
        <div><a class="back-link" href="{{ route('products.index') }}"><x-icon name="chevron" class="icon flip-rtl"/>{{ __('admin.common.back') }}</a><h2>{{ __('admin.products.details_title') }}</h2><p>{{ $product->name }}</p></div>
        <div class="intro-buttons">@can('products.edit')<a class="button button-primary" href="{{ route('products.edit', $product) }}"><x-icon name="edit" class="icon"/>{{ __('admin.common.edit') }}</a>@endcan</div>
    </section>
    <section class="panel product-detail-panel">
        <div class="detail-image">@if ($product->image_path)<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->image_path) }}" alt="{{ $product->name }}">@else<x-icon name="box" class="icon detail-placeholder-icon"/>@endif</div>
        <div class="detail-content">
            <div class="detail-title-row"><div><span class="code-chip">{{ $product->sku }}</span><h3>{{ $product->name }}</h3></div><x-status-badge :status="$product->status">{{ __('admin.products.'.$product->status) }}</x-status-badge></div>
            <p class="detail-description">{{ $product->description ?: __('admin.common.no_description') }}</p>
            <dl class="detail-grid">
                <div><dt>{{ __('admin.products.category') }}</dt><dd>{{ $product->category->name }}</dd></div>
                <div><dt>{{ __('admin.common.price') }}</dt><dd>@if ($product->sale_price !== null){{ number_format((float) $product->sale_price, 2) }} <small>{{ __('admin.common.currency') }}</small> <del class="muted-cell">{{ number_format((float) $product->price, 2) }}</del>@else{{ number_format((float) $product->price, 2) }} {{ __('admin.common.currency') }}@endif</dd></div>
                <div><dt>{{ __('admin.products.stock') }}</dt><dd>{{ number_format($product->stock_quantity) }} {{ __('admin.dashboard.units') }}</dd></div>
                <div><dt>{{ __('admin.common.created') }}</dt><dd>{{ $product->created_at->translatedFormat('F j, Y') }}</dd></div>
            </dl>
        </div>
    </section>
@endsection
