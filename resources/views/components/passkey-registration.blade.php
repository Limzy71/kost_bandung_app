@assets
@vite('resources/js/passkeys.js')
@endassets

<div
    x-data="{
        supported: false,
        showForm: false,
        name: '',
        loading: false,
        error: null,
        updateSupport() {
            this.supported = Boolean(window.Passkeys?.isSupported());
        },
        getDefaultPasskeyName() {
            const ua = navigator.userAgent;

            const browser = [
                { pattern: /Edg|Edge/, name: 'Edge' },
                { pattern: /OPR|Opera|OPiOS/, name: 'Opera' },
                { pattern: /Firefox|FxiOS/, name: 'Firefox' },
                { pattern: /Chrome|CriOS/, name: 'Chrome' },
                { pattern: /Safari/, name: 'Safari' },
            ].find(({ pattern }) => pattern.test(ua))?.name;

            const os = [
                { pattern: /iPhone/, name: 'iPhone' },
                { pattern: /iPad|Macintosh(?=.*Mobile)/, name: 'iPad' },
                { pattern: /Android/, name: 'Android' },
                { pattern: /Mac/, name: 'Mac' },
                { pattern: /Windows/, name: 'Windows' },
            ].find(({ pattern }) => pattern.test(ua))?.name;

            return [browser, os].filter(Boolean).join(' on ') || '';
        },
        init() {
            this.name = this.getDefaultPasskeyName();
            this.updateSupport();

            window.addEventListener('passkeys:ready', () => this.updateSupport(), { once: true });
        },
        async register() {
            if (!this.name.trim()) return;

            this.loading = true;
            this.error = null;

            try {
                await window.Passkeys.register({ name: this.name });
                this.name = '';
                this.showForm = false;
                await $wire.loadPasskeys();
            } catch (e) {
                if (e.constructor?.name !== 'UserCancelledError') {
                    this.error = e.message;
                }
            } finally {
                this.loading = false;
            }
        },
        cancel() {
            this.showForm = false;
            this.name = '';
            this.error = null;
        },
    }"
>
    <template x-if="!supported">
        <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400">Passkey tidak didukung pada peramban ini.</p>
    </template>

    <template x-if="supported && !showForm">
        <div>
            <button
                type="button"
                x-on:click="showForm = true"
                class="bg-yellow-400 hover:bg-yellow-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase px-4 py-2.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer inline-flex items-center gap-1.5"
            >
                <x-icon name="lucide-plus" class="w-4 h-4 text-black stroke-[2.5]" />
                <span>Tambah Passkey</span>
            </button>
        </div>
    </template>

    <template x-if="supported && showForm">
        <div class="space-y-4 rounded-xl border-3 border-black dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/60 p-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]">
            <div>
                <label class="block text-xs font-black uppercase text-black dark:text-white mb-1.5">Nama Passkey</label>
                <input
                    type="text"
                    x-model="name"
                    placeholder="Contoh: MacBook Pro, iPhone"
                    x-on:keydown.enter.prevent="register()"
                    x-ref="passkeyNameInput"
                    x-init="$nextTick(() => $refs.passkeyNameInput?.focus())"
                    class="w-full px-4 py-2.5 text-sm bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 rounded-lg text-black dark:text-white font-bold placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] transition-all"
                />
                <p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 mt-1">Beri nama passkey ini agar mudah dikenali nantinya.</p>
            </div>

            <p x-show="error" x-text="error" x-cloak class="text-xs font-black text-rose-500 uppercase"></p>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    x-on:click="register()"
                    x-bind:disabled="loading || !name.trim()"
                    class="bg-lime-400 hover:bg-lime-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase px-4 py-2 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer disabled:opacity-50"
                >
                    <span x-show="!loading">Daftarkan Passkey</span>
                    <span x-show="loading" x-cloak>Mendaftarkan...</span>
                </button>
                <button
                    type="button"
                    x-on:click="cancel()"
                    class="bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 text-black dark:text-white border-2 border-black dark:border-zinc-700 font-black text-xs uppercase px-4 py-2 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer"
                >
                    Batal
                </button>
            </div>
        </div>
    </template>
</div>
