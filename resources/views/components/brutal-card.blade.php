@props([
    'padding' => 'p-6 md:p-8',
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border-3 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] '.$padding]) }}>
    {{ $slot }}
</div>
