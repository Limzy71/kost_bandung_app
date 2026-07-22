<div class="min-h-screen bg-[#f8f9fa] bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] bg-[size:24px_24px] pb-28 lg:pb-16 pt-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Navigation Back Button -->
        <div class="flex items-center justify-between">
            <a 
                href="{{ route('home') }}" 
                class="inline-flex items-center gap-2 bg-white text-black border-3 border-black font-black text-xs uppercase px-4 py-2.5 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all"
            >
                <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
                <span>Kembali ke Pencarian</span>
            </a>
        </div>

        <!-- Header Galeri Foto Neo-Brutalist -->
        <div class="relative grid grid-cols-1 md:grid-cols-4 gap-4 h-auto lg:h-[480px]">
            <!-- Main Hero Image -->
            <div class="md:col-span-3 h-72 md:h-full relative group rounded-2xl overflow-hidden border-4 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] bg-white">
                <img 
                    src="{{ Str::startsWith($kost->primaryImage?->image_path ?? '', 'http') ? $kost->primaryImage->image_path : Storage::url($kost->primaryImage?->image_path) }}" 
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                    alt="{{ $kost->name }}"
                >
                <button class="absolute bottom-5 right-5 px-5 py-2.5 bg-yellow-300 text-black border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg flex items-center gap-2">
                    <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    <span>Lihat Semua Foto</span>
                </button>
            </div>

            <!-- Thumbnail Side Grid -->
            <div class="hidden md:flex flex-col gap-4 h-full">
                @foreach(range(1, 3) as $i)
                    <div class="flex-1 rounded-xl overflow-hidden border-3 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] bg-zinc-200 relative group">
                        <img 
                            src="https://placehold.co/400x300/eeeeee/31343c?text=Foto+{{ $i }}" 
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                        >
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Layout Utama Detail -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Konten Utama Kiri -->
            <div class="lg:col-span-2 bg-white border-4 border-black p-6 sm:p-8 rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] space-y-8">
                
                <!-- Badges & Title -->
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-3.5 py-1 bg-pink-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider">
                            Tipe {{ $kost->gender_type }}
                        </span>

                        @if($kost->is_available)
                            <span class="px-3.5 py-1 bg-lime-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider">
                                ✓ Kamar Tersedia
                            </span>
                        @else
                            <span class="px-3.5 py-1 bg-rose-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider">
                                ✕ Kamar Penuh
                            </span>
                        @endif

                        @if($kost->boosted_at)
                            <span class="px-3.5 py-1 bg-yellow-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider flex items-center gap-1">
                                ⚡ Properti Rekomendasi
                            </span>
                        @endif
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-black text-black tracking-tight uppercase leading-tight">
                        {{ $kost->name }}
                    </h1>

                    <div class="flex items-start gap-2 text-zinc-700 text-sm sm:text-base font-bold">
                        <span class="text-xl">📍</span>
                        <span>{{ $kost->address }}, Kecamatan {{ $kost->district }}, Kota Bandung</span>
                    </div>
                </div>

                <!-- Divider Neo-Brutalist -->
                <div class="border-t-4 border-black"></div>

                <!-- Deskripsi -->
                <div class="space-y-3">
                    <h2 class="text-xl font-black text-black uppercase tracking-tight flex items-center gap-2">
                        <span>📝 Tentang Kost Ini</span>
                    </h2>
                    <p class="leading-relaxed text-zinc-700 font-medium text-base sm:text-lg">
                        {{ $kost->description ?? 'Kost nyaman dengan fasilitas modern dan lokasi strategis di Bandung. Cocok untuk Anda yang memiliki mobilitas tinggi namun tetap menginginkan hunian yang tenang dan asri.' }}
                    </p>
                </div>

                <!-- Divider Neo-Brutalist -->
                <div class="border-t-4 border-black"></div>

                <!-- Fasilitas Utama -->
                <div class="space-y-4">
                    <h2 class="text-xl font-black text-black uppercase tracking-tight flex items-center gap-2">
                        <span>✨ Fasilitas Properti</span>
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @forelse($kost->facilities as $facility)
                            <div class="flex items-center gap-2.5 bg-yellow-100 border-2 border-black px-3.5 py-2.5 rounded-xl text-sm font-black text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                <span class="text-lime-600 font-extrabold text-base">✓</span>
                                <span>{{ $facility->name }}</span>
                            </div>
                        @empty
                            <p class="text-zinc-500 font-bold">Tidak ada fasilitas terdaftar.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Divider Neo-Brutalist -->
                <div class="border-t-4 border-black"></div>

                <!-- Aturan Kost -->
                <div class="space-y-4">
                    <h2 class="text-xl font-black text-black uppercase tracking-tight flex items-center gap-2">
                        <span>⚠️ Aturan Properti</span>
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @forelse($kost->rules as $rule)
                            <div class="flex items-start gap-3 bg-zinc-100 border-2 border-black p-3.5 rounded-xl text-sm font-bold text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                <span class="text-pink-500 font-black">📌</span>
                                <span>{{ $rule->name }}</span>
                            </div>
                        @empty
                            <p class="text-zinc-500 font-bold">Tidak ada aturan khusus.</p>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- Sticky Action Box (Kanan Desktop) -->
            <div class="lg:col-span-1">
                <div class="sticky top-24 bg-white border-4 border-black rounded-2xl p-6 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] hidden lg:block space-y-6">
                    
                    <!-- Display Harga -->
                    <div class="bg-yellow-300 border-3 border-black p-4 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-1">
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
                            $waMessage = rawurlencode("Halo, saya tertarik dengan kost \"" . $kost->name . "\" di " . $kost->district . ". Apakah kamar masih tersedia?");
                        @endphp
                        
                        <a 
                            href="https://wa.me/6281234567890?text={{ $waMessage }}" 
                            target="_blank"
                            class="w-full py-4 bg-emerald-400 hover:bg-emerald-300 text-black border-3 border-black font-black text-base uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl flex items-center justify-center gap-2 group"
                        >
                            <span class="text-xl group-hover:scale-110 transition-transform">💬</span>
                            <span>Tanya via WhatsApp</span>
                        </a>

                        <button 
                            type="button" 
                            class="w-full py-4 bg-cyan-300 hover:bg-cyan-200 text-black border-3 border-black font-black text-base uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span>Kirim Pesan Internal</span>
                        </button>
                    </div>

                    <!-- Owner Info Card -->
                    <div class="pt-4 border-t-3 border-black text-center space-y-1">
                        <p class="text-xs font-black uppercase text-zinc-500">Disewakan Oleh</p>
                        <p class="text-sm font-black text-black bg-zinc-100 border-2 border-black py-1.5 px-3 rounded-lg inline-block shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            👤 {{ $kost->user->name ?? 'Pemilik Kost' }}
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Floating Mobile Bar Neo-Brutalist -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t-4 border-black p-4 shadow-[0_-6px_0px_0px_rgba(0,0,0,1)] lg:hidden z-50">
        <div class="flex items-center justify-between gap-4 max-w-7xl mx-auto">
            <div>
                <p class="text-[10px] font-black uppercase text-zinc-500">Harga Sewa</p>
                <div class="flex items-baseline gap-1">
                    <span class="text-lg font-black text-black">Rp {{ number_format($kost->price_monthly, 0, ',', '.') }}</span>
                    <span class="text-[10px] font-bold text-black">/bln</span>
                </div>
            </div>

            @php
                $waMessageMobile = rawurlencode("Halo, saya tertarik dengan kost \"" . $kost->name . "\" di " . $kost->district . ". Apakah kamar masih tersedia?");
            @endphp
            <a 
                href="https://wa.me/6281234567890?text={{ $waMessageMobile }}" 
                target="_blank"
                class="px-5 py-3 bg-emerald-400 hover:bg-emerald-300 text-black border-3 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl whitespace-nowrap inline-flex items-center gap-1.5"
            >
                <span>💬 Tanya WA</span>
            </a>
        </div>
    </div>
</div>
