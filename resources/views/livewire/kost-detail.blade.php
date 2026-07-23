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
                            <span class="px-3.5 py-1 bg-yellow-400 text-black border-2 border-black text-xs font-black uppercase rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] tracking-wider inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 shrink-0 drop-shadow-[1px_1px_0px_rgba(0,0,0,1)]" viewBox="0 0 20 20">
                                    <defs>
                                        <linearGradient id="bolt-grad-detail" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" stop-color="#FBBF24" />
                                            <stop offset="100%" stop-color="#F97316" />
                                        </linearGradient>
                                    </defs>
                                    <path fill="url(#bolt-grad-detail)" stroke="#000000" stroke-width="0.8" fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z" clip-rule="evenodd" />
                                </svg>
                                <span>Properti Rekomendasi</span>
                            </span>
                        @endif
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-black text-black tracking-tight uppercase leading-tight">
                        {{ $kost->name }}
                    </h1>

                    <div class="flex items-start gap-2 text-zinc-700 text-sm sm:text-base font-bold">
                        <svg class="w-5 h-5 text-black shrink-0 stroke-[2.5] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>{{ $kost->address }}, Kecamatan {{ $kost->district }}, Kota Bandung</span>
                    </div>
                </div>

                <!-- Divider Neo-Brutalist -->
                <div class="border-t-4 border-black"></div>

                <!-- Deskripsi -->
                <div class="space-y-3">
                    <h2 class="text-xl font-black text-black uppercase tracking-tight flex items-center gap-2">
                        <svg class="w-5 h-5 text-black stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Tentang Kost Ini</span>
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
                        <svg class="w-5 h-5 text-black stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                        <span>Fasilitas Properti</span>
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
                        <svg class="w-5 h-5 text-black stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span>Aturan Properti</span>
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @forelse($kost->rules as $rule)
                            <div class="flex items-start gap-3 bg-zinc-100 border-2 border-black p-3.5 rounded-xl text-sm font-bold text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                <svg class="w-4 h-4 text-pink-600 shrink-0 stroke-[2.5] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                </svg>
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
                            <svg class="w-5 h-5 text-black stroke-[2.5] group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span>Tanya via WhatsApp</span>
                        </a>

                        <button 
                            type="button" 
                            @click="showModal = true"
                            class="w-full py-4 bg-cyan-300 hover:bg-cyan-200 text-black border-3 border-black font-black text-base uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl flex items-center justify-center gap-2"
                        >
                            <svg class="w-5 h-5 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>Kirim Pesan Internal</span>
                        </button>
                    </div>

                    <!-- Owner Info Card -->
                    <div class="pt-4 border-t-3 border-black text-center space-y-1">
                        <p class="text-xs font-black uppercase text-zinc-500">Disewakan Oleh</p>
                        <p class="text-sm font-black text-black bg-zinc-100 border-2 border-black py-1.5 px-3 rounded-lg inline-flex items-center gap-1.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            <svg class="w-4 h-4 text-black stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>{{ $kost->user->name ?? 'Pemilik Kost' }}</span>
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Floating Mobile Bar Neo-Brutalist -->
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t-4 border-black p-4 shadow-[0_-6px_0px_0px_rgba(0,0,0,1)] lg:hidden z-50">
        <div class="flex items-center justify-between gap-4 max-w-7xl mx-auto">
            <div x-data="{ showModal: false }" @inquiry-sent.window="showModal = false">
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
                <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span>Tanya WA</span>
            </a>
        </div>
    </div>

    <!-- Neo-Brutalist Inquiry Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0">
        <!-- Backdrop -->
        <div x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showModal = false"></div>
        
        <!-- Modal Content -->
        <div x-show="showModal" 
             x-transition:enter="transition ease-out duration-200" 
             x-transition:enter-start="opacity-0 translate-y-4" 
             x-transition:enter-end="opacity-100 translate-y-0"
             class="bg-white border-4 border-black rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] w-full max-w-lg relative z-10 flex flex-col max-h-[90vh]">
            
            <div class="p-5 border-b-4 border-black flex items-center justify-between bg-yellow-300 rounded-t-xl shrink-0">
                <h3 class="text-xl font-black text-black uppercase tracking-tight">Kirim Pesan ke Pemilik</h3>
                <button type="button" @click="showModal = false" class="w-8 h-8 bg-white border-2 border-black rounded flex items-center justify-center text-black hover:bg-rose-400 active:translate-y-0.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:shadow-none transition-all">
                    <svg class="w-5 h-5 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto">
                <form wire:submit.prevent="sendInquiry" class="space-y-4">
                    <div>
                        <label class="block text-sm font-black uppercase text-black mb-1.5">Nama Lengkap</label>
                        <input type="text" wire:model="inquiry_name" class="w-full bg-zinc-100 border-3 border-black rounded-xl px-4 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:bg-white focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all" placeholder="Masukkan nama Anda">
                        @error('inquiry_name') <span class="text-xs font-bold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-black uppercase text-black mb-1.5">Nomor WhatsApp</label>
                        <input type="text" wire:model="inquiry_phone" class="w-full bg-zinc-100 border-3 border-black rounded-xl px-4 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:bg-white focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all" placeholder="Contoh: 081234567890">
                        @error('inquiry_phone') <span class="text-xs font-bold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-black uppercase text-black mb-1.5">Pesan Anda</label>
                        <textarea wire:model="inquiry_message" rows="4" class="w-full bg-zinc-100 border-3 border-black rounded-xl px-4 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:bg-white focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all resize-none" placeholder="Tuliskan pertanyaan Anda mengenai ketersediaan kamar, fasilitas, dll..."></textarea>
                        @error('inquiry_message') <span class="text-xs font-bold text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <button type="submit" class="w-full mt-4 py-3.5 bg-cyan-400 hover:bg-cyan-300 text-black border-3 border-black font-black text-sm uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="sendInquiry">Kirim Sekarang</span>
                        <span wire:loading wire:target="sendInquiry" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Mengirim...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Toast Notification Neo-Brutalist -->
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition class="fixed bottom-24 lg:bottom-10 right-4 lg:right-10 z-[110]">
            <div class="bg-lime-400 border-4 border-black p-4 rounded-xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] flex items-center gap-4 max-w-sm">
                <div class="w-10 h-10 bg-white border-2 border-black rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-black stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-black uppercase">Berhasil!</h4>
                    <p class="text-xs font-bold text-black mt-0.5">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-black hover:text-rose-500 transition-colors ml-auto">
                    <svg class="w-5 h-5 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    @endif

</div>
