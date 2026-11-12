@extends('layouts.admin')

@section('page-title', __('admin.roles.title'))

@section('content')
    <section class="page-intro page-intro-actions">
        <div><span class="eyebrow">{{ __('admin.nav.access') }}</span><h2>{{ __('admin.roles.title') }}</h2><p>{{ __('admin.roles.subtitle') }}</p></div>
        <a class="button button-primary" href="{{ route('roles.create') }}"><x-icon name="plus" class="icon"/>{{ __('admin.roles.new') }}</a>
    </section>
    <div class="role-grid">
        @foreach ($roles as $role)
            <article class="panel role-card">
                <div class="role-card-heading">
                    <span class="role-mark"><x-icon name="shield" class="icon"/></span>
                    <div class="role-heading-copy"><h3>{{ $role->name }}</h3><span>{{ $role->is_system ? __('admin.roles.system_role') : __('admin.roles.custom_role') }}</span></div>
                    @unless ($role->is_system)
                        <a class="icon-button table-action" href="{{ route('roles.edit', $role) }}" aria-label="{{ __('admin.common.edit') }}" title="{{ __('admin.common.edit') }}"><x-icon name="edit" class="icon"/></a>
                    @endunless
                </div>
                @if ($role->description)<p class="role-description">{{ $role->description }}</p>@endif
                <div class="role-meta"><span><x-icon name="users" class="icon"/>{{ number_format($role->users_count) }} {{ __('admin.roles.members') }}</span><span>{{ $role->permissions->count() }} {{ __('admin.roles.permissions') }}</span></div>
                <div class="role-permission-list">
                    @forelse ($role->permissions as $permission)
                        <span class="permission-chip">{{ __('admin.permissions.'.$permission->name, [], app()->getLocale()) }}</span>
                    @empty
                        <span class="muted-cell">{{ __('admin.roles.no_permissions') }}</span>
                    @endforelse
                </div>
                @if (! $role->is_system && $role->users_count === 0)
                    <form class="role-delete" action="{{ route('roles.destroy', $role) }}" method="POST" data-confirm="{{ __('admin.confirm.delete_role') }}">@csrf @method('DELETE')<button class="button button-ghost button-small danger-text" type="submit"><x-icon name="trash" class="icon"/>{{ __('admin.common.delete') }}</button></form>
                @endif
            </article>
        @endforeach
    </div>
    <div class="table-footer role-pagination"><x-pagination :items="$roles"/></div>

    @can('permissions.manage')
        <section class="panel permission-panel">
            <div class="panel-heading"><div><h3>{{ __('admin.roles.permission_catalog') }}</h3><p>{{ __('admin.roles.protect_system') }}</p></div></div>
            <form class="permission-create-form" action="{{ route('permissions.store') }}" method="POST" data-async-form>
                @csrf
                <x-field name="name" :label="__('admin.roles.permission_key')" required><input class="form-control" id="name" name="name" :placeholder="__('admin.roles.permission_example')" value="{{ old('name') }}" pattern="[a-z][a-z0-9]*(\.[a-z][a-z0-9]*)+" required></x-field>
                <x-field name="group" :label="__('admin.roles.group')" required><input class="form-control" id="group" name="group" value="{{ old('group') }}" maxlength="60" required></x-field>
                <x-field name="description" :label="__('admin.roles.permission_description')"><input class="form-control" id="description" name="description" value="{{ old('description') }}" maxlength="500"></x-field>
                <button class="button button-secondary" type="submit"><x-icon name="plus" class="icon"/>{{ __('admin.roles.add_permission') }}</button>
            </form>
            <div class="permission-catalog">
                @foreach ($permissions->groupBy('group') as $group => $groupPermissions)
                    <section class="permission-group"><h4>{{ __('admin.permission_groups.'.$group) }}</h4>
                        @foreach ($groupPermissions as $permission)
                            <div class="permission-row"><span><strong>{{ __('admin.permissions.'.$permission->name) }}</strong><small>{{ $permission->name }}</small></span>
                                @if ($permission->is_system)<span class="system-lock"><x-icon name="shield" class="icon"/>{{ __('admin.roles.system_role') }}</span>
                                @else<form action="{{ route('permissions.destroy', $permission) }}" method="POST" data-confirm="{{ __('admin.confirm.delete_permission') }}">@csrf @method('DELETE')<button class="icon-button table-action danger-action" type="submit" aria-label="{{ __('admin.common.delete') }}" title="{{ __('admin.common.delete') }}"><x-icon name="trash" class="icon"/></button></form>@endif
                            </div>
                        @endforeach
                    </section>
                @endforeach
            </div>
            <div class="table-footer permission-pagination"><x-pagination :items="$permissions"/></div>
        </section>
    @endcan
@endsection
