<div 
    x-data 
    x-init="
        const urlParams = new URLSearchParams(window.location.search);
        const page = urlParams.get('page');
        if (page && parseInt(page) > 1) {
            setTimeout(() => {
                const el = document.getElementById('home-list-section');
                if (el) {
                    const targetY = el.getBoundingClientRect().top + window.pageYOffset - 100;
                    window.scrollTo({ top: Math.max(0, targetY), behavior: 'smooth' });
                }
            }, 100);
        } else {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    "
    wire:poll.10s
    @scroll-to-home-list.window="
        setTimeout(() => {
            const el = document.getElementById('home-list-section');
            if (el) {
                const targetY = el.getBoundingClientRect().top + window.pageYOffset - 100;
                window.scrollTo({ top: Math.max(0, targetY), behavior: 'smooth' });
            }
        }, 50);
    "
>
    <!-- Floating Filter Pill -->
    <div class="relative z-20 bg-white shadow-sm rounded-2xl md:rounded-full border border-gray-200 mb-16 p-2">
        <div class="flex flex-col md:flex-row items-center divide-y md:divide-y-0 md:divide-x divide-gray-100 w-full">
            <!-- Search Input -->
            <div class="w-full md:w-1/3 relative flex items-center px-4 py-2">
                <svg class="h-5 w-5 text-gray-400 flex-shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="9" r="6"></circle>
                    <path d="M15 15l4 4"></path>
                </svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau jalan..."
                    class="w-full h-10 pl-3 pr-2 bg-transparent border-none focus:ring-0 text-gray-950 placeholder-gray-400 outline-none">
            </div>

            <!-- Gender -->
            <div class="w-full md:w-auto flex-1 px-4 py-2">
                <select wire:model.live="gender"
                    class="w-full h-10 bg-transparent border-none focus:ring-0 text-gray-950 font-medium outline-none cursor-pointer appearance-none md:bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%239CA3AF%22%20stroke-width%3D%222%22%20fill%3D%22none%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-right">
                    <option value="">Semua Tipe</option>
                    <option value="putra">Putra</option>
                    <option value="putri">Putri</option>
                    <option value="campur">Campur</option>
                </select>
            </div>

            <!-- District -->
            <div class="w-full md:w-auto flex-1 px-4 py-2">
                <select wire:model.live="district"
                    class="w-full h-10 bg-transparent border-none focus:ring-0 text-gray-950 font-medium outline-none cursor-pointer appearance-none md:bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%239CA3AF%22%20stroke-width%3D%222%22%20fill%3D%22none%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-right">
                    <option value="">Kecamatan</option>
                    @foreach ($districts as $dist)
                        <option value="{{ $dist }}">{{ $dist }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Min Price -->
            <div class="w-full md:w-auto flex-1 px-4 py-2">
                <select wire:model.live="price_min"
                    class="w-full h-10 bg-transparent border-none focus:ring-0 text-gray-950 font-medium outline-none cursor-pointer appearance-none md:bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%239CA3AF%22%20stroke-width%3D%222%22%20fill%3D%22none%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-right">
                    <option value="">Min Harga</option>
                    <option value="500000">Rp 500.000</option>
                    <option value="1000000">Rp 1.000.000</option>
                    <option value="1500000">Rp 1.500.000</option>
                    <option value="2000000">Rp 2.000.000</option>
                    <option value="3000000">Rp 3.000.000</option>
                    <option value="5000000">Rp 5.000.000</option>
                </select>
            </div>

            <!-- Max Price -->
            <div class="w-full md:w-auto flex-1 px-4 py-2">
                <select wire:model.live="price_max"
                    class="w-full h-10 bg-transparent border-none focus:ring-0 text-gray-950 font-medium outline-none cursor-pointer appearance-none md:bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%239CA3AF%22%20stroke-width%3D%222%22%20fill%3D%22none%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-right">
                    <option value="">Max Harga</option>
                    <option value="500000">Rp 500.000</option>
                    <option value="1000000">Rp 1.000.000</option>
                    <option value="1500000">Rp 1.500.000</option>
                    <option value="2000000">Rp 2.000.000</option>
                    <option value="3000000">Rp 3.000.000</option>
                    <option value="5000000">Rp 5.000.000</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Grid List Kost -->
    <div id="home-list-section" class="grid grid-cols-1 md:grid-cols-3 gap-8 relative z-10">
        <div wire:loading class="absolute inset-0 bg-white/50 backdrop-blur-sm z-20 rounded-2xl"></div>

        @forelse($kosts as $kost)
            <div class="group flex flex-col">
                <!-- Image Container -->
                <div class="aspect-[4/3] bg-gray-100 relative overflow-hidden rounded-2xl flex-shrink-0 cursor-pointer"
                    onclick="window.location.href='{{ route('kost.show', $kost->slug) }}'">
                    @if ($kost->primaryImage)
                        <img src="{{ Str::startsWith($kost->primaryImage->image_path, 'http') ? $kost->primaryImage->image_path : Storage::url($kost->primaryImage->image_path) }}"
                            alt="{{ $kost->name }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-100">
                            <svg class="w-10 h-10 text-gray-300 animate-pulse" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                    @endif

                    <!-- Badges Top Left -->
                    <div class="absolute top-4 left-4 flex flex-col gap-2 pointer-events-none">
                        @if ($kost->boosted_at)
                            <span
                                class="px-3 py-1 bg-gray-950 text-white rounded-full text-[10px] font-bold flex items-center gap-1.5 shadow-sm uppercase tracking-wider">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z" />
                                </svg>
                                Super
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Content -->
                <div class="mt-4 flex-1 flex flex-col justify-between">
                    <div>
                        <h2
                            class="text-lg font-bold text-gray-900 leading-tight group-hover:text-gray-600 transition-colors line-clamp-1">
                            <a href="{{ route('kost.show', $kost->slug) }}" class="focus:outline-none">
                                {{ $kost->name }}
                            </a>
                        </h2>

                        <p class="text-sm text-gray-500 mt-1 line-clamp-1">
                            {{ $kost->address }}
                        </p>

                        <!-- Badges Info -->
                        <div class="mt-3 flex flex-wrap items-center gap-2 relative z-10">
                            <!-- Format price as Rp 1,5 Juta or Rp 1.500.000 -> "Rp 1.5Jt" -->
                            <span
                                class="rounded-full border border-gray-200 text-xs text-gray-700 px-3 py-1 font-semibold">
                                Rp {{ number_format($kost->price_monthly, 0, ',', '.') }}
                            </span>

                            <span
                                class="rounded-full border border-gray-200 text-xs text-gray-700 px-3 py-1 uppercase font-semibold">
                                {{ $kost->gender_type }}
                            </span>

                            @if ($kost->facilities && $kost->facilities->count() > 0)
                                @foreach ($kost->facilities->take(2) as $facility)
                                    <span class="rounded-full border border-gray-200 text-xs text-gray-700 px-3 py-1">
                                        {{ $facility->name }}
                                    </span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-950">Tidak ada hunian ditemukan</h3>
                <p class="text-gray-500 mt-2 max-w-md font-light">Coba ubah filter atau kata kunci pencarian Anda untuk
                    menemukan hunian yang sesuai.</p>
                <button
                    wire:click="$set('search', ''); $set('gender', ''); $set('district', ''); $set('price_min', ''); $set('price_max', '');"
                    class="mt-6 px-6 py-2.5 bg-gray-950 text-white font-medium rounded-full hover:bg-gray-800 transition-colors shadow-md relative z-10">Reset
                    Filter</button>
            </div>
        @endforelse
    </div>

    <div class="mt-12 relative z-10">
        {{ $kosts->links() }}
    </div>
</div>
