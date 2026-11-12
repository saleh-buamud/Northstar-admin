@extends('layouts.admin')

@section('page-title', __('admin.users.edit_title'))

@section('content')
    <section class="page-intro"><div><a class="back-link" href="{{ route('users.show', $user) }}"><x-icon name="chevron" class="icon flip-rtl"/>{{ __('admin.common.back') }}</a><h2>{{ __('admin.users.edit_title') }}</h2><p>{{ $user->name }}</p></div></section>
    @include('users.form', ['action' => route('users.update', $user), 'method' => 'PUT', 'submitLabel' => __('admin.common.save')])
@endsection
