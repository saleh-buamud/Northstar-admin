<form action="{{ $action }}" method="POST" class="panel form-panel form-stack" data-async-form>
    @csrf
    @if ($method !== 'POST')@method($method)@endif
    <div class="form-grid">
        <x-field name="name" :label="__('admin.common.name')" required><input class="form-control" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" maxlength="120" required></x-field>
        <x-field name="email" :label="__('admin.common.email')" required><input class="form-control" id="email" name="email" type="email" value="{{ old('email', $user->email ?? '') }}" maxlength="255" required></x-field>
        <x-field name="phone_number" :label="__('admin.fields.phone_number')"><input class="form-control" id="phone_number" name="phone_number" type="tel" value="{{ old('phone_number', $user->phone_number ?? '') }}" maxlength="30" autocomplete="tel"></x-field>
        <x-field name="role_id" :label="__('admin.users.role')" required><select class="form-control" id="role_id" name="role_id" required><option value="">{{ __('admin.users.no_role') }}</option>@foreach ($roles as $role)<option value="{{ $role->id }}" @selected(old('role_id', isset($user) ? $user->roles->first()?->id : '') == $role->id)>{{ $role->name }}</option>@endforeach</select></x-field>
        @if ($method !== 'POST')
            <x-field name="is_active" :label="__('admin.fields.is_active')" required><select class="form-control" id="is_active" name="is_active" required><option value="1" @selected((string) old('is_active', (int) $user->is_active) === '1')>{{ __('admin.users.active') }}</option><option value="0" @selected((string) old('is_active', (int) $user->is_active) === '0')>{{ __('admin.users.inactive') }}</option></select></x-field>
        @endif
        <x-field name="password" :label="$method === 'POST' ? __('admin.users.password') : __('admin.users.new_password')" :hint="$method === 'POST' ? __('admin.auth.password_hint') : __('admin.users.keep_password')" :required="$method === 'POST'">
            <input class="form-control" id="password" name="password" type="password" autocomplete="new-password" minlength="12" @required($method === 'POST')>
        </x-field>
        <x-field name="password_confirmation" :label="__('admin.users.password_confirmation')" :required="$method === 'POST'"><input class="form-control" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" minlength="12" @required($method === 'POST')></x-field>
    </div>
    <div class="form-actions"><a class="button button-secondary" href="{{ isset($user) ? route('users.show', $user) : route('users.index') }}">{{ __('admin.common.cancel') }}</a><button class="button button-primary" type="submit">{{ $submitLabel }} <x-icon name="arrow" class="icon flip-rtl"/></button></div>
</form>
