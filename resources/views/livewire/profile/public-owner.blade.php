<div class="min-h-screen bg-[#f8f9fa] bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] bg-[size:24px_24px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-8">

        <!-- Back -->
        <div>
            <a href="{{ $backUrl }}"
                class="inline-flex items-center gap-2 bg-white text-black border-3 border-black font-black text-xs uppercase px-4 py-2.5 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer">
                <x-icon name="lucide-chevron-left" class="w-4 h-4 stroke-[3]" />
                <span>{{ $backLabel }}</span>
            </a>
        </div>

        <!-- Owner Header Card -->
        <div class="bg-white border-4 border-black p-6 md:p-8 rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <div class="w-24 h-24 md:w-28 md:h-28 rounded-2xl bg-lime-300 border-4 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex items-center justify-center shrink-0">
                    <span class="text-3xl md:text-4xl font-black text-black uppercase">{{ $user->initials() }}</span>
                </div>

                <div class="flex-1 text-center sm:text-left space-y-2">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <span class="px-3 py-1 bg-lime-400 text-black border-2 border-black font-extrabold text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] inline-flex items-center gap-1.5">
                            <x-icon name="lucide-building-2" class="w-3.5 h-3.5 stroke-[2.5]" />
                            <span>Pemilik Kost</span>
                        </span>
                        <span class="px-3 py-1 bg-white text-black border-2 border-black font-extrabold text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            Akun Terverifikasi
                        </span>
                    </div>

                    <h2 class="text-2xl md:text-3xl font-black text-black uppercase tracking-tight">
                        {{ $user->business_name ?: $user->name }}
                    </h2>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 pt-2 text-sm">
                        <div class="flex items-center gap-2 text-zinc-700 font-bold">
                            <x-icon name="lucide-user" class="w-4 h-4 text-black shrink-0 stroke-[2.5]" />
                            <span>{{ $user->name }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-zinc-700 font-bold">
                            <x-icon name="lucide-phone" class="w-4 h-4 text-black shrink-0 stroke-[2.5]" />
                            <span>{{ $user->phone_number ?: '-' }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-zinc-700 font-bold">
                            <x-icon name="lucide-mail" class="w-4 h-4 text-black shrink-0 stroke-[2.5]" />
                            <span class="truncate">{{ $user->email }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-zinc-700 font-bold">
                            <x-icon name="lucide-calendar" class="w-4 h-4 text-black shrink-0 stroke-[2.5]" />
                            <span>Terdaftar sejak {{ $user->created_at?->translatedFormat('F Y') }}</span>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-stat-card label="Total Kost" :value="$totalKosts" hint="Semua properti terdaftar" icon="lucide-building-2" color="bg-cyan-300" />
            <x-stat-card label="Kost Tersedia" :value="$availableKosts" hint="Properti yang siap huni" icon="lucide-circle-check" color="bg-lime-300" />
        </div>

        <!-- Published Kosts -->
        <div class="bg-white border-4 border-black rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] overflow-hidden">
            <div class="bg-yellow-300 border-b-4 border-black px-6 py-4 flex items-center gap-3">
                <div class="w-9 h-9 bg-black rounded flex items-center justify-center shrink-0">
                    <x-icon name="lucide-building-2" class="w-5 h-5 text-yellow-300 stroke-[2.5]" />
                </div>
                <div>
                    <h2 class="text-xl font-black text-black uppercase tracking-tight">Daftar Kost</h2>
                    <p class="text-xs font-bold text-black/70">Kost yang sedang ditayangkan oleh pemilik ini.</p>
                </div>
            </div>

            <div class="divide-y divide-zinc-200">
                @forelse ($kosts as $kost)
                    <a href="{{ route('kost.show', $kost->slug) }}"
                        class="p-5 flex flex-col sm:flex-row sm:items-center gap-4 hover:bg-zinc-50 transition-colors group">
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
                                <span class="font-black text-black uppercase text-sm group-hover:text-yellow-600 transition-colors truncate">{{ $kost->name }}</span>
                                <x-status-badge :status="$kost->is_available ? 'available' : 'full'" />
                            </div>
                            <p class="text-xs font-bold text-zinc-600 flex items-center gap-1.5">
                                <x-icon name="lucide-map-pin" class="w-3.5 h-3.5 stroke-[2.5]" />
                                {{ $kost->address }}, Kec. {{ $kost->district }}
                            </p>
                            <p class="text-xs font-black uppercase text-black bg-yellow-200 border-2 border-black px-2 py-0.5 rounded inline-block">
                                Rp {{ number_format($kost->price_monthly, 0, ',', '.') }}{{ \App\Models\Kost::rentPeriodUnit($kost->rent_period) }}
                            </p>
                        </div>

                        <div class="shrink-0">
                            <span class="inline-flex items-center gap-1.5 bg-black text-yellow-300 hover:bg-zinc-800 border-2 border-black font-black text-xs uppercase px-3.5 py-2 rounded-xl shadow-[3px_3px_0px_0px_rgba(255,255,255,0.4)] group-hover:-translate-x-0.5 group-hover:-translate-y-0.5 transition-all">
                                <span>Lihat Kost</span>
                                <x-icon name="lucide-arrow-right" class="w-4 h-4 stroke-[2.5]" />
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="p-10 text-center space-y-3">
                        <div class="w-16 h-16 mx-auto rounded-2xl bg-zinc-100 border-3 border-black flex items-center justify-center">
                            <x-icon name="lucide-building-2" class="w-8 h-8 stroke-[2]" />
                        </div>
                        <p class="text-sm font-black uppercase text-zinc-500">Belum ada kost yang ditayangkan</p>
                        <p class="text-xs font-bold text-zinc-500">Pemilik ini belum memiliki kost yang sedang tayang publik.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
