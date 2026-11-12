@props(['title', 'hint', 'icon' => 'box'])
<div class="empty-state">
    <span class="empty-icon"><x-icon :name="$icon"/></span>
    <h3>{{ $title }}</h3>
    <p>{{ $hint }}</p>
    @if (isset($action))
        <div class="empty-action">{{ $action }}</div>
    @endif
</div>
