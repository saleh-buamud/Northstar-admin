@extends('layouts.auth')

@section('page-title', __('admin.auth.login_title'))

@section('content')
    <section class="auth-card">
        <div class="auth-card-heading">
            <span class="auth-eyebrow">{{ __('admin.app_name') }}</span>
            <h1>{{ __('admin.auth.login_title') }}</h1>
            <p>{{ __('admin.auth.login_subtitle') }}</p>
        </div>
        <form action="{{ url('/login') }}" method="POST" class="form-stack">
            @csrf
            <x-field name="email" :label="__('admin.common.email')" required>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus class="form-control">
            </x-field>
            <x-field name="password" :label="__('admin.users.password')" required>
                <input id="password" name="password" type="password" autocomplete="current-password" required class="form-control">
            </x-field>
            <label class="checkbox-row remember-row"><input type="checkbox" name="remember" value="1" @checked(old('remember'))><span>{{ __('admin.auth.remember') }}</span></label>
            <button type="submit" class="button button-primary button-wide">{{ __('admin.auth.sign_in') }} <x-icon name="arrow" class="icon flip-rtl"/></button>
        </form>
    </section>
@endsection
