<div class="w-full">
    {{-- ===== Neo-Brutalist Register Card ===== --}}
    <div class="bg-white border-4 border-black p-8 md:p-10 shadow-[12px_12px_0px_0px_rgba(0,0,0,1)] rounded-lg w-full">

        {{-- Card Header --}}
        <div class="mb-8">
            <div class="inline-flex items-center gap-2 bg-[#FFE500] border-2 border-black px-3 py-1 rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mb-4">
                <span class="text-[10px] font-black uppercase tracking-widest text-black">Daftar Akun Baru</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-black uppercase tracking-tight leading-tight">
                Bergabung Sekarang!
            </h1>
            <p class="mt-2 text-sm font-bold text-zinc-600">
                Cari kost impian atau publikasikan properti Anda di KostBandung.id.
            </p>
        </div>

        {{-- Divider --}}
        <div class="border-t-4 border-black mb-8"></div>

        <form wire:submit.prevent="register" class="space-y-5">

            {{-- ===== Role Selector Tabs ===== --}}
            <div>
                <p class="block text-xs font-black uppercase tracking-wider text-black mb-3">Tipe Akun Saya</p>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" wire:click="$set('role', 'user')"
                        class="py-3.5 px-4 text-xs font-black text-black uppercase border-2 border-black rounded-lg transition-all cursor-pointer focus:outline-none focus:ring-0 {{ $role === 'user'
                            ? 'bg-[#FFE500] shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] -translate-x-0.5 -translate-y-0.5'
                            : 'bg-white hover:bg-zinc-100 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none' }}">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            <span>Pencari Kost</span>
                        </div>
                    </button>
                    <button type="button" wire:click="$set('role', 'owner')"
                        class="py-3.5 px-4 text-xs font-black text-black uppercase border-2 border-black rounded-lg transition-all cursor-pointer focus:outline-none focus:ring-0 {{ $role === 'owner'
                            ? 'bg-[#FFE500] shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] -translate-x-0.5 -translate-y-0.5'
                            : 'bg-white hover:bg-zinc-100 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none' }}">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                            <span>Pemilik Kost</span>
                        </div>
                    </button>
                </div>
                @error('role')
                    <p class="mt-2 text-xs font-bold text-rose-600">✕ {{ $message }}</p>
                @enderror
            </div>

            {{-- Divider --}}
            <div class="border-t-2 border-dashed border-zinc-300"></div>

            {{-- Nama Lengkap --}}
            <div>
                <label for="name" class="block text-xs font-black uppercase tracking-wider text-black mb-2">
                    Nama Lengkap
                </label>
                <input wire:model="name" type="text" id="name" autocomplete="name"
                    class="w-full px-4 py-3 text-sm bg-zinc-50 border-3 border-black rounded-lg text-black placeholder-zinc-400 focus:outline-none focus:ring-0 focus:bg-[#FFE500]/10 focus:border-black transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]"
                    placeholder="Nama lengkap Anda">
                @error('name')
                    <p class="mt-2 text-xs font-bold text-rose-600">✕ {{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs font-black uppercase tracking-wider text-black mb-2">
                    Alamat Email
                </label>
                <input wire:model="email" type="email" id="email" autocomplete="email"
                    class="w-full px-4 py-3 text-sm bg-zinc-50 border-3 border-black rounded-lg text-black placeholder-zinc-400 focus:outline-none focus:ring-0 focus:bg-[#FFE500]/10 focus:border-black transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]"
                    placeholder="nama@email.com">
                @error('email')
                    <p class="mt-2 text-xs font-bold text-rose-600">✕ {{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-xs font-black uppercase tracking-wider text-black mb-2">
                    Kata Sandi
                    <span class="normal-case font-bold text-zinc-500 ml-1">(min. 8 karakter, huruf + angka)</span>
                </label>
                <input wire:model="password" type="password" id="password" autocomplete="new-password"
                    class="w-full px-4 py-3 text-sm bg-zinc-50 border-3 border-black rounded-lg text-black placeholder-zinc-400 focus:outline-none focus:ring-0 focus:bg-[#FFE500]/10 focus:border-black transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]"
                    placeholder="Min. 8 karakter, huruf dan angka">
                @error('password')
                    <p class="mt-2 text-xs font-bold text-rose-600">✕ {{ $message }}</p>
                @enderror
            </div>

            {{-- Konfirmasi Password --}}
            <div>
                <label for="password_confirmation" class="block text-xs font-black uppercase tracking-wider text-black mb-2">
                    Konfirmasi Kata Sandi
                </label>
                <input wire:model="password_confirmation" type="password" id="password_confirmation" autocomplete="new-password"
                    class="w-full px-4 py-3 text-sm bg-zinc-50 border-3 border-black rounded-lg text-black placeholder-zinc-400 focus:outline-none focus:ring-0 focus:bg-[#FFE500]/10 focus:border-black transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]"
                    placeholder="Ulangi kata sandi">
            </div>

            {{-- ===== Dynamic Owner Fields ===== --}}
            @if ($role === 'owner')
                <div class="rounded-lg border-4 border-black bg-zinc-50 p-5 space-y-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]"
                    x-data x-show="true"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="flex items-center gap-2 pb-1 border-b-2 border-black">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                        <p class="text-xs font-black uppercase tracking-wider text-black">Data Pemilik Kost</p>
                    </div>

                    {{-- Nomor WhatsApp / Telepon Bisnis --}}
                    <div>
                        <label for="phone_number" class="block text-xs font-black uppercase tracking-wider text-black mb-2">
                            Nomor WhatsApp / Telepon Bisnis <span class="text-rose-500">*</span>
                        </label>
                        <input wire:model="phone_number" type="tel" id="phone_number"
                            class="w-full px-4 py-3 text-sm bg-white border-3 border-black rounded-lg text-black placeholder-zinc-400 focus:outline-none focus:ring-0 focus:bg-[#FFE500]/10 focus:border-black transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]"
                            placeholder="Contoh: 081234567890">
                        @error('phone_number')
                            <p class="mt-2 text-xs font-bold text-rose-600">✕ {{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Properti / Usaha Kost --}}
                    <div>
                        <label for="business_name" class="block text-xs font-black uppercase tracking-wider text-black mb-2">
                            Nama Properti / Usaha Kost <span class="text-rose-500">*</span>
                        </label>
                        <input wire:model="business_name" type="text" id="business_name"
                            class="w-full px-4 py-3 text-sm bg-white border-3 border-black rounded-lg text-black placeholder-zinc-400 focus:outline-none focus:ring-0 focus:bg-[#FFE500]/10 focus:border-black transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]"
                            placeholder="Contoh: Kost Putra Sejahtera">
                        @error('business_name')
                            <p class="mt-2 text-xs font-bold text-rose-600">✕ {{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endif
            {{-- ===== End Dynamic Owner Fields ===== --}}

            {{-- Submit Button --}}
            <button type="submit"
                class="w-full py-4 px-6 bg-[#FFE500] hover:bg-yellow-400 text-black border-4 border-black font-black text-sm uppercase shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg flex items-center justify-center gap-2 cursor-pointer mt-2"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-60 cursor-not-allowed">
                <span wire:loading.remove wire:target="register">Daftar Sekarang →</span>
                <span wire:loading wire:target="register" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Memproses...
                </span>
            </button>
        </form>

        {{-- Divider --}}
        <div class="my-7 border-t-4 border-dashed border-black"></div>

        {{-- Login Link --}}
        <p class="text-center text-xs font-bold text-black">
            Sudah punya akun?
            <a href="{{ route('login') }}" wire:navigate
                class="font-black text-black underline decoration-2 underline-offset-2 hover:bg-[#FFE500] hover:no-underline px-1 rounded transition-all">
                Masuk Di Sini
            </a>
        </p>
    </div>
</div>
