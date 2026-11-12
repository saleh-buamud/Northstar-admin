@extends('layouts.admin')

@section('page-title', __('admin.categories.edit_title'))

@section('content')
    <section class="page-intro"><div><a class="back-link" href="{{ route('categories.index') }}"><x-icon name="chevron" class="icon flip-rtl"/>{{ __('admin.common.back') }}</a><h2>{{ __('admin.categories.edit_title') }}</h2><p>{{ $category->name }}</p></div></section>
    @include('categories.form', ['action' => route('categories.update', $category), 'method' => 'PUT', 'submitLabel' => __('admin.common.save')])
@endsection
