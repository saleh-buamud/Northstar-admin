@extends('layouts.admin')

@section('page-title', __('admin.users.title'))

@section('content')
    <section class="page-intro page-intro-actions">
        <div><span class="eyebrow">{{ __('admin.nav.workspace') }}</span><h2>{{ __('admin.users.title') }}</h2><p>{{ __('admin.users.subtitle') }}</p></div>
        @can('users.create')<a class="button button-primary" href="{{ route('users.create') }}"><x-icon name="plus" class="icon"/>{{ __('admin.users.new') }}</a>@endcan
    </section>
    <section class="panel table-panel">
        <form class="filter-bar" action="{{ route('users.index') }}" method="GET">
            <input type="hidden" name="per_page" value="{{ request('per_page', 10) }}">
            <label class="search-control"><x-icon name="search" class="icon"/><input type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('admin.users.search_placeholder') }}" aria-label="{{ __('admin.common.search') }}"></label>
            <label class="sr-only" for="user-status">{{ __('admin.common.status') }}</label>
            <select class="filter-select" id="user-status" name="status"><option value="">{{ __('admin.common.all') }}</option><option value="active" @selected(request('status') === 'active')>{{ __('admin.users.active') }}</option><option value="inactive" @selected(request('status') === 'inactive')>{{ __('admin.users.inactive') }}</option></select>
            <button type="submit" class="button button-secondary">{{ __('admin.common.search') }}</button>
            @if (request()->hasAny(['q', 'status']))<a class="button button-ghost" href="{{ route('users.index') }}">{{ __('admin.common.clear') }}</a>@endif
        </form>
        @if ($users->isEmpty())
            <x-empty-state :title="__('admin.users.empty_title')" :hint="__('admin.users.empty_hint')" icon="users"/>
            @can('users.create')
                <div class="empty-state-action"><a class="button button-primary button-small" href="{{ route('users.create') }}"><x-icon name="plus" class="icon"/>{{ __('admin.users.new') }}</a></div>
            @endcan
        @else
            <div class="table-scroll"><table class="data-table">
                <thead><tr><th>{{ __('admin.users.user') }}</th><th>{{ __('admin.users.role') }}</th><th>{{ __('admin.users.registered') }}</th><th>{{ __('admin.common.status') }}</th><th>{{ __('admin.common.actions') }}</th></tr></thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td><a class="table-product" href="{{ route('users.show', $user) }}"><span class="user-avatar user-avatar-small">@if ($user->avatar_path)<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar_path) }}" alt="" loading="lazy">@else{{ \Illuminate\Support\Str::substr($user->name, 0, 1) }}@endif</span><span><strong>{{ $user->name }}</strong><small>{{ $user->email }}@if ($user->phone_number) · {{ $user->phone_number }}@endif</small></span></a></td>
                            <td>{{ $user->roles->first()?->name ?? __('admin.users.no_role') }}</td>
                            <td>{{ $user->created_at->translatedFormat('M j, Y') }}</td>
                            <td><x-status-badge :status="$user->is_active ? 'active' : 'inactive'">{{ $user->is_active ? __('admin.users.active') : __('admin.users.inactive') }}</x-status-badge></td>
                            <td><div class="row-actions">
                                <a class="icon-button table-action" href="{{ route('users.show', $user) }}" aria-label="{{ __('admin.common.view') }}" title="{{ __('admin.common.view') }}"><x-icon name="eye" class="icon"/></a>
                                @can('users.edit')@if (! $user->hasRole('super-admin') && ! auth()->user()->is($user))<a class="icon-button table-action" href="{{ route('users.edit', $user) }}" aria-label="{{ __('admin.common.edit') }}" title="{{ __('admin.common.edit') }}"><x-icon name="edit" class="icon"/></a>@endif @endcan
                                @can('users.delete')@if (! $user->hasRole('super-admin') && ! auth()->user()->is($user))<form action="{{ route('users.destroy', $user) }}" method="POST" data-confirm="{{ __('admin.confirm.delete_user') }}">@csrf @method('DELETE')<button class="icon-button table-action danger-action" type="submit" aria-label="{{ __('admin.common.delete') }}" title="{{ __('admin.common.delete') }}"><x-icon name="trash" class="icon"/></button></form>@endif @endcan
                            </div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table></div>
            <div class="table-footer"><x-pagination :items="$users"/></div>
        @endif
    </section>
@endsection
