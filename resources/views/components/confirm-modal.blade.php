<div
    x-data="{
        open: false,
        title: '',
        message: '',
        confirmLabel: 'Ya, Hapus',
        danger: false,
        action: null,
        openConfirm({ title, message, confirmLabel, danger, action }) {
            this.title = title || 'Konfirmasi';
            this.message = message || '';
            this.confirmLabel = confirmLabel || 'Ya, Lanjutkan';
            this.danger = danger === true;
            this.action = action || null;
            this.open = true;
        },
        confirm() {
            const run = this.action;
            this.open = false;
            if (typeof run === 'function') run();
        }
    }"
    x-on:open-confirm.window="openConfirm($event.detail)"
    @keydown.escape.window="open = false"
>
    <div x-show="open" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center p-4" @click.self="open = false">
        <div class="absolute inset-0 bg-black/60"></div>
        <div class="relative bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 rounded-2xl p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] w-full max-w-md space-y-4"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-lg font-black text-black dark:text-white uppercase flex items-center gap-2">
                    <x-icon name="lucide-triangle-alert" x-show="danger" class="w-5 h-5 text-rose-600 dark:text-rose-400 stroke-[2.5]" />
                    <x-icon name="lucide-help-circle" x-show="!danger" class="w-5 h-5 text-amber-500 dark:text-amber-400 stroke-[2.5]" />
                    <span x-text="title"></span>
                </h3>
                <button type="button" @click="open = false"
                    class="w-8 h-8 bg-rose-500 hover:bg-rose-400 text-white border-3 border-black dark:border-zinc-700 rounded font-black text-sm cursor-pointer shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all flex items-center justify-center">✕</button>
            </div>
            <p class="text-sm font-bold text-zinc-600 dark:text-zinc-400 leading-relaxed" x-text="message"></p>
            <div class="grid grid-cols-2 gap-3 pt-1">
                <button type="button" @click="open = false"
                    class="py-3 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-600 text-black dark:text-white border-3 border-black dark:border-zinc-700 font-black text-xs uppercase rounded-xl cursor-pointer">Batal</button>
                <button type="button" @click="confirm()"
                    :class="danger ? 'bg-rose-500 hover:bg-rose-400 text-white' : 'bg-lime-400 hover:bg-lime-300 text-black'"
                    class="py-3 border-3 border-black dark:border-zinc-700 font-black text-xs uppercase rounded-xl cursor-pointer shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all">
                    <span x-text="confirmLabel"></span>
                </button>
            </div>
        </div>
    </div>
</div>
