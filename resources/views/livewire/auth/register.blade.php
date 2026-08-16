<div class="w-full" x-data="{
    locked: @entangle('rateLimitSeconds'),
    lockedSeconds: 0,
    timer: null,
    init() {
        this.lockedSeconds = this.locked;
        this.startTimer();
        this.$watch('locked', (val) => {
            this.lockedSeconds = val;
            this.startTimer();
        });
    },
    startTimer() {
        if (this.timer) clearInterval(this.timer);
        if (this.lockedSeconds <= 0) {
            this.timer = null;
            return;
        }
        this.timer = setInterval(() => {
            if (this.lockedSeconds > 0) {
                this.lockedSeconds--;
            } else {
                clearInterval(this.timer);
                this.timer = null;
            }
        }, 1000);
    }
}">
    {{-- ===== Neo-Brutalist Register Card ===== --}}
    <div class="bg-white border-4 border-black p-8 md:p-10 shadow-[12px_12px_0px_0px_rgba(0,0,0,1)] rounded-lg w-full dark:bg-zinc-900 dark:border-zinc-700 dark:shadow-[12px_12px_0px_0px_rgba(255,255,255,0.25)]">

        {{-- Card Header --}}
        <div class="mb-8">
            <div class="inline-flex items-center gap-2 bg-[#FFE500] border-2 border-black px-3 py-1 rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mb-4 dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                <span class="text-[10px] font-black uppercase tracking-widest text-black">Daftar Akun Baru</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-black uppercase tracking-tight leading-tight dark:text-white">
                Bergabung Sekarang!
            </h1>
            <p class="mt-2 text-sm font-bold text-zinc-600 dark:text-zinc-400">
                Cari kost impian atau publikasikan properti Anda di KostBandung
            </p>
        </div>

        {{-- Divider --}}
        <div class="border-t-4 border-black mb-8 dark:border-zinc-700"></div>

        {{-- Rate Limit / General Error Banner --}}
        <div x-cloak x-show="lockedSeconds > 0" class="mb-6 flex items-center gap-3 bg-rose-400 border-4 border-black p-4 rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:border-zinc-700 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]">
            <x-icon name="lucide-triangle-alert" class="w-5 h-5 text-black shrink-0 stroke-[3]" />
            <span class="text-xs font-black uppercase text-black">
                TERLALU BANYAK PENDAFTARAN DARI PERANGKAT INI. SILAKAN TUNGGU <span x-text="lockedSeconds"></span> DETIK.
            </span>
        </div>

        <form wire:submit="register" class="space-y-5">

            {{-- ===== Role Selector Tabs ===== --}}
            <div>
                <p class="block text-xs font-black uppercase tracking-wider text-black mb-3 dark:text-white">Tipe Akun Saya</p>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" wire:click="$set('role', 'user')"
                        class="py-3.5 px-4 text-xs font-black text-black uppercase border-2 border-black rounded-lg transition-all cursor-pointer focus:outline-none focus:ring-0 dark:text-white dark:border-zinc-700 {{ $role === 'user'
                            ? 'bg-[#FFE500] shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] -translate-x-0.5 -translate-y-0.5'
                            : 'bg-white hover:bg-zinc-100 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none dark:bg-zinc-900 dark:hover:bg-zinc-800 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]' }}">
                        <div class="flex items-center justify-center gap-2">
                            <x-icon name="lucide-search" class="w-4 h-4" />
                            <span>Pencari Kost</span>
                        </div>
                    </button>
                    <button type="button" wire:click="$set('role', 'owner')"
                        class="py-3.5 px-4 text-xs font-black text-black uppercase border-2 border-black rounded-lg transition-all cursor-pointer focus:outline-none focus:ring-0 dark:text-white dark:border-zinc-700 {{ $role === 'owner'
                            ? 'bg-[#FFE500] shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] -translate-x-0.5 -translate-y-0.5'
                            : 'bg-white hover:bg-zinc-100 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none dark:bg-zinc-900 dark:hover:bg-zinc-800 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]' }}">
                        <div class="flex items-center justify-center gap-2">
                            <x-icon name="lucide-house" class="w-4 h-4" />
                            <span>Pemilik Kost</span>
                        </div>
                    </button>
                </div>
                @error('role')
                    <p class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400">✕ {{ $message }}</p>
                @enderror
            </div>

            {{-- Divider --}}
            <div class="border-t-2 border-dashed border-zinc-300"></div>

            {{-- Nama Lengkap --}}
            <div>
                <label for="name" class="block text-xs font-black uppercase tracking-wider text-black mb-2 dark:text-white">
                    Nama Lengkap
                </label>
                <input wire:model="name" type="text" id="name" autocomplete="name"
                    class="w-full px-4 py-3 text-sm bg-zinc-50 border-3 @error('name') border-rose-600 @else border-black @enderror rounded-lg text-black placeholder-zinc-400 focus:outline-none focus:ring-0 focus:bg-[#FFE500]/10 focus:border-black transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:bg-zinc-900 dark:border-zinc-700 dark:text-white dark:placeholder-zinc-500 dark:focus:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] dark:focus:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]"
                    placeholder="Nama lengkap Anda">
                @error('name')
                    <p class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400">✕ {{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs font-black uppercase tracking-wider text-black mb-2 dark:text-white">
                    Alamat Email
                </label>
                <input wire:model="email" type="email" id="email" autocomplete="email"
                    class="w-full px-4 py-3 text-sm bg-zinc-50 border-3 @error('email') border-rose-600 @else border-black @enderror rounded-lg text-black placeholder-zinc-400 focus:outline-none focus:ring-0 focus:bg-[#FFE500]/10 focus:border-black transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:bg-zinc-900 dark:border-zinc-700 dark:text-white dark:placeholder-zinc-500 dark:focus:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] dark:focus:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]"
                    placeholder="nama@email.com">
                @error('email')
                    <p x-show="lockedSeconds == 0" class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400">✕ {{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-xs font-black uppercase tracking-wider text-black mb-2 dark:text-white">
                    Kata Sandi
                    <span class="normal-case font-bold text-zinc-500 ml-1 dark:text-zinc-400">(min. 8 karakter, huruf + angka)</span>
                </label>
                <div class="relative flex items-center" x-data="{ show: false }">
                    <input wire:model="password" :type="show ? 'text' : 'password'" id="password" autocomplete="new-password"
                        class="block w-full px-4 pr-12 py-3 text-sm bg-zinc-50 border-3 border-black rounded-lg text-black placeholder-zinc-400 focus:outline-none focus:ring-0 focus:bg-[#FFE500]/10 focus:border-black transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:bg-zinc-900 dark:border-zinc-700 dark:text-white dark:placeholder-zinc-500 dark:focus:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] dark:focus:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]"
                        placeholder="Min. 8 karakter, huruf dan angka">
                    <button type="button" @click="show = !show" :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                        class="absolute inset-y-0 right-1 flex items-center px-3 text-zinc-500 hover:text-black transition-colors cursor-pointer focus:outline-none dark:text-zinc-400 dark:hover:text-white">
                        <x-icon x-show="!show" name="lucide-eye" class="w-5 h-5" />
                        <x-icon x-cloak x-show="show" name="lucide-eye-off" class="w-5 h-5" />
                    </button>
                </div>
                @error('password')
                    <p class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400">✕ {{ $message }}</p>
                @enderror
            </div>

            {{-- Konfirmasi Password --}}
            <div>
                <label for="password_confirmation" class="block text-xs font-black uppercase tracking-wider text-black mb-2 dark:text-white">
                    Konfirmasi Kata Sandi
                </label>
                <div class="relative flex items-center" x-data="{ show: false }">
                    <input wire:model="password_confirmation" :type="show ? 'text' : 'password'" id="password_confirmation" autocomplete="new-password"
                        class="block w-full px-4 pr-12 py-3 text-sm bg-zinc-50 border-3 border-black rounded-lg text-black placeholder-zinc-400 focus:outline-none focus:ring-0 focus:bg-[#FFE500]/10 focus:border-black transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:bg-zinc-900 dark:border-zinc-700 dark:text-white dark:placeholder-zinc-500 dark:focus:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] dark:focus:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]"
                        placeholder="Ulangi kata sandi">
                    <button type="button" @click="show = !show" :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                        class="absolute inset-y-0 right-1 flex items-center px-3 text-zinc-500 hover:text-black transition-colors cursor-pointer focus:outline-none dark:text-zinc-400 dark:hover:text-white">
                        <x-icon x-show="!show" name="lucide-eye" class="w-5 h-5" />
                        <x-icon x-cloak x-show="show" name="lucide-eye-off" class="w-5 h-5" />
                    </button>
                </div>
            </div>

            {{-- Nomor WhatsApp / Telepon --}}
            <div>
                <label for="phone_number" class="block text-xs font-black uppercase tracking-wider text-black mb-2 dark:text-white">
                    Nomor WhatsApp <span class="text-rose-500">*</span>
                </label>
                <input wire:model="phone_number" type="tel" id="phone_number" inputmode="numeric" oninput="let v = this.value.replace(/[^0-9]/g, ''); if(v.startsWith('62')) v = '0' + v.slice(2); else if(v.length > 0 && v[0] !== '0') v = '0' + v; this.value = v;" maxlength="16"
                    class="w-full px-4 py-3 text-sm bg-zinc-50 border-3 border-black rounded-lg text-black placeholder-zinc-400 focus:outline-none focus:ring-0 focus:bg-[#FFE500]/10 focus:border-black transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:bg-zinc-900 dark:border-zinc-700 dark:text-white dark:placeholder-zinc-500 dark:focus:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] dark:focus:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]"
                    placeholder="Contoh: 081234567890">
                @error('phone_number')
                    <p class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400">✕ {{ $message }}</p>
                @enderror
            </div>

            {{-- ===== Dynamic Owner Fields ===== --}}
            @if ($role === 'owner')
                <div class="rounded-lg border-4 border-black bg-zinc-50 p-5 space-y-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:border-zinc-700 dark:bg-zinc-900 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]"
                    x-data x-show="true"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="flex items-center gap-2 pb-1 border-b-2 border-black text-black dark:border-zinc-700 dark:text-white">
                        <x-icon name="lucide-house" class="w-5 h-5 text-black dark:text-white" />
                        <p class="text-xs font-black uppercase tracking-wider text-black dark:text-white">Data Pemilik Kost</p>
                    </div>

                    {{-- Nama Properti / Usaha Kost --}}
                    <div>
                        <label for="business_name" class="block text-xs font-black uppercase tracking-wider text-black mb-2 dark:text-white">
                            Nama Properti / Usaha Kost <span class="text-rose-500">*</span>
                        </label>
                        <input wire:model="business_name" type="text" id="business_name"
                            class="w-full px-4 py-3 text-sm bg-white border-3 border-black rounded-lg text-black placeholder-zinc-400 focus:outline-none focus:ring-0 focus:bg-[#FFE500]/10 focus:border-black transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:bg-zinc-900 dark:border-zinc-700 dark:text-white dark:placeholder-zinc-500 dark:focus:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] dark:focus:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]"
                            placeholder="Contoh: Kost Putra Sejahtera">
                        @error('business_name')
                            <p class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400">✕ {{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endif
            {{-- ===== End Dynamic Owner Fields ===== --}}

            {{-- Terms & Conditions --}}
            <div class="flex items-start gap-2.5">
                <input wire:model="terms" type="checkbox" id="terms"
                    class="w-4 h-4 mt-1 border-2 border-black rounded-sm bg-zinc-50 checked:bg-[#FFE500] checked:border-black focus:ring-0 focus:ring-offset-0 cursor-pointer dark:border-zinc-700 dark:bg-zinc-900 dark:checked:border-zinc-700">
                <label for="terms" class="text-xs font-bold text-black cursor-pointer dark:text-white">
                    Saya menyetujui
                    <a href="{{ route('terms') }}" target="_blank" class="font-black underline underline-offset-2 hover:text-yellow-600">Syarat & Ketentuan</a>
                    penggunaan layanan KostBandung.
                </label>
            </div>
            @error('terms')
                <p class="text-xs font-bold text-rose-600 dark:text-rose-400">✕ {{ $message }}</p>
            @enderror

            {{-- Submit Button --}}
            <button type="submit"
                x-bind:disabled="lockedSeconds > 0"
                x-bind:class="lockedSeconds > 0 ? 'opacity-60 cursor-not-allowed hover:-translate-x-0 hover:-translate-y-0 hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]' : ''"
                class="w-full py-4 px-6 bg-[#FFE500] hover:bg-yellow-400 text-black border-4 border-black font-black text-sm uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] active:translate-x-1 active:translate-y-1 active:shadow-none transition-all duration-75 rounded-lg flex items-center justify-center gap-2 cursor-pointer mt-2 dark:border-zinc-600 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)]"
                wire:loading.attr="disabled"
                wire:target="register"
                wire:loading.class="opacity-60 cursor-not-allowed">
                <span wire:loading.remove wire:target="register">Daftar Sekarang</span>
                <span wire:loading.inline-flex wire:target="register" class="items-center justify-center gap-2" style="display: none;">
                    <x-icon name="lucide-loader-circle" class="animate-spin h-4 w-4 shrink-0" />
                    <span>Memproses...</span>
                </span>
            </button>
        </form>

        {{-- Divider --}}
        <div class="my-7 border-t-4 border-dashed border-black dark:border-zinc-700"></div>

        {{-- Login Link --}}
        <p class="text-center text-xs font-bold text-black dark:text-white">
            Sudah punya akun?
            <a href="{{ route('login') }}" wire:navigate
                class="font-black text-black underline decoration-2 underline-offset-2 hover:bg-[#FFE500] hover:no-underline px-1 rounded transition-all dark:text-white">
                Masuk Di Sini
            </a>
        </p>
    </div>
</div>
