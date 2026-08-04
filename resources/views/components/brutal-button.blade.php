@props([
    'color' => 'yellow',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
])

@php
    $colors = [
        'yellow' => 'bg-yellow-400 hover:bg-yellow-300',
        'lime' => 'bg-lime-400 hover:bg-lime-300',
        'cyan' => 'bg-cyan-400 hover:bg-cyan-300',
        'rose' => 'bg-rose-500 hover:bg-rose-400',
        'white' => 'bg-white hover:bg-zinc-100',
    ];

    $sizes = [
        'sm' => 'text-xs px-4 py-2 border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none rounded',
        'md' => 'text-sm px-6 py-3.5 border-3 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:translate-x-1 active:translate-y-1 active:shadow-none rounded-lg',
    ];

    $class = 'inline-flex items-center justify-center gap-2 font-black uppercase text-black transition-all cursor-pointer '
        .$colors[$color].' '.$sizes[$size];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $class]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $class]) }}>
        {{ $slot }}
    </button>
@endif
