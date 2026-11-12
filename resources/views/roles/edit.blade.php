@extends('layouts.admin')

@section('page-title', __('admin.roles.edit_title'))

@section('content')
    <section class="page-intro"><div><a class="back-link" href="{{ route('roles.index') }}"><x-icon name="chevron" class="icon flip-rtl"/>{{ __('admin.common.back') }}</a><h2>{{ __('admin.roles.edit_title') }}</h2><p>{{ $role->name }}</p></div></section>
    @include('roles.form', ['action' => route('roles.update', $role), 'method' => 'PUT', 'submitLabel' => __('admin.common.save')])
@endsection
