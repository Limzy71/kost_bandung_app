<x-app-layout>
    <!-- Hero Section -->
    <div class="bg-gradient-to-b from-purple-50 to-white pt-16 pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">
                Cari Kost Nyaman di Bandung 🏡
            </h1>
            <p class="mt-4 text-lg text-gray-500 max-w-2xl mx-auto">
                Temukan kost dekat kampus, kantor, dan fasilitas umum terdekat dengan mudah dan cepat.
            </p>
        </div>
    </div>

    <!-- Main Content (Search & List) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 -mt-16">
        <livewire:kost-search />
    </div>
</x-app-layout>