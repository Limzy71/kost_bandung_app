<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <x-stat-card label="Total Properti" :value="$stats['totalKosts']" hint="Kost terdaftar dalam sistem" icon="lucide-building-2" color="bg-cyan-300" />
    <x-stat-card label="Kost Tersedia" :value="$stats['availableKosts']" hint="Properti siap huni" icon="lucide-circle-check" color="bg-lime-300" />
    <x-stat-card label="Menunggu Moderasi" :value="$stats['pendingKosts']" hint="Pengajuan belum ditinjau admin" icon="lucide-hourglass" color="bg-yellow-300" />
    <x-stat-card label="Pesan Masuk" :value="$stats['inquiries']" hint="Pertanyaan dari calon penyewa" icon="lucide-message-square" color="bg-pink-300" />
</div>

<!-- Kost List -->
<div class="bg-white border-4 border-black rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
    <div class="bg-yellow-300 border-b-4 border-black px-6 py-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-black rounded flex items-center justify-center shrink-0">
                <x-icon name="lucide-building-2" class="w-5 h-5 text-yellow-300 stroke-[2.5]" />
            </div>
            <div>
                <h2 class="text-xl font-black text-black uppercase tracking-tight">Daftar Kost Saya</h2>
                <p class="text-xs font-bold text-black/70">Kelola properti yang Anda miliki.</p>
            </div>
        </div>
        <a href="{{ route('dashboard.kost.create') }}"
            class="hidden sm:inline-flex items-center gap-1.5 bg-black text-yellow-300 hover:bg-zinc-800 border-2 border-black font-black text-xs uppercase px-3.5 py-2 rounded-xl shadow-[3px_3px_0px_0px_rgba(255,255,255,0.4)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all shrink-0">
            <x-icon name="lucide-plus" class="w-4 h-4 stroke-[2.5]" />
            <span>Tambah Kost</span>
        </a>
    </div>

    <div class="divide-y divide-zinc-200">
        @forelse ($stats['kosts'] as $kost)
            <div class="p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="w-20 h-20 rounded-xl border-3 border-black bg-zinc-100 overflow-hidden shrink-0">
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
                            class="font-black text-black uppercase text-sm hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all truncate">
                            {{ $kost->name }}
                        </a>
                        <x-status-badge :status="$kost->status" />
                        <x-status-badge :status="$kost->is_available ? 'available' : 'full'" />
                    </div>
                    <p class="text-xs font-bold text-zinc-600 flex items-center gap-1.5">
                        <x-icon name="lucide-map-pin" class="w-3.5 h-3.5 stroke-[2.5]" />
                        {{ $kost->address }}, Kec. {{ $kost->district }}
                    </p>
                    <p class="text-xs font-black uppercase text-black bg-yellow-200 border-2 border-black px-2 py-0.5 rounded inline-block">
                        Rp {{ number_format($kost->price_monthly, 0, ',', '.') }} / bulan
                    </p>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('dashboard.kost.edit', $kost->slug) }}"
                        class="inline-flex items-center gap-1.5 bg-white hover:bg-zinc-100 text-black border-2 border-black font-black text-xs uppercase px-3.5 py-2 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg">
                        <x-icon name="lucide-pencil" class="w-3.5 h-3.5 stroke-[2.5]" />
                        <span>Edit</span>
                    </a>
                    <a href="{{ route('kost.show', $kost->slug) }}"
                        class="inline-flex items-center gap-1.5 bg-cyan-300 hover:bg-cyan-200 text-black border-2 border-black font-black text-xs uppercase px-3.5 py-2 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg">
                        <x-icon name="lucide-external-link" class="w-3.5 h-3.5 stroke-[2.5]" />
                        <span>Lihat</span>
                    </a>
                </div>
            </div>
        @empty
            <div class="p-10 text-center space-y-3">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-zinc-100 border-3 border-black flex items-center justify-center">
                    <x-icon name="lucide-building-2" class="w-8 h-8 stroke-[2]" />
                </div>
                <p class="text-sm font-black uppercase text-zinc-500">Belum ada kost terdaftar</p>
                <p class="text-xs font-bold text-zinc-500">Daftarkan properti kost pertama Anda untuk mulai menerima pertanyaan penyewa.</p>
                <a href="{{ route('dashboard.kost.create') }}"
                    class="inline-flex items-center gap-2 bg-yellow-400 hover:bg-yellow-300 text-black border-3 border-black font-black text-xs uppercase px-5 py-2.5 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg">
                    <x-icon name="lucide-plus" class="w-4 h-4 stroke-[2.5]" />
                    <span>Tambah Kost Baru</span>
                </a>
            </div>
        @endforelse
    </div>

    <div class="px-6 py-4 border-t-3 border-black bg-zinc-50">
        <a href="{{ route('dashboard') }}"
            class="inline-flex items-center gap-1.5 text-black font-black text-xs uppercase hover:text-yellow-600 hover:underline decoration-3 underline-offset-4 transition-all">
            <x-icon name="lucide-arrow-right" class="w-4 h-4 stroke-[2.5]" />
            <span>Buka Dashboard Pemilik</span>
        </a>
    </div>
</div>
