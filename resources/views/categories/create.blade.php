@extends('layouts.admin')

@section('page-title', __('admin.categories.create_title'))

@section('content')
    <section class="page-intro"><div><a class="back-link" href="{{ route('categories.index') }}"><x-icon name="chevron" class="icon flip-rtl"/>{{ __('admin.common.back') }}</a><h2>{{ __('admin.categories.create_title') }}</h2><p>{{ __('admin.categories.subtitle') }}</p></div></section>
    @include('categories.form', ['action' => route('categories.store'), 'method' => 'POST', 'submitLabel' => __('admin.common.create')])
@endsection
