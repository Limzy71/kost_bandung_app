<div
    x-data="{
        show: false,
        message: '',
        type: 'success',
        timer: null,
        styles: {
            success: {
                bg: 'bg-lime-300',
                iconBg: 'bg-black text-lime-300',
                icon: 'check',
            },
            error: {
                bg: 'bg-rose-300',
                iconBg: 'bg-black text-rose-300',
                icon: 'x',
            },
            info: {
                bg: 'bg-cyan-300',
                iconBg: 'bg-black text-cyan-300',
                icon: 'info',
            },
        },
        trigger({ message, type }) {
            this.message = message || '';
            this.type = ['success', 'error', 'info'].includes(type) ? type : 'success';
            this.show = true;
            if (this.timer) clearTimeout(this.timer);
            this.timer = setTimeout(() => { this.show = false; }, 4000);
        }
    }"
    x-on:show-toast.window="trigger($event.detail)"
    x-show="show"
    x-cloak
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-200 transform"
    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 scale-95"
    :class="styles[type].bg"
    class="fixed bottom-6 right-6 z-50 border-4 border-black dark:border-zinc-700 p-4 rounded-xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] text-black flex items-center gap-3 max-w-md"
>
    <div :class="styles[type].iconBg" class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-black shrink-0">
        <span x-show="type === 'success'">✓</span>
        <span x-show="type === 'error'" class="text-sm">✕</span>
        <span x-show="type === 'info'" class="text-sm">i</span>
    </div>
    <p class="text-xs sm:text-sm font-black text-black leading-snug">
        <span x-text="message"></span>
    </p>
    <button type="button" @click="show = false" class="ml-auto text-black hover:bg-black/10 p-1 rounded font-black text-xs cursor-pointer transition-colors">✕</button>
</div>
