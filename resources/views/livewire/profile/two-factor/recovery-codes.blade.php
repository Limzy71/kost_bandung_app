<div
    class="p-6 bg-zinc-50 dark:bg-zinc-800/60 border-3 border-black dark:border-zinc-700 rounded-xl space-y-4"
    wire:cloak
    x-data="{ showRecoveryCodes: false }"
>
    <div class="space-y-1">
        <div class="flex items-center gap-2">
            <x-icon name="lucide-lock" class="w-4 h-4 text-black dark:text-white stroke-[2.5]" />
            <h4 class="text-base font-black text-black dark:text-white uppercase tracking-tight">Kode Pemulihan 2FA</h4>
        </div>
        <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 leading-relaxed">
            Kode pemulihan digunakan untuk masuk jika Anda kehilangan akses ke perangkat 2FA. Simpan kode ini di tempat yang aman.
        </p>
    </div>

    <div class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
            <button
                type="button"
                x-show="!showRecoveryCodes"
                @click="showRecoveryCodes = true"
                class="bg-yellow-400 hover:bg-yellow-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase px-4 py-2.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer inline-flex items-center gap-1.5 shrink-0"
            >
                <x-icon name="lucide-eye" class="w-4 h-4 stroke-[2.5]" />
                <span>Tampilkan Kode Pemulihan</span>
            </button>

            <button
                type="button"
                x-show="showRecoveryCodes"
                @click="showRecoveryCodes = false"
                class="bg-zinc-200 dark:bg-zinc-700 hover:bg-zinc-300 text-black dark:text-white border-2 border-black dark:border-zinc-700 font-black text-xs uppercase px-4 py-2.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer inline-flex items-center gap-1.5 shrink-0"
            >
                <x-icon name="lucide-eye-off" class="w-4 h-4 stroke-[2.5]" />
                <span>Sembunyikan Kode Pemulihan</span>
            </button>

            @if (filled($recoveryCodes))
                <button
                    type="button"
                    x-show="showRecoveryCodes"
                    wire:click="regenerateRecoveryCodes"
                    class="bg-cyan-300 hover:bg-cyan-200 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase px-4 py-2.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer inline-flex items-center gap-1.5 shrink-0"
                >
                    <x-icon name="lucide-refresh-cw" class="w-4 h-4 text-black stroke-[2.5]" />
                    <span>Buat Ulang Kode</span>
                </button>
            @endif
        </div>

        <div
            x-show="showRecoveryCodes"
            x-transition
            class="space-y-3"
        >
            @error('recoveryCodes')
                <p class="text-xs font-black text-rose-500 uppercase">{{ $message }}</p>
            @enderror

            @if (filled($recoveryCodes))
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 p-4 bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 rounded-lg font-mono text-xs font-bold text-black dark:text-white">
                    @foreach($recoveryCodes as $code)
                        <div class="p-1.5 bg-zinc-100 dark:bg-zinc-800 rounded border border-zinc-300 dark:border-zinc-700 select-all text-center">
                            {{ $code }}
                        </div>
                    @endforeach
                </div>
                <p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 leading-normal">
                    Setiap kode pemulihan hanya dapat digunakan satu kali. Simpan kode ini baik-baik.
                </p>
            @endif
        </div>
    </div>
</div>
