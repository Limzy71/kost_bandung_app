@php
    $allImages = $kost->images->map(function ($img) {
        return Str::startsWith($img->image_path ?? '', 'http')
            ? $img->image_path
            : Storage::url($img->image_path);
    })->values();

    if ($allImages->isEmpty()) {
        $allImages = collect(['https://placehold.co/800x500/eeeeee/31343c?text=Foto+Utama']);
    }

    $waNumber = preg_replace('/\D+/', '', $kost->whatsapp_contact ?: ($kost->user->phone_number ?? ''));
    if (Str::startsWith($waNumber, '0')) {
        $waNumber = '62' . Str::substr($waNumber, 1);
    }
    if ($waNumber === '' || ! Str::startsWith($waNumber, '62')) {
        $waNumber = '62' . $waNumber;
    }
    if ($waNumber === '62') {
        $waNumber = '6281234567890';
    }

    $rentPeriodLabels = [
        'daily' => 'Per Hari',
        'weekly' => 'Per Minggu',
        'monthly' => 'Per Bulan',
        'yearly' => 'Per Tahun',
    ];
    $rentPeriodLabel = $rentPeriodLabels[$kost->rent_period] ?? 'Per Bulan';
@endphp

<div class="min-h-screen bg-[#f8f9fa] bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] bg-[size:24px_24px] pb-28 lg:pb-16 pt-8"
    x-data="{ 
        showModal: false, 
        showGalleryModal: false, 
        activeIndex: 0, 
        totalImages: {{ $allImages->count() }}, 
        images: {{ Js::from($allImages) }},
        nextImage() {
            let current = parseInt(this.activeIndex, 10);
            let total   = parseInt(this.totalImages, 10);
            this.activeIndex = (current >= total - 1) ? 0 : (current + 1);
        },
        prevImage() {
            let current = parseInt(this.activeIndex, 10);
            let total   = parseInt(this.totalImages, 10);
            this.activeIndex = (current <= 0) ? (total - 1) : (current - 1);
        }
    }" 
    x-effect="document.body.style.overflow = (showGalleryModal || showModal) ? 'hidden' : ''"
    @inquiry-sent.window="showModal = false">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Navigation Back Button -->
        <div class="flex items-center justify-between">
            <a href="{{ $backUrl }}"
                class="inline-flex items-center gap-2 bg-white text-black border-3 border-black font-black text-xs uppercase px-4 py-2.5 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer">
                <x-icon name="lucide-chevron-left" class="w-4 h-4 stroke-[3]" />
                <span>{{ $backLabel }}</span>
            </a>
        </div>

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
                        class="md:col-span-2 relative group rounded-2xl overflow-hidden border-4 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] bg-zinc-200 aspect-video md:aspect-auto md:h-96 cursor-pointer">
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
                            class="absolute bottom-4 right-4 px-4 py-2 bg-yellow-300 hover:bg-yellow-400 text-black border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg flex items-center gap-2 cursor-pointer z-10">
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
                                class="flex-1 min-h-0 rounded-xl overflow-hidden border-3 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] bg-zinc-200 relative group cursor-pointer">
                                <img src="{{ $thumbSrc }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    alt="Foto {{ $i + 2 }}">
                                @if ($i === 2 && $kost->images->count() > 4)
                                    <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                        <span
                                            class="text-white font-black text-2xl">+{{ $kost->images->count() - 4 }}</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- END PHOTO GALLERY -->

                <!-- MAIN CONTENT CARD -->
                <div
                    class="bg-white border-4 border-black p-6 sm:p-8 rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] space-y-8">

                    <!-- Badges & Title -->
                    <div class="space-y-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                class="px-3.5 py-1 bg-pink-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider">
                                Tipe {{ $kost->gender_type }}
                            </span>

                            <span
                                class="px-3.5 py-1 bg-cyan-300 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider">
                                Sewa {{ $rentPeriodLabel }}
                            </span>

                            @if ($kost->is_available)
                                <span
                                    class="px-3.5 py-1 bg-lime-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider">
                                    ✓ Kamar Tersedia
                                </span>
                            @else
                                <span
                                    class="px-3.5 py-1 bg-rose-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider">
                                    ✕ Kamar Penuh
                                </span>
                            @endif

                            @if ($kost->boosted_at)
                                <span
                                    class="px-3.5 py-1 bg-yellow-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider inline-flex items-center gap-1.5">
                                    <x-icon name="lucide-zap" fill="#FBBF24" stroke-width="0.8" class="w-4 h-4 shrink-0 drop-shadow-[1px_1px_0px_rgba(0,0,0,1)]" />
                                    <span>Properti Rekomendasi</span>
                                </span>
                            @endif
                        </div>

                        <h1 class="text-3xl sm:text-5xl font-black text-black tracking-tight uppercase leading-tight">
                            {{ $kost->name }}
                        </h1>

                        <div class="flex items-start gap-2 text-zinc-700 text-sm sm:text-base font-bold">
                            <x-icon name="lucide-map-pin" class="w-5 h-5 text-black shrink-0 stroke-[2.5] mt-0.5" />
                            <span>{{ $kost->address }}, Kecamatan {{ $kost->district }}, Kota Bandung</span>
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t-4 border-black"></div>

                    <!-- Deskripsi -->
                    <div class="space-y-3">
                        <h2 class="text-xl font-black text-black uppercase tracking-tight flex items-center gap-2">
                            <x-icon name="lucide-file-text" class="w-5 h-5 text-black stroke-[2.5]" />
                            <span>Tentang Kost Ini</span>
                        </h2>
                        <p class="leading-relaxed text-zinc-700 font-medium text-base sm:text-lg">
                            {{ $kost->description ?? 'Kost nyaman dengan fasilitas modern dan lokasi strategis di Bandung. Cocok untuk Anda yang memiliki mobilitas tinggi namun tetap menginginkan hunian yang tenang dan asri.' }}
                        </p>
                    </div>

                    <!-- Divider -->
                    <div class="border-t-4 border-black"></div>

                    <!-- Info Properti -->
                    @php
                        $infoItems = [];
                        $infoItems[] = ['label' => 'Periode Sewa', 'value' => $rentPeriodLabel];
                        if ($kost->price_deposit !== null) {
                            $infoItems[] = ['label' => 'Uang Deposit', 'value' => 'Rp ' . number_format((float) $kost->price_deposit, 0, ',', '.')];
                        }
                        $infoItems[] = ['label' => 'Listrik & Air', 'value' => $kost->include_utilities ? 'Sudah Termasuk' : 'Terpisah / Diluar Sewa'];
                        if ($kost->nearby_landmarks) {
                            $infoItems[] = ['label' => 'Titik Terdekat', 'value' => $kost->nearby_landmarks];
                        }
                        $infoItems[] = ['label' => 'Ketersediaan Kamar', 'value' => $kost->available_rooms . ' dari ' . $kost->total_rooms . ' kamar tersedia'];
                    @endphp
                    <div class="space-y-4">
                        <h2 class="text-xl font-black text-black uppercase tracking-tight flex items-center gap-2">
                            <x-icon name="lucide-info" class="w-5 h-5 text-black stroke-[2.5]" />
                            <span>Info Properti</span>
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach ($infoItems as $item)
                                <div
                                    class="bg-cyan-50 border-2 border-black rounded-xl px-4 py-3 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                    <p class="text-[10px] font-black uppercase tracking-wider text-zinc-500">
                                        {{ $item['label'] }}</p>
                                    <p class="text-sm font-black text-black mt-0.5">{{ $item['value'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Divider -->
                    <div class="border-t-4 border-black"></div>

                    <!-- Fasilitas Utama -->
                    <div class="space-y-6">
                        <h2 class="text-xl font-black text-black uppercase tracking-tight flex items-center gap-2">
                            <x-icon name="lucide-sparkles" class="w-5 h-5 text-black stroke-[2.5]" />
                            <span>Fasilitas Properti</span>
                        </h2>

                        @php
                            $roomFacilities = $kost->facilities->where('type', 'room');
                            $buildingFacilities = $kost->facilities->where('type', 'building');
                            $parkingFacilities = $kost->facilities->where('type', 'parking');
                        @endphp

                        @if ($roomFacilities->count() > 0)
                            <div class="space-y-3">
                                <h3 class="text-sm font-black uppercase tracking-wider text-black flex items-center gap-2">
                                    <x-icon name="lucide-user" class="w-4 h-4 text-black stroke-[2.5]" />
                                    Fasilitas Kamar
                                    <span
                                        class="text-[10px] font-black uppercase bg-lime-300 border-2 border-black px-2 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                                        {{ $roomFacilities->count() }}
                                    </span>
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach ($roomFacilities as $facility)
                                        <div
                                            class="flex items-center gap-2.5 bg-lime-100 border-2 border-black px-3.5 py-2.5 rounded-xl text-sm font-black text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                            <span class="text-lime-600 font-extrabold text-base">✓</span>
                                            <span>{{ $facility->name }}</span>
                                            @if (auth()->check() && auth()->id() === $kost->user_id && $facility->status === 'pending' && $facility->user_id === auth()->id())
                                                <span
                                                    class="ml-auto inline-flex items-center gap-1.5 text-[9px] font-black uppercase bg-amber-300 border-2 border-black px-1.5 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                                                    Menunggu review
                                                    <button type="button" wire:click="removeFacility({{ $facility->id }})"
                                                        wire:confirm="Hapus fasilitas '{{ $facility->name }}' dari kost ini?"
                                                        class="w-4 h-4 rounded bg-rose-500 hover:bg-rose-400 border border-black text-white text-[9px] font-black leading-none flex items-center justify-center cursor-pointer"
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
                                <h3 class="text-sm font-black uppercase tracking-wider text-black flex items-center gap-2">
                                    <x-icon name="lucide-building-2" class="w-4 h-4 text-black stroke-[2.5]" />
                                    Fasilitas Umum
                                    <span
                                        class="text-[10px] font-black uppercase bg-cyan-300 border-2 border-black px-2 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                                        {{ $buildingFacilities->count() }}
                                    </span>
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach ($buildingFacilities as $facility)
                                        <div
                                            class="flex items-center gap-2.5 bg-cyan-50 border-2 border-black px-3.5 py-2.5 rounded-xl text-sm font-black text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                            <span class="text-cyan-600 font-extrabold text-base">✓</span>
                                            <span>{{ $facility->name }}</span>
                                            @if (auth()->check() && auth()->id() === $kost->user_id && $facility->status === 'pending' && $facility->user_id === auth()->id())
                                                <span
                                                    class="ml-auto inline-flex items-center gap-1.5 text-[9px] font-black uppercase bg-amber-300 border-2 border-black px-1.5 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                                                    Menunggu review
                                                    <button type="button" wire:click="removeFacility({{ $facility->id }})"
                                                        wire:confirm="Hapus fasilitas '{{ $facility->name }}' dari kost ini?"
                                                        class="w-4 h-4 rounded bg-rose-500 hover:bg-rose-400 border border-black text-white text-[9px] font-black leading-none flex items-center justify-center cursor-pointer"
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
                                <h3 class="text-sm font-black uppercase tracking-wider text-black flex items-center gap-2">
                                    <x-icon name="lucide-square-parking" class="w-4 h-4 text-black stroke-[2.5]" />
                                    Fasilitas Parkir
                                    <span class="text-[10px] font-black uppercase bg-yellow-300 border-2 border-black px-2 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                                        {{ $parkingFacilities->count() }}
                                    </span>
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach ($parkingFacilities as $facility)
                                        <div class="flex items-center gap-2.5 bg-yellow-50 border-2 border-black px-3.5 py-2.5 rounded-xl text-sm font-black text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                            <span class="text-yellow-600 font-extrabold text-base">✓</span>
                                            <span>{{ $facility->name }}</span>
                                            @if (auth()->check() && auth()->id() === $kost->user_id && $facility->status === 'pending' && $facility->user_id === auth()->id())
                                                <span
                                                    class="ml-auto inline-flex items-center gap-1.5 text-[9px] font-black uppercase bg-amber-300 border-2 border-black px-1.5 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                                                    Menunggu review
                                                    <button type="button" wire:click="removeFacility({{ $facility->id }})"
                                                        wire:confirm="Hapus fasilitas '{{ $facility->name }}' dari kost ini?"
                                                        class="w-4 h-4 rounded bg-rose-500 hover:bg-rose-400 border border-black text-white text-[9px] font-black leading-none flex items-center justify-center cursor-pointer"
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
                            <p class="text-zinc-500 font-bold">Tidak ada fasilitas terdaftar.</p>
                        @endif
                    </div>

                    <!-- Divider -->
                    <div class="border-t-4 border-black"></div>

                    <!-- Aturan Kost -->
                    <div class="space-y-4">
                        <h2 class="text-xl font-black text-black uppercase tracking-tight flex items-center gap-2">
                            <x-icon name="lucide-triangle-alert" class="w-5 h-5 text-black stroke-[2.5]" />
                            <span>Aturan Properti</span>
                        </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @forelse($kost->rules as $rule)
                                <div
                                    class="flex items-start gap-3 bg-zinc-100 border-2 border-black p-3.5 rounded-xl text-sm font-bold text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                    <x-icon name="lucide-check" class="w-4 h-4 text-pink-600 shrink-0 stroke-[2.5] mt-0.5" />
                                    <span>{{ $rule->name }}</span>
                                </div>
                            @empty
                                <p class="text-zinc-500 font-bold">Tidak ada aturan khusus.</p>
                            @endforelse
                        </div>

                        @if ($kost->additional_rules_note)
                            <div
                                class="bg-amber-50 border-2 border-black rounded-xl p-4 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                <p class="text-[10px] font-black uppercase tracking-wider text-zinc-500">Aturan
                                    Tambahan</p>
                                <p class="text-sm font-bold text-black mt-0.5">{{ $kost->additional_rules_note }}</p>
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
                    class="sticky top-24 w-full bg-white border-4 border-black rounded-2xl p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-1 hover:translate-x-0.5 hover:shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] transition-all duration-300 ease-out hidden lg:block space-y-6">

                    <!-- Display Harga -->
                    <div
                        class="bg-yellow-300 border-3 border-black p-4 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-1">
                        <p class="text-xs font-black uppercase tracking-wider text-black">Harga Sewa Bulanan</p>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-black text-black tracking-tight">
                                Rp {{ number_format($kost->price_monthly, 0, ',', '.') }}
                            </span>
                            <span class="text-xs font-black text-black">/ bulan</span>
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div class="space-y-3">
                        @php
                            $waMessage = rawurlencode(
                                "Halo, saya tertarik dengan kost \"" .
                                    $kost->name .
                                    "\" di " .
                                    $kost->district .
                                    '. Apakah kamar masih tersedia?',
                            );
                        @endphp

                        <a href="https://wa.me/{{ $waNumber }}?text={{ $waMessage }}" target="_blank"
                            class="w-full py-4 bg-emerald-400 hover:bg-emerald-300 text-black border-3 border-black font-black text-base uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl flex items-center justify-center gap-2 group cursor-pointer">
                            <x-icon name="lucide-message-square" class="w-5 h-5 text-black stroke-[2.5] group-hover:scale-110 transition-transform" />
                            <span>Tanya via WhatsApp</span>
                        </a>

                        <button type="button" @click="showModal = true"
                            class="w-full py-4 bg-cyan-300 hover:bg-cyan-200 text-black border-3 border-black font-black text-base uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl flex items-center justify-center gap-2 cursor-pointer">
                            <x-icon name="lucide-mail" class="w-5 h-5 stroke-[2.5]" />
                            <span>Kirim Pesan Internal</span>
                        </button>
                    </div>

                    <!-- Owner Info Card -->
                    <div class="pt-4 border-t-3 border-black text-center space-y-1">
                        <p class="text-xs font-black uppercase text-zinc-500">Disewakan Oleh</p>
                        <p
                            class="text-sm font-black text-black bg-zinc-100 border-2 border-black py-1.5 px-3 rounded-lg inline-flex items-center gap-1.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            <x-icon name="lucide-user" class="w-4 h-4 text-black stroke-[2.5]" />
                            <span>{{ $kost->user->name ?? 'Pemilik Kost' }}</span>
                        </p>
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
                    class="border-4 border-black bg-white rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden">

                    <!-- Section Header -->
                    <div class="bg-yellow-300 border-b-4 border-black px-6 py-4 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 bg-black rounded flex items-center justify-center shrink-0">
                                <x-icon name="lucide-map-pin" class="w-5 h-5 text-yellow-300 stroke-[2.5]" />
                            </div>
                            <div class="truncate">
                                <h2 class="text-xl font-black text-black uppercase tracking-tight truncate">Lokasi Kost</h2>
                                <p class="text-xs font-bold text-black/70 truncate">{{ $kost->address }}, Kec.
                                    {{ $kost->district }}, Kota Bandung</p>
                            </div>
                        </div>
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $kost->latitude }},{{ $kost->longitude }}"
                            target="_blank" rel="noopener noreferrer"
                            class="ml-auto inline-flex items-center gap-1.5 bg-black text-yellow-300 hover:bg-zinc-800 border-2 border-black font-black text-xs uppercase px-3.5 py-2.5 rounded-xl shadow-[3px_3px_0px_0px_rgba(255,255,255,0.4)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all shrink-0 cursor-pointer">
                            <x-icon name="lucide-external-link" class="w-4 h-4 stroke-[2.5]" />
                            <span>BUKA DI GOOGLE MAPS</span>
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
                                    'bg-yellow-400 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]' :
                                    'bg-white border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-zinc-100'"
                                class="px-3.5 py-1.5 text-xs font-black uppercase border-2 rounded-lg text-black transition-all cursor-pointer"
                                title="Peta Standard">Peta</button>
                            <button type="button" @click="switchLayer('satellite')"
                                :class="currentLayer === 'satellite' ?
                                    'bg-cyan-300 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]' :
                                    'bg-white border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:bg-zinc-100'"
                                class="px-3.5 py-1.5 text-xs font-black uppercase border-2 rounded-lg text-black transition-all cursor-pointer"
                                title="Tampilan Satelit">Satelit</button>
                        </div>

                        <!-- Map Canvas -->
                        <div x-ref="detailMap" class="w-full h-[400px] z-0 bg-zinc-100"></div>
                    </div>
                </div>
                <!-- END LOKASI KOST -->
            </div>

        </div>
        <!-- END MAIN TWO-COLUMN LAYOUT -->

    </div>

    <!-- Floating Mobile Bar Neo-Brutalist -->
    <div
        class="fixed bottom-0 left-0 right-0 bg-white border-t-4 border-black p-4 shadow-[0_-6px_0px_0px_rgba(0,0,0,1)] lg:hidden z-50">
        <div class="flex items-center justify-between gap-4 max-w-7xl mx-auto">
            <div>
                <p class="text-[10px] font-black uppercase text-zinc-500">Harga Sewa</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-lg font-black text-black">Rp
                        {{ number_format($kost->price_monthly, 0, ',', '.') }}</span>
                    <span class="text-[10px] font-bold text-black">/bln</span>
                </div>
            </div>

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
                <button type="button" @click="showModal = true"
                    class="px-4 py-3 bg-cyan-300 hover:bg-cyan-200 text-black border-3 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl whitespace-nowrap cursor-pointer">
                    Pesan
                </button>
                <a href="https://wa.me/{{ $waNumber }}?text={{ $waMessageMobile }}" target="_blank"
                    class="px-5 py-3 bg-emerald-400 hover:bg-emerald-300 text-black border-3 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl whitespace-nowrap inline-flex items-center gap-1.5 cursor-pointer">
                    <x-icon name="lucide-message-square" class="w-4 h-4 stroke-[2.5]" />
                    <span>Tanya WA</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Neo-Brutalist Inquiry Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
        <!-- Backdrop -->
        <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-sm"
            @click="showModal = false"></div>

        <!-- Modal Content -->
        <div x-show="showModal" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white border-4 border-black rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] w-full max-w-lg relative z-10 flex flex-col max-h-[90vh]">

            <div
                class="p-5 border-b-4 border-black flex items-center justify-between bg-yellow-300 rounded-t-xl shrink-0">
                <h3 class="text-xl font-black text-black uppercase tracking-tight">Kirim Pesan ke Pemilik</h3>
                <button type="button" @click="showModal = false"
                    class="w-8 h-8 bg-white border-2 border-black rounded flex items-center justify-center text-black hover:bg-rose-400 active:translate-y-0.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none transition-all cursor-pointer">
                    <x-icon name="lucide-x" class="w-5 h-5 stroke-[3]" />
                </button>
            </div>

            <div class="p-6 overflow-y-auto">
                <form wire:submit.prevent="sendInquiry" class="space-y-4">
                    <div>
                        <label class="block text-sm font-black uppercase text-black mb-1.5">Nama Lengkap</label>
                        <input type="text" wire:model="inquiry_name"
                            class="w-full bg-zinc-100 border-3 border-black rounded-xl px-4 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:bg-white focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all"
                            placeholder="Masukkan nama Anda">
                        @error('inquiry_name')
                            <span class="text-xs font-bold text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-black uppercase text-black mb-1.5">Nomor WhatsApp</label>
                        <input type="text" wire:model="inquiry_phone"
                            class="w-full bg-zinc-100 border-3 border-black rounded-xl px-4 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:bg-white focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all"
                            placeholder="Contoh: 081234567890">
                        @error('inquiry_phone')
                            <span class="text-xs font-bold text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-black uppercase text-black mb-1.5">Pesan Anda</label>
                        <textarea wire:model="inquiry_message" rows="4"
                            class="w-full bg-zinc-100 border-3 border-black rounded-xl px-4 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:bg-white focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all resize-none"
                            placeholder="Tuliskan pertanyaan Anda mengenai ketersediaan kamar, fasilitas, dll..."></textarea>
                        @error('inquiry_message')
                            <span class="text-xs font-bold text-rose-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" wire:loading.attr="disabled" wire:target="sendInquiry"
                        wire:loading.class="opacity-50 cursor-not-allowed"
                        class="w-full mt-4 py-3.5 bg-cyan-400 hover:bg-cyan-300 text-black border-3 border-black font-black text-sm uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl flex items-center justify-center gap-2 cursor-pointer">
                        <span wire:loading.remove wire:target="sendInquiry">Kirim Sekarang</span>
                        <span wire:loading wire:target="sendInquiry" class="flex items-center gap-2">
                            <x-icon name="lucide-loader-circle" class="animate-spin h-5 w-5 text-black" />
                            Mengirim...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Toast Notification Neo-Brutalist -->
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition
            class="fixed bottom-24 lg:bottom-10 right-4 lg:right-10 z-[110]">
            <div
                class="bg-lime-400 border-4 border-black p-4 rounded-xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] flex items-center gap-4 max-w-sm">
                <div
                    class="w-10 h-10 bg-white border-2 border-black rounded-lg flex items-center justify-center shrink-0">
                    <x-icon name="lucide-check" class="w-6 h-6 text-black stroke-[3]" />
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
                FOTO <span x-text="Number(activeIndex) + 1"></span> DARI <span x-text="totalImages"></span>
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
                class="max-h-[72vh] max-w-[85vw] w-auto h-auto object-contain border-4 border-black bg-zinc-900 shadow-[8px_8px_0px_0px_#FFE500] transition-all duration-300 rounded-xl"
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

            init() {
                this.initDetailMap();
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
                const houseSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="none"><rect x="2" y="2" width="32" height="32" rx="8" fill="${bg}" stroke="#000000" stroke-width="2.5"/><path d="M19 31v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8" stroke="#000000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M7 20a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 25 20v9a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2z" stroke="#000000" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>`;
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

                    this.layers.satellite = L.tileLayer(
                        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                            maxZoom: 19,
                            attribution: '© Esri, DigitalGlobe, GeoEye'
                        });

                    this.layers.terrain = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
                        maxZoom: 17,
                        attribution: '© <a href="https://opentopomap.org">OpenTopoMap</a>'
                    });

                    // Start with street layer
                    this.layers.street.addTo(this.map);
                    this.currentLayer = 'street';

                    this.leafletMarker = L.marker([lat, lng], {
                        icon: this.getHouseIcon('street')
                    }).addTo(this.map);
                };

                const loadLeafletAndInit = () => {
                    if (typeof L !== 'undefined') {
                        initLeaflet();
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
                        script.onload = () => initLeaflet();
                        document.head.appendChild(script);
                    } else {
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
                    if (this.googleMarker) {
                        this.googleMarker.setIcon(this.getGoogleHouseIcon(type));
                    }
                } else if (this.map && this.layers[this.currentLayer] && this.layers[type]) {
                    this.map.removeLayer(this.layers[this.currentLayer]);
                    this.layers[type].addTo(this.map);
                    if (this.leafletMarker) {
                        this.leafletMarker.setIcon(this.getHouseIcon(type));
                    }
                }
                
                this.currentLayer = type;
            }
        };
    }
</script>
