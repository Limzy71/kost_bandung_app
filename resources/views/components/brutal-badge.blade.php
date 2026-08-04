@props([
    'color' => 'zinc',
    'icon' => null,
])

@php
    $colors = [
        'lime' => 'bg-lime-400',
        'yellow' => 'bg-yellow-300',
        'cyan' => 'bg-cyan-300',
        'rose' => 'bg-rose-400',
        'zinc' => 'bg-zinc-200',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'px-3.5 py-1 '.$colors[$color].' text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider inline-flex items-center gap-1']) }}>
    @if ($icon)
        <x-icon :name="$icon" class="w-3.5 h-3.5 stroke-[2.5]" />
    @endif
    {{ $slot }}
</span>
