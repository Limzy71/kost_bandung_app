@props([
    'color' => 'zinc',
    'icon' => null,
])

@php
    $colors = [
        'lime' => 'bg-lime-400 text-black',
        'yellow' => 'bg-yellow-300 text-black',
        'cyan' => 'bg-cyan-300 text-black',
        'rose' => 'bg-rose-400 text-black',
        'zinc' => 'bg-zinc-200 dark:bg-zinc-700 dark:text-zinc-100',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'px-3.5 py-1 '.$colors[$color].' border-2 border-black dark:border-zinc-600 text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] tracking-wider inline-flex items-center gap-1']) }}>
    @if ($icon)
        <x-icon :name="$icon" class="w-3.5 h-3.5 stroke-[2.5]" />
    @endif
    {{ $slot }}
</span>
