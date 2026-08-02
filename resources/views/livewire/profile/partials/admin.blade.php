<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <x-stat-card label="Kost Menunggu" :value="$stats['pendingKosts']" hint="Pengajuan yang belum ditinjau" icon="lucide-hourglass" color="bg-yellow-300" />
    <x-stat-card label="Kost Tayang" :value="$stats['publishedKosts']" hint="Properti yang tampil publik" icon="lucide-circle-check" color="bg-lime-300" />
    <x-stat-card label="Kost Ditolak" :value="$stats['rejectedKosts']" hint="Pengajuan yang ditolak" icon="lucide-x-circle" color="bg-rose-400" />
    <x-stat-card label="Total Pencari Kost" :value="$stats['totalUsers']" hint="Akun pengguna terdaftar" icon="lucide-users" color="bg-cyan-300" />
    <x-stat-card label="Total Pemilik Kost" :value="$stats['totalOwners']" hint="Akun owner terdaftar" icon="lucide-building-2" color="bg-violet-300" />
    <x-stat-card label="Fasilitas Menunggu" :value="$stats['pendingFacilities']" hint="Fasilitas custom belum disetujui" icon="lucide-layers" color="bg-pink-300" />
</div>

<!-- Moderation Link -->
<div class="bg-white border-4 border-black rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
    <div class="bg-yellow-300 border-b-4 border-black px-6 py-4 flex items-center gap-3">
        <div class="w-9 h-9 bg-black rounded flex items-center justify-center shrink-0">
            <x-icon name="lucide-shield-check" class="w-5 h-5 text-yellow-300 stroke-[2.5]" />
        </div>
        <div>
            <h2 class="text-xl font-black text-black uppercase tracking-tight">Panel Moderasi</h2>
            <p class="text-xs font-bold text-black/70">Tinjau pengajuan kost dan fasilitas dari pemilik.</p>
        </div>
    </div>

    <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <p class="text-sm font-bold text-zinc-600 max-w-2xl">
            Anda memiliki <span class="bg-yellow-200 border-b-2 border-black px-1 font-black text-black">{{ $stats['pendingKosts'] }} kost</span>
            dan <span class="bg-yellow-200 border-b-2 border-black px-1 font-black text-black">{{ $stats['pendingFacilities'] }} fasilitas</span>
            yang menunggu keputusan moderasi.
        </p>
        <a href="{{ route('admin.moderation') }}"
            class="inline-flex items-center gap-2 bg-lime-400 hover:bg-lime-300 text-black border-3 border-black font-black text-sm uppercase px-6 py-3 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-1 active:translate-y-1 active:shadow-none transition-all rounded-lg shrink-0">
            <x-icon name="lucide-shield-check" class="w-5 h-5 stroke-[2.5]" />
            <span>Buka Moderasi</span>
        </a>
    </div>
</div>
