<div class="pb-24 lg:pb-12 bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-gray-500 hover:text-gray-950 font-medium inline-flex items-center gap-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path></svg>
                Kembali ke Pencarian
            </a>
        </div>

        <!-- Header Galeri -->
        <div class="relative grid grid-cols-1 md:grid-cols-4 gap-3 mb-12 h-auto lg:h-[500px]">
            <div class="md:col-span-3 h-64 md:h-full relative group rounded-2xl overflow-hidden min-h-0">
                <img src="{{ Str::startsWith($kost->primaryImage?->image_path ?? '', 'http') ? $kost->primaryImage->image_path : Storage::url($kost->primaryImage?->image_path) }}" 
                     class="w-full h-full object-cover" alt="{{ $kost->name }}">
                <button class="absolute bottom-6 right-6 px-6 py-2.5 bg-white/80 backdrop-blur-md rounded-full text-sm font-semibold text-gray-950 shadow-sm border border-white/50 hover:bg-white transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    Lihat Semua Foto
                </button>
            </div>
            <div class="hidden md:flex flex-col gap-3 h-full min-h-0">
                @foreach(range(1, 3) as $i)
                <div class="flex-1 rounded-2xl overflow-hidden bg-gray-100 relative group min-h-0">
                    <img src="https://placehold.co/400x300/eeeeee/31343c?text=Foto+{{ $i }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                </div>
                @endforeach
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Konten Utama Kiri -->
            <div class="lg:col-span-2">
                <!-- Badges & Judul -->
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <span class="rounded-full border border-gray-200 text-xs text-gray-700 px-3 py-1 uppercase font-semibold">
                        {{ $kost->gender_type }}
                    </span>
                    <span class="rounded-full border border-emerald-200 bg-emerald-50 text-emerald-700 px-3 py-1 text-xs font-bold uppercase tracking-wide">
                        Sisa 2 Kamar
                    </span>
                </div>
                
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-950 tracking-tight leading-tight mb-4">
                    {{ $kost->name }}
                </h1>
                
                <div class="flex items-start gap-2 text-gray-500 text-sm md:text-base font-medium mb-12">
                    <svg class="w-5 h-5 flex-shrink-0 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span>{{ $kost->address }}</span>
                </div>
                
                <hr class="border-gray-100 mb-8">
                
                <!-- Deskripsi -->
                <div class="mb-10">
                    <h2 class="text-xl font-bold text-gray-950 mb-4">Tentang Kost Ini</h2>
                    <p class="leading-relaxed text-gray-600">
                        {{ $kost->description ?? 'Kost nyaman dengan fasilitas modern dan lokasi strategis. Cocok untuk Anda yang memiliki mobilitas tinggi namun tetap menginginkan hunian yang tenang dan asri.' }}
                    </p>
                </div>
                
                <!-- Fasilitas -->
                <div class="mb-10">
                    <h2 class="text-xl font-bold text-gray-950 mb-4">Fasilitas Utama</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-y-4 gap-x-2">
                        @forelse($kost->facilities as $facility)
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400 stroke-[1.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                            <span class="text-gray-700 font-medium text-sm">{{ $facility->name }}</span>
                        </div>
                        @empty
                        <p class="text-gray-500">Tidak ada fasilitas terdaftar.</p>
                        @endforelse
                    </div>
                </div>
                
                <!-- Aturan Kost -->
                <div class="mb-10">
                    <h2 class="text-xl font-bold text-gray-950 mb-4">Aturan Kost</h2>
                    <ul class="space-y-4">
                        @forelse($kost->rules as $rule)
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-gray-600">{{ $rule->name }}</span>
                        </li>
                        @empty
                        <li class="text-gray-500">Tidak ada aturan khusus.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            
            <!-- Sticky Action Card (Kanan) -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 bg-white rounded-3xl p-6 shadow-2xl shadow-gray-200/50 hidden lg:block">
                    <div class="flex items-end gap-1 mb-6">
                        <span class="text-3xl font-extrabold text-gray-950 tracking-tight">Rp {{ number_format($kost->price_monthly, 0, ',', '.') }}</span>
                        <span class="text-sm font-medium text-gray-500 mb-1">/ bulan</span>
                    </div>
                    
                    <div class="space-y-3">
                        <button class="w-full py-3.5 bg-gray-950 text-white rounded-full font-semibold hover:bg-gray-800 transition-colors shadow-sm flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                            Tanya via WhatsApp
                        </button>
                        <button class="w-full py-3.5 bg-white border border-gray-300 text-gray-900 rounded-full font-semibold hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                            Kirim Pesan Internal
                        </button>
                    </div>
                    
                    <div class="mt-6 text-center text-xs text-gray-400">
                        Disewakan oleh <span class="font-bold text-gray-600">{{ $kost->user->name ?? 'Pemilik' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Bar Mobile -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 shadow-[0_-10px_40px_rgba(0,0,0,0.05)] lg:hidden z-50">
        <div class="flex items-center justify-between gap-4 max-w-7xl mx-auto">
            <div>
                <p class="text-xs text-gray-500 font-medium mb-0.5">Mulai dari</p>
                <div class="flex items-end gap-1">
                    <span class="text-xl font-extrabold text-gray-950">Rp {{ number_format($kost->price_monthly, 0, ',', '.') }}</span>
                    <span class="text-xs text-gray-500 mb-0.5">/ bln</span>
                </div>
            </div>
            <button class="px-6 py-3 bg-gray-950 text-white rounded-full font-semibold hover:bg-gray-800 transition-colors whitespace-nowrap">
                Tanya via WA
            </button>
        </div>
    </div>
</div>
