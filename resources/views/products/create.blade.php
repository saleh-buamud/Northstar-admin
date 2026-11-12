@extends('layouts.admin')

@section('page-title', __('admin.products.create_title'))

@section('content')
    <section class="page-intro"><div><a class="back-link" href="{{ route('products.index') }}"><x-icon name="chevron" class="icon flip-rtl"/>{{ __('admin.common.back') }}</a><h2>{{ __('admin.products.create_title') }}</h2><p>{{ __('admin.products.subtitle') }}</p></div></section>
    @if ($categories->isEmpty())
        <section class="panel"><x-empty-state :title="__('admin.categories.empty_title')" :hint="__('admin.categories.empty_hint')" icon="layers"><x-slot:action><a class="button button-primary" href="{{ route('categories.create') }}"><x-icon name="plus" class="icon"/>{{ __('admin.categories.new') }}</a></x-slot:action></x-empty-state></section>
    @else
        @include('products.form', ['action' => route('products.store'), 'method' => 'POST', 'submitLabel' => __('admin.common.create')])
    @endif
@endsection
