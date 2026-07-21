<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-3xl p-8 md:p-12 shadow-2xl shadow-gray-200/50 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                        Dashboard Pemilik Kost
                    </span>
                    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-950 tracking-tight mt-3">
                        Selamat Datang, {{ auth()->user()->name }}!
                    </h1>
                </div>
            </div>
            <p class="text-gray-600 leading-relaxed max-w-2xl text-base">
                Ini adalah panel kendali khusus Pemilik Kost. Di sini Anda nantinya dapat mengelola properti kost, menanggapi pesan calon penyewa, dan memantau status iklan Anda di Bandung.
            </p>
            <div class="mt-8 pt-8 border-t border-gray-100 flex gap-4">
                <a href="{{ route('home') }}" class="px-6 py-3 bg-gray-950 hover:bg-gray-800 text-white font-semibold text-sm rounded-full transition-all">
                    Lihat Beranda Kost
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
