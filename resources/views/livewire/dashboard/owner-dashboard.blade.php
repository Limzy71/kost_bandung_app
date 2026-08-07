<div 
    x-data 
    x-init="window.scrollTo({ top: 0, behavior: 'auto' })"
    @scroll-to-list.window="document.getElementById('property-list-section')?.scrollIntoView({ behavior: 'smooth', block: 'start' })"
    class="min-h-screen bg-[#f8f9fa] dark:bg-zinc-950 bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] dark:bg-[linear-gradient(to_right,#27272a_1px,transparent_1px),linear-gradient(to_bottom,#27272a_1px,transparent_1px)] bg-[size:24px_24px]"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-8">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 bg-white dark:bg-zinc-900 p-6 md:p-8 border-4 border-black dark:border-zinc-700 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] rounded-xl">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 bg-yellow-300 text-black border-2 border-black dark:border-zinc-700 font-extrabold text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                        Portal Pemilik
                    </span>
                    @if($owner->isIdentityVerified())
                        <span class="px-3 py-1 bg-emerald-300 text-black border-2 border-black dark:border-zinc-700 font-extrabold text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1">
                            <x-icon name="lucide-badge-check" class="w-3.5 h-3.5 stroke-[2.5]" />
                            <span>Identitas Terverifikasi</span>
                        </span>
                    @endif
                </div>
                <h1 class="text-3xl md:text-5xl font-black text-black dark:text-white tracking-tight uppercase">
                    Dashboard Pemilik
                </h1>
                <p class="text-zinc-700 dark:text-white text-sm md:text-base font-bold">
                    Selamat datang kembali, <span class="bg-yellow-200 dark:bg-yellow-400 border-b-2 border-black px-1 text-black font-extrabold" title="{{ $owner->name }}">{{ Str::limit(trim($owner->name), 30) }}</span>! Kelola iklan & ketersediaan kost Anda.
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <x-brutal-button :href="route('dashboard.kost.create')" class="hover:-translate-x-0.5 hover:-translate-y-0.5 group">
                    <x-icon name="lucide-plus" class="w-5 h-5 text-black stroke-[2.5] group-hover:rotate-90 transition-transform duration-300" />
                    <span>Tambah Kost Baru</span>
                </x-brutal-button>
            </div>
        </div>

        <!-- Verifikasi KTP Akun -->
        @if (! $owner->isIdentityVerified())
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 p-4 rounded-xl shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] dark:shadow-[5px_5px_0px_0px_rgba(255,255,255,0.25)]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-cyan-300 border-2 border-black dark:border-zinc-700 flex items-center justify-center shrink-0">
                        <x-icon name="lucide-id-card" class="w-5 h-5 text-black stroke-[2.5]" />
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase text-black dark:text-white">
                            @if ($owner->identity_verification_status === 'pending')
                                Menunggu Verifikasi KTP
                            @elseif ($owner->identity_verification_status === 'rejected')
                                Dokumen KTP Ditolak
                            @else
                                Verifikasi KTP untuk Badge "Terverifikasi"
                            @endif
                        </p>
                        <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400">
                            @if ($owner->identity_verification_status === 'rejected' && $owner->identity_rejection_note)
                                Alasan: {{ $owner->identity_rejection_note }}
                            @elseif ($owner->identity_verification_status === 'pending')
                                Dokumen sedang ditinjau tim admin.
                            @else
                                Unggah KTP sekali di halaman Profil — berlaku untuk semua kost Anda.
                            @endif
                        </p>
                    </div>
                </div>
                <a href="{{ route('profile.show') }}"
                    class="inline-flex items-center justify-center gap-1.5 bg-yellow-400 hover:bg-yellow-300 text-black border-3 border-black dark:border-zinc-700 font-black text-xs uppercase px-5 py-2.5 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg shrink-0">
                    <x-icon name="lucide-arrow-right" class="w-4 h-4 stroke-[2.5]" />
                    @if ($owner->identity_verification_status === 'rejected')
                        Unggah Ulang KTP
                    @else
                        Kelola di Profil
                    @endif
                </a>
            </div>
        @endif

        <!-- Floating Auto-Dismiss Toast Notification -->
        <div 
            x-data="{
                show: false,
                message: '',
                timer: null,
                trigger(msg) {
                    this.message = msg;
                    this.show = true;
                    if (this.timer) clearTimeout(this.timer);
                    this.timer = setTimeout(() => {
                        this.show = false;
                    }, 3000);
                }
            }"
            x-on:show-toast.window="trigger($event.detail.message)"
            x-show="show" 
            x-cloak
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            class="fixed bottom-6 right-6 z-50 bg-lime-300 border-3 border-black dark:border-zinc-700 p-4 rounded-xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] text-black flex items-center gap-3 max-w-md"
        >
            <div class="w-7 h-7 rounded-full bg-black text-lime-300 flex items-center justify-center text-xs font-black shrink-0">
                ✓
            </div>
            <p class="text-xs font-bold text-black leading-relaxed">
                <span x-text="message"></span>
            </p>
            <button type="button" @click="show = false" class="ml-auto text-black hover:bg-black/10 p-1 rounded font-black text-xs cursor-pointer transition-colors">✕</button>
        </div>

        <!-- Delete Confirmation Modal -->
        <div 
            x-data="{ open: false }"
            @delete-modal-opened.window="open = true"
            @delete-modal-closed.window="open = false"
            x-show="open"
            x-cloak
            x-transition.opacity.duration.200ms
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
        >
            <!-- Backdrop -->
            <div 
                class="absolute inset-0 bg-black/70"
                @click="open = false; $wire.closeDeleteModal()"
            ></div>

            <!-- Modal Card -->
            <div 
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-md bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] rounded-2xl p-6"
            >
                    <div class="flex items-start justify-between gap-4">
                        <div class="w-12 h-12 bg-rose-500 border-2 border-black dark:border-zinc-700 rounded-lg flex items-center justify-center text-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] shrink-0">
                            <x-icon name="lucide-trash-2" class="w-6 h-6 stroke-[2.5]" />
                        </div>
                        <button 
                            type="button"
                            @click="open = false; $wire.closeDeleteModal()"
                            class="text-black dark:text-white hover:bg-black/10 p-1.5 rounded font-black cursor-pointer transition-colors"
                        >
                            <x-icon name="lucide-x" class="w-4 h-4 stroke-[2.5]" />
                        </button>
                    </div>

                    <h3 class="text-xl font-black text-black dark:text-white uppercase tracking-tight mt-4">Hapus Kost Permanen?</h3>
                    <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 mt-2 leading-relaxed">
                        Anda akan menghapus kost 
                        <span class="bg-rose-100 dark:bg-rose-950/40 border-b-2 border-rose-400 px-1 font-black">"{{ $deleteTargetName }}"</span>.
                    </p>

                    <div class="mt-4 border-2 border-black dark:border-zinc-700 bg-rose-50 dark:bg-rose-950/40 p-3 rounded-lg flex items-start gap-2">
                        <x-icon name="lucide-triangle-alert" class="w-4 h-4 text-rose-700 dark:text-rose-400 stroke-[2.5] shrink-0 mt-0.5" />
                        <p class="text-[11px] font-black uppercase text-rose-700 dark:text-rose-400 leading-relaxed">
                            Tindakan ini PERMANEN. Seluruh foto, harga sewa, dan pesan masuk terkait kost ini akan dihapus dari sistem dan TIDAK dapat dipulihkan.
                        </p>
                    </div>

                    <div class="mt-4" x-data="{ showConfirmError: false }">
                        <label for="delete-confirm-text" class="text-[10px] font-black uppercase text-zinc-500 dark:text-zinc-400 tracking-wider">Ketik "HAPUS" untuk mengonfirmasi</label>
                        <input 
                            id="delete-confirm-text"
                            type="text"
                            wire:model.live="deleteConfirmText"
                            @input="showConfirmError = false"
                            placeholder="HAPUS"
                            class="mt-1 w-full bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 rounded-lg px-3 py-2.5 text-xs font-black uppercase text-black dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:focus:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]"
                        >
                        <p x-show="showConfirmError" x-transition class="text-[11px] font-black text-rose-600 dark:text-rose-400 mt-1.5 flex items-center gap-1">
                            <x-icon name="lucide-circle-alert" class="w-3.5 h-3.5 stroke-[2.5] shrink-0" />
                            Anda wajib mengetik HAPUS untuk melanjutkan.
                        </p>
                        @error('deleteConfirmText')
                            <p class="text-[11px] font-black text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                        @enderror

                        <div class="flex items-center gap-2 mt-6">
                            <button 
                                type="button"
                                @click="open = false; $wire.closeDeleteModal()"
                                class="flex-1 h-10 px-3 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-800 text-black dark:text-white border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all duration-150 rounded-lg cursor-pointer"
                            >
                                Batal
                            </button>
                            <button 
                                type="button"
                                @click="
                                    if ($wire.deleteConfirmText.trim().toUpperCase() !== 'HAPUS') {
                                        showConfirmError = true;
                                    } else {
                                        showConfirmError = false;
                                        $wire.deleteKost();
                                    }
                                "
                                wire:loading.attr="disabled"
                                class="flex-1 h-10 px-3 bg-rose-500 hover:bg-rose-400 text-white border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all duration-150 rounded-lg cursor-pointer"
                            >
                                <span wire:loading.remove wire:target="deleteKost">Hapus Permanen</span>
                                <span wire:loading.inline-flex wire:target="deleteKost" class="items-center gap-1.5 whitespace-nowrap">
                                    <x-icon name="lucide-loader-circle" class="animate-spin h-4 w-4 text-white shrink-0" />
                                    <span>Menghapus...</span>
                                </span>
                            </button>
                        </div>
                    </div>
            </div>
        </div>

        <!-- Quick Stats Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: Total Properti -->
            <div class="bg-cyan-300 border-3 border-black dark:border-zinc-700 p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] rounded-xl relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wider text-black">Total Properti</p>
                        <h3 class="text-4xl font-black text-black mt-2 tracking-tighter">{{ $totalProperti }}</h3>
                        <p class="text-xs font-bold text-black/80 mt-1">Kost terdaftar dalam sistem</p>
                    </div>
                    <div class="w-14 h-14 rounded-lg bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 flex items-center justify-center text-black dark:text-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                        <x-icon name="lucide-building-2" class="w-7 h-7 stroke-[2]" />
                    </div>
                </div>
            </div>

            <!-- Card 2: Status Kamar / Properti Siap Huni -->
            <div class="bg-lime-300 border-3 border-black dark:border-zinc-700 p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] rounded-xl relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wider text-black">Ketersediaan Kamar</p>
                        <h3 class="text-4xl font-black text-black mt-2 tracking-tighter">{{ $totalKamarTersedia }} <span class="text-sm font-bold text-black/70">/ {{ $totalProperti }} Kost</span></h3>
                        <span class="text-xs font-black text-black dark:text-white bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 px-2.5 py-0.5 rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-block mt-2 uppercase">Status Siap Huni</span>
                    </div>
                    <div class="w-14 h-14 rounded-lg bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 flex items-center justify-center text-black dark:text-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                        <x-icon name="lucide-circle-check" class="w-7 h-7 stroke-[2]" />
                    </div>
                </div>
            </div>

            <!-- Card 3: Pesan Masuk / Chat -->
            <div class="bg-pink-300 border-3 border-black dark:border-zinc-700 p-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] rounded-xl relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black uppercase tracking-wider text-black">Pesan Masuk</p>
                        <h3 class="text-4xl font-black text-black mt-2 tracking-tighter">{{ $pesanMasuk }}</h3>
                        <p class="text-xs font-bold text-black/80 mt-1">Chat masuk dari pencari kost</p>
                    </div>
                    <div class="w-14 h-14 rounded-lg bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 flex items-center justify-center text-black dark:text-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                        <x-icon name="lucide-message-square" class="w-7 h-7 stroke-[2]" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Section List Properti -->
        <div id="property-list-section" class="space-y-6 scroll-mt-20">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 p-5 rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)]">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-black dark:text-white uppercase tracking-tight flex items-center gap-2">
                        <x-icon name="lucide-building-2" class="w-6 h-6 text-black dark:text-white stroke-[2.5]" />
                        <span>Daftar Properti Kost</span>
                    </h2>
                    <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 mt-0.5">Kelola status ketersediaan & informasi properti kost Anda.</p>
                </div>

                <!-- Search Input (Direct live search with clear button for Owner Dashboard) -->
                <div class="relative w-full sm:w-80" x-data="{ query: @entangle('search') }">
                    <input 
                        x-ref="searchInput"
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Cari nama atau lokasi..." 
                        class="w-full bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-xl pl-10 pr-10 py-2.5 text-xs font-black uppercase text-black dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:focus:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] transition-all shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]"
                    >
                    <x-icon name="lucide-search" class="w-5 h-5 text-black dark:text-white absolute left-3 top-2.5 pointer-events-none stroke-[2.5]" />

                    <!-- Clear Search Input ✕ Button -->
                    <template x-if="query || ($refs.searchInput && $refs.searchInput.value)">
                        <button 
                            type="button" 
                            @click="$refs.searchInput.value = ''; $refs.searchInput.dispatchEvent(new Event('input')); $wire.resetSearch()"
                            class="absolute right-2.5 top-2.5 w-6 h-6 bg-rose-400 hover:bg-rose-300 border-2 border-black dark:border-zinc-700 rounded text-black font-black text-xs shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all flex items-center justify-center cursor-pointer"
                            title="Hapus kata kunci pencarian"
                        >
                            ✕
                        </button>
                    </template>
                </div>
            </div>

            <!-- Grid Card Properties -->
            @if($kosts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($kosts as $kost)
                        <div class="bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-xl overflow-hidden shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] dark:shadow-[5px_5px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-y-1 hover:shadow-[7px_7px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[7px_7px_0px_0px_rgba(255,255,255,0.25)] transition-[transform,box-shadow] duration-300 ease-out will-change-transform flex flex-col justify-between group">
                            <div>
                                <!-- Image Header -->
                                <div class="aspect-[4/3] bg-zinc-200 dark:bg-zinc-800 relative overflow-hidden border-b-3 border-black dark:border-zinc-700">
                                    @if($kost->primaryImage)
                                        <img src="{{ Str::startsWith($kost->primaryImage->image_path, 'http') ? $kost->primaryImage->image_path : Storage::url($kost->primaryImage->image_path) }}" alt="{{ $kost->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-out">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-yellow-100 dark:bg-yellow-950/40 text-black dark:text-white">
                                            <x-icon name="lucide-image" class="w-12 h-12 stroke-[2]" />
                                        </div>
                                    @endif

                                    <!-- Top Left Badges -->
                                    <div class="absolute top-3 left-3 flex flex-col gap-1.5 pointer-events-none">
                                        <span class="px-2.5 py-1 bg-pink-400 text-black border-2 border-black dark:border-zinc-700 text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] tracking-wider">
                                            {{ $kost->gender_type }}
                                        </span>
                                        @if($kost->boosted_at)
                                            <span class="px-2.5 py-1 bg-yellow-400 text-black border-2 border-black dark:border-zinc-700 text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] tracking-wider flex items-center gap-1">
                                                <x-icon name="lucide-zap" fill="#FBBF24" stroke-width="0.8" class="w-3.5 h-3.5 shrink-0" />
                                                <span>Super Boost</span>
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Top Right Status Badges -->
                                    <div class="absolute top-3 right-3 flex flex-col items-end gap-1.5">
                                        @if($kost->status === 'pending')
                                            <span class="px-3 py-1 bg-amber-300 text-black border-2 border-black dark:border-zinc-700 text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1.5 animate-pulse">
                                                <span class="relative flex h-2 w-2 shrink-0">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-600 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-700"></span>
                                                </span>
                                                <x-icon name="lucide-clock" class="w-3.5 h-3.5 text-black stroke-[2.5] shrink-0" />
                                                <span>Menunggu Review</span>
                                            </span>
                                        @elseif($kost->status === 'rejected')
                                            <span class="px-3 py-1 bg-rose-400 text-black border-2 border-black dark:border-zinc-700 text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                                                ✕ Ditolak Admin
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-emerald-300 text-black border-2 border-black dark:border-zinc-700 text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                                                ✓ Tayang Publik
                                            </span>
                                        @endif

                                        @if($kost->is_available)
                                            <span class="px-2.5 py-0.5 bg-lime-400 text-black border-2 border-black dark:border-zinc-700 text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                                                Sisa {{ $kost->available_rooms }} Kamar
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 bg-rose-400 text-black border-2 border-black dark:border-zinc-700 text-[10px] font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                                                Penuh
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Card Content -->
                                <div class="p-5 space-y-4">
                                    <div>
                                        <h3 class="text-lg font-black text-black dark:text-white leading-snug line-clamp-1 hover:underline">
                                            <a href="{{ route('kost.show', $kost->slug) }}?from=dashboard">
                                                {{ $kost->name }}
                                            </a>
                                        </h3>
                                        <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 mt-1 line-clamp-1 inline-flex items-center gap-1">
                                            <x-icon name="lucide-map-pin" class="w-3.5 h-3.5 text-zinc-700 dark:text-zinc-300 shrink-0 stroke-[2.5]" />
                                            <span>{{ $kost->address }}, {{ $kost->district }}</span>
                                        </p>
                                    </div>

                                    <!-- Verifikasi Dokumen -->
                                    <div class="pt-3 border-t-2 border-black dark:border-zinc-700 space-y-2">
                                        <p class="text-[10px] font-black uppercase text-zinc-500 dark:text-zinc-400 flex items-center gap-1.5">
                                            <x-icon name="lucide-shield-check" class="w-3.5 h-3.5 text-black dark:text-white stroke-[2.5]" />
                                            <span>Verifikasi Dokumen</span>
                                        </p>
                                        <div class="flex flex-wrap items-center gap-1.5">
                                            @php
                                                $ownStatus = $kost->ownership_verification_status;
                                                $ownColor = $ownStatus === 'verified' ? 'bg-emerald-400' : ($ownStatus === 'rejected' ? 'bg-rose-400' : ($ownStatus === 'pending' ? 'bg-amber-300' : 'bg-zinc-200'));
                                            @endphp
                                            <span class="px-2 py-0.5 {{ $ownColor }} text-black border-2 border-black dark:border-zinc-700 text-[10px] font-black uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1">
                                                <x-icon name="lucide-file-text" class="w-3 h-3 stroke-[2.5]" />
                                                {{ $kost->ownershipStatusLabel() }}
                                            </span>
                                        </div>
                                        @if ($ownStatus === 'rejected' && $kost->ownership_rejection_note)
                                            <p class="text-[10px] font-bold text-rose-600 dark:text-rose-400 leading-snug flex items-start gap-1">
                                                <x-icon name="lucide-triangle-alert" class="w-3 h-3 stroke-[2.5] shrink-0 mt-0.5" />
                                                Kepemilikan: {{ $kost->ownership_rejection_note }}
                                            </p>
                                        @endif
                                        @if ($ownStatus !== 'verified')
                                            @php
                                                $ownCta = $ownStatus === 'rejected'
                                                    ? 'Unggah Ulang Dokumen'
                                                    : ($ownStatus === 'pending'
                                                        ? 'Lihat Status Verifikasi'
                                                        : 'Verifikasi untuk Badge Kepercayaan');
                                            @endphp
                                            <a href="{{ route('dashboard.kost.edit', $kost->slug) }}"
                                                class="inline-flex items-center gap-1 text-[10px] font-black uppercase text-black bg-cyan-300 hover:bg-cyan-200 border-2 border-black dark:border-zinc-700 px-2 py-1 rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all">
                                                <x-icon name="lucide-file-up" class="w-3 h-3 stroke-[2.5]" />
                                                {{ $ownCta }}
                                            </a>
                                        @endif
                                    </div>

                                    <!-- Price & Facilities -->
                                    <div class="pt-3 border-t-2 border-black dark:border-zinc-700 flex items-center justify-between">
                                        <div>
                                            <p class="text-[10px] font-black uppercase text-zinc-500 dark:text-zinc-400">Harga Sewa</p>
                                            <span class="bg-yellow-300 border-2 border-black dark:border-zinc-700 font-black text-black px-2.5 py-0.5 rounded text-sm shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-block mt-0.5">
                                                Rp {{ number_format($kost->price_monthly, 0, ',', '.') }}<span class="text-[10px] font-bold">{{ \App\Models\Kost::rentPeriodUnit($kost->rent_period) }}</span>
                                            </span>
                                        </div>

                                        <div class="text-right">
                                            <p class="text-[10px] font-black uppercase text-zinc-500 dark:text-zinc-400">Obrolan</p>
                                            <span class="bg-cyan-300 border-2 border-black dark:border-zinc-700 font-black text-black px-2.5 py-0.5 rounded text-xs shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-block mt-0.5">
                                                {{ $kost->conversations_count }} Obrolan
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer Actions -->
                            <div class="px-5 py-4 bg-zinc-100 dark:bg-zinc-800 border-t-3 border-black dark:border-zinc-700 flex flex-col gap-2 shrink-0">
                                <!-- Toggle Availability Button -->
                                <button 
                                    wire:click="toggleAvailability({{ $kost->id }})" 
                                    wire:loading.attr="disabled"
                                    class="w-full h-10 px-3.5 border-2 border-black dark:border-zinc-700 text-xs font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] hover:brightness-110 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all duration-150 rounded-lg cursor-pointer flex items-center justify-center whitespace-nowrap {{ $kost->is_available ? 'bg-rose-400 hover:bg-rose-300 text-black' : 'bg-lime-400 hover:bg-lime-300 text-black' }}"
                                >
                                    <span wire:loading.remove wire:target="toggleAvailability({{ $kost->id }})" class="inline-flex items-center gap-1.5 whitespace-nowrap">
                                        @if($kost->is_available)
                                            <x-icon name="lucide-ban" class="w-4 h-4 stroke-[2.5] shrink-0" />
                                            <span>Set Status Penuh</span>
                                        @else
                                            <x-icon name="lucide-circle-check" class="w-4 h-4 stroke-[2.5] shrink-0" />
                                            <span>Set Status Tersedia</span>
                                        @endif
                                    </span>
                                    <span wire:loading.inline-flex wire:target="toggleAvailability({{ $kost->id }})" class="items-center gap-1.5 whitespace-nowrap">
                                        <x-icon name="lucide-loader-circle" class="animate-spin h-4 w-4 text-black shrink-0" />
                                        <span>Memproses...</span>
                                    </span>
                                </button>

                                <!-- Edit & Detail Links Group -->
                                <div class="flex items-center gap-2 w-full">
                                    <!-- Edit Link Button -->
                                    <a 
                                        href="{{ route('dashboard.kost.edit', $kost->slug) }}" 
                                        class="h-10 px-2 bg-cyan-400 hover:bg-cyan-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] hover:brightness-110 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all duration-150 rounded-lg inline-flex items-center justify-center gap-1 whitespace-nowrap flex-1"
                                    >
                                        <span>Edit</span>
                                        <x-icon name="lucide-pencil" class="w-3.5 h-3.5 stroke-[3]" />
                                    </a>

                                    <!-- Detail Link Button -->
                                    <a 
                                        href="{{ route('kost.show', $kost->slug) }}?from=dashboard" 
                                        class="h-10 px-2 bg-orange-400 hover:bg-orange-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] hover:brightness-110 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all duration-150 rounded-lg inline-flex items-center justify-center gap-1 whitespace-nowrap flex-1"
                                    >
                                        <span>Lihat</span>
                                        <x-icon name="lucide-arrow-right" class="w-3.5 h-3.5 stroke-[3]" />
                                    </a>
                                </div>

                                <!-- Delete Button -->
                                <button 
                                    wire:click="openDeleteModal({{ $kost->id }})" 
                                    wire:loading.attr="disabled"
                                    class="w-full h-10 px-3.5 bg-rose-500 hover:bg-rose-400 text-white border-2 border-black dark:border-zinc-700 text-xs font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all duration-150 rounded-lg cursor-pointer flex items-center justify-center gap-1.5"
                                >
                                    <span wire:loading.remove wire:target="openDeleteModal({{ $kost->id }})" class="inline-flex items-center gap-1.5 whitespace-nowrap">
                                        <x-icon name="lucide-trash-2" class="w-4 h-4 stroke-[2.5] shrink-0" />
                                        <span>Hapus Kost</span>
                                    </span>
                                    <span wire:loading.inline-flex wire:target="openDeleteModal({{ $kost->id }})" class="items-center gap-1.5 whitespace-nowrap">
                                        <x-icon name="lucide-loader-circle" class="animate-spin h-4 w-4 text-white shrink-0" />
                                        <span>Memproses...</span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $kosts->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-yellow-100 dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-xl p-12 text-center shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] space-y-4">
                    <div class="w-16 h-16 bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 rounded-lg flex items-center justify-center mx-auto text-black dark:text-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                        <x-icon name="lucide-building-2" class="w-8 h-8 stroke-[2]" />
                    </div>
                    <div>
                        <h3 class="text-2xl font-black text-black dark:text-white uppercase">Belum Ada Properti Kost</h3>
                        <p class="text-sm font-bold text-zinc-700 dark:text-zinc-300 max-w-md mx-auto mt-1">
                            @if($search)
                                Tidak ada properti kost yang cocok dengan kata kunci "{{ $search }}".
                            @else
                                Anda belum memiliki properti kost yang terdaftar. Mulai tambahkan properti pertama Anda untuk menarik calon penyewa di Bandung.
                            @endif
                        </p>
                    </div>
                    @if($search)
                        <button wire:click="resetSearch()" class="px-5 py-2.5 bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800 text-black dark:text-white font-black text-xs uppercase border-2 border-black dark:border-zinc-700 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded">
                            Reset Pencarian
                        </button>
                    @else
                        <x-brutal-button :href="route('dashboard.kost.create')" class="hover:-translate-x-0.5 hover:-translate-y-0.5">
                            <x-icon name="lucide-plus" class="w-4 h-4 stroke-[3]" />
                            <span>Tambah Properti Pertama</span>
                        </x-brutal-button>
                    @endif
                </div>
            @endif
        </div>

    </div>
</div>
