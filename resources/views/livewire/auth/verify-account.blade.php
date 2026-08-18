<div class="w-full">
    {{-- ===== Neo-Brutalist Unified Verification Hub ===== --}}
    <div class="bg-white border-4 border-black p-8 sm:p-10 md:p-12 shadow-[12px_12px_0px_0px_rgba(0,0,0,1)] rounded-2xl w-full dark:bg-zinc-900 dark:border-zinc-700 dark:shadow-[12px_12px_0px_0px_rgba(255,255,255,0.25)] space-y-8">

        {{-- Card Header --}}
        <div>
            <div class="inline-flex items-center gap-2 bg-[#FFE500] border-2 border-black px-3.5 py-1.5 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mb-4 dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                <x-icon name="lucide-shield-check" class="w-4 h-4 text-black stroke-[3]" />
                <span class="text-xs font-black uppercase tracking-widest text-black">Aktivasi Akun</span>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-black uppercase tracking-tight leading-tight dark:text-white">
                Verifikasi Akun Anda
            </h1>
            <p class="mt-3 text-sm sm:text-base font-bold text-zinc-600 dark:text-zinc-400 leading-relaxed">
                Hai <span class="text-black dark:text-white font-black bg-yellow-200 dark:bg-yellow-400 px-1.5 py-0.5 border border-black dark:border-white rounded">{{ $user?->name }}</span>, pilih salah satu metode cepat di bawah ini untuk mengaktifkan akun Anda:
            </p>
        </div>

        {{-- Divider --}}
        <div class="border-t-4 border-black dark:border-zinc-700"></div>

        {{-- Method 1: WhatsApp OTP (Fastest Option) --}}
        @if ($user?->phone_number)
            <div
                x-data="{
                    timer: @js($otpCooldown),
                    init() {
                        if (this.timer > 0) {
                            const interval = setInterval(() => {
                                if (this.timer > 0) {
                                    this.timer--;
                                } else {
                                    clearInterval(interval);
                                }
                            }, 1000);
                        }
                    }
                }"
                class="p-6 sm:p-8 bg-lime-50 dark:bg-zinc-800/80 border-3 border-black dark:border-zinc-700 rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.2)] space-y-5"
            >
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <span class="px-3 py-1 bg-lime-400 border-2 border-black dark:border-zinc-700 font-black text-xs uppercase text-black rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        ⚡ Opsi Paling Cepat
                    </span>
                    <span class="text-xs font-black text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">
                        WhatsApp OTP
                    </span>
                </div>

                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-black dark:text-white uppercase tracking-tight">
                        1. Masukkan Kode OTP WhatsApp
                    </h2>
                    <p class="text-xs sm:text-sm font-bold text-zinc-600 dark:text-zinc-400 mt-1.5 leading-relaxed">
                        Kode 6 digit telah dikirimkan ke nomor WhatsApp <span class="font-extrabold text-black dark:text-white bg-yellow-200 dark:bg-yellow-400 px-1.5 py-0.5 border border-black dark:border-white rounded font-mono">{{ $user->phone_number }}</span>.
                    </p>
                </div>

                <form wire:submit="verifyPhoneOtp" class="space-y-4 pt-2">
                    <div>
                        <label for="phoneOtp" class="block text-xs font-black uppercase text-black dark:text-white mb-2 tracking-wide">
                            Kode Verifikasi (6 Digit)
                        </label>
                        <input
                            type="text"
                            id="phoneOtp"
                            wire:model="phoneOtp"
                            inputmode="numeric"
                            maxlength="6"
                            placeholder="• • • • • •"
                            class="w-full h-14 sm:h-16 text-center text-2xl sm:text-3xl tracking-[0.4em] sm:tracking-[0.5em] bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-xl text-black dark:text-white font-black focus:outline-none focus:ring-0 focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:focus:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] transition-all uppercase"
                        >
                        @error('phoneOtp')
                            <p class="text-xs font-black text-rose-500 mt-2 uppercase">{{ $message }}</p>
                        @enderror
                        @if ($otpErrorMessage)
                            <p class="text-xs font-black text-rose-500 mt-2 uppercase">{{ $otpErrorMessage }}</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                        <button type="submit" wire:loading.attr="disabled"
                            class="sm:col-span-2 h-12 sm:h-14 bg-lime-400 hover:bg-lime-300 disabled:opacity-50 text-black border-3 border-black dark:border-zinc-700 font-black text-sm uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer inline-flex items-center justify-center gap-2">
                            <x-icon name="lucide-check-circle" class="w-5 h-5 stroke-[2.5]" />
                            <span wire:loading.remove wire:target="verifyPhoneOtp">Verifikasi &amp; Masuk</span>
                            <span wire:loading wire:target="verifyPhoneOtp">Memverifikasi...</span>
                        </button>
                        <button type="button"
                            x-show="timer === 0"
                            wire:click="resendPhoneOtp"
                            class="h-12 sm:h-14 px-4 bg-white hover:bg-zinc-100 text-black border-3 border-black dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-white dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer inline-flex items-center justify-center gap-1.5 whitespace-nowrap">
                            <x-icon name="lucide-refresh-cw" class="w-4 h-4 stroke-[2.5]" />
                            <span>Kirim Ulang</span>
                        </button>
                    </div>

                    <div x-show="timer > 0" class="text-xs font-bold text-zinc-500 dark:text-zinc-400 pt-1">
                        Kirim ulang kode OTP tersedia dalam <span x-text="timer" class="font-black text-black dark:text-white font-mono"></span> detik
                    </div>
                </form>
            </div>

            {{-- Middle Visual Separator --}}
            <div class="relative flex items-center justify-center py-2">
                <div class="border-t-2 border-dashed border-zinc-300 dark:border-zinc-700 w-full"></div>
                <span class="bg-white dark:bg-zinc-900 px-4 text-xs font-black uppercase text-zinc-500 dark:text-zinc-400 absolute">
                    Atau Lewat Email
                </span>
            </div>
        @endif

        {{-- Method 2: Email Verification Link (Alternative) --}}
        <div class="p-6 sm:p-8 bg-cyan-50 dark:bg-zinc-800/80 border-3 border-black dark:border-zinc-700 rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.2)] space-y-4">
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-cyan-300 border-2 border-black dark:border-zinc-700 font-black text-xs uppercase text-black rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                    ✉️ Opsi Email
                </span>
            </div>

            <div>
                <h2 class="text-xl sm:text-2xl font-black text-black dark:text-white uppercase tracking-tight">
                    2. Tautan Verifikasi Email
                </h2>
                <p class="text-xs sm:text-sm font-bold text-zinc-600 dark:text-zinc-400 mt-1.5 leading-relaxed">
                    Tautan konfirmasi telah dikirim ke alamat email <span class="font-extrabold text-black dark:text-white bg-cyan-200 dark:bg-cyan-400 px-1.5 py-0.5 border border-black dark:border-white rounded">{{ $user?->email }}</span>. Buka email Anda dan klik tombol aktivasi.
                </p>
            </div>

            @if ($emailStatusMessage)
                <div class="flex items-center gap-2.5 bg-lime-300 border-3 border-black dark:border-zinc-700 p-3.5 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                    <x-icon name="lucide-check-circle" class="w-5 h-5 text-black shrink-0 stroke-[3]" />
                    <span class="text-xs font-black text-black uppercase">{{ $emailStatusMessage }}</span>
                </div>
            @endif

            <div class="pt-2">
                <button type="button" wire:click="resendEmailVerification"
                    class="w-full h-12 sm:h-14 bg-cyan-300 hover:bg-cyan-400 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none text-black border-3 border-black dark:border-zinc-700 font-black text-xs sm:text-sm uppercase tracking-wider rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <x-icon name="lucide-send" class="w-4 h-4 text-black stroke-[3]" />
                    <span>Kirim Ulang Tautan ke Email Saya</span>
                </button>
            </div>
        </div>

        {{-- Logout Action --}}
        <div class="text-center pt-4 border-t-2 border-zinc-200 dark:border-zinc-800">
            <button type="button" wire:click="logout"
                class="px-4 py-2 text-xs font-black text-zinc-600 dark:text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 underline uppercase cursor-pointer transition-colors">
                Keluar &amp; Masuk dengan Akun Lain
            </button>
        </div>
    </div>
</div>
