@extends('layouts.admin')

@section('page-title', __('admin.products.edit_title'))

@section('content')
    <section class="page-intro"><div><a class="back-link" href="{{ route('products.show', $product) }}"><x-icon name="chevron" class="icon flip-rtl"/>{{ __('admin.common.back') }}</a><h2>{{ __('admin.products.edit_title') }}</h2><p>{{ $product->name }}</p></div></section>
    @include('products.form', ['action' => route('products.update', $product), 'method' => 'PUT', 'submitLabel' => __('admin.common.save')])
@endsection
