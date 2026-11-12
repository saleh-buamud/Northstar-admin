@if (session('success') || session('error'))
    <div class="toast {{ session('error') ? 'toast-error' : 'toast-success' }}" role="status" aria-live="polite" aria-atomic="true" data-toast>
        <x-icon :name="session('error') ? 'warning' : 'check'" class="icon"/>
        <span>{{ session('error') ?? session('success') }}</span>
        <button class="toast-close" type="button" aria-label="{{ __('admin.common.close') }}" data-toast-close><x-icon name="close" class="icon"/></button>
    </div>
@endif
