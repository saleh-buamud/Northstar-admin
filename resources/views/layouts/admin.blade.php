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
                const theme = saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
                document.documentElement.dataset.theme = theme;
            } catch (_) {
                document.documentElement.dataset.theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="mobile-scrim" data-menu-close></div>
    @include('components.sidebar')
    <div class="main-panel">
        @include('components.topbar')
        <main class="page-content" id="main-content">
            @include('components.toast')
            @yield('content')
        </main>
        <footer class="app-footer"><span>{{ __('admin.app_name') }}</span><span>{{ now()->year }}</span></footer>
    </div>
</body>
</html>
