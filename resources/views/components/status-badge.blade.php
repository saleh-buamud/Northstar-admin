@props(['status'])
@php($statusClass = in_array($status, ['active', 'Active'], true) ? 'success' : (in_array($status, ['inactive', 'Inactive'], true) ? 'muted' : 'warning'))
<span class="badge badge-{{ $statusClass }}">{{ $slot }}</span>
