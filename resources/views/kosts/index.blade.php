<x-app-layout>
    <!-- Hero Section -->
    <div class="bg-gradient-to-b from-slate-50 to-white pt-20 pb-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight leading-tight">
                Temukan Hunian Eksklusif di Bandung.
            </h1>
            <p class="mt-4 text-lg md:text-xl text-zinc-500 max-w-2xl mx-auto font-light tracking-wide">
                Eksplorasi kost premium dan apartemen pilihan dengan lokasi strategis, fasilitas lengkap, dan kenyamanan tanpa kompromi.
            </p>
        </div>
    </div>

    <!-- Main Content (Search & List) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 -mt-24">
        <livewire:kost-search />
    </div>
</x-app-layout>