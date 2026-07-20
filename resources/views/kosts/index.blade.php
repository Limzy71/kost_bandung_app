<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Hero / Search Section -->
        <div class="mb-8 text-center sm:text-left">
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight sm:text-4xl">
                Cari Kost Nyaman di Bandung 🏡
            </h1>
            <p class="mt-2 text-slate-600">
                Temukan kost dekat kampus, kantor, dan fasilitas umum terdekat.
            </p>
        </div>

        <!-- Grid List Kost -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($kosts as $kost)
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition duration-200 flex flex-col">
                    <div class="h-48 bg-slate-200 relative">
                        <!-- Badge Gender -->
                        <span class="absolute top-3 left-3 px-3 py-1 bg-white/90 backdrop-blur-md rounded-full text-xs font-semibold text-slate-700 capitalize">
                            {{ $kost->gender_type }}
                        </span>
                    </div>
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-medium text-indigo-600 uppercase tracking-wider">{{ $kost->district }}</span>
                            <h2 class="text-lg font-bold text-slate-900 mt-1 hover:text-indigo-600 transition">
                                <a href="{{ route('kost.show', $kost->slug) }}">{{ $kost->name }}</a>
                            </h2>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-1">📍 {{ $kost->address }}</p>
                        </div>
                        <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between">
                            <div>
                                <span class="text-lg font-extrabold text-slate-900">Rp {{ number_format($kost->price_monthly, 0, ',', '.') }}</span>
                                <span class="text-xs text-slate-500">/ bulan</span>
                            </div>
                            <a href="{{ route('kost.show', $kost->slug) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">
                                Detail →
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</x-app-layout>