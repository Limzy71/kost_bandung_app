<x-app-layout>
    <!-- Hero Section -->
    <div class="bg-gray-50 pt-24 pb-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center flex flex-col items-center">
            <h1 class="text-5xl md:text-7xl font-extrabold text-gray-950 tracking-tight leading-tight">
                Buy. Sell. Rent.
            </h1>
            <p class="mt-6 text-lg md:text-xl text-gray-600 max-w-2xl mx-auto font-medium">
                Temukan kost premium dan apartemen pilihan dengan lokasi strategis, fasilitas lengkap, dan kenyamanan tanpa kompromi.
            </p>
            <div class="mt-10">
                <button class="inline-block rounded-full bg-gray-950 text-white px-8 py-3.5 text-sm font-semibold hover:bg-gray-800 transition-all shadow-md">
                    Mulai Pencarian
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content (Search & List) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 -mt-20">
        <livewire:kost-search />
    </div>
</x-app-layout>