<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <x-stat-card label="Total Properti" :value="$stats['totalKosts']" hint="Kost terdaftar dalam sistem" icon="lucide-building-2" color="bg-cyan-300" />
    <x-stat-card label="Kost Tersedia" :value="$stats['availableKosts']" hint="Properti siap huni" icon="lucide-circle-check" color="bg-lime-300" />
    <x-stat-card label="Menunggu Moderasi" :value="$stats['pendingKosts']" hint="Pengajuan belum ditinjau admin" icon="lucide-hourglass" color="bg-yellow-300" />
    <x-stat-card label="Pesan Masuk" :value="$stats['pesanMasuk']" hint="Chat masuk yang belum dibaca" icon="lucide-message-circle" color="bg-pink-300" />
</div>

<!-- Verifikasi Identitas (KTP) -->
<div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] overflow-hidden" x-data="{ showDeleteIdentity: false }">
    <div class="bg-cyan-300 border-b-4 border-black dark:border-zinc-700 px-6 py-4 flex items-center gap-3">
        <div class="w-9 h-9 bg-black rounded flex items-center justify-center shrink-0">
            <x-icon name="lucide-id-card" class="w-5 h-5 text-cyan-300 stroke-[2.5]" />
        </div>
        <div>
            <h2 class="text-xl font-black text-black uppercase tracking-tight">Verifikasi Identitas (KTP)</h2>
            <p class="text-xs font-bold text-black">Unggah sekali, berlaku untuk semua kost Anda.</p>
        </div>
    </div>

    <div class="p-6 space-y-4">
        @php $idStatus = $user->identity_verification_status; @endphp

        @if ($user->isIdentityVerified())
            <div class="p-4 bg-emerald-100 border-2 border-black dark:border-zinc-700 rounded-xl flex items-center gap-3 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                <div class="w-10 h-10 rounded bg-emerald-400 border-2 border-black dark:border-zinc-700 flex items-center justify-center shrink-0">
                    <x-icon name="lucide-badge-check" class="w-5 h-5 text-black dark:text-white stroke-[2.5]" />
                </div>
                <div>
                    <p class="text-xs font-black text-black dark:text-white uppercase">Identitas Anda Telah Terverifikasi</p>
                    <p class="text-xs font-bold text-emerald-800">KTP telah disetujui tim KostBandung. Properti Anda berhak atas badge "Terverifikasi".</p>
                </div>
                <button type="button" @click="showDeleteIdentity = true"
                    class="ml-auto shrink-0 inline-flex items-center gap-1.5 bg-rose-500 hover:bg-rose-600 text-white border-2 border-black dark:border-zinc-700 font-black text-[10px] uppercase px-3 py-1.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all rounded cursor-pointer">
                    <x-icon name="lucide-trash-2" class="w-3.5 h-3.5 stroke-[2.5]" />
                    Hapus
                </button>
            </div>

            <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border-2 border-black dark:border-zinc-700 rounded-xl flex items-start gap-2">
                <x-icon name="lucide-shield-check" class="w-4 h-4 text-black dark:text-white stroke-[2.5] shrink-0 mt-0.5" />
                <p class="text-xs font-bold text-zinc-700 dark:text-zinc-300 leading-relaxed">
                    Dokumen KTP tersimpan rahasia, hanya admin yang dapat melihatnya, dan tidak pernah
                    ditampilkan ke publik (sesuai UU PDP). Anda dapat menghapusnya lalu mengunggah ulang jika keliru.
                </p>
            </div>
        @else
            @if ($idStatus === 'pending')
                <div class="p-4 bg-amber-100 border-2 border-black dark:border-zinc-700 rounded-xl flex items-center gap-3 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                    <div class="w-10 h-10 rounded bg-amber-400 border-2 border-black dark:border-zinc-700 flex items-center justify-center shrink-0">
                        <x-icon name="lucide-hourglass" class="w-5 h-5 text-black dark:text-white stroke-[2.5]" />
                    </div>
                    <div>
                        <p class="text-xs font-black text-black dark:text-white uppercase">Menunggu Verifikasi Admin</p>
                        <p class="text-xs font-bold text-amber-800">Dokumen KTP Anda sedang ditinjau oleh tim admin.</p>
                    </div>
                    <button type="button" @click="showDeleteIdentity = true"
                        class="ml-auto shrink-0 inline-flex items-center gap-1.5 bg-rose-500 hover:bg-rose-600 text-white border-2 border-black dark:border-zinc-700 font-black text-[10px] uppercase px-3 py-1.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all rounded cursor-pointer">
                        <x-icon name="lucide-trash-2" class="w-3.5 h-3.5 stroke-[2.5]" />
                        Hapus
                    </button>
                </div>
            @elseif ($idStatus === 'rejected' && $user->identity_rejection_note)
                <div class="p-4 bg-rose-100 border-2 border-black dark:border-zinc-700 rounded-xl flex items-start gap-3 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                    <div class="w-10 h-10 rounded bg-rose-400 border-2 border-black dark:border-zinc-700 flex items-center justify-center shrink-0">
                        <x-icon name="lucide-x-circle" class="w-5 h-5 text-black dark:text-white stroke-[2.5]" />
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-black text-black dark:text-white uppercase">Dokumen KTP Ditolak</p>
                        <p class="text-xs font-bold text-rose-800">Alasan: {{ $user->identity_rejection_note }}</p>
                        <p class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Silakan unggah ulang KTP yang jelas di bawah ini.</p>
                    </div>
                    <button type="button" @click="showDeleteIdentity = true"
                        class="ml-auto shrink-0 inline-flex items-center gap-1.5 bg-rose-500 hover:bg-rose-600 text-white border-2 border-black dark:border-zinc-700 font-black text-[10px] uppercase px-3 py-1.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all rounded cursor-pointer">
                        <x-icon name="lucide-trash-2" class="w-3.5 h-3.5 stroke-[2.5]" />
                        Hapus
                    </button>
                </div>
            @endif

            <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border-2 border-black dark:border-zinc-700 rounded-xl flex items-start gap-2">
                <x-icon name="lucide-shield-check" class="w-4 h-4 text-black dark:text-white stroke-[2.5] shrink-0 mt-0.5" />
                <p class="text-xs font-bold text-zinc-700 dark:text-zinc-300 leading-relaxed">
                    Unggah foto KTP untuk mendapatkan badge
                    <span class="font-black text-black dark:text-white">"Terverifikasi"</span>. Dokumen disimpan rahasia, hanya
                    admin yang dapat melihatnya, dan tidak pernah ditampilkan ke publik (sesuai UU PDP).
                </p>
            </div>

            <div class="space-y-3"
                x-data="{ uploading: false, progress: 0 }"
                x-on:livewire-upload-start="uploading = true; progress = 0"
                x-on:livewire-upload-finish="uploading = false; progress = 100"
                x-on:livewire-upload-error="uploading = false; progress = 0"
                x-on:livewire-upload-progress="progress = $event.detail.progress">
                <div class="relative border-3 border-dashed border-black dark:border-zinc-700 rounded-xl p-6 text-center bg-zinc-50 dark:bg-zinc-900 hover:bg-yellow-100/70 transition-all cursor-pointer shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                    <input type="file" wire:model="identity_doc" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    <div class="space-y-2 pointer-events-none">
                        <div class="w-11 h-11 rounded-lg bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 flex items-center justify-center mx-auto text-black dark:text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                            <x-icon name="lucide-id-card" class="w-5 h-5 stroke-[2]" />
                        </div>
                        <p class="text-xs font-black text-black dark:text-white uppercase">
                            {{ $idStatus === 'rejected' ? 'Unggah Ulang Foto KTP' : 'Klik atau seret foto KTP ke area ini' }}
                        </p>
                        <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400">Format: JPG, PNG, WEBP &middot; Maks 2MB</p>
                    </div>
                </div>

                @if ($identity_doc)
                    <div class="bg-lime-100 border-3 border-black dark:border-zinc-700 p-3 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] space-y-2 relative">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-[10px] font-black uppercase text-black dark:text-white flex items-center gap-2">
                                <x-icon name="lucide-file-check" class="w-3.5 h-3.5 stroke-[2.5]" />
                                Pratinjau KTP Terpilih
                            </p>
                            <button type="button" wire:click="$set('identity_doc', null)"
                                class="inline-flex items-center gap-1 bg-rose-500 hover:bg-rose-600 text-white border-2 border-black dark:border-zinc-700 font-black text-[10px] uppercase px-2 py-1 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none transition-all rounded cursor-pointer shrink-0">
                                <x-icon name="lucide-trash-2" class="w-3 h-3 stroke-[3]" />
                                Batal
                            </button>
                        </div>
                        <img src="{{ $identity_doc->temporaryUrl() }}" alt="Pratinjau KTP"
                            class="w-full max-h-52 object-contain rounded-lg border-2 border-black dark:border-zinc-700 bg-white dark:bg-zinc-900">
                    </div>
                @endif

                <div x-show="uploading" x-cloak class="space-y-1.5">
                    <div class="w-full bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 rounded-lg h-5 p-0.5 relative overflow-hidden">
                        <div class="bg-lime-400 border-r-2 border-black dark:border-zinc-700 h-full transition-all duration-300" :style="'width: ' + progress + '%'"></div>
                    </div>
                    <p class="text-[10px] font-black uppercase text-zinc-600 dark:text-zinc-400" x-text="'Mengunggah ' + progress + '%'"></p>
                </div>

                @error('identity_doc')
                    <p class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md inline-block">{{ $message }}</p>
                @enderror
            </div>
        @endif
    </div>

    <!-- Hapus Dokumen KTP: Konfirmasi Modal -->
    <div x-show="showDeleteIdentity" x-cloak x-transition.opacity.duration.150ms
        class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-black/70" @click="showDeleteIdentity = false"></div>
        <div x-show="showDeleteIdentity" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="relative w-full max-w-md bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] rounded-2xl p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="w-12 h-12 bg-rose-500 border-2 border-black dark:border-zinc-700 rounded-lg flex items-center justify-center text-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] shrink-0">
                    <x-icon name="lucide-trash-2" class="w-6 h-6 stroke-[2.5]" />
                </div>
                <button type="button" @click="showDeleteIdentity = false"
                    class="text-black dark:text-white hover:bg-black/10 p-1.5 rounded font-black cursor-pointer transition-colors">
                    <x-icon name="lucide-x" class="w-4 h-4 stroke-[2.5]" />
                </button>
            </div>

            <h3 class="text-xl font-black text-black dark:text-white uppercase tracking-tight mt-4">Hapus Dokumen KTP?</h3>
            <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 mt-2 leading-relaxed">
                @if ($user->isIdentityVerified())
                    Dokumen KTP Anda akan dihapus dari sistem dan status verifikasi akun dikembalikan ke
                    <span class="bg-rose-100 border-b-2 border-rose-400 px-1 font-black">"Belum Diverifikasi"</span>.
                @else
                    Data pengajuan KTP Anda akan dibatalkan dan dihapus dari sistem.
                @endif
                Anda dapat mengunggah ulang KTP kapan saja dari halaman ini.
            </p>

            @if ($user->isIdentityVerified())
                <div class="mt-4 border-2 border-black dark:border-zinc-700 bg-rose-50 p-3 rounded-lg flex items-start gap-2">
                    <x-icon name="lucide-triangle-alert" class="w-4 h-4 text-rose-700 stroke-[2.5] shrink-0 mt-0.5" />
                    <p class="text-[11px] font-black uppercase text-rose-700 leading-relaxed">
                        Badge "Terverifikasi" di kost Anda akan hilang hingga KTP baru disetujui admin.
                    </p>
                </div>
            @else
                <div class="mt-4 border-2 border-black dark:border-zinc-700 bg-amber-50 p-3 rounded-lg flex items-start gap-2">
                    <x-icon name="lucide-info" class="w-4 h-4 text-amber-700 stroke-[2.5] shrink-0 mt-0.5" />
                    <p class="text-[11px] font-black uppercase text-amber-700 leading-relaxed">
                        Data pengajuan KTP saat ini akan dihapus dari sistem.
                    </p>
                </div>
            @endif

            <div class="flex items-center gap-2 mt-6">
                <button type="button" @click="showDeleteIdentity = false"
                    class="flex-1 h-10 px-3 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-black dark:text-white border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all duration-150 rounded-lg cursor-pointer">
                    Batal
                </button>
                <button type="button" wire:click="deleteIdentityDocument" @click="showDeleteIdentity = false"
                    class="flex-1 h-10 px-3 bg-rose-500 hover:bg-rose-600 text-white border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all duration-150 rounded-lg cursor-pointer">
                    Ya, Hapus Dokumen
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Kost List -->
<div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] overflow-hidden">
    <div class="bg-yellow-300 border-b-4 border-black dark:border-zinc-700 px-6 py-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-black rounded flex items-center justify-center shrink-0">
                <x-icon name="lucide-building-2" class="w-5 h-5 text-yellow-300 stroke-[2.5]" />
            </div>
            <div>
                <h2 class="text-xl font-black text-black uppercase tracking-tight">Daftar Kost Saya</h2>
                <p class="text-xs font-bold text-black">Kelola properti yang Anda miliki.</p>
            </div>
        </div>
        <a href="{{ route('dashboard.kost.create') }}"
            class="hidden sm:inline-flex items-center gap-1.5 bg-black text-yellow-300 hover:bg-zinc-800 border-2 border-black dark:border-zinc-700 font-black text-xs uppercase px-3.5 py-2 rounded-xl shadow-[3px_3px_0px_0px_rgba(255,255,255,0.4)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all shrink-0">
            <x-icon name="lucide-plus" class="w-4 h-4 stroke-[2.5]" />
            <span>Tambah Kost</span>
        </a>
    </div>

    <div class="divide-y divide-zinc-200">
        @forelse ($stats['kosts'] as $kost)
            <div class="p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="w-20 h-20 rounded-xl border-3 border-black dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-800 overflow-hidden shrink-0">
                    @if ($kost->primaryImage)
                        <img src="{{ \Illuminate\Support\Str::startsWith($kost->primaryImage->image_path, 'http') ? $kost->primaryImage->image_path : \Illuminate\Support\Facades\Storage::url($kost->primaryImage->image_path) }}"
                            alt="{{ $kost->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <x-icon name="lucide-building-2" class="w-8 h-8 stroke-[2]" />
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0 space-y-1.5">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('kost.show', $kost->slug) }}"
                            class="font-black text-black dark:text-white uppercase text-sm hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all truncate">
                            {{ $kost->name }}
                        </a>
                        <x-status-badge :status="$kost->status" />
                        <x-status-badge :status="$kost->is_available ? 'available' : 'full'" />
                    </div>
                    <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 flex items-center gap-1.5">
                        <x-icon name="lucide-map-pin" class="w-3.5 h-3.5 stroke-[2.5]" />
                        {{ $kost->address }}, Kec. {{ $kost->district }}
                    </p>
                    <p class="text-xs font-black uppercase text-black dark:text-white bg-yellow-200 border-2 border-black dark:border-zinc-700 px-2 py-0.5 rounded inline-block">
                        Rp {{ number_format($kost->price_monthly, 0, ',', '.') }}{{ \App\Models\Kost::rentPeriodUnit($kost->rent_period) }}
                    </p>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('dashboard.kost.edit', $kost->slug) }}"
                        class="inline-flex items-center gap-1.5 bg-white dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-black dark:text-white border-2 border-black dark:border-zinc-700 font-black text-xs uppercase px-3.5 py-2 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg">
                        <x-icon name="lucide-pencil" class="w-3.5 h-3.5 stroke-[2.5]" />
                        <span>Edit</span>
                    </a>
                    <a href="{{ route('kost.show', $kost->slug) }}"
                        class="inline-flex items-center gap-1.5 bg-cyan-300 hover:bg-cyan-200 text-black dark:text-white border-2 border-black dark:border-zinc-700 font-black text-xs uppercase px-3.5 py-2 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg">
                        <x-icon name="lucide-external-link" class="w-3.5 h-3.5 stroke-[2.5]" />
                        <span>Lihat</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="p-10 text-center space-y-3">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-zinc-100 dark:bg-zinc-800 border-3 border-black dark:border-zinc-700 flex items-center justify-center">
                    <x-icon name="lucide-building-2" class="w-8 h-8 stroke-[2]" />
                </div>
                <p class="text-sm font-black uppercase text-zinc-500 dark:text-zinc-400">Belum ada kost terdaftar</p>
                <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400">Daftarkan properti kost pertama Anda untuk mulai menerima pertanyaan penyewa.</p>
                <a href="{{ route('dashboard.kost.create') }}"
                    class="inline-flex items-center gap-2 bg-yellow-400 hover:bg-yellow-300 text-black dark:text-white border-3 border-black dark:border-zinc-700 font-black text-xs uppercase px-5 py-2.5 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg">
                    <x-icon name="lucide-plus" class="w-4 h-4 stroke-[2.5]" />
                    <span>Tambah Kost Baru</span>
                </a>
            </div>
        @endforelse
    </div>

    <div class="px-6 py-4 border-t-3 border-black dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
        <a href="{{ route('dashboard') }}"
            class="inline-flex items-center gap-1.5 text-black dark:text-white font-black text-xs uppercase hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all">
            <x-icon name="lucide-arrow-right" class="w-4 h-4 stroke-[2.5]" />
            <span>Buka Dashboard Pemilik</span>
        </a>
    </div>
</div>
