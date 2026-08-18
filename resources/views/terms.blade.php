<x-app-layout>
    <x-slot name="title">Syarat & Ketentuan Layanan - KostBandung</x-slot>

    <div class="max-w-4xl mx-auto py-10 px-4 sm:px-6 min-h-[70vh]">
        {{-- ===== Main Legal Document Card ===== --}}
        <div class="bg-white border-4 border-black p-6 sm:p-10 shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] rounded-2xl dark:bg-zinc-900 dark:border-zinc-700 dark:shadow-[10px_10px_0px_0px_rgba(255,255,255,0.25)]">
            
            {{-- Header Badge & Title --}}
            <div class="mb-8">
                <div class="inline-flex items-center gap-2 bg-[#FFE500] border-2 border-black px-3 py-1 rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mb-4 dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                    <x-icon name="lucide-scroll-text" class="w-3.5 h-3.5 text-black stroke-[2.5]" />
                    <span class="text-[10px] font-black uppercase tracking-widest text-black">Dokumen Resmi</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black text-black dark:text-white uppercase tracking-tight leading-tight">
                    Syarat &amp; Ketentuan Layanan
                </h1>
                <p class="mt-2 text-xs sm:text-sm font-bold text-zinc-600 dark:text-zinc-400">
                    Terakhir diperbarui: <span class="text-black dark:text-white font-black">18 Agustus 2026</span> • Harap baca ketentuan penggunaan platform KostBandung dengan seksama.
                </p>
            </div>

            {{-- Divider --}}
            <div class="border-t-4 border-black mb-8 dark:border-zinc-700"></div>

            {{-- Introduction Notice --}}
            <div class="mb-8 p-4 sm:p-5 bg-yellow-50 dark:bg-zinc-800/80 border-3 border-black dark:border-zinc-700 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.2)] flex items-start gap-3.5">
                <div class="w-8 h-8 rounded-lg bg-[#FFE500] border-2 border-black dark:border-zinc-700 flex items-center justify-center text-black shrink-0 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                    <x-icon name="lucide-info" class="w-4 h-4 stroke-[2.5]" />
                </div>
                <p class="text-xs sm:text-sm font-bold text-zinc-800 dark:text-zinc-200 leading-relaxed">
                    Selamat datang di <strong>KostBandung</strong>. Dengan mendaftar, mengakses, atau menggunakan platform direktori kost Kota Bandung ini, Anda secara sadar telah membaca, memahami, dan menyetujui seluruh ketentuan di bawah ini.
                </p>
            </div>

            {{-- Sections Grid --}}
            <div class="space-y-6">
                
                {{-- Section 1 --}}
                <div class="p-5 sm:p-6 bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.2)] transition-all">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="w-7 h-7 bg-[#FFE500] border-2 border-black dark:border-zinc-700 text-black font-black text-xs rounded-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] shrink-0">
                            01
                        </span>
                        <h2 class="text-base sm:text-lg font-black text-black dark:text-white uppercase tracking-tight">
                            Penggunaan Layanan &amp; Akun
                        </h2>
                    </div>
                    <p class="text-xs sm:text-sm font-bold text-zinc-700 dark:text-zinc-300 leading-relaxed pl-10">
                        Anda bertanggung jawab penuh atas kerahasiaan akun dan kata sandi Anda, serta seluruh aktivitas yang terjadi melalui akun tersebut. Anda dilarang keras menggunakan layanan KostBandung untuk segala bentuk aktivitas ilegal, penipuan, spamming, atau penyebaran konten yang merugikan pihak lain.
                    </p>
                </div>

                {{-- Section 2 --}}
                <div class="p-5 sm:p-6 bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.2)] transition-all">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="w-7 h-7 bg-[#FFE500] border-2 border-black dark:border-zinc-700 text-black font-black text-xs rounded-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] shrink-0">
                            02
                        </span>
                        <h2 class="text-base sm:text-lg font-black text-black dark:text-white uppercase tracking-tight">
                            Privasi &amp; Perlindungan Data
                        </h2>
                    </div>
                    <p class="text-xs sm:text-sm font-bold text-zinc-700 dark:text-zinc-300 leading-relaxed pl-10">
                        Kami sangat menghargai privasi Anda. Seluruh data pribadi yang Anda berikan (nama, email, nomor WhatsApp, dan dokumen verifikasi kepemilikan) dikelola secara aman dan hanya digunakan untuk operasional platform serta memfasilitasi komunikasi resmi antara pencari kost dan pemilik properti.
                    </p>
                </div>

                {{-- Section 3 --}}
                <div class="p-5 sm:p-6 bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.2)] transition-all">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="w-7 h-7 bg-[#FFE500] border-2 border-black dark:border-zinc-700 text-black font-black text-xs rounded-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] shrink-0">
                            03
                        </span>
                        <h2 class="text-base sm:text-lg font-black text-black dark:text-white uppercase tracking-tight">
                            Tanggung Jawab Sewa &amp; Transaksi
                        </h2>
                    </div>
                    <p class="text-xs sm:text-sm font-bold text-zinc-700 dark:text-zinc-300 leading-relaxed pl-10">
                        KostBandung beroperasi sebagai platform direktori dan penghubung informasi. Segala bentuk perjanjian sewa-menyewa, survei lokasi langsung, transaksi pembayaran, serta penyelesaian sengketa adalah tanggung jawab langsung antara pencari kost dan pemilik properti. Pastikan selalu memverifikasi lokasi dan identitas sebelum melakukan transfer dana.
                    </p>
                </div>

                {{-- Section 4 --}}
                <div class="p-5 sm:p-6 bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.2)] transition-all">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="w-7 h-7 bg-[#FFE500] border-2 border-black dark:border-zinc-700 text-black font-black text-xs rounded-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] shrink-0">
                            04
                        </span>
                        <h2 class="text-base sm:text-lg font-black text-black dark:text-white uppercase tracking-tight">
                            Verifikasi Konten &amp; Iklan Kost
                        </h2>
                    </div>
                    <p class="text-xs sm:text-sm font-bold text-zinc-700 dark:text-zinc-300 leading-relaxed pl-10">
                        Pemilik kost wajib menyajikan informasi ketersediaan kamar, harga sewa, fasilitas, dan foto asli yang akurat. Tim moderator KostBandung berhak mengubah, menangguhkan, atau menghapus iklan properti yang terindikasi fiktif, menyesatkan, atau melanggar norma hukum tanpa pemberitahuan sebelumnya.
                    </p>
                </div>

                {{-- Section 5 --}}
                <div class="p-5 sm:p-6 bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.2)] transition-all">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="w-7 h-7 bg-[#FFE500] border-2 border-black dark:border-zinc-700 text-black font-black text-xs rounded-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] shrink-0">
                            05
                        </span>
                        <h2 class="text-base sm:text-lg font-black text-black dark:text-white uppercase tracking-tight">
                            Perubahan &amp; Pembaruan Ketentuan
                        </h2>
                    </div>
                    <p class="text-xs sm:text-sm font-bold text-zinc-700 dark:text-zinc-300 leading-relaxed pl-10">
                        Kami berhak memperbarui atau merevisi Syarat &amp; Ketentuan ini sewaktu-waktu guna peningkatan kualitas layanan. Pembaruan akan ditampilkan pada halaman ini dan kelanjutan penggunaan platform dianggap sebagai persetujuan Anda terhadap perubahan tersebut.
                    </p>
                </div>

            </div>

            {{-- Footer Action Buttons --}}
            <div class="mt-10 pt-6 border-t-4 border-dashed border-black dark:border-zinc-700 flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="{{ route('home') }}" wire:navigate
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white hover:bg-zinc-100 text-black border-3 border-black font-black text-xs uppercase px-5 py-3 rounded-lg shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer dark:bg-zinc-800 dark:text-white dark:border-zinc-700 dark:hover:bg-zinc-700 dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.2)]">
                    <x-icon name="lucide-arrow-left" class="w-4 h-4 stroke-3" />
                    <span>Kembali ke Beranda</span>
                </a>

                @guest
                    <a href="{{ route('register') }}" wire:navigate
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#FFE500] hover:bg-yellow-400 text-black border-3 border-black font-black text-xs uppercase px-6 py-3 rounded-lg shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer dark:border-zinc-700 dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                        <span>Lanjut Mendaftar</span>
                        <x-icon name="lucide-arrow-right" class="w-4 h-4 stroke-3" />
                    </a>
                @endguest
            </div>

        </div>
    </div>
</x-app-layout>
