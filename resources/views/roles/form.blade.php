<form action="{{ $action }}" method="POST" class="panel form-panel form-stack" data-async-form>
    @csrf
    @if ($method !== 'POST')@method($method)@endif
    <div class="form-grid">
        <x-field name="name" :label="__('admin.common.name')" required><input class="form-control" id="name" name="name" value="{{ old('name', $role->name ?? '') }}" maxlength="80" required></x-field>
        <div class="form-field"><label for="description">{{ __('admin.common.description') }}</label><input class="form-control" id="description" name="description" value="{{ old('description', $role->description ?? '') }}" maxlength="500">@error('description')<span class="field-error">{{ $message }}</span>@enderror</div>
    </div>
    <fieldset class="permission-fieldset">
        <legend>{{ __('admin.roles.choose_permissions') }}</legend>
        @error('permissions')<span class="field-error">{{ $message }}</span>@enderror
        <div class="permission-checkbox-grid">
            @foreach ($permissions->groupBy('group') as $group => $groupPermissions)
                <section class="permission-checkbox-group"><h4>{{ __('admin.permission_groups.'.$group) }}</h4>
                    @foreach ($groupPermissions as $permission)
                        <label class="permission-checkbox"><input type="checkbox" name="permissions[]" value="{{ $permission->id }}" @checked(in_array($permission->id, old('permissions', isset($role) ? $role->permissions->modelKeys() : [])))><span><strong>{{ __('admin.permissions.'.$permission->name) }}</strong><small>{{ $permission->name }}</small></span></label>
                    @endforeach
                </section>
            @endforeach
        </div>
    </fieldset>
    <div class="form-actions"><a class="button button-secondary" href="{{ route('roles.index') }}">{{ __('admin.common.cancel') }}</a><button class="button button-primary" type="submit">{{ $submitLabel }} <x-icon name="arrow" class="icon flip-rtl"/></button></div>
</form>
