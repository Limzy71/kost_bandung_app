<div>
    <a href="{{ route('dashboard') }}"
        class="inline-flex items-center gap-2 text-xs font-black uppercase text-black dark:text-white bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 px-3.5 py-2 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] hover:bg-yellow-300 dark:hover:bg-zinc-800 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg mb-6 group">
        <x-icon name="lucide-arrow-left" class="w-4 h-4 text-black dark:text-white group-hover:-translate-x-1 transition-transform stroke-[3]" />
        <span>Kembali ke Dashboard</span>
    </a>

    <div
        class="{{ $bgClass }} border-4 border-black dark:border-zinc-700 p-6 md:p-8 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] rounded-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <span
                class="px-3 py-1 {{ $badgeClass }} font-extrabold text-xs uppercase tracking-wider border border-black dark:border-zinc-700 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                {{ $badge }}
            </span>
            <h1 class="text-3xl md:text-4xl font-black text-black tracking-tight uppercase mt-2">
                {{ $title }}
            </h1>
            <p class="text-sm font-bold text-black/80 mt-1">
                {{ $subtitle }}
            </p>
        </div>
    </div>
</div>
