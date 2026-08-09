@if (! $kostUnavailable)
@php
    $allImages = $kost->images
        ->filter(fn ($img) => filled($img->image_path))
        ->map(function ($img) {
            return Str::startsWith($img->image_path, 'http')
                ? $img->image_path
                : Storage::url($img->image_path);
        })
        ->values()
        ->unique()
        ->values();

    if ($allImages->isEmpty()) {
        $allImages = collect(['https://placehold.co/800x500/eeeeee/31343c?text=Foto+Utama']);
    }

    $waNumber = preg_replace('/\D+/', '', $kost->user?->phone_number ?: ($kost->whatsapp_contact ?? ''));
    if (Str::startsWith($waNumber, '0')) {
        $waNumber = '62' . Str::substr($waNumber, 1);
    } elseif ($waNumber !== '' && ! Str::startsWith($waNumber, '62')) {
        $waNumber = '62' . $waNumber;
    }
    $hasWaNumber = strlen($waNumber) >= 11;
    if (! $hasWaNumber) {
        $waNumber = '';
    }

    $rentPeriodLabels = \App\Models\Kost::rentPeriodLabels();
    $rentPeriodLabel = $rentPeriodLabels[$kost->rent_period] ?? 'Per Bulan';
    $rentPeriodUnit = \App\Models\Kost::rentPeriodUnit($kost->rent_period);

    $extraPeriodLabels = \App\Models\KostPrice::periodLabels();
    $periodOrder = \App\Models\Kost::rentPeriodOrder();
    $periodSummary = collect([$rentPeriodLabel])
        ->concat(
            $kost->prices
                ->sortBy(fn ($p) => $periodOrder[$p->period] ?? 99)
                ->map(fn ($p) => $extraPeriodLabels[$p->period] ?? ucfirst($p->period)),
        )
        ->values();
    $priceOptions = collect([['label' => $rentPeriodLabel, 'price' => $kost->price_monthly]])
        ->concat(
            $kost->prices
                ->sortBy(fn ($p) => $periodOrder[$p->period] ?? 99)
                ->map(fn ($p) => [
                    'label' => $extraPeriodLabels[$p->period] ?? ucfirst($p->period),
                    'price' => $p->price,
                ]),
        )
        ->values();
@endphp

