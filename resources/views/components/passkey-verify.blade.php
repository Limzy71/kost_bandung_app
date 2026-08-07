@props([
    'optionsRoute' => 'passkey.login-options',
    'submitRoute' => 'passkey.login',
    'label' => __('Sign in with a passkey'),
    'loadingLabel' => __('Authenticating...'),
    'separator' => __('Or continue with email'),
])

@assets
@vite('resources/js/passkeys.js')
@endassets

<div
    x-data="{
        supported: false,
        loading: false,
        error: null,
        updateSupport() {
            this.supported = Boolean(window.Passkeys?.isSupported());
        },
        init() {
            this.updateSupport();

            window.addEventListener('passkeys:ready', () => this.updateSupport(), { once: true });
        },
        async verify() {
            this.loading = true;
            this.error = null;
            try {
                const response = await window.Passkeys.verify({
                    routes: {
                        options: '{{ route($optionsRoute) }}',
                        submit: '{{ route($submitRoute) }}',
                    },
                });
                Livewire.navigate(response.redirect || '/dashboard');
            } catch (e) {
                if (e.constructor?.name !== 'UserCancelledError') {
                    this.error = e.message;
                }
            } finally {
                this.loading = false;
            }
        },
    }"
>
    <template x-if="supported">
        <div>
            <div class="grid gap-2">
                <button
                    type="button"
                    class="w-full py-4 px-6 bg-white hover:bg-zinc-100 text-black border-4 border-black font-black text-sm uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] active:translate-x-1 active:translate-y-1 active:shadow-none transition-all duration-75 rounded-lg flex items-center justify-center gap-2 cursor-pointer dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-white dark:border-zinc-600 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)]"
                    x-on:click="verify()"
                    x-bind:disabled="loading"
                    x-bind:class="{'opacity-60 cursor-not-allowed': loading}"
                >
                    <x-icon name="lucide-fingerprint" class="w-5 h-5 shrink-0" />
                    <span x-show="!loading">{{ $label }}</span>
                    <span x-show="loading" x-cloak>{{ $loadingLabel }}</span>
                </button>
                <p x-show="error" x-text="error" x-cloak
                   class="text-xs font-bold text-center text-rose-600 dark:text-rose-400 mt-2 flex justify-center items-center gap-1">
                   <span class="font-black">✕</span> <span x-text="error"></span>
                </p>
            </div>

            <div class="my-7 border-t-4 border-dashed border-black dark:border-zinc-700 relative">
                <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-white dark:bg-zinc-900 px-3">
                    <span class="text-[10px] font-black text-black dark:text-zinc-400 uppercase tracking-widest bg-white dark:bg-zinc-900 px-2">
                        {{ $separator }}
                    </span>
                </div>
            </div>
        </div>
    </template>
</div>
