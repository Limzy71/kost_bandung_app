<div class="space-y-4 pt-4 border-t-3 border-black">
    @if ($errors->any())
        <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-rose-100 border-3 border-black p-4 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex items-center justify-between gap-3 text-rose-900 font-black text-xs">
            <div class="flex items-center gap-2">
                <x-icon name="lucide-triangle-alert" class="w-5 h-5 text-rose-600 shrink-0 stroke-[2.5]" />
                <span>{{ $isEdit ? 'Gagal menyimpan perubahan. Silakan periksa kembali bagian berpita merah di atas.' : 'Gagal menyimpan properti. Silakan periksa kembali bagian berpita merah di atas.' }}</span>
            </div>
            <button type="button" @click="show = false" class="text-black hover:bg-rose-200 px-2 py-0.5 rounded border border-black font-black text-xs">✕</button>
        </div>
    @endif

    <div class="flex items-center justify-end gap-4">
        <a href="{{ route('dashboard') }}"
            class="px-6 py-3 bg-white hover:bg-zinc-100 text-black border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded">
            Batal
        </a>

        <button type="submit" wire:loading.attr="disabled" wire:target="save" x-bind:disabled="formIsOutOfBounds"
            :class="formIsOutOfBounds ? 'opacity-50 cursor-not-allowed bg-zinc-300' : '{{ $isEdit ? 'bg-cyan-400 hover:bg-cyan-300' : 'bg-yellow-400 hover:bg-yellow-300' }} active:translate-x-1 active:translate-y-1 active:shadow-none'"
            class="min-w-[220px] justify-center px-8 py-3.5 text-black border-3 border-black font-black text-sm uppercase shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] transition-all rounded inline-flex items-center gap-2">
            <span wire:loading.remove wire:target="save">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Properti Kost' }}</span>
            <span wire:loading.flex wire:target="save" class="items-center gap-2">
                <svg class="animate-spin h-5 w-5 text-black shrink-0" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Menyimpan Data...
            </span>
        </button>
    </div>
</div>