<div class="min-h-screen bg-[#f8f9fa] bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] bg-[size:24px_24px] pb-28 lg:pb-16 pt-8 dark:bg-zinc-950 dark:bg-[linear-gradient(to_right,#27272a_1px,transparent_1px),linear-gradient(to_bottom,#27272a_1px,transparent_1px)]"
    x-data="{ 
        showModal: false, 
        showGalleryModal: false, 
        activeIndex: 0, 
        images: {{ Js::from($allImages) }},
        openChatModal() {
            @auth
                this.showModal = true;
            @else
                window.location.href = '{{ route('login') }}?redirect=' + encodeURIComponent(window.location.pathname + window.location.search);
            @endauth
        },
        nextImage() {
            const total = this.images.length;
            if (total <= 1) return;
            const cur = this.images[this.activeIndex];
            for (let step = 1; step <= total; step++) {
                const i = (this.activeIndex + step) % total;
                if (this.images[i] !== cur) {
                    this.activeIndex = i;
                    return;
                }
            }
        },
        prevImage() {
            const total = this.images.length;
            if (total <= 1) return;
            const cur = this.images[this.activeIndex];
            for (let step = 1; step <= total; step++) {
                const i = (this.activeIndex - step + total) % total;
                if (this.images[i] !== cur) {
                    this.activeIndex = i;
                    return;
                }
            }
        }
    }" 
    x-effect="document.body.style.overflow = (showGalleryModal || showModal) ? 'hidden' : ''">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Admin Review Notice Banner -->
        @if (auth()->check() && auth()->user()->role === 'admin' && $kost->status !== 'published')
            <div class="bg-amber-300 border-4 border-black p-4 sm:p-5 rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] flex items-center gap-3 dark:border-zinc-700 dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)]">
                <x-icon name="lucide-shield-alert" class="w-7 h-7 text-black stroke-[2.5] shrink-0" />
                <div>
                    <p class="text-xs sm:text-sm font-black uppercase text-black">Mode Peninjauan Admin</p>
                    <p class="text-xs font-bold text-black/80">Properti ini berstatus <span class="uppercase font-black text-rose-800">'{{ $kost->status }}'</span> dan belum ditayangkan secara publik.</p>
                </div>
            </div>
        @endif

        <!-- Navigation Back Button -->
        <div class="flex items-center justify-between">
            <a href="{{ $backUrl }}"
                class="inline-flex items-center gap-2 bg-white text-black border-3 border-black font-black text-xs uppercase px-4 py-2.5 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer dark:bg-zinc-900 dark:text-white dark:border-zinc-700 dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                <x-icon name="lucide-chevron-left" class="w-4 h-4 stroke-[3]" />
                <span>{{ $backLabel }}</span>
            </a>
        </div>

        <!-- Keamanan Transaksi Callout -->
        @unless (auth()->check() && auth()->id() === $kost->user_id)
            <div class="bg-rose-50 border-3 border-black rounded-2xl p-4 sm:p-5 shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] flex items-start gap-3 dark:bg-rose-950/40 dark:border-zinc-700 dark:shadow-[5px_5px_0px_0px_rgba(255,255,255,0.25)]">
                <div class="w-10 h-10 shrink-0 rounded-xl bg-rose-500 border-2 border-black flex items-center justify-center text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                    <x-icon name="lucide-shield-alert" class="w-5 h-5 stroke-[2.5]" />
                </div>
                <div class="space-y-1 min-w-0">
                    <p class="text-xs font-black uppercase tracking-wider text-rose-800 flex items-center gap-1.5">
                        <x-icon name="lucide-info" class="w-4 h-4 stroke-[2.5]" />
                        Panduan Keamanan Transaksi
                    </p>
                    <p class="text-sm font-bold text-zinc-800 leading-relaxed dark:text-zinc-200">
                        Jangan pernah melakukan transaksi sebelum melihat/datang langsung ke kost tersebut.
                        <span class="text-zinc-600 dark:text-zinc-400">KostBandung hanya mempertemukan pemilik dan pencari kost serta tidak memfasilitasi pembayaran sewa.</span>
                    </p>
                </div>
            </div>
        @endunless

        <!-- ================================================================
            MAIN TWO-COLUMN LAYOUT — Grid structure stretched for sticky right sidebar
            Left (lg:col-span-2): Gallery + Content + Map
            Right (lg:col-span-1): Sticky Price Card
            ================================================================ -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- ============================================================
                LEFT COLUMN — Photo Gallery + All Content Sections
                ============================================================ -->
            <div class="lg:col-span-2 space-y-6">

                <!-- PHOTO GALLERY — Clean sub-grid, no bleed into content below -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Primary / Hero Image — spans 2 columns on md+ -->
                    <div @click="showGalleryModal = true; activeIndex = 0"
                        class="md:col-span-2 relative group rounded-2xl overflow-hidden border-4 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] bg-zinc-200 aspect-video md:aspect-auto md:h-96 cursor-pointer dark:border-zinc-700 dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] dark:bg-zinc-800">
                        @php
                            $primaryImg = $kost->primaryImage;
                            $primarySrc = $primaryImg
                                ? (Str::startsWith($primaryImg->image_path ?? '', 'http')
                                    ? $primaryImg->image_path
                                    : Storage::url($primaryImg->image_path))
                                : 'https://placehold.co/800x500/eeeeee/31343c?text=Foto+Utama';
                        @endphp
                        <img src="{{ $primarySrc }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            alt="{{ $kost->name }}">
                        <button type="button" @click.stop="showGalleryModal = true; activeIndex = 0"
                            class="absolute bottom-4 right-4 px-4 py-2 bg-yellow-300 hover:bg-yellow-400 text-black border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg flex items-center gap-2 cursor-pointer z-10 dark:border-zinc-700 dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                            <x-icon name="lucide-images" class="w-4 h-4 stroke-[2.5]" />
                            <span>Lihat Semua Foto</span>
                        </button>
                    </div>

                    <!-- Thumbnail Stack — 3rd column, stacks vertically -->
                    <div class="hidden md:flex flex-col gap-4 md:h-96">
                        @php
                            $thumbnails = $kost->images->where('is_primary', false)->take(3)->values();
                        @endphp
                        @foreach (range(0, 2) as $i)
                            @php
                                $thumb = $thumbnails->get($i);
                                $thumbSrc = $thumb
                                    ? (Str::startsWith($thumb->image_path ?? '', 'http')
                                        ? $thumb->image_path
                                        : Storage::url($thumb->image_path))
                                    : 'https://placehold.co/400x300/e5e7eb/31343c?text=Foto+' . ($i + 2);
                                $imgIdx = $thumb ? $allImages->search($thumbSrc) : ($i + 1 < $allImages->count() ? $i + 1 : 0);
                                if ($imgIdx === false) {
                                    $imgIdx = 0;
                                }
                            @endphp
                            <div @click="showGalleryModal = true; activeIndex = {{ $imgIdx }}"
                                class="flex-1 min-h-0 rounded-xl overflow-hidden border-3 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] bg-zinc-200 relative group cursor-pointer dark:border-zinc-700 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] dark:bg-zinc-800">
                                <img src="{{ $thumbSrc }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    alt="Foto {{ $i + 2 }}">
                                @if ($i === 2 && $allImages->count() > 4)
                                    <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                        <span
                                            class="text-white font-black text-2xl">+{{ $allImages->count() - 4 }}</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- END PHOTO GALLERY -->

                <!-- MAIN CONTENT CARD -->
                <div
                    class="bg-white border-4 border-black p-6 sm:p-8 rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] space-y-8 dark:bg-zinc-900 dark:border-zinc-700 dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)]">

                    <!-- Badges & Title -->
                    <div class="space-y-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                class="px-3.5 py-1 bg-pink-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">>
                                Tipe {{ $kost->gender_type }}
                            </span>

                            <span
                                class="px-3.5 py-1 bg-cyan-300 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">>
                                Sewa {{ $rentPeriodLabel }}
                            </span>

                            @if ($kost->is_available)
                                <x-brutal-badge color="lime">
                                    ✓ Kamar Tersedia
                                </x-brutal-badge>
                            @else
                                <x-brutal-badge color="rose">
                                    ✕ Kamar Penuh
                                </x-brutal-badge>
                            @endif

                            @if ($kost->boosted_at)
                                <span
                                    class="px-3.5 py-1 bg-yellow-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider inline-flex items-center gap-1.5 dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">>
                                    <x-icon name="lucide-zap" fill="#FBBF24" stroke-width="0.8" class="w-4 h-4 shrink-0 drop-shadow-[1px_1px_0px_rgba(0,0,0,1)]" />
                                    <span>Properti Rekomendasi</span>
                                </span>
                            @endif

                            @if ($kost->isVerified())
                                <span
                                    class="px-3.5 py-1 bg-emerald-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider inline-flex items-center gap-1.5 dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">>
                                    <x-icon name="lucide-badge-check" class="w-4 h-4 shrink-0 stroke-[2.5]" />
                                    <span>Kepemilikan Terverifikasi</span>
                                </span>
                            @else
                                <span title="Kost ini belum diverifikasi kepemilikannya"
                                    class="px-3.5 py-1 bg-rose-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider inline-flex items-center gap-1.5 dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">>
                                    <x-icon name="lucide-shield-alert" class="w-4 h-4 shrink-0 stroke-[2.5]" />
                                    <span>Belum Terverifikasi</span>
                                </span>
                            @endif
                        </div>

                        <h1 class="text-3xl sm:text-5xl font-black text-black tracking-tight uppercase leading-tight dark:text-white">
                            {{ $kost->name }}
                        </h1>

                        <div class="flex items-start gap-2 text-zinc-700 text-sm sm:text-base font-bold dark:text-zinc-300">
                            <x-icon name="lucide-map-pin" class="w-5 h-5 text-black shrink-0 stroke-[2.5] mt-0.5 dark:text-white" />
                            <span>{{ $kost->address }}, Kecamatan {{ $kost->district }}, Kota Bandung</span>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t-4 border-black dark:border-zinc-700"></div>

                    <!-- Deskripsi -->
                    <div class="space-y-3">
                        <h2 class="text-xl font-black text-black uppercase tracking-tight flex items-center gap-2 dark:text-white">
                            <x-icon name="lucide-file-text" class="w-5 h-5 text-black stroke-[2.5] dark:text-white" />
                            <span>Tentang Kost Ini</span>
                        </h2>
                        <p class="leading-relaxed text-zinc-700 font-medium text-base sm:text-lg dark:text-zinc-300">
                            {{ $kost->description ?? 'Kost nyaman dengan fasilitas modern dan lokasi strategis di Bandung. Cocok untuk Anda yang memiliki mobilitas tinggi namun tetap menginginkan hunian yang tenang dan asri.' }}
                        </p>
                    </div>

                    <!-- Divider -->
                    <div class="border-t-4 border-black dark:border-zinc-700"></div>

                    <!-- Info Properti -->
                    @php
                        $infoItems = [];
                        $infoItems[] = ['label' => 'Periode Sewa', 'value' => $periodSummary->join(', ')];
                        if ($kost->price_deposit !== null) {
                            $infoItems[] = ['label' => 'Uang Deposit', 'value' => 'Rp ' . number_format((float) $kost->price_deposit, 0, ',', '.'), 'note' => 'Dikembalikan saat keluar'];
                        }
                        $infoItems[] = ['label' => 'Listrik & Air', 'value' => $kost->include_utilities ? 'Sudah Termasuk' : 'Terpisah / Diluar Sewa'];
                        $infoItems[] = ['label' => 'Ketersediaan Kamar', 'value' => $kost->available_rooms . ' dari ' . $kost->total_rooms . ' kamar tersedia'];
                    @endphp
                    <div class="space-y-4">
                        <h2 class="text-xl font-black text-black uppercase tracking-tight flex items-center gap-2 dark:text-white">
                            <x-icon name="lucide-info" class="w-5 h-5 text-black stroke-[2.5] dark:text-white" />
                            <span>Info Properti</span>
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach ($infoItems as $item)
                                <div
                                    class="bg-cyan-50 border-2 border-black rounded-xl px-4 py-3 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:bg-cyan-950/50 dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                                    <p class="text-[10px] font-black uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                        {{ $item['label'] }}</p>
                                    <p class="text-sm font-black text-black mt-0.5 dark:text-white">{{ $item['value'] }}</p>
                                    @if (!empty($item['note']))
                                        <p class="text-[10px] font-bold text-zinc-500 mt-0.5 italic dark:text-zinc-400">⟳ {{ $item['note'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        @if ($kost->nearby_landmarks)
                            @php
                                $landmarks = array_filter(array_map('trim', explode(',', $kost->nearby_landmarks)));
                            @endphp
                            @if (count($landmarks) > 0)
                                <!-- Titik Terdekat / Landmark Badges -->
                                <div class="space-y-2 pt-2">
                                    <p class="text-[10px] font-black uppercase tracking-wider text-zinc-600 flex items-center gap-1.5 dark:text-zinc-400">
                                        <x-icon name="lucide-map-pin" class="w-3.5 h-3.5 text-black stroke-[2.5] dark:text-white" />
                                        Titik Terdekat & Landmark Strategis
                                    </p>
                                    <div class="flex flex-wrap gap-2.5">
                                        @foreach ($landmarks as $landmark)
                                            <div
                                                class="inline-flex items-center gap-2 bg-yellow-200 border-2 border-black px-3.5 py-2 rounded-xl text-xs sm:text-sm font-black text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                                                <x-icon name="lucide-map-pin" class="w-3.5 h-3.5 text-black stroke-[2.5] shrink-0" />
                                                <span>{{ $landmark }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Opsi Harga Sewa (Mobile) -->
                    @if ($priceOptions->count() > 1)
                        <div class="lg:hidden space-y-3">
                            <h2 class="text-xl font-black text-black uppercase tracking-tight flex items-center gap-2 dark:text-white">
                                <x-icon name="lucide-tag" class="w-5 h-5 text-black stroke-[2.5] dark:text-white" />
                                <span>Opsi Harga Sewa</span>
                            </h2>
                            <div class="border-2 border-black rounded-lg divide-y-2 divide-black bg-zinc-50 overflow-hidden dark:border-zinc-700 dark:divide-zinc-700 dark:bg-zinc-900">
                                @foreach ($priceOptions as $option)
                                    <div class="flex items-center justify-between gap-3 px-3 py-2.5">
                                        <span class="text-xs font-black uppercase text-zinc-700 dark:text-zinc-300">{{ $option['label'] }}</span>
                                        <span class="text-sm font-black text-black dark:text-white">
                                            Rp {{ number_format((float) $option['price'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-[10px] font-bold italic text-zinc-500 dark:text-zinc-400">Harga di atas adalah total bayar untuk masing-masing periode.</p>
                        </div>
                    @endif

                    <!-- Divider -->
                    <div class="border-t-4 border-black dark:border-zinc-700"></div>

                    <!-- Fasilitas Utama -->
                    <div class="space-y-6">
                        <h2 class="text-xl font-black text-black uppercase tracking-tight flex items-center gap-2 dark:text-white">
                            <x-icon name="lucide-home" class="w-5 h-5 text-black stroke-[2.5] dark:text-white" />
                            <span>Fasilitas Properti</span>
                        </h2>

                        @php
                            $roomFacilities = $kost->facilities->where('type', 'room');
                            $buildingFacilities = $kost->facilities->where('type', 'building');
                            $parkingFacilities = $kost->facilities->where('type', 'parking');
                        @endphp

                        @if ($roomFacilities->count() > 0)
                            <div class="space-y-3">
                                <h3 class="text-sm font-black uppercase tracking-wider text-black flex items-center gap-2 dark:text-white">
                                    <x-icon name="lucide-bed-double" class="w-4 h-4 text-black stroke-[2.5] dark:text-white" />
                                    Fasilitas Kamar
                                    <span
                                        class="text-[10px] font-black uppercase bg-lime-300 border-2 border-black px-2 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:border-zinc-700 dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)]">>
                                        {{ $roomFacilities->count() }}
                                    </span>
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach ($roomFacilities as $facility)
                                        <div
                                            class="flex items-center gap-2.5 bg-lime-100 border-2 border-black px-3.5 py-2.5 rounded-xl text-sm font-black text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:bg-lime-950/50 dark:border-zinc-700 dark:text-white dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">>

                                            <span>{{ $facility->name }}</span>
                                            @if (auth()->check() && auth()->id() === $kost->user_id && $facility->status === 'pending' && $facility->user_id === auth()->id())
                                                <span
                                                    class="ml-auto inline-flex items-center gap-1.5 text-[9px] font-black uppercase bg-amber-300 border-2 border-black px-1.5 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:border-zinc-700 dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)]">
                                                    Menunggu review
                                                    <button type="button" wire:click="removeFacility({{ $facility->id }})"
                                                        wire:confirm="Hapus fasilitas '{{ $facility->name }}' dari kost ini?"
                                                        class="w-4 h-4 rounded bg-rose-500 hover:bg-rose-400 border border-black text-white text-[9px] font-black leading-none flex items-center justify-center cursor-pointer dark:border-zinc-700"
                                                        title="Hapus fasilitas">
                                                        ✕
                                                    </button>
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($buildingFacilities->count() > 0)
                            <div class="space-y-3">
                                <h3 class="text-sm font-black uppercase tracking-wider text-black flex items-center gap-2 dark:text-white">
                                    <x-icon name="lucide-building-2" class="w-4 h-4 text-black stroke-[2.5] dark:text-white" />
                                    Fasilitas Umum
                                    <span
                                        class="text-[10px] font-black uppercase bg-cyan-300 border-2 border-black px-2 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:border-zinc-700 dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)]">>
                                        {{ $buildingFacilities->count() }}
                                    </span>
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach ($buildingFacilities as $facility)
                                        <div
                                            class="flex items-center gap-2.5 bg-cyan-50 border-2 border-black px-3.5 py-2.5 rounded-xl text-sm font-black text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:bg-cyan-950/50 dark:border-zinc-700 dark:text-white dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">

                                            <span>{{ $facility->name }}</span>
                                            @if (auth()->check() && auth()->id() === $kost->user_id && $facility->status === 'pending' && $facility->user_id === auth()->id())
                                                <span
                                                    class="ml-auto inline-flex items-center gap-1.5 text-[9px] font-black uppercase bg-amber-300 border-2 border-black px-1.5 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:border-zinc-700 dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)]">
                                                    Menunggu review
                                                    <button type="button" wire:click="removeFacility({{ $facility->id }})"
                                                        wire:confirm="Hapus fasilitas '{{ $facility->name }}' dari kost ini?"
                                                        class="w-4 h-4 rounded bg-rose-500 hover:bg-rose-400 border border-black text-white text-[9px] font-black leading-none flex items-center justify-center cursor-pointer dark:border-zinc-700"
                                                        title="Hapus fasilitas">
                                                        ✕
                                                    </button>
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($parkingFacilities->count() > 0)
                            <div class="space-y-3">
                                <h3 class="text-sm font-black uppercase tracking-wider text-black flex items-center gap-2 dark:text-white">
                                    <x-icon name="lucide-square-parking" class="w-4 h-4 text-black stroke-[2.5] dark:text-white" />
                                    Fasilitas Parkir
                                    <span class="text-[10px] font-black uppercase bg-yellow-300 border-2 border-black px-2 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:border-zinc-700 dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)]">>
                                        {{ $parkingFacilities->count() }}
                                    </span>
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach ($parkingFacilities as $facility)
                                        <div class="flex items-center gap-2.5 bg-yellow-50 border-2 border-black px-3.5 py-2.5 rounded-xl text-sm font-black text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:bg-yellow-950/40 dark:border-zinc-700 dark:text-white dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">

                                            <span>{{ $facility->name }}</span>
                                            @if (auth()->check() && auth()->id() === $kost->user_id && $facility->status === 'pending' && $facility->user_id === auth()->id())
                                                <span
                                                    class="ml-auto inline-flex items-center gap-1.5 text-[9px] font-black uppercase bg-amber-300 border-2 border-black px-1.5 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:border-zinc-700 dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)]">
                                                    Menunggu review
                                                    <button type="button" wire:click="removeFacility({{ $facility->id }})"
                                                        wire:confirm="Hapus fasilitas '{{ $facility->name }}' dari kost ini?"
                                                        class="w-4 h-4 rounded bg-rose-500 hover:bg-rose-400 border border-black text-white text-[9px] font-black leading-none flex items-center justify-center cursor-pointer dark:border-zinc-700"
                                                        title="Hapus fasilitas">
                                                        ✕
                                                    </button>
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($kost->facilities->isEmpty())
                            <p class="text-zinc-500 font-bold dark:text-zinc-400">Tidak ada fasilitas terdaftar.</p>
                        @endif
                    </div>

                    <!-- Divider -->
                    <div class="border-t-4 border-black dark:border-zinc-700"></div>

                    <!-- Aturan Kost -->
                    <div class="space-y-4">
                        <h2 class="text-xl font-black text-black uppercase tracking-tight flex items-center gap-2 dark:text-white">
                            <x-icon name="lucide-triangle-alert" class="w-5 h-5 text-black stroke-[2.5] dark:text-white" />
                            <span>Aturan Properti</span>
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @forelse($kost->rules as $rule)
                                <div
                                    class="flex items-start gap-3 bg-zinc-100 border-2 border-black p-3.5 rounded-xl text-sm font-bold text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:bg-zinc-800 dark:border-zinc-700 dark:text-white dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                                    <x-icon name="lucide-check" class="w-4 h-4 text-pink-600 shrink-0 stroke-[2.5] mt-0.5" />
                                    <div>
                                        <span class="font-black">{{ $rule->name }}</span>
                                        @if (Str::contains(strtolower($rule->name), 'pasutri'))
                                            <span class="block text-[11px] font-black text-rose-600 mt-0.5 dark:text-rose-400">
                                                (Wajib Sertakan Surat Nikah Saat Pengajuan Sewa)
                                            </span>
                                        @elseif (Str::contains(strtolower($rule->name), 'membawa anak') || Str::contains(strtolower($rule->name), 'bawa anak'))
                                            <span class="block text-[11px] font-black text-blue-600 mt-0.5 dark:text-blue-400">
                                                (Wajib Sertakan Kartu Keluarga Saat Pengajuan Sewa)
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-zinc-500 font-bold dark:text-zinc-400">Tidak ada aturan khusus.</p>
                            @endforelse
                        </div>

                        @if ($kost->additional_rules_note)
                            <div
                                class="bg-amber-50 border-2 border-black rounded-xl p-4 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:bg-amber-950/40 dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                                <p class="text-[10px] font-black uppercase tracking-wider text-zinc-500 dark:text-zinc-400">Aturan
                                    Tambahan</p>
                                <p class="text-sm font-bold text-black mt-0.5 dark:text-white">{{ $kost->additional_rules_note }}</p>
                            </div>
                        @endif
                    </div>

                </div>
                <!-- END MAIN CONTENT CARD -->


            </div>
            <!-- END LEFT COLUMN -->

            <!-- ============================================================
                RIGHT COLUMN — Sticky Price Card with Smooth Hover Animations
                ============================================================ -->
            <div class="lg:col-span-1 h-full">
                <div
                    class="sticky top-24 w-full bg-white border-4 border-black rounded-2xl p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-1 hover:translate-x-0.5 hover:shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] transition-all duration-300 ease-out hidden lg:block space-y-6 dark:bg-zinc-900 dark:border-zinc-700 dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] dark:hover:shadow-[10px_10px_0px_0px_rgba(255,255,255,0.25)]">

                    <!-- Display Harga -->
                    <div
                        class="bg-yellow-300 border-3 border-black p-4 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-1 dark:border-zinc-700 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]">
                        <p class="text-xs font-black uppercase tracking-wider text-black">Harga Sewa Utama</p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black text-black tracking-tight">
                                Rp {{ number_format($kost->price_monthly, 0, ',', '.') }}
                            </span>
                            <span class="text-xs font-black text-black">{{ $rentPeriodUnit }}</span>
                        </div>
                    </div>

                    <!-- Opsi Harga Sewa -->
                    @if ($priceOptions->count() > 1)
                        <div class="space-y-1.5">
                            <p class="text-xs font-black uppercase tracking-wider text-black dark:text-white">Opsi Harga Sewa</p>
                            <div class="border-2 border-black rounded-lg divide-y-2 divide-black bg-zinc-50 overflow-hidden dark:border-zinc-700 dark:divide-zinc-700 dark:bg-zinc-900">
                                @foreach ($priceOptions as $option)
                                    <div class="flex items-center justify-between gap-3 px-3 py-2">
                                        <span class="text-xs font-black uppercase text-zinc-700 dark:text-zinc-300">{{ $option['label'] }}</span>
                                        <span class="text-sm font-black text-black dark:text-white">
                                            Rp {{ number_format((float) $option['price'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-[10px] font-bold italic text-zinc-500 dark:text-zinc-400">Harga di atas adalah total bayar untuk masing-masing periode.</p>
                        </div>
                    @endif

                    <!-- CTA Buttons -->
                    <div class="space-y-3">
                        @if (auth()->check() && auth()->id() === $kost->user_id)
                            <a href="{{ route('dashboard.kost.edit', $kost->slug) }}"
                                class="w-full py-4 bg-yellow-300 hover:bg-yellow-200 text-black border-3 border-black font-black text-base uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl flex items-center justify-center gap-2 cursor-pointer dark:border-zinc-700 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)]">
                                <x-icon name="lucide-pencil" class="w-5 h-5 stroke-[2.5]" />
                                <span>Kelola Properti Ini</span>
                            </a>
                        @else
                            @if ($kost->is_available)
                                @php
                                    $waMessage = rawurlencode(
                                        "Halo, saya tertarik dengan kost \"" .
                                            $kost->name .
                                            "\" di " .
                                            $kost->district .
                                            '. Apakah kamar masih tersedia?',
                                    );
                                @endphp

                                @if ($hasWaNumber)
                                    <a href="https://wa.me/{{ $waNumber }}?text={{ $waMessage }}" target="_blank"
                                        class="w-full py-4 bg-emerald-400 hover:bg-emerald-300 text-black border-3 border-black font-black text-base uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl flex items-center justify-center gap-2 group cursor-pointer dark:border-zinc-700 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)]">
                                        <svg class="w-5 h-5 shrink-0 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                        <span>Tanya via WhatsApp</span>
                                    </a>
                                @else
                                    <div
                                        class="w-full py-4 bg-zinc-200 text-zinc-500 border-3 border-black font-black text-base uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] rounded-xl flex items-center justify-center gap-2 cursor-not-allowed dark:bg-zinc-800 dark:text-zinc-400 dark:border-zinc-700 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]"
                                        title="Kontak WhatsApp belum tersedia">
                                        <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                        </svg>
                                        <span>Kontak WA Belum Tersedia</span>
                                    </div>
                                @endif

                                @if ($this->existingConversation)
                                    <a href="{{ route('user.chats', ['conversation' => $this->existingConversation->id]) }}"
                                        class="w-full py-4 bg-yellow-300 hover:bg-yellow-200 text-black border-3 border-black font-black text-base uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl flex items-center justify-center gap-2 cursor-pointer dark:border-zinc-700 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)]">
                                        <x-icon name="lucide-message-circle" class="w-5 h-5 stroke-[2.5]" />
                                        <span>Buka Obrolan</span>
                                    </a>
                                @else
                                    <button type="button" @click="openChatModal()"
                                        class="w-full py-4 bg-cyan-300 hover:bg-cyan-200 text-black border-3 border-black font-black text-base uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl flex items-center justify-center gap-2 cursor-pointer dark:border-zinc-700 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)]">
                                        <x-icon name="lucide-mail" class="w-5 h-5 stroke-[2.5]" />
                                        <span>Kirim Pesan Internal</span>
                                    </button>
                                @endif
                            @else
                                <div
                                    class="w-full py-4 bg-rose-100 text-rose-700 border-3 border-black font-black text-base uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] rounded-xl flex items-center justify-center gap-2 cursor-not-allowed dark:bg-rose-950/40 dark:text-rose-400 dark:border-zinc-700 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]"
                                    title="Kost ini sedang penuh">
                                    <x-icon name="lucide-ban" class="w-5 h-5 stroke-[2.5]" />
                                    <span>Kost Sedang Penuh</span>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Owner Info Card -->
                    <div class="pt-4 border-t-3 border-black text-center space-y-2 dark:border-zinc-700">
                        <p class="text-xs font-black uppercase text-zinc-500 dark:text-zinc-400">Disewakan Oleh</p>
                        <div class="flex items-stretch justify-center gap-2">
                            <p class="text-sm font-black text-black bg-zinc-100 border-2 border-black py-1.5 px-3 rounded-lg inline-flex items-center gap-1.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] flex-1 min-w-0 dark:text-white dark:bg-zinc-800 dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                                <x-icon name="lucide-user" class="w-4 h-4 text-black stroke-[2.5] shrink-0 dark:text-white" />
                                <span class="truncate" title="{{ $kost->user->name ?? 'Pemilik Kost' }}">{{ Str::limit($kost->user->name ?? 'Pemilik Kost', 20) }}</span>
                            </p>
                            @if ($kost->user)
                                <a href="{{ route('profile.owner', $kost->user) }}?from=kost&kost={{ $kost->slug }}"
                                    class="inline-flex items-center gap-1.5 text-black font-black text-xs uppercase bg-white hover:bg-zinc-100 border-2 border-black px-3 py-1.5 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all shrink-0 dark:text-white dark:bg-zinc-900 dark:hover:bg-zinc-800 dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                                    <x-icon name="lucide-building-2" class="w-3.5 h-3.5 stroke-[2.5]" />
                                    <span>Lihat Profil Pemilik</span>
                                </a>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            <!-- END RIGHT COLUMN -->
            <!-- FULL WIDTH MAP -->
            <div class="lg:col-span-3 mt-4">
                <!-- ============================================================
                    LOKASI KOST — Clean Interactive Map with Layer Controls
                    ============================================================ -->
                <div
                    class="border-4 border-black bg-white rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden isolate relative z-0 dark:border-zinc-700 dark:bg-zinc-900 dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)]">

                    <!-- Section Header -->
                    <div class="bg-yellow-300 border-b-4 border-black px-4 sm:px-6 py-4 flex items-center justify-between gap-3 dark:border-zinc-700">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-9 h-9 bg-black rounded flex items-center justify-center shrink-0">
                                <x-icon name="lucide-map-pin" class="w-5 h-5 text-yellow-300 stroke-[2.5]" />
                            </div>
                            <div class="min-w-0">
                                <h2 class="text-xl font-black text-black uppercase tracking-tight truncate">Lokasi Kost</h2>
                                <p class="text-xs font-bold text-black/70 truncate">{{ $kost->address }}, Kec.
                                    {{ $kost->district }}, Kota Bandung</p>
                            </div>
                        </div>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $kost->latitude }},{{ $kost->longitude }}"
                            target="_blank" rel="noopener noreferrer"
                            class="ml-auto inline-flex items-center gap-1.5 bg-black text-yellow-300 hover:bg-zinc-800 border-2 border-black font-black text-xs uppercase px-3.5 py-2.5 rounded-xl shadow-[3px_3px_0px_0px_rgba(255,255,255,0.4)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all shrink-0 cursor-pointer dark:border-zinc-700">
                            <x-icon name="lucide-external-link" class="w-4 h-4 stroke-[2.5]" />
                            <span class="hidden sm:inline">BUKA DI GOOGLE MAPS</span>
                            <span class="sm:hidden">BUKA MAPS</span>
                        </a>
                    </div>

                    <!-- Map Container with Clean Alpine Component -->
                    <div x-data="kostDetailMap({
                        lat: {{ (float) ($kost->latitude ?? -6.917464) }},
                        lng: {{ (float) ($kost->longitude ?? 107.619123) }},
                        title: @js($kost->name),
                        address: @js($kost->address . ', Kec. ' . $kost->district . ', Kota Bandung'),
                        googleMapsApiKey: @js($googleMapsApiKey ?? '')
                    })" class="relative">
                        <!-- Map Type Switcher Buttons -->
                        <div class="absolute top-3 left-3 z-[400] flex gap-2"
                            x-show="(map !== null || googleMap !== null)" x-cloak>
                            <button type="button" @click="switchLayer('street')"
                                :class="currentLayer === 'street' ?
                                    'bg-yellow-400 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:border-zinc-700 dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]' :
                                    'bg-white border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-zinc-100 dark:bg-zinc-900 dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] dark:hover:bg-zinc-800 dark:text-white'"
                                class="px-3.5 py-1.5 text-xs font-black uppercase border-2 rounded-lg text-black transition-all cursor-pointer"
                                title="Peta Standard">Peta</button>
                            <button type="button" @click="switchLayer('satellite')"
                                :class="currentLayer === 'satellite' ?
                                    'bg-cyan-300 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:border-zinc-700 dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]' :
                                    'bg-white border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-zinc-100 dark:bg-zinc-900 dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] dark:hover:bg-zinc-800 dark:text-white'"
                                class="px-3.5 py-1.5 text-xs font-black uppercase border-2 rounded-lg text-black transition-all cursor-pointer"
                                title="Tampilan Satelit">Satelit</button>
                        </div>

                        <!-- Map Canvas -->
                        <div x-ref="detailMap" class="w-full h-[400px] z-0 bg-zinc-100 dark:bg-zinc-800"></div>
                    </div>
                </div>
                <!-- END LOKASI KOST -->
            </div>

        </div>
        <!-- END MAIN TWO-COLUMN LAYOUT -->

    </div>

    <!-- Floating Mobile Bar Neo-Brutalist -->
    <div
        class="fixed bottom-0 left-0 right-0 bg-white border-t-4 border-black p-4 shadow-[0_-6px_0px_0px_rgba(0,0,0,1)] lg:hidden z-50 dark:bg-zinc-900 dark:border-zinc-700 dark:shadow-[0_-6px_0px_0px_rgba(255,255,255,0.25)]">
        <div class="flex items-center justify-between gap-4 max-w-7xl mx-auto">
            <div>
                <p class="text-[10px] font-black uppercase text-zinc-500 dark:text-zinc-400">Harga Sewa</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-lg font-black text-black dark:text-white">Rp
                        {{ number_format($kost->price_monthly, 0, ',', '.') }}</span>
                    <span class="text-[10px] font-bold text-black dark:text-white">{{ $rentPeriodUnit }}</span>
                </div>
            </div>

            @if (auth()->check() && auth()->id() === $kost->user_id)
                <div class="flex items-center gap-2">
                    <a href="{{ route('dashboard.kost.edit', $kost->slug) }}"
                        class="px-3 sm:px-5 py-2.5 sm:py-3 bg-yellow-300 hover:bg-yellow-200 text-black border-3 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl inline-flex items-center justify-center gap-1.5 cursor-pointer dark:border-zinc-700 dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] flex-1 min-w-0">
                        <x-icon name="lucide-pencil" class="w-4 h-4 stroke-[2.5] shrink-0" />
                        <span>Kelola Properti</span>
                    </a>
                </div>
            @else
                @php
                    $waMessageMobile = rawurlencode(
                        "Halo, saya tertarik dengan kost \"" .
                            $kost->name .
                            "\" di " .
                            $kost->district .
                            '. Apakah kamar masih tersedia?',
                    );
                @endphp
                <div class="flex items-center gap-2">
                    @if ($this->existingConversation)
                        <a href="{{ route('user.chats', ['conversation' => $this->existingConversation->id]) }}"
                            class="px-3 sm:px-4 py-2.5 sm:py-3 bg-yellow-300 hover:bg-yellow-200 text-black border-3 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl inline-flex items-center justify-center gap-1.5 cursor-pointer dark:border-zinc-700 dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] flex-1 min-w-0">
                            <x-icon name="lucide-message-circle" class="w-4 h-4 stroke-[2.5] shrink-0" />
                            <span class="truncate">Obrolan</span>
                        </a>
                    @else
                        <button type="button" @click="openChatModal()"
                            class="px-3 sm:px-4 py-2.5 sm:py-3 bg-cyan-300 hover:bg-cyan-200 text-black border-3 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl inline-flex items-center justify-center gap-1.5 cursor-pointer dark:border-zinc-700 dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] flex-1 min-w-0">
                            <x-icon name="lucide-mail" class="w-4 h-4 stroke-[2.5] shrink-0" />
                            <span class="truncate">Pesan</span>
                        </button>
                    @endif
                    @if ($hasWaNumber)
                        <a href="https://wa.me/{{ $waNumber }}?text={{ $waMessageMobile }}" target="_blank"
                            class="px-3 sm:px-5 py-2.5 sm:py-3 bg-emerald-400 hover:bg-emerald-300 text-black border-3 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl inline-flex items-center justify-center gap-1.5 cursor-pointer dark:border-zinc-700 dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] flex-1 min-w-0">
                            <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <span class="truncate hidden sm:inline">Tanya WA</span>
                            <span class="truncate sm:hidden">WA</span>
                        </a>
                    @else
                        <div class="px-5 py-3 bg-zinc-200 text-zinc-500 border-3 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] rounded-xl whitespace-nowrap inline-flex items-center gap-1.5 cursor-not-allowed dark:bg-zinc-800 dark:text-zinc-400 dark:border-zinc-700 dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]"
                            title="WhatsApp belum tersedia">
                            <svg class="w-4 h-4 shrink-0 opacity-50" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            <span>WA</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Neo-Brutalist Chat Modal -->
    <template x-teleport="body">
        <div x-show="showModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
            <!-- Backdrop -->
        <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-sm"
            @click="showModal = false"></div>

        <!-- Modal Content -->
        <div x-show="showModal" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white border-4 border-black rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] w-full max-w-lg relative z-10 flex flex-col max-h-[90vh] dark:bg-zinc-900 dark:border-zinc-700 dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)]">

            <div
                class="p-5 border-b-4 border-black flex items-center justify-between bg-yellow-300 rounded-t-xl shrink-0 dark:border-zinc-700">
                <h3 class="text-xl font-black text-black uppercase tracking-tight">Kirim Pesan ke Pemilik</h3>
                <button type="button" @click="showModal = false"
                    class="w-8 h-8 bg-white border-2 border-black rounded flex items-center justify-center text-black hover:bg-rose-400 active:translate-y-0.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none transition-all cursor-pointer dark:bg-zinc-900 dark:border-zinc-700 dark:text-white dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                    <x-icon name="lucide-x" class="w-5 h-5 stroke-[3]" />
                </button>
            </div>

            <div class="p-6 overflow-y-auto">
                @guest
                    <div class="text-center space-y-4">
                        <div
                            class="mx-auto w-16 h-16 bg-amber-300 border-3 border-black rounded-2xl flex items-center justify-center shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:border-zinc-700 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]">
                            <x-icon name="lucide-lock" class="w-8 h-8 text-black" />
                        </div>
                        <p class="text-sm font-bold text-black dark:text-white">
                            Anda harus masuk untuk mengirim pesan ke pemilik kost.
                        </p>
                        <x-brutal-button color="cyan" :href="route('login')" class="rounded-xl">
                            Masuk Sekarang
                        </x-brutal-button>
                    </div>
                @else
                @if ($kost->is_available)
                <form wire:submit.prevent="startChat" class="space-y-4">
                    <div>
                        <label class="block text-sm font-black uppercase text-black mb-1.5 dark:text-white">Nama Lengkap</label>
                        @if ($message_name)
                            <div class="w-full bg-white border-3 border-black rounded-xl px-4 py-3 text-sm font-bold text-black flex items-center justify-between gap-3 dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                                <span class="truncate" title="{{ $message_name }}">{{ Str::limit($message_name, 30) }}</span>
                                <span class="text-[10px] font-black uppercase text-zinc-500 shrink-0 dark:text-zinc-400">Dari profil Anda</span>
                            </div>
                        @else
                            <input type="text" wire:model="message_name"
                                class="w-full bg-zinc-100 border-3 border-black rounded-xl px-4 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:bg-white focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all dark:bg-zinc-800 dark:border-zinc-700 dark:text-white dark:focus:bg-zinc-900 dark:focus:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]"
                                placeholder="Masukkan nama Anda">
                            @error('message_name')
                                <span class="text-xs font-bold text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-black uppercase text-black mb-1.5 dark:text-white">Nomor WhatsApp</label>
                        @if ($message_phone)
                            <div class="w-full bg-white border-3 border-black rounded-xl px-4 py-3 text-sm font-bold text-black flex items-center justify-between gap-3 dark:bg-zinc-900 dark:border-zinc-700 dark:text-white">
                                <span class="truncate">{{ $message_phone }}</span>
                                <span class="text-[10px] font-black uppercase text-zinc-500 shrink-0 dark:text-zinc-400">Dari profil Anda</span>
                            </div>
                        @else
                            <input type="text" wire:model="message_phone"
                                inputmode="numeric" oninput="let v = this.value.replace(/[^0-9]/g, ''); if(v.startsWith('62')) v = '0' + v.slice(2); else if(v.length > 0 && v[0] !== '0') v = '0' + v; this.value = v;"
                                maxlength="16"
                                class="w-full bg-zinc-100 border-3 border-black rounded-xl px-4 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:bg-white focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all dark:bg-zinc-800 dark:border-zinc-700 dark:text-white dark:focus:bg-zinc-900 dark:focus:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]"
                                placeholder="Contoh: 081234567890">
                            @error('message_phone')
                                <span class="text-xs font-bold text-rose-500 mt-1 block">{{ $message }}</span>
                            @enderror
                            <p class="text-[10px] font-bold text-zinc-500 mt-1 dark:text-zinc-400">
                                Simpan nomor di <a href="{{ route('profile.show') }}" class="font-black underline">Profil</a> agar terisi otomatis.
                            </p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-black uppercase text-black mb-1.5 dark:text-white">Pesan Anda</label>
                        <textarea wire:model="message_body" rows="4"
                            class="w-full bg-zinc-100 border-3 border-black rounded-xl px-4 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:bg-white focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all resize-none dark:bg-zinc-800 dark:border-zinc-700 dark:text-white dark:focus:bg-zinc-900 dark:focus:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]"
                            placeholder="Tuliskan pertanyaan Anda mengenai ketersediaan kamar, fasilitas, dll..."></textarea>
                        @error('message_body')
                            <span class="text-xs font-bold text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <x-brutal-button type="submit" color="cyan" wire:loading.attr="disabled" wire:target="startChat"
                        wire:loading.class="opacity-50 cursor-not-allowed" class="w-full mt-4 rounded-xl flex items-center justify-center">
                        <span wire:loading.remove wire:target="startChat">Kirim Sekarang</span>
                        <span wire:loading.flex wire:target="startChat" class="items-center justify-center gap-2">
                            <x-icon name="lucide-loader-circle" class="animate-spin h-5 w-5 text-black" />
                            Mengirim...
                        </span>
                    </x-brutal-button>
                    </form>
                    @else
                    <div class="text-center space-y-4">
                        <div
                            class="mx-auto w-16 h-16 bg-rose-200 border-3 border-black rounded-2xl flex items-center justify-center shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:border-zinc-700 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]">
                            <x-icon name="lucide-ban" class="w-8 h-8 text-black" />
                        </div>
                        <p class="text-sm font-black text-black dark:text-white">
                            Maaf, kost ini sedang PENUH dan tidak menerima pesan baru saat ini.
                        </p>
                    </div>
                    @endif
                @endguest
            </div>
        </div>
    </template>

    <!-- Success Toast Notification Neo-Brutalist -->
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
            class="fixed bottom-24 lg:bottom-10 right-4 lg:right-10 z-[110]">
            <div
                class="bg-lime-400 border-4 border-black p-4 rounded-xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] flex items-center gap-4 max-w-sm dark:border-zinc-700 dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)]">
                <div
                    class="w-10 h-10 bg-white border-2 border-black rounded-lg flex items-center justify-center shrink-0 dark:bg-zinc-900 dark:border-zinc-700">
                    <x-icon name="lucide-check" class="w-6 h-6 text-black stroke-[3] dark:text-white" />
                </div>
                <div>
                    <h4 class="text-sm font-black text-black uppercase">Berhasil!</h4>
                    <p class="text-xs font-bold text-black mt-0.5">{{ session('success') }}</p>
                </div>
                <button @click="show = false"
                    class="text-black hover:text-rose-500 transition-colors ml-auto cursor-pointer">
                    <x-icon name="lucide-x" class="w-5 h-5 stroke-[3]" />
                </button>
            </div>
        </div>
    @endif

    <!-- Neo-Brutalist Photo Lightbox Modal -->
    <template x-teleport="body">
        <div x-show="showGalleryModal" x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[100] bg-zinc-950/98 backdrop-blur-md flex flex-col justify-between select-none"
            @keydown.escape.window="showGalleryModal = false"
            @keydown.left.window="if(showGalleryModal) prevImage()"
            @keydown.right.window="if(showGalleryModal) nextImage()">

            <!-- Top Bar -->
            <div class="w-full h-16 px-6 bg-zinc-900 border-b-2 border-zinc-800 flex items-center justify-between z-50 shrink-0">
                <!-- Left: Badge Counter -->
                <div class="bg-[#FFE500] text-black px-3 py-1 font-black border-2 border-black text-sm uppercase rounded shadow-[2px_2px_0px_0px_rgba(255,255,255,1)]">
                    FOTO <span x-text="Number(activeIndex) + 1"></span> DARI <span x-text="images.length"></span>
                </div>

                <!-- Right: Close Button -->
                <button type="button" @click="showGalleryModal = false"
                    class="px-4 py-2 bg-[#FFE500] hover:bg-yellow-400 text-black border-3 border-black font-black text-xs uppercase shadow-[4px_4px_0px_0px_rgba(255,255,255,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl flex items-center gap-2 cursor-pointer">
                    <x-icon name="lucide-x" class="w-4 h-4 stroke-[3]" />
                    <span>TUTUP GALERI</span>
                </button>
            </div>

            <!-- Main Image Container -->
            <div class="flex-1 flex items-center justify-center p-4 md:p-8 relative overflow-hidden">
                <!-- Left Arrow -->
                <button type="button" @click.stop="prevImage()"
                    class="absolute left-4 md:left-8 top-1/2 -translate-y-1/2 p-4 bg-white hover:bg-[#FFE500] text-black border-4 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer z-50 rounded-xl"
                    title="Foto Sebelumnya (Panah Kiri)">
                    <x-icon name="lucide-chevron-left" class="w-6 h-6 stroke-[3]" />
                </button>

                <!-- Active Heroic Image -->
                <img :src="images[activeIndex]"
                    class="max-h-[72vh] max-w-[85vw] w-auto h-auto object-contain border-4 border-black bg-zinc-900 shadow-[8px_8px_0px_#FFE500] transition-all duration-300 rounded-xl"
                    :alt="'Foto Kost ' + (Number(activeIndex) + 1)">

                <!-- Right Arrow -->
                <button type="button" @click.stop="nextImage()"
                    class="absolute right-4 md:right-8 top-1/2 -translate-y-1/2 p-4 bg-white hover:bg-[#FFE500] text-black border-4 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer z-50 rounded-xl"
                    title="Foto Selanjutnya (Panah Kanan)">
                    <x-icon name="lucide-chevron-right" class="w-6 h-6 stroke-[3]" />
                </button>
            </div>

            <!-- Dedicated Bottom Thumbnail Dock -->
            <div class="w-full h-24 bg-zinc-900 border-t-2 border-zinc-800 flex items-center justify-center gap-3 px-6 overflow-x-auto shrink-0 z-50">
                <template x-for="(img, idx) in images" :key="idx">
                    <button type="button" @click="activeIndex = Number(idx)"
                        :class="Number(activeIndex) === Number(idx) ? 'border-[#FFE500] border-4 scale-105 shadow-[4px_4px_0px_0px_#FFE500] opacity-100' : 'border-zinc-700 opacity-50 hover:opacity-80'"
                        class="h-16 w-24 shrink-0 cursor-pointer overflow-hidden border-2 transition-all duration-200 rounded-lg bg-zinc-800">
                        <img :src="img" class="w-full h-full object-cover">
                    </button>
                </template>
            </div>
        </div>
    </template>

</div>

<!-- Encapsulated JavaScript function for Map Component to prevent DOM text leakage -->
<script>
    function kostDetailMap(config) {
        return {
            map: null,
            googleMap: null,
            leafletMarker: null,
            googleMarker: null,
            currentLayer: 'street',
            layers: {},
            activeLeafletLayer: null,
            themeObserver: null,

            init() {
                this.initDetailMap();

                // Re-skin the map whenever the site theme (dark/light) changes
                this.themeObserver = new MutationObserver(() => {
                    if (this.googleMap) {
                        this.googleMap.setOptions({
                            styles: this.isDarkMode() ? (window.KOST_DARK_MAP_STYLES || null) : null
                        });
                    } else if (this.map && this.activeLeafletLayer && this.currentLayer === 'street') {
                        const want = this.isDarkMode() ? this.layers.streetDark : this.layers.street;
                        if (this.activeLeafletLayer !== want) {
                            this.map.removeLayer(this.activeLeafletLayer);
                            this.activeLeafletLayer = want;
                            want.addTo(this.map);
                        }
                    }
                });
                this.themeObserver.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['class'],
                });
            },

            isDarkMode() {
                return document.documentElement.classList.contains('dark');
            },

            getHouseIcon(layerType) {
                const bg = layerType === 'satellite' ? '#22D3EE' : '#FACC15';
                return L.divIcon({
                    html: `<div style="width:36px;height:36px;background:${bg};border:2.5px solid #000;border-radius:10px;box-shadow:3px 3px 0 #000;display:flex;align-items:center;justify-content:center;">
                        <svg width="20" height="20" fill="none" stroke="black" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        </svg>
                    </div>`,
                    iconSize: [36, 36],
                    iconAnchor: [18, 18],
                    className: ''
                });
            },

            getGoogleHouseIcon(layerType) {
                const bg = layerType === 'satellite' ? '#22D3EE' : '#FACC15';
                const houseSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="none"><rect x="2" y="2" width="32" height="32" rx="8" fill="${bg}" stroke="#000000" stroke-width="2.5"/><g transform="translate(6, 6)"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8" stroke="#000000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" stroke="#000000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></g></svg>`;
                return {
                    url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(houseSvg),
                    scaledSize: new google.maps.Size(36, 36),
                    anchor: new google.maps.Point(18, 18)
                };
            },

            initDetailMap() {
                const lat = config.lat;
                const lng = config.lng;
                const kostTitle = config.title;
                const kostAddress = config.address;
                const mapsUrl = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
                const hasGoogleKey = config.googleMapsApiKey;

                let leafletRetries = 0;
                const LEAFLET_MAX_RETRIES = 5;

                const initLeaflet = () => {
                    if (this.map || typeof L === 'undefined') return;

                    this.map = L.map(this.$refs.detailMap, {
                        center: [lat, lng],
                        zoom: 16,
                        zoomControl: true,
                        attributionControl: true,
                    });

                    // Define tile layers
                    this.layers.street = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© <a href="https://www.openstreetmap.org">OpenStreetMap</a>'
                    });

                    this.layers.streetDark = L.tileLayer(
                        'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
                            maxZoom: 20,
                            subdomains: 'abcd',
                            attribution: '© <a href="https://www.openstreetmap.org">OpenStreetMap</a> © <a href="https://carto.com/attributions">CARTO</a>'
                        });

                    this.layers.satellite = L.tileLayer(
                        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                            maxZoom: 19,
                            attribution: '© Esri, DigitalGlobe, GeoEye'
                        });

                    this.layers.terrain = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                        maxZoom: 17,
                        attribution: '© <a href="https://opentopomap.org">OpenTopoMap</a>'
                    });

                    // Start with street layer (theme-aware)
                    const startLayer = this.isDarkMode() ? this.layers.streetDark : this.layers.street;
                    this.activeLeafletLayer = startLayer;
                    startLayer.addTo(this.map);
                    this.currentLayer = 'street';

                    this.leafletMarker = L.marker([lat, lng], {
                        icon: this.getHouseIcon('street')
                    }).addTo(this.map);
                };

                const loadLeafletAndInit = () => {
                    if (typeof L !== 'undefined') {
                        leafletRetries = 0;
                        initLeaflet();
                        return;
                    }
                    if (leafletRetries >= LEAFLET_MAX_RETRIES) {
                        if (!this.map) window.dispatchEvent(new Event('map-load-error'));
                        return;
                    }
                    if (!document.getElementById('leaflet-detail-css')) {
                        const link = document.createElement('link');
                        link.id = 'leaflet-detail-css';
                        link.rel = 'stylesheet';
                        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                        document.head.appendChild(link);
                    }
                    if (!document.getElementById('leaflet-detail-js')) {
                        const script = document.createElement('script');
                        script.id = 'leaflet-detail-js';
                        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                        script.onload = () => {
                            leafletRetries = 0;
                            initLeaflet();
                        };
                        script.onerror = () => {
                            const el = document.getElementById('leaflet-detail-js');
                            if (el) el.remove();
                            window.dispatchEvent(new Event('map-load-error'));
                        };
                        document.head.appendChild(script);
                    } else {
                        leafletRetries++;
                        setTimeout(() => loadLeafletAndInit(), 200);
                    }
                };

                // Use Google Maps if API key exists, with Leaflet fallback
                if (hasGoogleKey && !window.google?.maps) {
                    if (!document.getElementById('google-detail-map-script')) {
                        window.initGoogleDetailMap = () => {
                            const mapEl = this.$refs.detailMap;
                            const gmap = new google.maps.Map(mapEl, {
                                center: {
                                    lat,
                                    lng
                                },
                                zoom: 16.5,
                                mapTypeControl: false,
                                streetViewControl: false,
                                fullscreenControl: false,
                                styles: this.isDarkMode() ? (window.KOST_DARK_MAP_STYLES || null) : null,
                            });
                            this.googleMap = gmap;
                            
                            this.googleMarker = new google.maps.Marker({
                                position: {
                                    lat,
                                    lng
                                },
                                map: gmap,
                                title: kostTitle,
                                icon: this.getGoogleHouseIcon('street')
                            });
                        };
                        const s = document.createElement('script');
                        s.id = 'google-detail-map-script';
                        s.src =
                            `https://maps.googleapis.com/maps/api/js?key=${hasGoogleKey}&callback=initGoogleDetailMap`;
                        s.async = true;
                        s.defer = true;
                        s.onerror = () => loadLeafletAndInit();
                        document.head.appendChild(s);
                    }
                    setTimeout(() => {
                        if (!this.map && typeof google === 'undefined') loadLeafletAndInit();
                    }, 3000);
                } else if (hasGoogleKey && window.google?.maps) {
                    // Google Maps already loaded
                    window.initGoogleDetailMap && window.initGoogleDetailMap();
                } else {
                    loadLeafletAndInit();
                }
            },

            switchLayer(type) {
                if (this.currentLayer === type) return;

                if (this.googleMap) {
                    if (type === 'street') this.googleMap.setMapTypeId('roadmap');
                    if (type === 'satellite') this.googleMap.setMapTypeId('satellite');
                    if (type === 'terrain') this.googleMap.setMapTypeId('terrain');
                    this.googleMap.setOptions({
                        styles: this.isDarkMode() ? (window.KOST_DARK_MAP_STYLES || null) : null
                    });
                    if (this.googleMarker) {
                        this.googleMarker.setIcon(this.getGoogleHouseIcon(type));
                    }
                } else if (this.map && this.activeLeafletLayer && this.layers[type]) {
                    this.map.removeLayer(this.activeLeafletLayer);
                    const nextLayer = type === 'street'
                        ? (this.isDarkMode() ? this.layers.streetDark : this.layers.street)
                        : this.layers[type];
                    this.activeLeafletLayer = nextLayer;
                    nextLayer.addTo(this.map);
                    if (this.leafletMarker) {
                        this.leafletMarker.setIcon(this.getHouseIcon(type));
                    }
                }
                
                this.currentLayer = type;
            }
        };
    }
