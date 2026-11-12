@extends('layouts.admin')

@section('page-title', __('admin.users.profile_title'))

@section('content')
    <section class="page-intro page-intro-actions">
        <div><a class="back-link" href="{{ route('users.index') }}"><x-icon name="chevron" class="icon flip-rtl"/>{{ __('admin.common.back') }}</a><h2>{{ __('admin.users.profile_title') }}</h2><p>{{ __('admin.users.subtitle') }}</p></div>
        <div class="intro-buttons">@can('users.edit')@if (! $user->hasRole('super-admin') && ! auth()->user()->is($user))<a class="button button-primary" href="{{ route('users.edit', $user) }}"><x-icon name="edit" class="icon"/>{{ __('admin.common.edit') }}</a>@endif @endcan</div>
    </section>
    <section class="panel user-profile-panel">
        <span class="user-avatar profile-avatar">@if ($user->avatar_path)<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($user->avatar_path) }}" alt="" loading="lazy">@else{{ \Illuminate\Support\Str::substr($user->name, 0, 1) }}@endif</span>
        <div class="profile-heading"><h3>{{ $user->name }}</h3><span>{{ $user->email }}</span></div>
        <x-status-badge :status="$user->is_active ? 'active' : 'inactive'">{{ $user->is_active ? __('admin.users.active') : __('admin.users.inactive') }}</x-status-badge>
        <dl class="detail-grid profile-details">
            <div><dt>{{ __('admin.users.role') }}</dt><dd>{{ $user->roles->first()?->name ?? __('admin.users.no_role') }}</dd></div>
            <div><dt>{{ __('admin.fields.phone_number') }}</dt><dd>{{ $user->phone_number ?: '—' }}</dd></div>
            <div><dt>{{ __('admin.users.registered') }}</dt><dd>{{ $user->created_at->translatedFormat('F j, Y') }}</dd></div>
            <div><dt>{{ __('admin.account.profile') }}</dt><dd>{{ $user->locale === 'ar' ? __('admin.language.ar') : __('admin.language.en') }}</dd></div>
        </dl>
    </section>
@endsection
