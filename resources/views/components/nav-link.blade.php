@props(['active'])

@php
    $href = (string) $attributes->get('href');
    $iconPath = 'M3 11.5 12 4l9 7.5M5 10v10h14V10M9 20v-6h6v6';
    if (str_contains($href, 'visit-tasks')) $iconPath = 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4';
    elseif (str_contains($href, 'visits') || str_contains($href, 'tracking')) $iconPath = 'M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11Z M12 12a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z';
    elseif (str_contains($href, 'customers')) $iconPath = 'M20 21a8 8 0 0 0-16 0 M12 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z';
    elseif (str_contains($href, 'products')) $iconPath = 'm21 8-9-5-9 5 9 5 9-5ZM3 12l9 5 9-5M3 16l9 5 9-5';
    elseif (str_contains($href, 'orders')) $iconPath = 'M6 3h12a2 2 0 0 1 2 2v14H4V5a2 2 0 0 1 2-2ZM8 8h8M8 12h6M8 16h4';
    elseif (str_contains($href, 'targets')) $iconPath = 'M4 19V5M4 19h16M8 15l3-4 3 2 5-7';
    elseif (str_contains($href, 'reports')) $iconPath = 'M6 3h9l3 3v15H6V3ZM9 11h6M9 15h6M9 7h4';
    elseif (str_contains($href, 'users')) $iconPath = 'M16 20a4 4 0 0 0-8 0M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6M19 20a3 3 0 0 0-2.5-3M17 6a2.5 2.5 0 0 1 0 5';
    elseif (str_contains($href, 'settings')) $iconPath = 'M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8ZM4 12H2m20 0h-2M12 4V2m0 20v-2M5.6 5.6 4.2 4.2m15.6 15.6-1.4-1.4M18.4 5.6l1.4-1.4M4.2 19.8l1.4-1.4';
    
    $classes = ($active ?? false)
        ? 'flex w-full items-center rounded-xl border-l-4 border-amber-400 bg-white/10 px-3.5 py-2.5 text-sm font-semibold leading-5 text-white transition duration-150 ease-in-out focus:outline-none shadow-xs'
        : 'flex w-full items-center rounded-xl border-l-4 border-transparent px-3.5 py-2.5 text-sm font-medium leading-5 text-slate-400 transition duration-150 ease-in-out hover:border-slate-700 hover:bg-white/5 hover:text-white focus:outline-none active:scale-[0.98]';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <svg class="me-3 h-4 w-4 shrink-0 {{ ($active ?? false) ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $iconPath }}" />
    </svg>
    <span>{{ $slot }}</span>
</a>