</script>
@else
    <div
        class="min-h-screen bg-[#f8f9fa] bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] bg-[size:24px_24px] pt-20 pb-28 lg:pb-16 dark:bg-zinc-950 dark:bg-[linear-gradient(to_right,#27272a_1px,transparent_1px),linear-gradient(to_bottom,#27272a_1px,transparent_1px)]">
        <div class="max-w-2xl mx-auto px-4 sm:px-6">
            <div
                class="bg-white border-4 border-black rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] p-8 sm:p-10 text-center space-y-6 dark:bg-zinc-900 dark:border-zinc-700 dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)]">
                <div
                    class="mx-auto w-20 h-20 bg-rose-200 border-3 border-black rounded-2xl flex items-center justify-center shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:border-zinc-700 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]">
                    <x-icon name="lucide-trash-2" class="w-10 h-10 text-black" />
                </div>
                <div class="space-y-2">
                    <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-tight text-black dark:text-white">Kost Tidak Tersedia</h1>
                    <p class="text-sm font-bold text-black/80 dark:text-white/80">
                        Kost ini telah dihapus oleh pemilik dan tidak lagi tersedia.
                    </p>
                </div>
                <a href="{{ $backUrl }}"
                    class="inline-flex items-center gap-2 bg-cyan-300 text-black border-3 border-black font-black text-xs uppercase px-6 py-3 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer dark:border-zinc-700 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]">
                    <x-icon name="lucide-arrow-left" class="w-4 h-4 stroke-[3]" />
                    <span>{{ $backLabel }}</span>
                </a>
            </div>
        </div>
    </div>
@endif
