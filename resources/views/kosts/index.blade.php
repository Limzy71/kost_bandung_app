<x-app-layout>
    <div 
        x-data 
        x-init="window.scrollTo({ top: 0, behavior: 'auto' })"
        class="min-h-screen bg-[#f8f9fa] bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] bg-[size:24px_24px] pt-8 pb-20"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            
            <!-- Hero Banner Neo-Brutalist -->
            <div class="bg-white border-4 border-black p-8 md:p-12 rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] relative overflow-hidden text-center md:text-left flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="space-y-4 max-w-2xl">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-2">
                        <span class="px-3.5 py-1 bg-yellow-300 text-black border-2 border-black font-extrabold text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] inline-flex items-center gap-1.5">
                            <svg class="w-4 h-4 shrink-0 drop-shadow-[1px_1px_0px_rgba(0,0,0,1)]" viewBox="0 0 20 20">
                                <defs>
                                    <linearGradient id="bolt-grad-hero" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" stop-color="#FBBF24" />
                                        <stop offset="100%" stop-color="#F97316" />
                                    </linearGradient>
                                </defs>
                                <path fill="url(#bolt-grad-hero)" stroke="#000000" stroke-width="0.8" fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z" clip-rule="evenodd" />
                            </svg>
                            <span>Hyper-Local Bandung</span>
                        </span>
                        <span class="px-3.5 py-1 bg-cyan-300 text-black border-2 border-black font-extrabold text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] inline-flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-black shrink-0 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Direktori Terverifikasi</span>
                        </span>
                    </div>
                    <h1 class="text-4xl sm:text-6xl md:text-7xl font-black text-black tracking-tight uppercase leading-none">
                        Cari. Pilih. <span class="bg-yellow-300 px-3 border-3 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] inline-block rotate-1">Huni!</span>
                    </h1>
                    <p class="text-zinc-700 text-base sm:text-lg font-bold leading-relaxed">
                        Temukan pilihan kost terbaik di Bandung. Dekat area kampus utama, fasilitas transparan, dan terhubung langsung dengan pemilik kost.
                    </p>
                </div>

                <!-- Professional Neo-Brutalist Architectural Building Icon -->
                <div class="shrink-0 hidden md:block">
                    <div class="w-28 h-28 bg-pink-300 border-4 border-black rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] flex items-center justify-center -rotate-3 hover:rotate-0 transition-transform">
                        <svg class="w-14 h-14 text-black stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 0h4m-4 0H9m4 0V7m0 0h4m-4 0H9"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Main Livewire Search & List -->
            <div>
                <livewire:kost-search />
            </div>

        </div>
    </div>
</x-app-layout>