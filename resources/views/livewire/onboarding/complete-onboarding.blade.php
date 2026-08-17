<div class="w-full">
    {{-- ===== Neo-Brutalist Onboarding Card ===== --}}
    <div class="bg-white border-4 border-black p-8 md:p-10 shadow-[12px_12px_0px_0px_rgba(0,0,0,1)] rounded-lg w-full dark:bg-zinc-900 dark:border-zinc-700 dark:shadow-[12px_12px_0px_0px_rgba(255,255,255,0.25)]">

        {{-- Card Header --}}
        <div class="mb-8">
            <div class="inline-flex items-center gap-2 bg-[#FFE500] border-2 border-black px-3 py-1 rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mb-4 dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                <span class="text-[10px] font-black uppercase tracking-widest text-black">Langkah Terakhir</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-black uppercase tracking-tight leading-tight dark:text-white">
                Pilih Tipe Akun
            </h1>
            <p class="mt-2 text-sm font-bold text-zinc-600 dark:text-zinc-400">
                Hai <span class="text-black dark:text-white font-black">{{ auth()->user()->name }}</span>, tentukan bagaimana Anda akan menggunakan KostBandung.
            </p>
        </div>

        {{-- Divider --}}
        <div class="border-t-4 border-black mb-8 dark:border-zinc-700"></div>

        @if (session('error'))
            <div class="mb-6 flex items-center gap-3 bg-rose-400 border-4 border-black p-4 rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:border-zinc-700 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]">
                <x-icon name="lucide-triangle-alert" class="w-5 h-5 text-black shrink-0 stroke-3" />
                <span class="text-xs font-black uppercase text-black">
                    {{ session('error') }}
                </span>
            </div>
        @endif

        {{-- All form state lives in Alpine, with instant client validation and zero-flicker one-shot submit. --}}
        <form
            wire:ignore
            @submit.prevent="submit()"
            x-data="{
                role: @js($role),
                business_name: @js($business_name),
                phone_number: @js($phone_number),
                terms: @js($terms),
                submitting: false,
                errors: {},

                validate() {
                    this.errors = {};
                    if (!this.role) {
                        this.errors.role = 'Silakan pilih peran akun Anda.';
                    }
                    if (this.role === 'owner') {
                        if (!this.business_name || !this.business_name.trim()) {
                            this.errors.business_name = 'Nama properti/usaha kost wajib diisi.';
                        }
                        if (!this.phone_number || !this.phone_number.trim()) {
                            this.errors.phone_number = 'Nomor WhatsApp wajib diisi.';
                        } else if (!/^0[0-9]{9,14}$/.test(this.phone_number.trim())) {
                            this.errors.phone_number = 'Nomor WhatsApp harus berawalan 0 dan terdiri dari 10-15 digit angka.';
                        }
                    }
                    if (!this.terms) {
                        this.errors.terms = 'Anda wajib menyetujui Syarat & Ketentuan.';
                    }
                    return Object.keys(this.errors).length === 0;
                },

                async submit() {
                    if (!this.validate()) {
                        return;
                    }
                    if (this.submitting) return;
                    this.submitting = true;
                    this.errors = {};
                    try {
                        await $wire.complete(
                            this.role,
                            this.role === 'owner' ? this.business_name.trim() : '',
                            this.role === 'owner' ? this.phone_number.trim() : '',
                            this.terms
                        );
                        // Livewire's redirect effect may not navigate reliably.
                        // Redirect manually from JS after successful save.
                        window.location.href = this.role === 'owner'
                            ? @js(route('dashboard'))
                            : @js(route('home'));
                    } catch (e) {
                        const serverErrors = $wire.errors;
                        if (serverErrors && typeof serverErrors === 'object') {
                            for (const key of ['role', 'business_name', 'phone_number', 'terms']) {
                                if (serverErrors[key]) {
                                    this.errors[key] = Array.isArray(serverErrors[key])
                                        ? serverErrors[key][0]
                                        : serverErrors[key];
                                }
                            }
                        }
                    } finally {
                        this.submitting = false;
                    }
                }
            }"
            class="space-y-6"
        >

            {{-- ===== Role Selector Cards ===== --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-wider text-black mb-3 dark:text-white">
                    Tipe Akun Saya <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- Pencari Kost --}}
                    <button type="button" @click="role = 'user'; if(errors.role) delete errors.role"
                        class="p-5 text-left border-3 rounded-xl transition-all cursor-pointer focus:outline-none focus:ring-0 select-none"
                        :class="role === 'user'
                            ? 'bg-[#FFE500] border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] -translate-x-0.5 -translate-y-0.5 dark:border-zinc-600 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]'
                            : 'bg-white border-black hover:bg-zinc-50 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none dark:bg-zinc-900 dark:border-zinc-700 dark:hover:bg-zinc-800 dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]'">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-9 h-9 border-2 rounded-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] shrink-0"
                                :class="role === 'user'
                                    ? 'bg-white border-black text-black'
                                    : 'bg-zinc-100 border-black text-black dark:bg-zinc-800 dark:border-zinc-700 dark:text-white dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.2)]'">
                                <x-icon name="lucide-search" class="w-5 h-5 stroke-[2.5]" />
                            </div>
                            <span class="text-base font-black uppercase tracking-tight"
                                :class="role === 'user' ? 'text-black' : 'text-black dark:text-white'">
                                Pencari Kost
                            </span>
                        </div>
                        <p class="text-xs font-bold leading-relaxed"
                            :class="role === 'user' ? 'text-black/85' : 'text-zinc-600 dark:text-zinc-400'">
                            Saya ingin mencari, membandingkan, dan menyewa kost idaman di Kota Bandung.
                        </p>
                    </button>

                    {{-- Pemilik Kost --}}
                    <button type="button" @click="role = 'owner'; if(errors.role) delete errors.role"
                        class="p-5 text-left border-3 rounded-xl transition-all cursor-pointer focus:outline-none focus:ring-0 select-none"
                        :class="role === 'owner'
                            ? 'bg-[#FFE500] border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] -translate-x-0.5 -translate-y-0.5 dark:border-zinc-600 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]'
                            : 'bg-white border-black hover:bg-zinc-50 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none dark:bg-zinc-900 dark:border-zinc-700 dark:hover:bg-zinc-800 dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]'">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-9 h-9 border-2 rounded-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] shrink-0"
                                :class="role === 'owner'
                                    ? 'bg-white border-black text-black'
                                    : 'bg-zinc-100 border-black text-black dark:bg-zinc-800 dark:border-zinc-700 dark:text-white dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.2)]'">
                                <x-icon name="lucide-house" class="w-5 h-5 stroke-[2.5]" />
                            </div>
                            <span class="text-base font-black uppercase tracking-tight"
                                :class="role === 'owner' ? 'text-black' : 'text-black dark:text-white'">
                                Pemilik Kost
                            </span>
                        </div>
                        <p class="text-xs font-bold leading-relaxed"
                            :class="role === 'owner' ? 'text-black/85' : 'text-zinc-600 dark:text-zinc-400'">
                            Saya memiliki properti kost dan ingin memasang iklan serta mengelola kamar.
                        </p>
                    </button>
                </div>
                <p x-show="errors.role" x-text="'✕ ' + errors.role" class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400" x-cloak></p>
                @error('role')
                    <p x-show="!errors.role" class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400">✕ {{ $message }}</p>
                @enderror
            </div>

            {{-- ===== Owner Extra Fields (Only if role === 'owner') ===== --}}
            <div x-show="role === 'owner'"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                x-cloak
                class="space-y-5 p-5 bg-yellow-50 dark:bg-zinc-800/60 border-3 border-black dark:border-zinc-700 rounded-xl">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <x-icon name="lucide-store" class="w-4 h-4 text-black dark:text-white stroke-[2.5]" />
                        <h3 class="text-xs font-black uppercase text-black dark:text-white tracking-wider">Informasi Usaha Kost</h3>
                    </div>
                    <p class="text-[11px] font-bold text-zinc-600 dark:text-zinc-400 mb-4">
                        Data ini akan ditampilkan pada profil properti Anda agar mudah dihubungi calon penyewa.
                    </p>
                </div>

                {{-- Nama Properti / Usaha --}}
                <div>
                    <label for="business_name" class="block text-xs font-black uppercase tracking-wider text-black mb-2 dark:text-white">
                        Nama Properti / Usaha Kost <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="business_name"
                        x-model="business_name"
                        @input="if(errors.business_name) delete errors.business_name"
                        placeholder="Contoh: Kost Putri Melati Dipatiukur"
                        class="w-full px-4 py-3.5 bg-white border-3 border-black rounded-lg text-sm font-bold text-black placeholder:text-zinc-400 focus:outline-none focus:ring-0 focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all dark:bg-zinc-900 dark:border-zinc-700 dark:text-white dark:placeholder:text-zinc-500 dark:focus:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]"
                    />
                    <p x-show="errors.business_name" x-text="'✕ ' + errors.business_name" class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400" x-cloak></p>
                    @error('business_name')
                        <p x-show="!errors.business_name" class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400">✕ {{ $message }}</p>
                    @enderror
                </div>

                {{-- Nomor WhatsApp --}}
                <div>
                    <label for="phone_number" class="block text-xs font-black uppercase tracking-wider text-black mb-2 dark:text-white">
                        Nomor WhatsApp Aktif <span class="text-rose-500">*</span>
                    </label>
                    <input
                        type="tel"
                        id="phone_number"
                        x-model="phone_number"
                        @input="if(errors.phone_number) delete errors.phone_number"
                        placeholder="081234567890"
                        class="w-full px-4 py-3.5 bg-white border-3 border-black rounded-lg text-sm font-bold text-black placeholder:text-zinc-400 focus:outline-none focus:ring-0 focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all dark:bg-zinc-900 dark:border-zinc-700 dark:text-white dark:placeholder:text-zinc-500 dark:focus:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]"
                    />
                    <p class="mt-1.5 text-[11px] font-bold text-zinc-500 dark:text-zinc-400">
                        Gunakan format awalan 0 (10–15 digit angka).
                    </p>
                    <p x-show="errors.phone_number" x-text="'✕ ' + errors.phone_number" class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400" x-cloak></p>
                    @error('phone_number')
                        <p x-show="!errors.phone_number" class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400">✕ {{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- ===== Terms & Conditions ===== --}}
            <div class="pt-2">
                <label class="flex items-start gap-3 cursor-pointer group">
                    <input
                        type="checkbox"
                        x-model="terms"
                        @change="if(terms && errors.terms) delete errors.terms"
                        class="mt-0.5 w-5 h-5 bg-white border-3 border-black rounded text-black focus:ring-0 cursor-pointer dark:bg-zinc-800 dark:border-zinc-700 shrink-0"
                    />
                    <span class="text-xs font-bold text-zinc-700 dark:text-zinc-300 leading-snug">
                        Saya menyetujui <a href="{{ route('terms') }}" target="_blank" class="font-black text-black dark:text-white underline underline-offset-2 decoration-2 hover:text-yellow-600 dark:hover:text-yellow-400 transition-colors">Syarat & Ketentuan</a> serta Kebijakan Privasi KostBandung. <span class="text-rose-500">*</span>
                    </span>
                </label>
                <p x-show="errors.terms" x-text="'✕ ' + errors.terms" class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400" x-cloak></p>
                @error('terms')
                    <p x-show="!errors.terms" class="mt-2 text-xs font-bold text-rose-600 dark:text-rose-400">✕ {{ $message }}</p>
                @enderror
            </div>

            {{-- ===== Submit Button ===== --}}
            <div class="pt-2">
                <button
                    type="submit"
                    :disabled="submitting"
                    class="w-full py-4 px-6 bg-[#FFE500] hover:bg-yellow-400 text-black border-3 border-black font-black text-sm uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] active:translate-x-1 active:translate-y-1 active:shadow-none transition-all duration-75 rounded-lg flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed dark:border-zinc-700 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]"
                >
                    <template x-if="!submitting">
                        <span class="flex items-center gap-2">
                            Selesaikan &amp; Lanjutkan
                            <x-icon name="lucide-arrow-right" class="w-4 h-4 stroke-3" />
                        </span>
                    </template>
                    <template x-if="submitting">
                        <span class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-black" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Menyimpan...
                        </span>
                    </template>
                </button>
            </div>

        </form>

        {{-- Logout Option --}}
        <div class="mt-8 pt-6 border-t-2 border-dashed border-zinc-300 dark:border-zinc-700 text-center">
            <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400">
                Bukan akun Anda?
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="font-black text-rose-600 hover:text-rose-500 hover:underline uppercase text-xs cursor-pointer ml-1 dark:text-rose-400 dark:hover:text-rose-300">
                        Keluar & Ganti Akun
                    </button>
                </form>
            </p>
        </div>

    </div>
</div>
