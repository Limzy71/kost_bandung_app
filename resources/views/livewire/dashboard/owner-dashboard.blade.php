<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-8">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 md:p-8 rounded-3xl border border-gray-100 shadow-sm">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full uppercase tracking-wider">
                        Portal Pemilik
                    </span>
                    @if($owner->role === 'owner')
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-semibold rounded-full">
                            Akun Terverifikasi
                        </span>
                    @endif
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-950 tracking-tight">
                    Dashboard Pemilik
                </h1>
                <p class="text-gray-500 text-sm md:text-base mt-1">
                    Selamat datang kembali, <span class="font-bold text-gray-900">{{ $owner->name }}</span>! Kelola iklan dan ketersediaan kost Anda.
                </p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="#tambah-kost" onclick="alert('Fitur pendaftaran form kost baru akan hadir pada modul berikutnya!'); return false;" class="bg-gray-950 hover:bg-gray-800 text-white rounded-full px-6 py-3 font-semibold text-sm shadow-md transition-all inline-flex items-center gap-2 group">
                    <svg class="w-4 h-4 text-white group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Kost Baru</span>
                </a>
            </div>
        </div>

        <!-- Flash Status Banner -->
        @if (session()->has('status'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between text-emerald-900 text-sm font-medium animate-fade-in">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        <!-- Quick Stats Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: Total Properti -->
            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Properti</p>
                        <h3 class="text-3xl font-extrabold text-gray-950 mt-2 tracking-tight">{{ $totalProperti }}</h3>
                        <p class="text-xs text-gray-400 mt-1">Kost terdaftar dalam sistem</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-900 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V7m0 0h4m-4 0H9"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Card 2: Status Kamar / Properti Siap Huni -->
            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Ketersediaan Kamar</p>
                        <h3 class="text-3xl font-extrabold text-emerald-600 mt-2 tracking-tight">{{ $totalKamarTersedia }} <span class="text-sm font-medium text-gray-400">/ {{ $totalProperti }} Properti</span></h3>
                        <p class="text-xs text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full inline-block mt-1 font-medium">Status Siap Huni</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Card 3: Pesan Masuk / Inquiry -->
            <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Pesan Masuk</p>
                        <h3 class="text-3xl font-extrabold text-gray-950 mt-2 tracking-tight">{{ $pesanMasuk }}</h3>
                        <p class="text-xs text-gray-400 mt-1">Inquiry dari calon penyewa</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center text-gray-900 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section List Properti -->
        <div class="space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-950 tracking-tight">Daftar Properti Kost</h2>
                    <p class="text-sm text-gray-500">Kelola status dan informasi ketersediaan properti kost Anda.</p>
                </div>

                <!-- Search Filter Input -->
                <div class="relative w-full sm:w-72">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search" 
                        placeholder="Cari nama atau lokasi..." 
                        class="w-full bg-white border border-gray-200 rounded-full pl-10 pr-4 py-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-950 focus:border-transparent shadow-sm transition"
                    >
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Grid Card Properties -->
            @if($kosts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($kosts as $kost)
                        <div class="bg-white border border-gray-200 rounded-3xl overflow-hidden shadow-sm hover:shadow-md transition-all flex flex-col justify-between group">
                            <div>
                                <!-- Image Header -->
                                <div class="aspect-[4/3] bg-gray-100 relative overflow-hidden">
                                    @if($kost->primaryImage)
                                        <img src="{{ Str::startsWith($kost->primaryImage->image_path, 'http') ? $kost->primaryImage->image_path : Storage::url($kost->primaryImage->image_path) }}" alt="{{ $kost->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-300">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif

                                    <!-- Top Left Badges -->
                                    <div class="absolute top-3 left-3 flex flex-col gap-1.5 pointer-events-none">
                                        <span class="px-3 py-1 bg-gray-950/80 backdrop-blur-md text-white text-[10px] font-bold uppercase rounded-full tracking-wider shadow-sm">
                                            {{ $kost->gender_type }}
                                        </span>
                                        @if($kost->boosted_at)
                                            <span class="px-3 py-1 bg-amber-500 text-white text-[10px] font-bold uppercase rounded-full tracking-wider shadow-sm flex items-center gap-1">
                                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z"/></svg>
                                                Sundul
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Top Right Status Badge -->
                                    <div class="absolute top-3 right-3">
                                        @if($kost->is_available)
                                            <span class="px-3 py-1 bg-emerald-500/90 backdrop-blur-md text-white text-xs font-semibold rounded-full shadow-sm">
                                                Tersedia
                                            </span>
                                        @else
                                            <span class="px-3 py-1 bg-rose-500/90 backdrop-blur-md text-white text-xs font-semibold rounded-full shadow-sm">
                                                Penuh
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Card Content -->
                                <div class="p-5 space-y-3">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-950 leading-tight group-hover:text-gray-600 transition-colors line-clamp-1">
                                            <a href="{{ route('kost.show', $kost->slug) }}">
                                                {{ $kost->name }}
                                            </a>
                                        </h3>
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-1">
                                            {{ $kost->address }}, {{ $kost->district }}
                                        </p>
                                    </div>

                                    <!-- Price & Facilities -->
                                    <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                                        <div>
                                            <p class="text-[10px] font-medium uppercase tracking-wider text-gray-400">Harga Sewa</p>
                                            <p class="text-base font-extrabold text-gray-950">
                                                Rp {{ number_format($kost->price_monthly, 0, ',', '.') }}
                                                <span class="text-xs font-normal text-gray-500">/bln</span>
                                            </p>
                                        </div>

                                        <div class="text-right">
                                            <p class="text-[10px] font-medium uppercase tracking-wider text-gray-400">Inquiry</p>
                                            <span class="text-sm font-bold text-gray-950">
                                                {{ $kost->inquiries->count() }} Pesan
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer Actions -->
                            <div class="px-5 py-4 bg-gray-50/70 border-t border-gray-100 flex items-center justify-between gap-2">
                                <!-- Toggle Availability Button -->
                                <button 
                                    wire:click="toggleAvailability({{ $kost->id }})" 
                                    class="px-4 py-2 rounded-full text-xs font-semibold transition-all border {{ $kost->is_available ? 'bg-white text-gray-700 border-gray-200 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200' : 'bg-emerald-600 text-white border-emerald-600 hover:bg-emerald-700' }}"
                                >
                                    {{ $kost->is_available ? 'Tandai Penuh' : 'Tandai Tersedia' }}
                                </button>

                                <!-- Detail Link Button -->
                                <a 
                                    href="{{ route('kost.show', $kost->slug) }}" 
                                    class="px-4 py-2 bg-gray-950 hover:bg-gray-800 text-white rounded-full text-xs font-semibold transition-all inline-flex items-center gap-1"
                                >
                                    <span>Lihat</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
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
                <div class="bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-sm space-y-4">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V7m0 0h4m-4 0H9"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-950">Belum Ada Properti Kost</h3>
                        <p class="text-sm text-gray-500 max-w-md mx-auto mt-1">
                            @if($search)
                                Tidak ada properti kost yang cocok dengan kata kunci "{{ $search }}".
                            @else
                                Anda belum memiliki properti kost yang terdaftar. Mulai tambahkan properti pertama Anda untuk menarik calon penyewa di Bandung.
                            @endif
                        </p>
                    </div>
                    @if($search)
                        <button wire:click="$set('search', '')" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-full text-xs font-semibold transition">
                            Reset Pencarian
                        </button>
                    @else
                        <button onclick="alert('Fitur pendaftaran form kost baru akan hadir pada modul berikutnya!');" class="px-6 py-3 bg-gray-950 hover:bg-gray-800 text-white rounded-full text-sm font-semibold shadow-md transition inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Tambah Properti Pertama</span>
                        </button>
                    @endif
                </div>
            @endif
        </div>

    </div>
</div>
