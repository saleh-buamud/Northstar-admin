<aside class="sidebar" id="app-sidebar" aria-label="{{ __('admin.nav.workspace') }}">
    <a class="brand" href="{{ route('dashboard') }}">
        <span class="brand-mark">N</span>
        <span class="brand-copy"><strong>{{ __('admin.app_name') }}</strong><small>{{ __('admin.app_tagline') }}</small></span>
    </a>
    <div class="sidebar-caption">{{ __('admin.nav.workspace') }}</div>
    <nav class="side-nav">
        @can('dashboard.view')
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}" href="{{ route('dashboard') }}"><x-icon name="grid" class="icon"/><span>{{ __('admin.nav.overview') }}</span></a>
        @endcan
        @can('products.view')
            <a class="nav-link {{ request()->routeIs('products.*') ? 'is-active' : '' }}" href="{{ route('products.index') }}"><x-icon name="box" class="icon"/><span>{{ __('admin.nav.products') }}</span></a>
            <a class="nav-link {{ request()->routeIs('categories.*') ? 'is-active' : '' }}" href="{{ route('categories.index') }}"><x-icon name="layers" class="icon"/><span>{{ __('admin.nav.categories') }}</span></a>
        @endcan
        @can('users.view')
            <a class="nav-link {{ request()->routeIs('users.*') ? 'is-active' : '' }}" href="{{ route('users.index') }}"><x-icon name="users" class="icon"/><span>{{ __('admin.nav.users') }}</span></a>
        @endcan
        @can('roles.manage')
            <div class="sidebar-caption sidebar-caption-spaced">{{ __('admin.nav.access') }}</div>
            <a class="nav-link {{ request()->routeIs('roles.*') ? 'is-active' : '' }}" href="{{ route('roles.index') }}"><x-icon name="shield" class="icon"/><span>{{ __('admin.nav.roles') }}</span></a>
        @endcan
    </nav>
    <div class="sidebar-bottom">
        <span class="sidebar-avatar">{{ \Illuminate\Support\Str::substr(auth()->user()->name, 0, 1) }}</span>
        <span class="sidebar-user"><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->roles->first()?->name ?? __('admin.users.no_role') }}</small></span>
    </div>
</aside>
