@extends('layouts.admin')

@section('page-title', __('admin.dashboard.title'))

@section('content')
    <section class="page-intro">
        <div>
            <span class="eyebrow">{{ __('admin.dashboard.eyebrow') }}</span>
            <h2>{{ __('admin.dashboard.welcome', ['name' => auth()->user()->name]) }}</h2>
            <p>{{ __('admin.dashboard.subtitle') }}</p>
        </div>
        <div class="intro-date"><x-icon name="calendar" class="icon"/>{{ now()->translatedFormat('F j, Y') }}</div>
    </section>

    <section class="stats-grid" aria-label="{{ __('admin.dashboard.title') }}">
        @can('products.view')
            <article class="stat-card">
                <div class="stat-top"><span class="stat-icon indigo"><x-icon name="box" class="icon"/></span><span class="stat-label">{{ __('admin.dashboard.products') }}</span></div>
                <strong class="stat-value" data-counter="{{ $stats['products'] }}">{{ number_format($stats['products']) }}</strong>
                <span class="stat-caption">{{ __('admin.dashboard.products') }}</span>
            </article>
        @endcan
        @can('users.view')
            <article class="stat-card">
                <div class="stat-top"><span class="stat-icon blue"><x-icon name="users" class="icon"/></span><span class="stat-label">{{ __('admin.dashboard.users') }}</span></div>
                <strong class="stat-value" data-counter="{{ $stats['users'] }}">{{ number_format($stats['users']) }}</strong>
                <span class="stat-caption">{{ __('admin.dashboard.users') }}</span>
            </article>
        @endcan
        @can('products.view')
            <article class="stat-card">
                <div class="stat-top"><span class="stat-icon violet"><x-icon name="layers" class="icon"/></span><span class="stat-label">{{ __('admin.dashboard.categories') }}</span></div>
                <strong class="stat-value" data-counter="{{ $stats['categories'] }}">{{ number_format($stats['categories']) }}</strong>
                <span class="stat-caption">{{ __('admin.dashboard.categories') }}</span>
            </article>
        @endcan
        @can('products.view')
            <article class="stat-card">
                <div class="stat-top"><span class="stat-icon green"><x-icon name="dollar" class="icon"/></span><span class="stat-label">{{ __('admin.dashboard.revenue') }}</span></div>
                <strong class="stat-value">{{ number_format($stats['revenue'], 2) }} <small>{{ __('admin.common.currency') }}</small></strong>
                <span class="stat-caption">{{ __('admin.dashboard.revenue') }}</span>
            </article>
        @endcan
    </section>

    <div class="dashboard-grid">
        <section class="panel chart-panel">
            <div class="panel-heading">
                <div><h3>{{ __('admin.dashboard.sales_overview') }}</h3><p>{{ __('admin.dashboard.last_six_months') }}</p></div>
                <span class="panel-icon"><x-icon name="chart" class="icon"/></span>
            </div>
            @php
                $chartCoordinates = $months->values()->map(function (array $month, int $index) use ($chartMax): array {
                    $x = 14 + ($index * 94);
                    $y = 120 - (($month['revenue'] / $chartMax) * 96);
                    return ['x' => $x, 'y' => number_format($y, 2, '.', '')];
                });
                $chartPoints = $chartCoordinates->map(fn (array $point): string => $point['x'].','.$point['y'])->implode(' ');
            @endphp
            <div class="chart-wrap" role="img" aria-label="{{ __('admin.dashboard.chart_description') }}">
                <svg class="revenue-chart" viewBox="0 0 500 140" preserveAspectRatio="none" aria-hidden="true">
                    <line x1="0" y1="24" x2="500" y2="24" class="chart-gridline"/>
                    <line x1="0" y1="70" x2="500" y2="70" class="chart-gridline"/>
                    <line x1="0" y1="120" x2="500" y2="120" class="chart-gridline"/>
                    <polyline points="{{ $chartPoints }}" class="chart-line"/>
                    @foreach ($months->values() as $month)
                        <circle cx="{{ $chartCoordinates[$loop->index]['x'] }}" cy="{{ $chartCoordinates[$loop->index]['y'] }}" r="3.5" class="chart-point"><title>{{ $month['label'] }}: {{ number_format($month['revenue'], 2) }} {{ __('admin.common.currency') }}</title></circle>
                    @endforeach
                </svg>
                <div class="chart-labels">@foreach ($months as $month)<span>{{ $month['label'] }}</span>@endforeach</div>
            </div>
            <div class="chart-legend"><span class="legend-dot"></span><span>{{ __('admin.dashboard.monthly_revenue') }}</span><strong>{{ number_format($stats['revenue'], 2) }} {{ __('admin.common.currency') }}</strong></div>
            @if ($months->sum('revenue') == 0)<p class="chart-empty">{{ __('admin.dashboard.chart_empty') }}</p>@endif
        </section>

        <section class="panel quick-panel">
            <div class="panel-heading"><div><h3>{{ __('admin.dashboard.quick_actions') }}</h3><p>{{ __('admin.dashboard.subtitle') }}</p></div></div>
            <div class="quick-actions">
                @can('products.create')
                    <a class="quick-action" href="{{ route('products.create') }}"><span class="quick-icon"><x-icon name="box" class="icon"/></span><span>{{ __('admin.dashboard.add_product') }}</span><x-icon name="chevron" class="icon quick-chevron"/></a>
                    <a class="quick-action" href="{{ route('categories.create') }}"><span class="quick-icon"><x-icon name="layers" class="icon"/></span><span>{{ __('admin.dashboard.add_category') }}</span><x-icon name="chevron" class="icon quick-chevron"/></a>
                @endcan
                @can('users.create')
                    <a class="quick-action" href="{{ route('users.create') }}"><span class="quick-icon"><x-icon name="users" class="icon"/></span><span>{{ __('admin.dashboard.add_user') }}</span><x-icon name="chevron" class="icon quick-chevron"/></a>
                @endcan
                @can('products.edit')
                    <button class="quick-action quick-action-button" type="button" data-dialog-open="record-sale"><span class="quick-icon"><x-icon name="dollar" class="icon"/></span><span>{{ __('admin.dashboard.record_sale') }}</span><x-icon name="chevron" class="icon quick-chevron"/></button>
                @endcan
            </div>
        </section>
    </div>

    <div class="dashboard-grid lower-grid">
        @can('products.view')
            <section class="panel">
                <div class="panel-heading"><div><h3>{{ __('admin.dashboard.recent_products') }}</h3><p>{{ __('admin.products.subtitle') }}</p></div><a class="text-link" href="{{ route('products.index') }}">{{ __('admin.common.view') }} <x-icon name="arrow" class="icon flip-rtl"/></a></div>
                @if ($recentProducts->isEmpty())
                    <x-empty-state :title="__('admin.products.empty_title')" :hint="__('admin.products.empty_hint')" icon="box"/>
                    @can('products.create')
                        <div class="empty-state-action"><a class="button button-secondary button-small" href="{{ route('products.create') }}">{{ __('admin.products.new') }}</a></div>
                    @endcan
                @else
                    <div class="compact-list">
                        @foreach ($recentProducts as $product)
                            <a class="compact-row" href="{{ route('products.show', $product) }}">
                                <span class="product-thumb">@if ($product->image_path)<span class="skeleton skeleton-image"></span><img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->image_path) }}" alt="" data-skeleton-image>@else<x-icon name="box" class="icon"/>@endif</span>
                                <span class="compact-main"><strong>{{ $product->name }}</strong><small>{{ $product->category->name }} · {{ $product->sku }}</small></span>
                                <span class="compact-value">{{ number_format((float) $product->price, 2) }} {{ __('admin.common.currency') }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>
        @endcan

        @can('products.view')
            <section class="panel">
                <div class="panel-heading"><div><h3>{{ __('admin.dashboard.low_stock') }}</h3><p>{{ __('admin.dashboard.low_stock_hint') }}</p></div><span class="panel-icon warning-icon"><x-icon name="warning" class="icon"/></span></div>
                @if ($lowStockProducts->isEmpty())
                    <x-empty-state :title="__('admin.empty.title')" :hint="__('admin.empty.hint')" icon="check"/>
                @else
                    <div class="compact-list">
                        @foreach ($lowStockProducts as $product)
                            <a class="compact-row" href="{{ route('products.show', $product) }}">
                                <span class="stock-indicator">{{ $product->stock_quantity }}</span>
                                <span class="compact-main"><strong>{{ $product->name }}</strong><small>{{ $product->sku }}</small></span>
                                <x-status-badge status="low">{{ __('admin.dashboard.stock_left', ['count' => $product->stock_quantity]) }}</x-status-badge>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>
        @endcan
    </div>

    <div class="dashboard-grid lower-grid">
        @can('users.view')
            <section class="panel">
                <div class="panel-heading"><div><h3>{{ __('admin.dashboard.recent_users') }}</h3><p>{{ __('admin.users.subtitle') }}</p></div><a class="text-link" href="{{ route('users.index') }}">{{ __('admin.common.view') }} <x-icon name="arrow" class="icon flip-rtl"/></a></div>
                @if ($recentUsers->isEmpty())
                    <x-empty-state :title="__('admin.users.empty_title')" :hint="__('admin.users.empty_hint')" icon="users"/>
                @else
                    <div class="compact-list">
                        @foreach ($recentUsers as $user)
                            <a class="compact-row" href="{{ route('users.show', $user) }}">
                                <span class="user-avatar user-avatar-small">{{ \Illuminate\Support\Str::substr($user->name, 0, 1) }}</span>
                                <span class="compact-main"><strong>{{ $user->name }}</strong><small>{{ $user->email }}</small></span>
                                <span class="compact-value">{{ $user->created_at->translatedFormat('M j') }}</span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>
        @endcan

        <section class="panel" id="activities">
            <div class="panel-heading"><div><h3>{{ __('admin.dashboard.recent_activity') }}</h3><p>{{ __('admin.dashboard.subtitle') }}</p></div><span class="panel-icon"><x-icon name="activity" class="icon"/></span></div>
            @if ($activities->isEmpty())
                <x-empty-state :title="__('admin.empty.title')" :hint="__('admin.empty.hint')" icon="activity"/>
            @else
                <div class="activity-list">
                    @foreach ($activities as $activity)
                        <div class="activity-row">
                            <span class="activity-dot"></span>
                            <div class="activity-copy"><strong>{{ __('admin.activity.'.$activity->action) }}</strong><small>{{ $activity->user?->name ?? __('admin.app_name') }} · {{ $activity->created_at->diffForHumans() }}</small></div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    @can('products.edit')
        <dialog class="modal" id="record-sale">
            <form action="{{ route('sales.store') }}" method="POST" class="modal-card">
                @csrf
                <div class="modal-heading"><div><span class="eyebrow">{{ __('admin.dashboard.quick_actions') }}</span><h2>{{ __('admin.dashboard.record_sale') }}</h2></div><button class="icon-button" type="button" aria-label="{{ __('admin.common.close') }}" data-dialog-close><x-icon name="close" class="icon"/></button></div>
                <x-field name="product_id" :label="__('admin.products.product')" required>
                    <select class="form-control" id="product_id" name="product_id" required>
                        <option value="">{{ __('admin.common.all') }}</option>
                        @foreach ($saleableProducts ?? collect() as $product)<option value="{{ $product->id }}">{{ $product->name }} · {{ $product->stock_quantity }} {{ __('admin.dashboard.units') }} · {{ number_format((float) $product->price, 2) }} {{ __('admin.common.currency') }}</option>@endforeach
                    </select>
                </x-field>
                <x-field name="quantity" :label="__('admin.dashboard.units')" required><input class="form-control" id="quantity" name="quantity" type="number" min="1" step="1" value="{{ old('quantity', 1) }}" required></x-field>
                <div class="modal-actions"><button class="button button-secondary" type="button" data-dialog-close>{{ __('admin.common.cancel') }}</button><button class="button button-primary" type="submit">{{ __('admin.dashboard.record_sale') }}</button></div>
            </form>
        </dialog>
    @endcan
@endsection
