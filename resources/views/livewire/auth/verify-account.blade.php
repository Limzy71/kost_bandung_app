<div class="w-full max-w-xl mx-auto">
    {{-- ===== Neo-Brutalist Unified Verification Hub ===== --}}
    <div class="bg-white border-4 border-black p-6 sm:p-10 shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] rounded-2xl dark:bg-zinc-900 dark:border-zinc-700 dark:shadow-[10px_10px_0px_0px_rgba(255,255,255,0.25)]">

        {{-- Card Header --}}
        <div class="mb-6 text-center sm:text-left">
            <div class="inline-flex items-center gap-2 bg-[#FFE500] border-2 border-black px-3 py-1 rounded-md shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mb-3 dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                <x-icon name="lucide-shield-check" class="w-4 h-4 text-black stroke-[3]" />
                <span class="text-[10px] font-black uppercase tracking-widest text-black">Aktivasi Akun</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-black text-black uppercase tracking-tight leading-tight dark:text-white">
                Verifikasi Akun Anda
            </h1>
            <p class="mt-2 text-xs sm:text-sm font-bold text-zinc-600 dark:text-zinc-400">
                Pilih salah satu metode cepat di bawah ini untuk mengaktifkan akun <span class="text-black dark:text-white font-black">{{ $user?->name }}</span>.
            </p>
        </div>

        {{-- Divider --}}
        <div class="border-t-4 border-black mb-6 dark:border-zinc-700"></div>

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
                class="p-5 sm:p-6 bg-lime-50 dark:bg-zinc-800/80 border-3 border-black dark:border-zinc-700 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.2)] mb-6"
            >
                <div class="flex items-center gap-2 mb-2">
                    <span class="px-2 py-0.5 bg-lime-400 border-2 border-black dark:border-zinc-700 font-black text-[10px] uppercase text-black rounded shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)]">
                        ⚡ Cara Paling Cepat
                    </span>
                </div>
                <h2 class="text-base sm:text-lg font-black text-black dark:text-white uppercase tracking-tight">
                    1. Masukkan Kode OTP WhatsApp
                </h2>
                <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 mt-1">
                    Kode 6 digit telah dikirim ke nomor <span class="font-extrabold text-black dark:text-white bg-yellow-200 dark:bg-yellow-400 px-1 border border-black dark:border-white">{{ $user->phone_number }}</span>.
                </p>

                <form wire:submit="verifyPhoneOtp" class="mt-4 space-y-3">
                    <div>
                        <input
                            type="text"
                            wire:model="phoneOtp"
                            inputmode="numeric"
                            maxlength="6"
                            placeholder="• • • • • •"
                            class="w-full h-12 text-center text-xl tracking-[0.4em] bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-lg text-black dark:text-white font-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:focus:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] transition-all uppercase"
                        >
                        @error('phoneOtp')
                            <p class="text-xs font-black text-rose-500 mt-1 uppercase">{{ $message }}</p>
                        @enderror
                        @if ($otpErrorMessage)
                            <p class="text-xs font-black text-rose-500 mt-1 uppercase">{{ $otpErrorMessage }}</p>
                        @endif
                    </div>

                    <div class="flex flex-col sm:flex-row items-center gap-2">
                        <button type="submit" wire:loading.attr="disabled"
                            class="w-full sm:flex-1 h-11 bg-lime-400 hover:bg-lime-300 disabled:opacity-50 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer inline-flex items-center justify-center gap-1.5">
                            <x-icon name="lucide-check-circle" class="w-4 h-4 stroke-[2.5]" />
                            <span wire:loading.remove wire:target="verifyPhoneOtp">Verifikasi &amp; Masuk</span>
                            <span wire:loading wire:target="verifyPhoneOtp">Memverifikasi...</span>
                        </button>
                        <button type="button"
                            x-show="timer === 0"
                            wire:click="resendPhoneOtp"
                            class="w-full sm:w-auto h-11 px-3 bg-white hover:bg-zinc-100 text-black border-2 border-black dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-white dark:border-zinc-700 font-black text-[11px] uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer inline-flex items-center justify-center gap-1">
                            <x-icon name="lucide-refresh-cw" class="w-3.5 h-3.5 stroke-[2.5]" />
                            <span>Kirim Ulang OTP</span>
                        </button>
                    </div>

                    <div x-show="timer > 0" class="text-[11px] font-bold text-zinc-500 text-center sm:text-left">
                        Kirim ulang OTP dalam <span x-text="timer" class="font-black text-black dark:text-white font-mono"></span> detik
                    </div>
                </form>
            </div>
        @endif

        {{-- Method 2: Email Verification Link (Alternative) --}}
        <div class="p-5 sm:p-6 bg-cyan-50 dark:bg-zinc-800/80 border-3 border-black dark:border-zinc-700 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.2)] mb-6">
            <h2 class="text-base sm:text-lg font-black text-black dark:text-white uppercase tracking-tight">
                2. Tautan Verifikasi Email
            </h2>
            <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 mt-1 leading-relaxed">
                Tautan konfirmasi telah dikirim ke <span class="font-extrabold text-black dark:text-white">{{ $user?->email }}</span>. Buka email Anda dan klik tautan aktivasi.
            </p>

            @if ($emailStatusMessage)
                <div class="mt-3 flex items-center gap-2 bg-lime-300 border-2 border-black dark:border-zinc-700 p-2.5 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                    <x-icon name="lucide-check-circle" class="w-4 h-4 text-black shrink-0 stroke-[3]" />
                    <span class="text-xs font-black text-black uppercase">{{ $emailStatusMessage }}</span>
                </div>
            @endif

            <div class="mt-4">
                <button type="button" wire:click="resendEmailVerification"
                    class="w-full h-11 bg-cyan-300 hover:bg-cyan-400 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase tracking-wider rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transition-all flex items-center justify-center gap-2 cursor-pointer">
                    <x-icon name="lucide-send" class="w-3.5 h-3.5 text-black stroke-[3]" />
                    <span>Kirim Ulang Email Verifikasi</span>
                </button>
            </div>
        </div>

        {{-- Logout Action --}}
        <div class="text-center pt-2">
            <button type="button" wire:click="logout"
                class="text-xs font-black text-zinc-600 dark:text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 underline uppercase cursor-pointer">
                Keluar &amp; Masuk dengan Akun Lain
            </button>
        </div>
    </div>
</div>
