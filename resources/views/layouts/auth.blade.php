<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('page-title') · {{ __('admin.app_name') }}</title>
    <script>
        (() => {
            try {
                const saved = localStorage.getItem('northstar-theme');
                document.documentElement.dataset.theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            } catch (_) {
                document.documentElement.dataset.theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-shell">
        <a class="brand auth-brand" href="{{ route('login') }}">
            <span class="brand-mark">N</span>
            <span class="brand-copy"><strong>{{ __('admin.app_name') }}</strong><small>{{ __('admin.app_tagline') }}</small></span>
        </a>
        <button class="icon-button auth-theme-toggle" type="button" aria-label="{{ __('admin.theme.toggle') }}" data-theme-toggle><x-icon name="sun" class="icon theme-light-icon"/><x-icon name="moon" class="icon theme-dark-icon"/></button>
        @include('components.toast')
        @yield('content')
        <div class="auth-language">
            <form action="{{ route('locale.update') }}" method="POST">
                @csrf
                <label for="auth-locale">{{ __('admin.language.label') }}</label>
                <select id="auth-locale" name="locale" onchange="this.form.requestSubmit()">
                    <option value="en" @selected(app()->getLocale() === 'en')>{{ __('admin.language.en') }}</option>
                    <option value="ar" @selected(app()->getLocale() === 'ar')>{{ __('admin.language.ar') }}</option>
                </select>
            </form>
        </div>
    </main>
</body>
</html>
