@extends('layouts.admin')

@section('page-title', __('admin.roles.create_title'))

@section('content')
    <section class="page-intro"><div><a class="back-link" href="{{ route('roles.index') }}"><x-icon name="chevron" class="icon flip-rtl"/>{{ __('admin.common.back') }}</a><h2>{{ __('admin.roles.create_title') }}</h2><p>{{ __('admin.roles.subtitle') }}</p></div></section>
    @include('roles.form', ['action' => route('roles.store'), 'method' => 'POST', 'submitLabel' => __('admin.common.create')])
@endsection
