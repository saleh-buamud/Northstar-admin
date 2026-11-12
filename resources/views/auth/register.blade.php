@extends('layouts.auth')

@section('page-title', __('admin.auth.register_title'))

@section('content')
    <section class="auth-card">
        <div class="auth-card-heading">
            <span class="auth-eyebrow">{{ __('admin.app_name') }}</span>
            <h1>{{ __('admin.auth.register_title') }}</h1>
            <p>{{ __('admin.auth.register_subtitle') }}</p>
        </div>
        <form action="{{ url('/register') }}" method="POST" class="form-stack">
            @csrf
            <x-field name="name" :label="__('admin.common.name')" required>
                <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" maxlength="120" required autofocus class="form-control">
            </x-field>
            <x-field name="email" :label="__('admin.common.email')" required>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required class="form-control">
            </x-field>
            <x-field name="password" :label="__('admin.users.password')" :hint="__('admin.auth.password_hint')" required>
                <input id="password" name="password" type="password" autocomplete="new-password" minlength="12" required class="form-control">
            </x-field>
            <x-field name="password_confirmation" :label="__('admin.users.password_confirmation')" required>
                <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="12" required class="form-control">
            </x-field>
            <button type="submit" class="button button-primary button-wide">{{ __('admin.auth.create_account') }} <x-icon name="arrow" class="icon flip-rtl"/></button>
        </form>
        <p class="auth-note"><x-icon name="shield" class="icon"/>{{ __('admin.auth.first_admin_note') }}</p>
    </section>
@endsection
