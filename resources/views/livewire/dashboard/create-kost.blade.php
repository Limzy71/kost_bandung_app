<div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-8">

        <!-- Top Header & Back Button -->
        <div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-gray-500 hover:text-gray-950 transition-colors mb-4 group">
                <svg class="w-4 h-4 text-gray-400 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali ke Dashboard</span>
            </a>
            
            <div class="bg-white p-6 md:p-8 rounded-3xl border border-gray-100 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs font-semibold rounded-full uppercase tracking-wider">
                        Form Pendaftaran
                    </span>
                    <h1 class="text-3xl font-extrabold text-gray-950 tracking-tight mt-2">
                        Tambah Properti Kost Baru
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Isi detail properti kost Anda dengan lengkap untuk menarik minat pencari kost di Kota Bandung.
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Start -->
        <form wire:submit.prevent="save" class="space-y-8">

            <!-- Seksi 1: Informasi Dasar -->
            <div class="bg-white rounded-3xl p-6 md:p-8 border border-gray-200 shadow-sm space-y-6">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                    <div class="w-10 h-10 rounded-2xl bg-gray-950 text-white flex items-center justify-center font-bold text-sm">
                        1
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-950 tracking-tight">Informasi Dasar Kost</h2>
                        <p class="text-xs text-gray-500">Nama, tipe kategori, dan deskripsi umum properti</p>
                    </div>
                </div>

                <!-- Nama Kost -->
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-semibold text-gray-900">
                        Nama Properti Kost <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        wire:model="name" 
                        placeholder="Contoh: Kost Eksklusif Dago Asri" 
                        class="w-full bg-gray-50/50 border border-gray-200 rounded-2xl px-4 py-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-950 focus:bg-white transition"
                    >
                    @error('name')
                        <p class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipe Kost (Pill Radio Buttons) -->
                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-gray-900">
                        Tipe Kategori Penghuni <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="gender_type" value="campur" class="peer sr-only">
                            <div class="px-4 py-3.5 rounded-2xl border border-gray-200 text-center font-semibold text-xs md:text-sm text-gray-700 peer-checked:border-gray-950 peer-checked:bg-gray-950 peer-checked:text-white transition-all shadow-sm flex items-center justify-center gap-2">
                                <span>👫 Campur</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" wire:model="gender_type" value="putri" class="peer sr-only">
                            <div class="px-4 py-3.5 rounded-2xl border border-gray-200 text-center font-semibold text-xs md:text-sm text-gray-700 peer-checked:border-gray-950 peer-checked:bg-gray-950 peer-checked:text-white transition-all shadow-sm flex items-center justify-center gap-2">
                                <span>👩 Khusus Putri</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" wire:model="gender_type" value="putra" class="peer sr-only">
                            <div class="px-4 py-3.5 rounded-2xl border border-gray-200 text-center font-semibold text-xs md:text-sm text-gray-700 peer-checked:border-gray-950 peer-checked:bg-gray-950 peer-checked:text-white transition-all shadow-sm flex items-center justify-center gap-2">
                                <span>👨 Khusus Putra</span>
                            </div>
                        </label>
                    </div>
                    @error('gender_type')
                        <p class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi Kost -->
                <div class="space-y-2">
                    <label for="description" class="block text-sm font-semibold text-gray-900">
                        Deskripsi Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <textarea 
                        id="description" 
                        wire:model="description" 
                        rows="4" 
                        placeholder="Jelaskan keunggulan, aksesibilitas ke kampus/perkantoran, dan suasana hunian kost Anda..." 
                        class="w-full bg-gray-50/50 border border-gray-200 rounded-2xl p-4 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-950 focus:bg-white transition"
                    ></textarea>
                    @error('description')
                        <p class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Seksi 2: Lokasi -->
            <div class="bg-white rounded-3xl p-6 md:p-8 border border-gray-200 shadow-sm space-y-6">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                    <div class="w-10 h-10 rounded-2xl bg-gray-950 text-white flex items-center justify-center font-bold text-sm">
                        2
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-950 tracking-tight">Lokasi Kost</h2>
                        <p class="text-xs text-gray-500">Area kecamatan dan alamat fisik properti di Bandung</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Dropdown Kecamatan -->
                    <div class="space-y-2 md:col-span-1">
                        <label for="district" class="block text-sm font-semibold text-gray-900">
                            Kecamatan <span class="text-rose-500">*</span>
                        </label>
                        <select 
                            id="district" 
                            wire:model="district" 
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-2xl px-4 py-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-950 focus:bg-white transition"
                        >
                            @foreach($districts as $dist)
                                <option value="{{ $dist }}">{{ $dist }}</option>
                            @endforeach
                        </select>
                        @error('district')
                            <p class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Alamat Lengkap -->
                    <div class="space-y-2 md:col-span-2">
                        <label for="address" class="block text-sm font-semibold text-gray-900">
                            Alamat Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="address" 
                            wire:model="address" 
                            placeholder="Contoh: Jl. Dipatiukur No. 80, RT 02/RW 05" 
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-2xl px-4 py-3 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-950 focus:bg-white transition"
                        >
                        @error('address')
                            <p class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Seksi 3: Harga & Fasilitas -->
            <div class="bg-white rounded-3xl p-6 md:p-8 border border-gray-200 shadow-sm space-y-6">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                    <div class="w-10 h-10 rounded-2xl bg-gray-950 text-white flex items-center justify-center font-bold text-sm">
                        3
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-950 tracking-tight">Harga & Fasilitas</h2>
                        <p class="text-xs text-gray-500">Tarif sewa bulanan dan fasilitas pendukung yang disediakan</p>
                    </div>
                </div>

                <!-- Harga Sewa per Bulan -->
                <div class="space-y-2 max-w-md">
                    <label for="price_monthly" class="block text-sm font-semibold text-gray-900">
                        Harga Sewa Per Bulan (IDR) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative rounded-2xl overflow-hidden shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500 font-bold text-sm">
                            Rp
                        </div>
                        <input 
                            type="number" 
                            id="price_monthly" 
                            wire:model="price_monthly" 
                            placeholder="1500000" 
                            class="w-full bg-gray-50/50 border border-gray-200 rounded-2xl pl-12 pr-16 py-3 text-sm font-bold text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-950 focus:bg-white transition"
                        >
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400 text-xs font-semibold">
                            / Bulan
                        </div>
                    </div>
                    @error('price_monthly')
                        <p class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Checkbox Fasilitas Kost -->
                <div class="space-y-3 pt-2">
                    <label class="block text-sm font-semibold text-gray-900">
                        Fasilitas Kost (Pilih yang tersedia)
                    </label>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($facilities as $facility)
                            <label class="cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    wire:model="selectedFacilities" 
                                    value="{{ $facility->id }}" 
                                    class="peer sr-only"
                                >
                                <div class="px-4 py-3 rounded-2xl border border-gray-200 bg-gray-50/50 text-gray-700 text-xs font-semibold flex items-center justify-between peer-checked:border-gray-950 peer-checked:bg-gray-950 peer-checked:text-white transition-all shadow-sm hover:border-gray-300">
                                    <span>{{ $facility->name }}</span>
                                    <svg class="w-4 h-4 text-emerald-400 opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('selectedFacilities')
                        <p class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Seksi 4: Foto Utama Kost -->
            <div class="bg-white rounded-3xl p-6 md:p-8 border border-gray-200 shadow-sm space-y-6">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4">
                    <div class="w-10 h-10 rounded-2xl bg-gray-950 text-white flex items-center justify-center font-bold text-sm">
                        4
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-950 tracking-tight">Foto Utama Properti</h2>
                        <p class="text-xs text-gray-500">Unggah foto fasad atau kamar terbaik properti kost Anda</p>
                    </div>
                </div>

                <!-- Drag & Drop Upload Dropzone -->
                <div class="space-y-3">
                    <div class="relative border-2 border-dashed border-gray-300 hover:border-gray-950 rounded-3xl p-8 text-center bg-gray-50/50 hover:bg-gray-50 transition-all cursor-pointer group">
                        <input 
                            type="file" 
                            wire:model="photo" 
                            accept="image/*" 
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                        >

                        <div class="space-y-3 pointer-events-none">
                            <div class="w-14 h-14 rounded-2xl bg-white border border-gray-200 flex items-center justify-center mx-auto text-gray-700 group-hover:scale-110 transition-transform shadow-sm">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>

                            <div>
                                <p class="text-sm font-bold text-gray-900">
                                    Klik atau seret file foto ke area ini
                                </p>
                                <p class="text-xs text-gray-400 mt-1">
                                    Format: JPG, PNG, WEBP — Maksimal file: 2MB
                                </p>
                            </div>
                        </div>

                        <!-- Loading Indicator -->
                        <div wire:loading wire:target="photo" class="absolute inset-0 bg-white/90 rounded-3xl flex items-center justify-center z-20">
                            <div class="flex items-center gap-2 text-sm font-bold text-gray-900">
                                <svg class="animate-spin h-5 w-5 text-gray-950" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Mengunggah foto...</span>
                            </div>
                        </div>
                    </div>

                    @error('photo')
                        <p class="text-xs text-rose-600 font-medium mt-1">{{ $message }}</p>
                    @enderror

                    <!-- Preview Photo -->
                    @if ($photo)
                        <div class="mt-4 p-4 bg-gray-50 rounded-2xl border border-gray-200 flex flex-col md:flex-row items-center gap-4">
                            <img src="{{ $photo->temporaryUrl() }}" alt="Preview Foto" class="w-32 h-24 object-cover rounded-xl shadow-sm">
                            <div class="flex-1 text-center md:text-left">
                                <p class="text-xs font-bold text-gray-900">Preview Foto Utama</p>
                                <p class="text-xs text-gray-500 mt-0.5">Foto siap disimpan sebagai tampilan utama iklan kost Anda.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Submit & Action Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-full text-sm font-semibold transition">
                    Batal
                </a>

                <button 
                    type="submit" 
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="px-8 py-3.5 bg-gray-950 hover:bg-gray-800 text-white rounded-full font-bold text-sm shadow-lg transition-all inline-flex items-center gap-2 group disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="save">Simpan Properti Kost</span>
                    <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menyimpan...
                    </span>
                </button>
            </div>

        </form>

    </div>
</div>
