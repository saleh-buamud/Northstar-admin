@props(['name', 'class' => ''])
<svg class="{{ $class }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    @switch($name)
        @case('grid') <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/> @break
        @case('box') <path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 8 9 5 9-5M3 8v9l9 5 9-5V8M12 13v9"/> @break
        @case('users') <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="10" cy="7" r="4"/><path d="M20 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/> @break
        @case('layers') <path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5M3 16l9 5 9-5"/> @break
        @case('shield') <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11Z"/><path d="m9 12 2 2 4-4"/> @break
        @case('menu') <path d="M4 6h16M4 12h16M4 18h16"/> @break
        @case('sun') <circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/> @break
        @case('moon') <path d="M20.9 13A9 9 0 0 1 11 3.1 9 9 0 1 0 20.9 13Z"/> @break
        @case('search') <circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/> @break
        @case('plus') <path d="M12 5v14m-7-7h14"/> @break
        @case('edit') <path d="m16 4 4 4L9 19l-5 1 1-5L16 4Z"/><path d="m14 6 4 4"/> @break
        @case('trash') <path d="M3 6h18M8 6V4h8v2m3 0-1 14H6L5 6m4 4v6m6-6v6"/> @break
        @case('eye') <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/> @break
        @case('arrow') <path d="M7 17 17 7M7 7h10v10"/> @break
        @case('dollar') <path d="M12 2v20m5-16H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/> @break
        @case('chart') <path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-5 5"/> @break
        @case('activity') <path d="M3 12h4l3-9 4 18 3-9h4"/> @break
        @case('clock') <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/> @break
        @case('mail') <rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/> @break
        @case('calendar') <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/> @break
        @case('globe') <circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/> @break
        @case('bell') <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9m-8 12h4"/> @break
        @case('logout') <path d="M10 17l5-5-5-5m5 5H3"/><path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"/> @break
        @case('check') <path d="m5 12 4 4L19 6"/> @break
        @case('warning') <path d="M10.3 3.9 2.4 17.5A2 2 0 0 0 4.1 20h15.8a2 2 0 0 0 1.7-2.5L13.7 3.9a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4m0 4h.01"/> @break
        @case('image') <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/> @break
        @case('chevron') <path d="m9 18 6-6-6-6"/> @break
        @case('close') <path d="m18 6-12 12M6 6l12 12"/> @break
    @endswitch
</svg>
