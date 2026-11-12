<form action="{{ $action }}" method="POST" class="panel form-panel form-stack" data-async-form>
    @csrf
    @if ($method !== 'POST')@method($method)@endif
    <div class="form-grid">
        <x-field name="name" :label="__('admin.common.name')" required><input class="form-control" id="name" name="name" value="{{ old('name', $category->name ?? '') }}" maxlength="120" required></x-field>
        <x-field name="status" :label="__('admin.common.status')" required><select class="form-control" id="status" name="status" required><option value="active" @selected(old('status', $category->status ?? 'active') === 'active')>{{ __('admin.products.active') }}</option><option value="inactive" @selected(old('status', $category->status ?? '') === 'inactive')>{{ __('admin.products.inactive') }}</option></select></x-field>
        <div class="form-field form-span-two"><label for="description">{{ __('admin.common.description') }}</label><textarea class="form-control" id="description" name="description" rows="5" maxlength="1000">{{ old('description', $category->description ?? '') }}</textarea>@error('description')<span class="field-error">{{ $message }}</span>@enderror</div>
    </div>
    <div class="form-actions"><a class="button button-secondary" href="{{ route('categories.index') }}">{{ __('admin.common.cancel') }}</a><button class="button button-primary" type="submit">{{ $submitLabel }} <x-icon name="arrow" class="icon flip-rtl"/></button></div>
</form>
