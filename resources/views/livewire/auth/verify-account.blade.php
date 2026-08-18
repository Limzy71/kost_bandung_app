<div class="w-full" wire:poll.5s="checkVerificationStatus">
    {{-- ===== Neo-Brutalist Unified Dual-Verification Hub ===== --}}
    <div class="bg-white border-4 border-black p-6 sm:p-10 shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] rounded-2xl w-full dark:bg-zinc-900 dark:border-zinc-700 dark:shadow-[10px_10px_0px_0px_rgba(255,255,255,0.25)] space-y-6 sm:space-y-8">

        {{-- Card Header --}}
        <div>
            <div class="inline-flex items-center gap-2 bg-[#FFE500] border-2 border-black px-3.5 py-1.5 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mb-3 dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                <x-icon name="lucide-shield-check" class="w-4 h-4 text-black stroke-[3]" />
                <span class="text-xs font-black uppercase tracking-widest text-black">Aktivasi Akun</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-black text-black uppercase tracking-tight leading-tight dark:text-white">
                Verifikasi Akun Anda
            </h1>
            <p class="mt-2 text-xs sm:text-sm font-bold text-zinc-600 dark:text-zinc-400 leading-relaxed">
                Hai <span class="text-black dark:text-white font-black bg-yellow-200 dark:bg-yellow-400 px-1.5 py-0.5 border border-black dark:border-white rounded">{{ $user?->name }}</span>, untuk keamanan akun Anda, silakan selesaikan 2 langkah verifikasi berikut:
            </p>
        </div>

        {{-- Progress Status Badges --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            {{-- WhatsApp Status --}}
            <div class="p-3.5 border-2 border-black dark:border-zinc-700 rounded-xl flex items-center justify-between shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] {{ $user?->isPhoneVerified() ? 'bg-lime-200 dark:bg-lime-950/50' : 'bg-zinc-100 dark:bg-zinc-800' }}">
                <div class="flex items-center gap-2">
                    <x-icon name="lucide-smartphone" class="w-4 h-4 text-black dark:text-white stroke-[2.5]" />
                    <span class="text-xs font-black uppercase text-black dark:text-white">1. WhatsApp OTP</span>
                </div>
                @if ($user?->isPhoneVerified())
                    <span class="px-2 py-0.5 bg-lime-400 border border-black font-black text-[10px] uppercase text-black rounded flex items-center gap-1">
                        <x-icon name="lucide-check" class="w-3 h-3 stroke-[3]" /> Selesai
                    </span>
                @else
                    <span class="px-2 py-0.5 bg-yellow-300 border border-black font-black text-[10px] uppercase text-black rounded">
                        Belum
                    </span>
                @endif
            </div>

            {{-- Email Status --}}
            <div class="p-3.5 border-2 border-black dark:border-zinc-700 rounded-xl flex items-center justify-between shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] {{ $user?->hasVerifiedEmail() ? 'bg-lime-200 dark:bg-lime-950/50' : 'bg-zinc-100 dark:bg-zinc-800' }}">
                <div class="flex items-center gap-2">
                    <x-icon name="lucide-mail" class="w-4 h-4 text-black dark:text-white stroke-[2.5]" />
                    <span class="text-xs font-black uppercase text-black dark:text-white">2. Tautan Email</span>
                </div>
                @if ($user?->hasVerifiedEmail())
                    <span class="px-2 py-0.5 bg-lime-400 border border-black font-black text-[10px] uppercase text-black rounded flex items-center gap-1">
                        <x-icon name="lucide-check" class="w-3 h-3 stroke-[3]" /> Selesai
                    </span>
                @else
                    <span class="px-2 py-0.5 bg-yellow-300 border border-black font-black text-[10px] uppercase text-black rounded">
                        Belum
                    </span>
                @endif
            </div>
        </div>

        {{-- Divider --}}
        <div class="border-t-4 border-black dark:border-zinc-700"></div>

        {{-- ============================================================ --}}
        {{-- STEP 1: WhatsApp Verification (OTP) --}}
        {{-- ============================================================ --}}
        <div class="space-y-4">
            @if ($user?->isPhoneVerified())
                {{-- Phone Verified State --}}
                <div class="p-5 sm:p-6 bg-lime-100 dark:bg-lime-950/40 border-3 border-black dark:border-zinc-700 rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex items-start gap-4">
                    <div class="w-10 h-10 bg-lime-400 border-2 border-black dark:border-zinc-700 rounded-xl flex items-center justify-center text-black shrink-0 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        <x-icon name="lucide-check-circle" class="w-6 h-6 stroke-[3]" />
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-black dark:text-white uppercase">
                            Nomor WhatsApp Terverifikasi ✅
                        </h2>
                        <p class="text-xs sm:text-sm font-bold text-zinc-700 dark:text-zinc-300 mt-1">
                            Nomor <span class="font-extrabold text-black dark:text-white font-mono">{{ $user->phone_number }}</span> telah berhasil diverifikasi.
                        </p>
                    </div>
                </div>
            @else
                {{-- Phone Unverified State --}}
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
                    class="p-5 sm:p-7 bg-lime-50 dark:bg-zinc-800/80 border-3 border-black dark:border-zinc-700 rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-4"
                >
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="px-2.5 py-1 bg-lime-400 border-2 border-black dark:border-zinc-700 font-black text-xs uppercase text-black rounded-lg shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)]">
                            Langkah 1: Verifikasi WhatsApp
                        </span>
                        {{-- Button Toggle Edit Phone --}}
                        <button type="button" wire:click="toggleEditPhone"
                            class="text-xs font-black text-zinc-700 dark:text-zinc-300 hover:text-black dark:hover:text-white underline uppercase cursor-pointer">
                            {{ $isEditingPhone ? '✕ Batal Ubah' : '✏️ Salah nomor HP? Ubah' }}
                        </button>
                    </div>

                    {{-- Form Ubah Nomor HP (Jika user salah ketik saat registrasi) --}}
                    @if ($isEditingPhone)
                        <div class="p-4 bg-yellow-100 dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 rounded-xl space-y-3">
                            <p class="text-xs font-black uppercase text-black dark:text-white">
                                Masukkan Nomor WhatsApp yang Benar:
                            </p>
                            <form wire:submit="updatePhoneNumber" class="space-y-2">
                                <input
                                    type="text"
                                    wire:model="newPhoneNumber"
                                    placeholder="Contoh: 081234567890"
                                    class="w-full h-11 px-3 bg-white dark:bg-zinc-800 border-2 border-black dark:border-zinc-700 rounded-lg text-black dark:text-white font-black text-sm focus:outline-none focus:ring-0 focus:shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]"
                                >
                                @error('newPhoneNumber')
                                    <p class="text-xs font-black text-rose-500 uppercase">{{ $message }}</p>
                                @enderror
                                <div class="flex items-center gap-2 pt-1">
                                    <button type="submit" wire:loading.attr="disabled"
                                        class="h-10 px-4 bg-[#FFE500] hover:bg-yellow-300 text-black border-2 border-black font-black text-xs uppercase rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] cursor-pointer">
                                        Simpan &amp; Kirim OTP Baru
                                    </button>
                                    <button type="button" wire:click="toggleEditPhone"
                                        class="h-10 px-3 bg-white hover:bg-zinc-100 text-black border-2 border-black font-black text-xs uppercase rounded-lg cursor-pointer">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div>
                            <h2 class="text-base sm:text-lg font-black text-black dark:text-white uppercase tracking-tight">
                                Masukkan 6 Digit Kode OTP WhatsApp
                            </h2>
                            <p class="text-xs sm:text-sm font-bold text-zinc-600 dark:text-zinc-400 mt-1">
                                Kode telah dikirimkan ke nomor <span class="font-extrabold text-black dark:text-white bg-yellow-200 dark:bg-yellow-400 px-1.5 py-0.5 border border-black dark:border-white rounded font-mono">{{ $user?->phone_number }}</span>.
                            </p>
                        </div>

                        @if ($phoneSuccessMessage)
                            <div class="p-3 bg-lime-300 border-2 border-black rounded-lg text-xs font-black uppercase text-black">
                                {{ $phoneSuccessMessage }}
                            </div>
                        @endif

                        <form wire:submit="verifyPhoneOtp" class="space-y-3 pt-1">
                            <div>
                                <input
                                    type="text"
                                    wire:model="phoneOtp"
                                    inputmode="numeric"
                                    maxlength="6"
                                    placeholder="• • • • • •"
                                    class="w-full h-12 sm:h-14 text-center text-xl sm:text-2xl tracking-[0.4em] bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-xl text-black dark:text-white font-black focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:focus:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] transition-all uppercase"
                                >
                                @error('phoneOtp')
                                    <p class="text-xs font-black text-rose-500 mt-1.5 uppercase">{{ $message }}</p>
                                @enderror
                                @if ($otpErrorMessage)
                                    <p class="text-xs font-black text-rose-500 mt-1.5 uppercase">{{ $otpErrorMessage }}</p>
                                @endif
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                <button type="submit" wire:loading.attr="disabled"
                                    class="sm:col-span-2 h-11 sm:h-12 bg-lime-400 hover:bg-lime-300 disabled:opacity-50 text-black border-2 border-black dark:border-zinc-700 font-black text-xs sm:text-sm uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer inline-flex items-center justify-center gap-1.5">
                                    <x-icon name="lucide-check-circle" class="w-4 h-4 stroke-[2.5]" />
                                    <span wire:loading.remove wire:target="verifyPhoneOtp">Verifikasi WhatsApp</span>
                                    <span wire:loading wire:target="verifyPhoneOtp">Memverifikasi...</span>
                                </button>
                                <button type="button"
                                    x-show="timer === 0"
                                    wire:click="resendPhoneOtp"
                                    class="h-11 sm:h-12 px-3 bg-white hover:bg-zinc-100 text-black border-2 border-black dark:bg-zinc-800 dark:hover:bg-zinc-700 dark:text-white dark:border-zinc-700 font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer inline-flex items-center justify-center gap-1">
                                    <x-icon name="lucide-refresh-cw" class="w-3.5 h-3.5 stroke-[2.5]" />
                                    <span>Kirim Ulang OTP</span>
                                </button>
                            </div>

                            <div x-show="timer > 0" class="text-xs font-bold text-zinc-500 dark:text-zinc-400">
                                Kirim ulang OTP dalam <span x-text="timer" class="font-black text-black dark:text-white font-mono"></span> detik
                            </div>
                        </form>
                    @endif
                </div>
            @endif
        </div>

        {{-- ============================================================ --}}
        {{-- STEP 2: Email Verification (Link) --}}
        {{-- ============================================================ --}}
        <div class="space-y-4">
            @if ($user?->hasVerifiedEmail())
                {{-- Email Verified State --}}
                <div class="p-5 sm:p-6 bg-lime-100 dark:bg-lime-950/40 border-3 border-black dark:border-zinc-700 rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex items-start gap-4">
                    <div class="w-10 h-10 bg-lime-400 border-2 border-black dark:border-zinc-700 rounded-xl flex items-center justify-center text-black shrink-0 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        <x-icon name="lucide-check-circle" class="w-6 h-6 stroke-[3]" />
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-black text-black dark:text-white uppercase">
                            Alamat Email Terverifikasi ✅
                        </h2>
                        <p class="text-xs sm:text-sm font-bold text-zinc-700 dark:text-zinc-300 mt-1">
                            Alamat email <span class="font-extrabold text-black dark:text-white">{{ $user->email }}</span> telah aktif dan terverifikasi.
                        </p>
                    </div>
                </div>
            @else
                {{-- Email Unverified State --}}
                <div class="p-5 sm:p-7 bg-cyan-50 dark:bg-zinc-800/80 border-3 border-black dark:border-zinc-700 rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] space-y-4">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 bg-cyan-300 border-2 border-black dark:border-zinc-700 font-black text-xs uppercase text-black rounded-lg shadow-[1.5px_1.5px_0px_0px_rgba(0,0,0,1)]">
                            Langkah 2: Verifikasi Alamat Email
                        </span>
                    </div>

                    <div>
                        <h2 class="text-base sm:text-lg font-black text-black dark:text-white uppercase tracking-tight">
                            Buka Kotak Masuk Email Anda
                        </h2>
                        <p class="text-xs sm:text-sm font-bold text-zinc-600 dark:text-zinc-400 mt-1 leading-relaxed">
                            Tautan konfirmasi telah dikirimkan ke <span class="font-extrabold text-black dark:text-white bg-cyan-200 dark:bg-cyan-400 px-1.5 py-0.5 border border-black dark:border-white rounded">{{ $user?->email }}</span>. Klik tombol aktivasi di email tersebut.
                        </p>
                    </div>

                    @if ($emailStatusMessage)
                        <div class="flex items-center gap-2.5 bg-lime-300 border-2 border-black dark:border-zinc-700 p-3 rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            <x-icon name="lucide-check-circle" class="w-4 h-4 text-black shrink-0 stroke-[3]" />
                            <span class="text-xs font-black text-black uppercase">{{ $emailStatusMessage }}</span>
                        </div>
                    @endif

                    <div class="pt-1">
                        <button type="button" wire:click="resendEmailVerification"
                            class="w-full h-11 sm:h-12 bg-cyan-300 hover:bg-cyan-400 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none text-black border-2 border-black dark:border-zinc-700 font-black text-xs sm:text-sm uppercase tracking-wider rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <x-icon name="lucide-send" class="w-4 h-4 text-black stroke-[3]" />
                            <span>Kirim Ulang Email Verifikasi</span>
                        </button>
                    </div>
                </div>
            @endif
        </div>

        {{-- Logout Action --}}
        <div class="text-center pt-2 border-t-2 border-zinc-200 dark:border-zinc-800">
            <button type="button" wire:click="logout"
                class="px-4 py-2 text-xs font-black text-zinc-600 dark:text-zinc-400 hover:text-rose-600 dark:hover:text-rose-400 underline uppercase cursor-pointer transition-colors">
                Keluar &amp; Masuk dengan Akun Lain
            </button>
        </div>
    </div>
</div>
