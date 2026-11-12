@props(['name', 'label', 'required' => false, 'hint' => null])
<div class="form-field">
    <label for="{{ $name }}">{{ $label }} @if ($required)<span class="required-mark" aria-hidden="true">*</span>@endif</label>
    {{ $slot }}
    @if ($hint)<small class="field-hint">{{ $hint }}</small>@endif
    @error($name)<span class="field-error">{{ $message }}</span>@enderror
</div>
