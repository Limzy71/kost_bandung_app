@props([
    'label' => '',
    'value' => '',
    'hint' => '',
    'icon' => 'lucide-circle-check',
    'color' => 'bg-cyan-300',
])

<div class="{{ $color }} border-3 border-black p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] rounded-xl relative overflow-hidden group">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs font-black uppercase tracking-wider text-black">{{ $label }}</p>
            <div class="text-4xl font-black text-black mt-2 tracking-tighter">{{ $value }}</div>
            @if ($hint)
                <p class="text-xs font-bold text-black/80 mt-1">{{ $hint }}</p>
            @endif
        </div>
        <div class="w-14 h-14 rounded-lg bg-white border-2 border-black flex items-center justify-center text-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] shrink-0">
            <x-icon :name="$icon" class="w-7 h-7 stroke-[2]" />
        </div>
    </div>
</div>
