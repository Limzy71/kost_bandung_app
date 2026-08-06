@props([
    'padding' => 'p-6 md:p-8',
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-xl border-3 border-black dark:bg-zinc-900 dark:border-zinc-700 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] '.$padding]) }}>
    {{ $slot }}
</div>
