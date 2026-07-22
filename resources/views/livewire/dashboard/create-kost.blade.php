<div class="min-h-screen bg-[#f8f9fa] bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] bg-[size:24px_24px]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-8">

        <!-- Top Header & Back Button -->
        <div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-xs font-black uppercase text-black bg-white border-2 border-black px-3.5 py-2 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-yellow-300 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg mb-6 group">
                <svg class="w-4 h-4 text-black group-hover:-translate-x-1 transition-transform stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Kembali ke Dashboard</span>
            </a>
            
            <div class="bg-yellow-300 border-4 border-black p-6 md:p-8 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] rounded-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <span class="px-3 py-1 bg-black text-yellow-300 font-extrabold text-xs uppercase tracking-wider border border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        Form Pendaftaran
                    </span>
                    <h1 class="text-3xl md:text-4xl font-black text-black tracking-tight uppercase mt-2">
                        Tambah Properti Kost Baru
                    </h1>
                    <p class="text-sm font-bold text-black/80 mt-1">
                        Isi detail properti kost Anda dengan lengkap untuk menarik minat pencari kost di Kota Bandung.
                    </p>
                </div>
            </div>
        </div>

        <!-- Form Start -->
        <form wire:submit.prevent="save" class="space-y-8">

            <!-- Seksi 1: Informasi Dasar -->
            <div class="bg-white rounded-xl p-6 md:p-8 border-3 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-6">
                <div class="flex items-center gap-3 border-b-3 border-black pb-4">
                    <div class="w-10 h-10 rounded bg-black text-yellow-300 border-2 border-black font-black text-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        1
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-black uppercase tracking-tight">Informasi Dasar Kost</h2>
                        <p class="text-xs font-bold text-zinc-600">Nama, tipe kategori, dan deskripsi umum properti</p>
                    </div>
                </div>

                <!-- Nama Kost -->
                <div class="space-y-2">
                    <label for="name" class="block text-xs font-black uppercase tracking-wider text-black">
                        Nama Properti Kost <span class="text-rose-600">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        wire:model="name" 
                        placeholder="Contoh: Kost Eksklusif Dago Asri" 
                        class="w-full bg-white border-2 border-black rounded-lg px-4 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all"
                    >
                    @error('name')
                        <p class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipe Kost (Pill Radio Buttons) -->
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-black">
                        Tipe Kategori Penghuni <span class="text-rose-600">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="gender_type" value="campur" class="peer sr-only">
                            <div class="px-4 py-3.5 rounded-lg border-2 border-black text-center font-black text-xs md:text-sm text-black bg-zinc-50 hover:bg-yellow-100 peer-checked:bg-yellow-400 peer-checked:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 md:w-5 md:h-5 text-black stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <span>Campur</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" wire:model="gender_type" value="putri" class="peer sr-only">
                            <div class="px-4 py-3.5 rounded-lg border-2 border-black text-center font-black text-xs md:text-sm text-black bg-zinc-50 hover:bg-pink-100 peer-checked:bg-pink-400 peer-checked:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 md:w-5 md:h-5 text-black stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z"/>
                                    <circle cx="12" cy="7" r="4" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 9c.5.8 1.5 1.2 2.5 1.2s2-.4 2.5-1.2"/>
                                </svg>
                                <span>Khusus Putri</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" wire:model="gender_type" value="putra" class="peer sr-only">
                            <div class="px-4 py-3.5 rounded-lg border-2 border-black text-center font-black text-xs md:text-sm text-black bg-zinc-50 hover:bg-cyan-100 peer-checked:bg-cyan-300 peer-checked:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4 md:w-5 md:h-5 text-black stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14c-4.418 0-8 2.239-8 5v1h16v-1c0-2.761-3.582-5-8-5z"/>
                                </svg>
                                <span>Khusus Putra</span>
                            </div>
                        </label>
                    </div>
                    @error('gender_type')
                        <p class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deskripsi Kost -->
                <div class="space-y-2">
                    <label for="description" class="block text-xs font-black uppercase tracking-wider text-black">
                        Deskripsi Lengkap <span class="text-rose-600">*</span>
                    </label>
                    <textarea 
                        id="description" 
                        wire:model="description" 
                        rows="4" 
                        placeholder="Jelaskan keunggulan, aksesibilitas ke kampus/perkantoran, dan suasana hunian kost Anda..." 
                        class="w-full bg-white border-2 border-black rounded-lg p-4 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all"
                    ></textarea>
                    @error('description')
                        <p class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Seksi 2: Lokasi -->
            <div class="bg-white rounded-xl p-6 md:p-8 border-3 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-6">
                <div class="flex items-center gap-3 border-b-3 border-black pb-4">
                    <div class="w-10 h-10 rounded bg-black text-yellow-300 border-2 border-black font-black text-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        2
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-black uppercase tracking-tight">Lokasi Kost</h2>
                        <p class="text-xs font-bold text-zinc-600">Area kecamatan dan alamat fisik properti di Bandung</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Dropdown Kecamatan -->
                    <div class="space-y-2 md:col-span-1">
                        <label for="district" class="block text-xs font-black uppercase tracking-wider text-black">
                            Kecamatan <span class="text-rose-600">*</span>
                        </label>
                        <select 
                            id="district" 
                            wire:model="district" 
                            class="w-full bg-white border-2 border-black rounded-lg px-4 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] cursor-pointer transition-all"
                        >
                            @foreach($districts as $dist)
                                <option value="{{ $dist }}">{{ $dist }}</option>
                            @endforeach
                        </select>
                        @error('district')
                            <p class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Alamat Lengkap -->
                    <div class="space-y-2 md:col-span-2">
                        <label for="address" class="block text-xs font-black uppercase tracking-wider text-black">
                            Alamat Lengkap <span class="text-rose-600">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="address" 
                            wire:model="address" 
                            placeholder="Contoh: Jl. Dipatiukur No. 80, RT 02/RW 05" 
                            class="w-full bg-white border-2 border-black rounded-lg px-4 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] transition-all"
                        >
                        @error('address')
                            <p class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Seksi 3: Harga & Fasilitas -->
            <div class="bg-white rounded-xl p-6 md:p-8 border-3 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-6">
                <div class="flex items-center gap-3 border-b-3 border-black pb-4">
                    <div class="w-10 h-10 rounded bg-black text-yellow-300 border-2 border-black font-black text-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        3
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-black uppercase tracking-tight">Harga & Fasilitas</h2>
                        <p class="text-xs font-bold text-zinc-600">Tarif sewa bulanan dan fasilitas pendukung yang disediakan</p>
                    </div>
                </div>

                <!-- Harga Sewa per Bulan -->
                <div class="space-y-2 max-w-md">
                    <label for="price_monthly" class="block text-xs font-black uppercase tracking-wider text-black">
                        Harga Sewa Per Bulan (IDR) <span class="text-rose-600">*</span>
                    </label>
                    <div class="relative rounded-lg overflow-hidden flex border-2 border-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                        <div class="bg-yellow-300 border-r-2 border-black px-4 flex items-center font-black text-sm text-black">
                            Rp
                        </div>
                        <input 
                            type="number" 
                            id="price_monthly" 
                            wire:model="price_monthly" 
                            placeholder="1500000" 
                            class="w-full bg-white px-4 py-3 text-sm font-black text-black focus:outline-none focus:bg-yellow-50"
                        >
                        <div class="bg-zinc-100 border-l-2 border-black px-4 flex items-center text-xs font-black text-black uppercase">
                            / Bln
                        </div>
                    </div>
                    @error('price_monthly')
                        <p class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Checkbox Fasilitas Kost -->
                <div class="space-y-3 pt-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-black">
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
                                <div class="px-4 py-3 rounded-lg border-2 border-black bg-zinc-50 text-black text-xs font-black flex items-center justify-between peer-checked:bg-lime-300 peer-checked:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:bg-yellow-100 transition-all">
                                    <span>{{ $facility->name }}</span>
                                    <span class="w-5 h-5 rounded border-2 border-black bg-white flex items-center justify-center text-black opacity-0 peer-checked:opacity-100 font-black text-xs">✓</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('selectedFacilities')
                        <p class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Seksi 4: Foto Utama Kost -->
            <div class="bg-white rounded-xl p-6 md:p-8 border-3 border-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-6">
                <div class="flex items-center gap-3 border-b-3 border-black pb-4">
                    <div class="w-10 h-10 rounded bg-black text-yellow-300 border-2 border-black font-black text-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        4
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-black uppercase tracking-tight">Foto Utama Properti</h2>
                        <p class="text-xs font-bold text-zinc-600">Unggah foto fasad atau kamar terbaik properti kost Anda</p>
                    </div>
                </div>

                <!-- Drag & Drop Upload Dropzone -->
                <div 
                    x-data="{
                        isUploading: false,
                        progress: 0,
                        startUpload() {
                            this.isUploading = true;
                            this.progress = 0;
                        },
                        updateProgress(val) {
                            this.progress = val;
                        },
                        finishUpload() {
                            this.progress = 100;
                            setTimeout(() => {
                                this.isUploading = false;
                                this.progress = 0;
                            }, 400);
                        },
                        errorUpload() {
                            this.isUploading = false;
                            this.progress = 0;
                        }
                    }"
                    x-on:livewire-upload-start="startUpload()"
                    x-on:livewire-upload-finish="finishUpload()"
                    x-on:livewire-upload-error="errorUpload()"
                    x-on:livewire-upload-progress="updateProgress($event.detail.progress)"
                    class="space-y-4"
                >
                    <div class="relative border-3 border-dashed border-black rounded-xl p-8 text-center bg-yellow-100/70 hover:bg-yellow-200/80 transition-all cursor-pointer group shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                        <input 
                            type="file" 
                            wire:model="photo" 
                            accept="image/*" 
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                        >

                        <div class="space-y-3 pointer-events-none">
                            <div class="w-14 h-14 rounded-lg bg-white border-2 border-black flex items-center justify-center mx-auto text-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] group-hover:scale-110 transition-transform">
                                <svg class="w-7 h-7 stroke-[2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>

                            <div>
                                <p class="text-sm font-black text-black uppercase">
                                    Klik atau seret file foto ke area ini
                                </p>
                                <p class="text-xs font-bold text-zinc-600 mt-1">
                                    Format: JPG, PNG, WEBP — Maksimal file: 2MB
                                </p>
                                <span class="inline-block mt-3 bg-yellow-400 text-black font-black text-xs uppercase px-4 py-2 border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] group-hover:bg-yellow-300 transition-all rounded">
                                    Pilih File Foto
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Neo-Brutalist Upload Progress Bar -->
                    <div 
                        x-show="isUploading" 
                        x-cloak 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="bg-lime-100 border-3 border-black p-4 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-2.5 font-black text-black"
                    >
                        <div class="flex items-center justify-between text-xs uppercase">
                            <span class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="progress < 100 ? 'Uploading...' : 'Selesai Mengunggah!'"></span>
                            </span>
                            <span x-text="progress + '%'" class="bg-yellow-300 border-2 border-black px-2.5 py-0.5 rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] text-xs font-black">0%</span>
                        </div>

                        <!-- Progress Track -->
                        <div class="w-full bg-white border-2 border-black rounded-lg h-6 p-0.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] relative overflow-hidden">
                            <!-- Progress Fill -->
                            <div 
                                class="bg-lime-400 border-r-2 border-black h-full transition-all duration-300 ease-out rounded-sm" 
                                :style="'width: ' + progress + '%'"
                            ></div>
                        </div>
                    </div>

                    @error('photo')
                        <p class="text-xs font-black text-rose-600 bg-rose-100 border-2 border-rose-500 px-2.5 py-1 rounded-md mt-1 inline-block">{{ $message }}</p>
                    @enderror

                    <!-- Preview Photo -->
                    @if ($photo)
                        <div class="mt-4 p-4 bg-lime-100 border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] rounded-xl flex flex-col md:flex-row items-center gap-4">
                            <img src="{{ $photo->temporaryUrl() }}" alt="Preview Foto" class="w-32 h-24 object-cover rounded border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            <div class="flex-1 text-center md:text-left">
                                <p class="text-xs font-black text-black uppercase">Preview Foto Utama</p>
                                <p class="text-xs font-bold text-zinc-700 mt-0.5">Foto siap disimpan sebagai tampilan utama iklan kost Anda.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Submit & Action Buttons -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t-3 border-black">
                <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-white hover:bg-zinc-100 text-black border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded">
                    Batal
                </a>

                <button 
                    type="submit" 
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="px-8 py-3.5 bg-yellow-400 hover:bg-yellow-300 text-black border-3 border-black font-black text-sm uppercase shadow-[5px_5px_0px_0px_rgba(0,0,0,1)] active:translate-x-1 active:translate-y-1 active:shadow-none transition-all rounded inline-flex items-center gap-2 disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="save">Simpan Properti Kost</span>
                    <span wire:loading wire:target="save" class="inline-flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
