<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="panel form-panel form-stack" data-async-form>
    @csrf
    @if ($method !== 'POST')@method($method)@endif
    <div class="form-grid">
        <x-field name="name" :label="__('admin.fields.product_name')" required><input class="form-control" id="name" name="name" value="{{ old('name', $product->name ?? '') }}" maxlength="180" required></x-field>
        <x-field name="sku" :label="__('admin.fields.sku')" required><input class="form-control" id="sku" name="sku" value="{{ old('sku', $product->sku ?? '') }}" maxlength="80" required></x-field>
        <x-field name="category_id" :label="__('admin.fields.category')" required>
            <select class="form-control" id="category_id" name="category_id" required><option value="">{{ __('admin.products.filter_category') }}</option>@foreach ($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $product->category_id ?? '') == $category->id)>{{ $category->name }}</option>@endforeach</select>
        </x-field>
        <x-field name="price" :label="__('admin.fields.price')" required><div class="input-affix"><input class="form-control" id="price" name="price" type="number" step="0.01" min="0" value="{{ old('price', $product->price ?? '') }}" required><span>{{ __('admin.common.currency') }}</span></div></x-field>
        <x-field name="sale_price" :label="__('admin.fields.sale_price')" :hint="__('admin.common.optional')"><div class="input-affix"><input class="form-control" id="sale_price" name="sale_price" type="number" step="0.01" min="0" value="{{ old('sale_price', $product->sale_price ?? '') }}"><span>{{ __('admin.common.currency') }}</span></div></x-field>
        <x-field name="stock_quantity" :label="__('admin.fields.stock_quantity')" required><input class="form-control" id="stock_quantity" name="stock_quantity" type="number" min="0" step="1" value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" required></x-field>
        <x-field name="status" :label="__('admin.fields.status')" required>
            <select class="form-control" id="status" name="status" required><option value="active" @selected(old('status', $product->status ?? 'active') === 'active')>{{ __('admin.products.active') }}</option><option value="inactive" @selected(old('status', $product->status ?? '') === 'inactive')>{{ __('admin.products.inactive') }}</option><option value="draft" @selected(old('status', $product->status ?? '') === 'draft')>{{ __('admin.products.draft') }}</option></select>
        </x-field>
        <div class="form-field form-span-two">
            <label for="description">{{ __('admin.fields.description') }}</label>
            <textarea class="form-control" id="description" name="description" rows="5" maxlength="5000">{{ old('description', $product->description ?? '') }}</textarea>
            @error('description')<span class="field-error">{{ $message }}</span>@enderror
        </div>
        <x-field name="image" :label="__('admin.products.image')" :hint="__('admin.common.optional')">
            @if (isset($product) && $product->image_path)<div class="image-preview"><img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($product->image_path) }}" alt="{{ $product->name }}">@if ($method !== 'POST')<label class="checkbox-row"><input type="checkbox" name="remove_image" value="1" @checked(old('remove_image'))><span>{{ __('admin.products.remove_image') }}</span></label>@endif</div>@endif
            <input class="form-control file-input" id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp">
        </x-field>
    </div>
    <div class="form-actions"><a class="button button-secondary" href="{{ isset($product) ? route('products.show', $product) : route('products.index') }}">{{ __('admin.common.cancel') }}</a><button class="button button-primary" type="submit">{{ $submitLabel }} <x-icon name="arrow" class="icon flip-rtl"/></button></div>
</form>
