<div
    class="min-h-screen bg-[#f8f9fa] bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] bg-[size:24px_24px]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-8">

        <!-- Top Header & Back Button -->
        <div>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center gap-2 text-xs font-black uppercase text-black bg-white border-2 border-black px-3.5 py-2 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-yellow-300 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg mb-6 group">
                <x-icon name="lucide-arrow-left" class="w-4 h-4 text-black group-hover:-translate-x-1 transition-transform stroke-[3]" />
                <span>Kembali ke Dashboard</span>
            </a>

            <div
                class="bg-yellow-300 border-4 border-black p-6 md:p-8 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] rounded-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span
                        class="px-3 py-1 bg-black text-yellow-300 font-extrabold text-xs uppercase tracking-wider border border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        Form Pendaftaran
                    </span>
                    <h1 class="text-3xl md:text-4xl font-black text-black tracking-tight uppercase mt-2">
                        Tambah Properti Kost Baru
                    </h1>
                    <p class="text-sm font-bold text-black/80 mt-1">
                        Isi detail properti kost Anda dengan lengkap untuk menarik minat pencari kost di Kota Bandung.
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Start -->
        <form wire:submit.prevent="save" x-data="{ formIsOutOfBounds: false }" @bounds-update.window="formIsOutOfBounds = $event.detail" class="space-y-8">

            @include('livewire.dashboard.partials.kost-form', ['isEdit' => false])

            <!-- Submit & Action Buttons -->
            <div class="space-y-4 pt-4 border-t-3 border-black">
                @if ($errors->any())
                    <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-rose-100 border-3 border-black p-4 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex items-center justify-between gap-3 text-rose-900 font-black text-xs">
                        <div class="flex items-center gap-2">
                            <x-icon name="lucide-triangle-alert" class="w-5 h-5 text-rose-600 shrink-0 stroke-[2.5]" />
                            <span>Mohon lengkapi seluruh kolom wajib bertanda merah sebelum menyimpan properti.</span>
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
                        :class="formIsOutOfBounds ? 'opacity-50 cursor-not-allowed bg-zinc-300' : 'bg-yellow-400 hover:bg-yellow-300 active:translate-x-1 active:translate-y-1 active:shadow-none'"
                        class="min-w-[220px] justify-center px-8 py-3.5 text-black border-3 border-black font-black text-sm uppercase shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] transition-all rounded inline-flex items-center gap-2">
                        <span wire:loading.remove wire:target="save">Simpan Properti Kost</span>
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

        </form>

    </div>
</div>
