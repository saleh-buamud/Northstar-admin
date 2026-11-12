<header class="topbar">
    <div class="topbar-start">
        <button class="icon-button sidebar-toggle-button" type="button" aria-label="{{ __('admin.nav.workspace') }}" aria-expanded="true" aria-controls="app-sidebar" data-menu-toggle><x-icon name="menu" class="icon"/></button>
        <div class="page-heading">
            <span class="breadcrumb">{{ __('admin.app_name') }} <x-icon name="chevron" class="icon"/></span>
            <h1>@yield('page-title', __('admin.dashboard.title'))</h1>
        </div>
    </div>
    <div class="topbar-actions">
        <form class="language-form" action="{{ route('locale.update') }}" method="POST">
            @csrf
            <label class="sr-only" for="locale-switcher">{{ __('admin.language.label') }}</label>
            <x-icon name="globe" class="icon"/>
            <select id="locale-switcher" name="locale" onchange="this.form.requestSubmit()">
                <option value="en" @selected(app()->getLocale() === 'en')>{{ __('admin.language.en') }}</option>
                <option value="ar" @selected(app()->getLocale() === 'ar')>{{ __('admin.language.ar') }}</option>
            </select>
        </form>
        <button class="icon-button" type="button" aria-label="{{ __('admin.theme.toggle') }}" title="{{ __('admin.theme.toggle') }}" data-theme-toggle>
            <x-icon name="sun" class="icon theme-light-icon"/>
            <x-icon name="moon" class="icon theme-dark-icon"/>
        </button>
        <span class="topbar-divider"></span>
        <div class="topbar-account">
            <span class="user-avatar">{{ \Illuminate\Support\Str::substr(auth()->user()->name, 0, 1) }}</span>
            <span class="topbar-user-name">{{ auth()->user()->name }}</span>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="icon-button signout-button" type="submit" aria-label="{{ __('admin.account.sign_out') }}" title="{{ __('admin.account.sign_out') }}"><x-icon name="logout" class="icon"/></button>
        </form>
    </div>
</header>
