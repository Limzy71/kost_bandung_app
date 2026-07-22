<x-app-layout>
    <div class="min-h-screen bg-[#f8f9fa] bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] bg-[size:24px_24px] pt-8 pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            
            <!-- Hero Banner Neo-Brutalist -->
            <div class="bg-white border-4 border-black p-8 md:p-12 rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] relative overflow-hidden text-center md:text-left flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="space-y-4 max-w-2xl">
                    <div class="flex flex-wrap items-center justify-center md:justify-start gap-2">
                        <span class="px-3.5 py-1 bg-yellow-300 text-black border-2 border-black font-extrabold text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            ⚡ Hyper-Local Bandung
                        </span>
                        <span class="px-3.5 py-1 bg-cyan-300 text-black border-2 border-black font-extrabold text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            Direktori Terverifikasi
                        </span>
                    </div>
                    <h1 class="text-4xl sm:text-6xl md:text-7xl font-black text-black tracking-tight uppercase leading-none">
                        Cari. Pilih. <span class="bg-yellow-300 px-3 border-3 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] inline-block rotate-1">Huni!</span>
                    </h1>
                    <p class="text-zinc-700 text-base sm:text-lg font-bold leading-relaxed">
                        Temukan pilihan kost terbaik di Bandung. Dekat area kampus utama, fasilitas transparan, dan terhubung langsung dengan pemilik kost.
                    </p>
                </div>

                <div class="shrink-0 hidden md:block">
                    <div class="w-28 h-28 bg-pink-300 border-4 border-black rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] flex items-center justify-center -rotate-3 hover:rotate-0 transition-transform">
                        <span class="text-5xl">🏠</span>
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