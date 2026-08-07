<div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 p-6 md:p-8 rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] space-y-8">

    <!-- Header Section -->
    <div class="flex items-center gap-3 border-b-3 border-black dark:border-zinc-700 pb-4">
        <div class="w-10 h-10 bg-yellow-300 border-2 border-black dark:border-zinc-700 rounded-lg flex items-center justify-center text-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] shrink-0">
            <x-icon name="lucide-shield-check" class="w-5 h-5 text-black stroke-[2.5]" />
        </div>
        <div>
            <h2 class="text-xl font-black text-black dark:text-white uppercase tracking-tight">Keamanan Akun & Password</h2>
            <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400">Kelola kata sandi, autentikasi dua faktor (2FA), dan passkeys akun Anda.</p>
        </div>
    </div>

    <!-- 1. Form Ubah Kata Sandi -->
    <div class="space-y-4">
        <div class="flex items-center gap-2 border-b-2 border-black/10 dark:border-zinc-800 pb-2">
            <x-icon name="lucide-key-round" class="w-4 h-4 text-black dark:text-white stroke-[2.5]" />
            <h3 class="text-base font-black text-black dark:text-white uppercase tracking-tight">Ubah Kata Sandi</h3>
        </div>

        <form wire:submit="updatePassword" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="current_password" class="block text-xs font-black uppercase text-black dark:text-white mb-1.5">Kata Sandi Saat Ini</label>
                <input type="password" id="current_password" wire:model="current_password" autocomplete="current-password"
                    class="w-full px-4 py-3 text-sm bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-lg text-black dark:text-white font-bold placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] transition-all"
                    placeholder="••••••••">
                @error('current_password') <p class="text-xs font-black text-rose-500 mt-1 uppercase">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-black uppercase text-black dark:text-white mb-1.5">Kata Sandi Baru</label>
                <input type="password" id="password" wire:model="password" autocomplete="new-password"
                    class="w-full px-4 py-3 text-sm bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-lg text-black dark:text-white font-bold placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] transition-all"
                    placeholder="Minimal 8 karakter">
                @error('password') <p class="text-xs font-black text-rose-500 mt-1 uppercase">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-black uppercase text-black dark:text-white mb-1.5">Konfirmasi Kata Sandi</label>
                <input type="password" id="password_confirmation" wire:model="password_confirmation" autocomplete="new-password"
                    class="w-full px-4 py-3 text-sm bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-lg text-black dark:text-white font-bold placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] transition-all"
                    placeholder="Ulangi kata sandi baru">
                @error('password_confirmation') <p class="text-xs font-black text-rose-500 mt-1 uppercase">{{ $message }}</p> @enderror
            </div>

            <div class="md:col-span-3 pt-2">
                <button type="submit"
                    class="bg-yellow-400 hover:bg-yellow-300 text-black border-3 border-black dark:border-zinc-700 font-black text-sm uppercase px-6 py-3 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-1 active:translate-y-1 active:shadow-none transition-all inline-flex items-center gap-2 rounded-lg cursor-pointer">
                    <x-icon name="lucide-save" class="w-5 h-5 text-black stroke-[2.5]" />
                    <span>Simpan Kata Sandi</span>
                </button>
            </div>
        </form>
    </div>

    @if ($canManageTwoFactor)
        <!-- 2. Autentikasi Dua Faktor (2FA) -->
        <div class="space-y-4 pt-4 border-t-2 border-black/10 dark:border-zinc-800">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b-2 border-black/10 dark:border-zinc-800 pb-2">
                <div class="flex items-center gap-2">
                    <x-icon name="lucide-smartphone" class="w-4 h-4 text-black dark:text-white stroke-[2.5]" />
                    <h3 class="text-base font-black text-black dark:text-white uppercase tracking-tight">Autentikasi Dua Faktor (2FA)</h3>
                </div>
                <div>
                    @if ($twoFactorEnabled)
                        <span class="px-3 py-1 bg-emerald-300 text-black border-2 border-black dark:border-zinc-700 font-extrabold text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1.5">
                            <x-icon name="lucide-check-circle-2" class="w-3.5 h-3.5 text-black stroke-[2.5]" />
                            <span>2FA Aktif</span>
                        </span>
                    @else
                        <span class="px-3 py-1 bg-zinc-200 text-black border-2 border-black dark:border-zinc-700 font-extrabold text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1.5">
                            <x-icon name="lucide-shield-alert" class="w-3.5 h-3.5 text-black stroke-[2.5]" />
                            <span>2FA Nonaktif</span>
                        </span>
                    @endif
                </div>
            </div>

            <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 leading-relaxed">
                Tingkatkan keamanan akun Anda dengan verifikasi 2FA menggunakan aplikasi otentikator seperti Google Authenticator atau Authy.
            </p>

            <div class="pt-2">
                @if ($twoFactorEnabled)
                    <div class="space-y-4">
                        <button type="button" wire:click="disable"
                            class="bg-rose-500 hover:bg-rose-400 text-white border-3 border-black dark:border-zinc-700 font-black text-xs uppercase px-5 py-2.5 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer inline-flex items-center gap-1.5">
                            <x-icon name="lucide-shield-off" class="w-4 h-4 stroke-[2.5]" />
                            <span>Nonaktifkan 2FA</span>
                        </button>

                        <livewire:profile.two-factor.recovery-codes :$requiresConfirmation />
                    </div>
                @else
                    <button type="button" wire:click="enable"
                        class="bg-cyan-300 hover:bg-cyan-200 text-black border-3 border-black dark:border-zinc-700 font-black text-xs uppercase px-5 py-2.5 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer inline-flex items-center gap-1.5">
                        <x-icon name="lucide-shield-plus" class="w-4 h-4 text-black stroke-[2.5]" />
                        <span>Aktifkan 2FA</span>
                    </button>
                @endif
            </div>

            <!-- Modal 2FA Setup -->
            <div
                x-data="{ showModal: @entangle('showModal') }"
                x-show="showModal"
                x-cloak
                class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            >
                <div class="absolute inset-0 bg-black/70" @click="$wire.closeModal()"></div>
                <div class="relative w-full max-w-md bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] rounded-2xl p-6 space-y-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="w-10 h-10 bg-cyan-300 border-2 border-black dark:border-zinc-700 rounded-lg flex items-center justify-center text-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] shrink-0">
                            <x-icon name="lucide-qr-code" class="w-5 h-5 text-black stroke-[2.5]" />
                        </div>
                        <button type="button" wire:click="closeModal" class="bg-rose-500 hover:bg-rose-400 text-white border-2 border-black dark:border-zinc-700 p-1.5 rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none font-black cursor-pointer transition-all">
                            <x-icon name="lucide-x" class="w-4 h-4 stroke-[3]" />
                        </button>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-xl font-black text-black dark:text-white uppercase tracking-tight">{{ $this->modalConfig['title'] }}</h3>
                        <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 leading-relaxed">{{ $this->modalConfig['description'] }}</p>
                    </div>

                    @if ($showVerificationStep)
                        <div class="space-y-4">
                            <div>
                                <label for="otp_code" class="block text-xs font-black uppercase text-black dark:text-white mb-1.5">Kode OTP 6 Digit</label>
                                <input type="text" id="otp_code" wire:model="code" maxlength="6" inputmode="numeric"
                                    class="w-full px-4 py-3 text-center text-lg tracking-widest font-mono bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-lg text-black dark:text-white font-black placeholder-zinc-400 focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]"
                                    placeholder="123456">
                                @error('code') <p class="text-xs font-black text-rose-500 mt-1 uppercase">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex gap-2">
                                <button type="button" wire:click="resetVerification"
                                    class="flex-1 py-2.5 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-black dark:text-white border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer">
                                    Kembali
                                </button>
                                <button type="button" wire:click="confirmTwoFactor"
                                    class="flex-1 py-2.5 bg-lime-400 hover:bg-lime-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer">
                                    Konfirmasi
                                </button>
                            </div>
                        </div>
                    @elseif ($showRecoveryStep)
                        <div class="space-y-4">
                            @if (filled($recoveryCodes))
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 p-4 bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 rounded-lg font-mono text-xs font-bold text-black dark:text-white">
                                    @foreach($recoveryCodes as $code)
                                        <div class="p-1.5 bg-zinc-100 dark:bg-zinc-800 rounded border border-zinc-300 dark:border-zinc-700 select-all text-center">
                                            {{ $code }}
                                        </div>
                                    @endforeach
                                </div>
                                <p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400 leading-normal">
                                    Setiap kode pemulihan hanya dapat digunakan satu kali. Simpan kode ini baik-baik.
                                </p>
                            @endif

                            <button type="button" wire:click="closeModal"
                                class="w-full py-3 bg-yellow-400 hover:bg-yellow-300 text-black border-3 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer">
                                Selesai
                            </button>
                        </div>
                    @else
                        @error('setupData')
                            <p class="text-xs font-black text-rose-500 uppercase">{{ $message }}</p>
                        @enderror

                        <div class="flex justify-center p-4 bg-white rounded-xl border-3 border-black dark:border-zinc-700 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]">
                            @empty($qrCodeSvg)
                                <div class="text-xs font-black uppercase animate-pulse text-black">Memuat QR Code...</div>
                            @else
                                <div class="p-2 bg-white rounded">
                                    {!! $qrCodeSvg !!}
                                </div>
                            @endempty
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black uppercase text-zinc-500 dark:text-zinc-400">Atau Masukkan Kunci Setup Manual</label>
                            <div x-data="{ copied: false, async copy() { await navigator.clipboard.writeText('{{ $manualSetupKey }}'); this.copied = true; setTimeout(() => this.copied = false, 1500); } }"
                                class="flex items-center bg-zinc-100 dark:bg-zinc-800 border-2 border-black dark:border-zinc-700 rounded-lg p-1.5">
                                <input type="text" readonly value="{{ $manualSetupKey }}" class="w-full bg-transparent text-xs font-mono font-bold text-black dark:text-white px-2 focus:outline-none">
                                <button type="button" @click="copy()" class="px-3 py-1 bg-yellow-300 hover:bg-yellow-200 text-black border border-black font-black text-[10px] uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] cursor-pointer shrink-0">
                                    <span x-show="!copied">Salin</span>
                                    <span x-show="copied" x-cloak>Tersalin!</span>
                                </button>
                            </div>
                        </div>

                        <button type="button" wire:click="showVerificationIfNecessary"
                            class="w-full py-3 bg-yellow-400 hover:bg-yellow-300 text-black border-3 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer">
                            {{ $this->modalConfig['buttonText'] }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- 3. Seksi Passkeys -->
    @if ($canManagePasskeys)
        <div class="space-y-4 pt-4 border-t-2 border-black/10 dark:border-zinc-800">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b-2 border-black/10 dark:border-zinc-800 pb-2">
                <div class="flex items-center gap-2">
                    <x-icon name="lucide-key" class="w-4 h-4 text-black dark:text-white stroke-[2.5]" />
                    <h3 class="text-base font-black text-black dark:text-white uppercase tracking-tight">Passkeys</h3>
                </div>
            </div>

            <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 leading-relaxed">
                Gunakan biometrik (Fingerprint / Face ID) atau kunci keamanan fisik untuk masuk tanpa password.
            </p>

            <div class="space-y-3">
                <div class="border-3 border-black dark:border-zinc-700 rounded-xl overflow-hidden bg-white dark:bg-zinc-900 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]">
                    @forelse ($passkeys as $passkey)
                        <div class="flex items-center justify-between p-4 {{ ! $loop->last ? 'border-b-2 border-black/10 dark:border-zinc-800' : '' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-yellow-300 border-2 border-black dark:border-zinc-700 flex items-center justify-center shrink-0 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                    <x-icon name="lucide-key" class="w-4 h-4 text-black stroke-[2.5]" />
                                </div>
                                <div class="space-y-0.5">
                                    <div class="flex items-center gap-2">
                                        <p class="font-black text-sm text-black dark:text-white">{{ $passkey['name'] }}</p>
                                        @if ($passkey['authenticator'])
                                            <span class="px-2 py-0.5 bg-cyan-300 text-black border border-black font-extrabold text-[10px] uppercase rounded">
                                                {{ $passkey['authenticator'] }}
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] font-bold text-zinc-500 dark:text-zinc-400">
                                        Ditambahkan {{ $passkey['created_at_diff'] }}
                                        @if ($passkey['last_used_at_diff'])
                                            • Terakhir digunakan {{ $passkey['last_used_at_diff'] }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <button type="button" wire:click="confirmDelete({{ $passkey['id'] }})"
                                class="p-2 bg-rose-500 hover:bg-rose-400 text-white border-2 border-black dark:border-zinc-700 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer"
                                title="Hapus Passkey">
                                <x-icon name="lucide-trash-2" class="w-4 h-4 stroke-[2.5]" />
                            </button>
                        </div>
                    @empty
                        <div class="p-6 text-center space-y-2">
                            <div class="w-12 h-12 mx-auto rounded-xl bg-zinc-100 dark:bg-zinc-800 border-2 border-black dark:border-zinc-700 flex items-center justify-center">
                                <x-icon name="lucide-key" class="w-6 h-6 text-black dark:text-white stroke-[2.5]" />
                            </div>
                            <p class="font-black text-sm text-black dark:text-white uppercase">Belum ada Passkey</p>
                            <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400">Tambahkan passkey untuk masuk tanpa ketik kata sandi.</p>
                        </div>
                    @endforelse
                </div>

                <x-passkey-registration />
            </div>

            <!-- Modal Delete Passkey -->
            <div
                x-data="{ showDeleteModal: @entangle('showDeleteModal') }"
                x-show="showDeleteModal"
                x-cloak
                class="fixed inset-0 z-[100] flex items-center justify-center p-4"
            >
                <div class="absolute inset-0 bg-black/70" @click="$wire.closeDeleteModal()"></div>
                <div class="relative w-full max-w-md bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] rounded-2xl p-6 space-y-4">
                    <div class="flex items-start justify-between gap-4">
                        <div class="w-10 h-10 bg-rose-500 border-2 border-black dark:border-zinc-700 rounded-lg flex items-center justify-center text-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] shrink-0">
                            <x-icon name="lucide-trash-2" class="w-5 h-5 stroke-[2.5]" />
                        </div>
                        <button type="button" wire:click="closeDeleteModal" class="text-black dark:text-white hover:bg-black/10 p-1.5 rounded font-black cursor-pointer transition-colors">
                            <x-icon name="lucide-x" class="w-4 h-4 stroke-[2.5]" />
                        </button>
                    </div>

                    <div class="space-y-2">
                        <h3 class="text-xl font-black text-black dark:text-white uppercase tracking-tight">Hapus Passkey?</h3>
                        <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 leading-relaxed">
                            Apakah Anda yakin ingin menghapus passkey "<span class="font-black text-black dark:text-white">{{ $deletingPasskeyName }}</span>"? Anda tidak dapat lagi menggunakannya untuk masuk.
                        </p>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <button type="button" wire:click="closeDeleteModal"
                            class="flex-1 py-2.5 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-black dark:text-white border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer">
                            Batal
                        </button>
                        <button type="button" wire:click="deletePasskey"
                            class="flex-1 py-2.5 bg-rose-500 hover:bg-rose-400 text-white border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer">
                            Hapus Passkey
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
