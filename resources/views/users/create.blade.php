@extends('layouts.admin')

@section('page-title', __('admin.users.create_title'))

@section('content')
    <section class="page-intro"><div><a class="back-link" href="{{ route('users.index') }}"><x-icon name="chevron" class="icon flip-rtl"/>{{ __('admin.common.back') }}</a><h2>{{ __('admin.users.create_title') }}</h2><p>{{ __('admin.users.subtitle') }}</p></div></section>
    @include('users.form', ['action' => route('users.store'), 'method' => 'POST', 'submitLabel' => __('admin.common.create')])
@endsection
