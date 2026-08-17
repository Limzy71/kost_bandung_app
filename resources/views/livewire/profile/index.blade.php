<div
    x-data
    class="min-h-screen bg-[#f8f9fa] dark:bg-zinc-950 bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] dark:bg-[linear-gradient(to_right,#27272a_1px,transparent_1px),linear-gradient(to_bottom,#27272a_1px,transparent_1px)] bg-[size:24px_24px]"
>
    @php
        $roleConfig = [
            'user' => ['label' => 'Pencari Kost', 'badge' => 'bg-cyan-300', 'icon' => 'lucide-search'],
            'owner' => ['label' => 'Pemilik Kost', 'badge' => 'bg-cyan-300', 'icon' => 'lucide-building-2'],
            'admin' => ['label' => 'Administrator', 'badge' => 'bg-yellow-300', 'icon' => 'lucide-shield-check'],
        ][$user->role] ?? ['label' => 'Pengguna', 'badge' => 'bg-zinc-200', 'icon' => 'lucide-user'];
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-8">

        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 bg-white dark:bg-zinc-900 p-6 md:p-8 border-4 border-black dark:border-zinc-700 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] rounded-xl">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 {{ $roleConfig['badge'] }} text-black border-2 border-black dark:border-zinc-700 font-extrabold text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1.5">
                        <x-icon :name="$roleConfig['icon']" class="w-3.5 h-3.5 stroke-[2.5]" />
                        <span>{{ $roleConfig['label'] }}</span>
                    </span>
                    @if ($user->role === 'owner' && $user->isIdentityVerified())
                        <span class="px-3 py-1 bg-emerald-300 text-black dark:text-white border-2 border-black dark:border-zinc-700 font-extrabold text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1">
                            <x-icon name="lucide-badge-check" class="w-3.5 h-3.5 stroke-[2.5]" />
                            <span>Identitas Terverifikasi</span>
                        </span>
                    @endif
                </div>
                <h1 class="text-3xl md:text-5xl font-black text-black dark:text-white tracking-tight uppercase">
                    Profil Saya
                </h1>
                <p class="text-zinc-700 dark:text-white text-sm md:text-base font-bold">
                    Kelola identitas akun Anda di <span class="bg-yellow-200 dark:bg-yellow-400 border-b-2 border-black dark:border-white px-1 text-black font-extrabold">KostBandung</span>.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <!-- Theme Switcher Neo-Brutalist -->
                <div x-data="{
                        theme: (() => {
                            const a = window.Flux?.appearance ?? localStorage.getItem('flux.appearance') ?? 'light';
                            return a === 'dark' ? 'dark' : 'light';
                        })(),
                        setTheme(val) {
                            this.theme = val;
                            window.Flux.appearance = val;
                        }
                    }" class="inline-flex p-1 bg-zinc-100 dark:bg-zinc-800 border-3 border-black dark:border-zinc-700 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]">
                    <button type="button" @click="setTheme('light')"
                        :class="theme === 'light' ? 'bg-yellow-300 text-black border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'text-zinc-700 dark:text-zinc-300 hover:text-black dark:hover:text-white border-2 border-transparent'"
                        class="px-3 py-1.5 rounded-lg font-black text-xs uppercase flex items-center gap-1.5 transition-all cursor-pointer">
                        <x-icon name="lucide-sun" class="w-4 h-4 stroke-[2.5]" />
                        <span class="hidden sm:inline">Terang</span>
                    </button>
                    <button type="button" @click="setTheme('dark')"
                        :class="theme === 'dark' ? 'bg-yellow-300 text-black border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'text-zinc-700 dark:text-zinc-300 hover:text-black dark:hover:text-white border-2 border-transparent'"
                        class="px-3 py-1.5 rounded-lg font-black text-xs uppercase flex items-center gap-1.5 transition-all cursor-pointer">
                        <x-icon name="lucide-moon" class="w-4 h-4 stroke-[2.5]" />
                        <span class="hidden sm:inline">Gelap</span>
                    </button>
                </div>

                <button type="button" wire:click="toggleEdit"
                    class="bg-yellow-400 hover:bg-yellow-300 text-black border-3 border-black dark:border-zinc-700 font-black text-sm uppercase px-6 py-3.5 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-1 active:translate-y-1 active:shadow-none transition-all inline-flex items-center gap-2 rounded-lg group cursor-pointer">
                    <x-icon name="lucide-pencil" class="w-5 h-5 text-black stroke-[2.5]" />
                    <span>{{ $editing ? 'Batal' : 'Edit Profil' }}</span>
                </button>
            </div>
        </div>

        <!-- Profile Card -->
        <div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 p-6 md:p-8 rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] overflow-hidden">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <div class="flex flex-col items-center shrink-0">
                    <div class="w-24 h-24 md:w-28 md:h-28 rounded-2xl bg-yellow-300 border-4 border-black dark:border-zinc-700 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] flex items-center justify-center overflow-hidden relative">
                        @if ($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover" referrerpolicy="no-referrer" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');" />
                        @endif
                        <span class="text-3xl md:text-4xl font-black text-black uppercase select-none {{ $user->avatar_url ? 'hidden' : '' }}">{{ $user->initials() }}</span>
                    </div>

                    <div class="flex items-center justify-center gap-1.5 mt-2.5">
                        <label class="px-2.5 py-1 bg-cyan-300 hover:bg-cyan-200 text-black border-2 border-black dark:border-zinc-700 font-black text-[10px] uppercase rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer inline-flex items-center gap-1">
                            <x-icon name="lucide-camera" class="w-3 h-3 text-black stroke-[2.5]" />
                            <span>{{ $user->avatar_url ? 'Ganti' : 'Unggah' }}</span>
                            <input type="file" wire:model="avatarUpload" accept="image/*" class="hidden" />
                        </label>
                        @if ($user->avatar_url)
                            <button type="button" @click="$dispatch('open-delete-avatar-modal')" class="px-2 py-1 bg-rose-400 hover:bg-rose-300 text-black dark:text-white border-2 border-black dark:border-zinc-700 font-black text-[10px] uppercase rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer inline-flex items-center gap-1" title="Hapus Foto Profil">
                                <x-icon name="lucide-trash-2" class="w-3 h-3 stroke-[2.5]" />
                            </button>
                        @endif
                    </div>

                    <div wire:loading wire:target="avatarUpload" class="text-[10px] font-black text-black dark:text-white mt-1 text-center animate-pulse">
                        Mengunggah...
                    </div>

                    @error('avatarUpload')
                        <p class="text-[10px] font-black text-rose-600 mt-1 text-center max-w-[140px]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex-1 min-w-0 text-center sm:text-left space-y-2">
                    <h2 class="text-2xl md:text-3xl font-black text-black dark:text-white uppercase tracking-tight truncate" title="{{ $user->name }}">{{ Str::limit($user->name, 30) }}</h2>
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <span class="px-2.5 py-1 {{ $roleConfig['badge'] }} border-2 border-black dark:border-zinc-700 text-[10px] font-black uppercase text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1">
                            <x-icon :name="$roleConfig['icon']" class="w-3 h-3 text-black stroke-[2.5]" />
                            <span>{{ $roleConfig['label'] }}</span>
                        </span>
                        <span class="text-[10px] font-black uppercase text-zinc-500 dark:text-zinc-400">Terdaftar sejak {{ $user->created_at?->translatedFormat('d F Y') }}</span>
                    </div>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 pt-2 text-sm">
                        <div class="flex items-center gap-2 text-zinc-700 dark:text-zinc-300 font-bold">
                            <x-icon name="lucide-mail" class="w-4 h-4 text-black dark:text-white shrink-0 stroke-[2.5]" />
                            <span class="truncate">{{ $user->email }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-zinc-700 dark:text-zinc-300 font-bold">
                            <x-icon name="lucide-phone" class="w-4 h-4 text-black dark:text-white shrink-0 stroke-[2.5]" />
                            <span>{{ $user->phone_number ?: 'Belum diisi' }}</span>
                        </div>
                        @if ($user->role === 'owner')
                            <div class="flex items-center gap-2 text-zinc-700 dark:text-zinc-300 font-bold sm:col-span-2">
                                <x-icon name="lucide-building-2" class="w-4 h-4 text-black dark:text-white shrink-0 stroke-[2.5]" />
                                <span>Nama Usaha: <span class="text-black dark:text-white">{{ $user->business_name ?: '-' }}</span></span>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <!-- Edit Profile Form -->
        @if ($editing)
            <div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 p-6 md:p-8 rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)]">
                <div class="flex items-center gap-3 border-b-3 border-black dark:border-zinc-700 pb-4 mb-6">
                    <div class="w-10 h-10 bg-yellow-300 border-2 border-black dark:border-zinc-700 rounded-lg flex items-center justify-center text-black shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                        <x-icon name="lucide-pencil" class="w-5 h-5 text-black stroke-[2.5]" />
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-black dark:text-white uppercase tracking-tight">Edit Data Profil</h2>
                        <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400">Perbarui identitas akun Anda.</p>
                    </div>
                </div>

                <form wire:submit="updateProfile" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-xs font-black uppercase text-black dark:text-white mb-1.5">Nama Lengkap</label>
                        <input type="text" id="name" wire:model="name"
                            class="w-full px-4 py-3 text-sm bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-lg text-black dark:text-white font-bold placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] transition-all"
                            placeholder="Nama lengkap Anda">
                        @error('name') <p class="text-xs font-black text-rose-500 mt-1 uppercase">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-black uppercase text-black dark:text-white mb-1.5">Email</label>
                        <input type="email" id="email" wire:model="email" @disabled($user->role === 'admin')
                            class="w-full px-4 py-3 text-sm bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-lg text-black dark:text-white font-bold placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] transition-all @if($user->role === 'admin') opacity-60 cursor-not-allowed @endif"
                            placeholder="nama@email.com">
                        @if($user->role === 'admin')
                            <p class="text-[10px] font-black text-zinc-500 dark:text-zinc-400 mt-1 uppercase">Email admin tidak dapat diubah.</p>
                        @endif
                        @error('email') <p class="text-xs font-black text-rose-500 mt-1 uppercase">{{ $message }}</p> @enderror
                    </div>

                    @if ($user->role !== 'admin')
                        <div>
                            <label for="phone_number" class="block text-xs font-black uppercase text-black dark:text-white mb-1.5">Nomor WhatsApp</label>
                            <input type="text" id="phone_number" wire:model="phone_number"
                                inputmode="numeric" oninput="let v = this.value.replace(/[^0-9]/g, ''); if(v.startsWith('62')) v = '0' + v.slice(2); else if(v.length > 0 && v[0] !== '0') v = '0' + v; this.value = v;"
                                maxlength="16"
                                class="w-full px-4 py-3 text-sm bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-lg text-black dark:text-white font-bold placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] transition-all"
                                placeholder="Contoh: 081234567890">
                            @error('phone_number') <p class="text-xs font-black text-rose-500 mt-1 uppercase">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    @if ($user->role === 'owner')
                        <div>
                            <label for="business_name" class="block text-xs font-black uppercase text-black dark:text-white mb-1.5">Nama Usaha / Properti</label>
                            <input type="text" id="business_name" wire:model="business_name"
                                class="w-full px-4 py-3 text-sm bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-lg text-black dark:text-white font-bold placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] transition-all"
                                placeholder="Contoh: Kost Putra Sejahtera">
                            @error('business_name') <p class="text-xs font-black text-rose-500 mt-1 uppercase">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <div class="md:col-span-2 flex flex-wrap items-center gap-3 pt-2">
                        <button type="submit"
                            class="bg-lime-400 hover:bg-lime-300 text-black dark:text-white border-3 border-black dark:border-zinc-700 font-black text-sm uppercase px-6 py-3 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-1 active:translate-y-1 active:shadow-none transition-all inline-flex items-center gap-2 rounded-lg cursor-pointer">
                            <x-icon name="lucide-check" class="w-5 h-5 stroke-[2.5]" />
                            <span>Simpan Perubahan</span>
                        </button>
                        <button type="button" wire:click="toggleEdit"
                            class="bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-black dark:text-white border-3 border-black dark:border-zinc-700 font-black text-sm uppercase px-6 py-3 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-1 active:translate-y-1 active:shadow-none transition-all rounded-lg cursor-pointer">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Role-specific Content -->
        @if ($user->role === 'owner')
            @include('livewire.profile.partials.owner', ['user' => $user, 'stats' => $stats])
        @elseif ($user->role === 'admin')
            @include('livewire.profile.partials.admin', ['user' => $user, 'stats' => $stats])
        @else
            @include('livewire.profile.partials.user', ['user' => $user, 'stats' => $stats])
        @endif

        <!-- Security Section -->
        <livewire:profile.security />

        @if ($this->showDeleteAccount)
            @php
                $deleteData = $user->role === 'owner'
                    ? 'daftar kost, dokumen verifikasi (KTP dan bukti kepemilikan), foto kost, foto profil, dan riwayat pesan'
                    : 'foto profil dan riwayat pesan';
            @endphp

            <!-- Delete Account Section -->
            <div class="bg-white dark:bg-zinc-900 border-4 border-rose-500 p-6 md:p-8 rounded-2xl shadow-[6px_6px_0px_0px_rgba(190,18,60,1)]">
                <div class="flex items-center gap-3 border-b-3 border-black dark:border-zinc-700 pb-4 mb-4">
                    <div class="w-10 h-10 bg-rose-500 border-2 border-black dark:border-zinc-700 rounded-lg flex items-center justify-center shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                        <x-icon name="lucide-trash-2" class="w-5 h-5 text-white stroke-[2.5]" />
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-black dark:text-white uppercase tracking-tight">Hapus Akun</h2>
                        <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400">Berhenti menggunakan KostBandung dan hapus seluruh data Anda.</p>
                    </div>
                </div>

                <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 leading-relaxed">
                    Akun akan dihapus secara permanen beserta {{ $deleteData }}. Tindakan ini tidak dapat dibatalkan.
                </p>

                <button type="button" @click="$dispatch('open-delete-account-modal')"
                    class="mt-5 bg-rose-500 hover:bg-rose-400 text-white border-3 border-black dark:border-zinc-700 font-black text-sm uppercase px-6 py-3 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-1 active:translate-y-1 active:shadow-none transition-all rounded-lg cursor-pointer inline-flex items-center gap-2">
                    <x-icon name="lucide-trash-2" class="w-5 h-5 stroke-[2.5]" />
                    <span>Hapus Akun</span>
                </button>
            </div>

        <!-- Delete Account Modal -->
        <div
            x-data="{ deleteAccountModalOpen: @entangle('deleteAccountModalOpen') }"
            @open-delete-account-modal.window="deleteAccountModalOpen = true"
        >
            <template x-teleport="body">
                <div
                    x-show="deleteAccountModalOpen"
                    x-cloak
                    x-transition.opacity.duration.200ms
                    class="fixed inset-0 z-[100] flex items-center justify-center p-4"
                >
                    <div class="absolute inset-0 bg-black/70" @click="deleteAccountModalOpen = false"></div>
            <div
                x-show="deleteAccountModalOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-md bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] rounded-2xl p-6"
            >
                <div class="flex items-start justify-between gap-4">
                    <div class="w-12 h-12 bg-rose-500 border-2 border-black dark:border-zinc-700 rounded-lg flex items-center justify-center text-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] shrink-0">
                        <x-icon name="lucide-trash-2" class="w-6 h-6 stroke-[2.5]" />
                    </div>
                    <button type="button" @click="deleteAccountModalOpen = false" class="text-black dark:text-white hover:bg-black/10 p-1.5 rounded font-black cursor-pointer transition-colors">
                        <x-icon name="lucide-x" class="w-4 h-4 stroke-[2.5]" />
                    </button>
                </div>

                <div class="mt-4">
                    <h3 class="text-xl font-black text-black dark:text-white uppercase tracking-tight">Hapus Akun Permanen?</h3>
                    <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 mt-2 leading-relaxed">
                        Seluruh data akun Anda termasuk {{ $deleteData }} akan dihapus permanen dan tidak dapat dikembalikan. {{ $user->password ? 'Masukkan password Anda untuk mengonfirmasi.' : 'Ketik kata HAPUS untuk mengonfirmasi.' }}
                    </p>
                </div>

                <form wire:submit.prevent="deleteAccount">
                    <input type="email" name="username" value="{{ $user->email }}" autocomplete="username" class="hidden" aria-hidden="true" readonly tabindex="-1">
                    @if ($user->password)
                        <div class="mt-4">
                            <label for="deletePassword" class="block text-xs font-black uppercase text-black dark:text-white mb-1.5">Password</label>
                            <input type="password" id="deletePassword" wire:model="deletePassword" autocomplete="current-password"
                                class="w-full px-4 py-3 text-sm bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-lg text-black dark:text-white font-bold placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] transition-all"
                                placeholder="Masukkan password Anda">
                            @error('deletePassword') <p class="text-xs font-black text-rose-500 mt-1 uppercase">{{ $message }}</p> @enderror
                        </div>
                    @else
                        <div class="mt-4">
                            <label for="deleteConfirmation" class="block text-xs font-black uppercase text-black dark:text-white mb-1.5">Ketik "HAPUS" untuk Konfirmasi</label>
                            <input type="text" id="deleteConfirmation" wire:model="deleteConfirmation" maxlength="10"
                                class="w-full px-4 py-3 text-sm bg-white dark:bg-zinc-900 border-3 border-black dark:border-zinc-700 rounded-lg text-black dark:text-white font-bold placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-0 focus:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] transition-all uppercase"
                                placeholder="HAPUS">
                            @error('deleteConfirmation') <p class="text-xs font-black text-rose-500 mt-1 uppercase">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    <div class="flex items-center gap-2 mt-6">
                        <button type="button" @click="deleteAccountModalOpen = false"
                            class="flex-1 h-10 px-3 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-black dark:text-white border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all duration-150 rounded-lg cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 h-10 px-3 bg-rose-500 hover:bg-rose-400 text-white border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all duration-150 rounded-lg cursor-pointer">
                            Ya, Hapus Akun
                        </button>
                    </div>
                </form>
            </div>
            </template>
        </div>
        @endif

        <!-- Delete Avatar Modal -->
        <!-- Delete Avatar Modal -->
        <div 
            x-data="{ open: false }"
            @open-delete-avatar-modal.window="open = true"
        >
            <template x-teleport="body">
                <div
                    x-show="open"
                    x-cloak
                    x-transition.opacity.duration.200ms
                    class="fixed inset-0 z-[100] flex items-center justify-center p-4"
                >
            <div class="absolute inset-0 bg-black/70" @click="open = false"></div>
            <div 
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-sm bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] rounded-2xl p-6"
            >
                <div class="flex items-start justify-between gap-4">
                    <div class="w-12 h-12 bg-rose-500 border-2 border-black dark:border-zinc-700 rounded-lg flex items-center justify-center text-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] shrink-0">
                        <x-icon name="lucide-trash-2" class="w-6 h-6 stroke-[2.5]" />
                    </div>
                    <button type="button" @click="open = false" class="text-black dark:text-white hover:bg-black/10 p-1.5 rounded font-black cursor-pointer transition-colors">
                        <x-icon name="lucide-x" class="w-4 h-4 stroke-[2.5]" />
                    </button>
                </div>
                
                <div class="mt-4">
                    <h3 class="text-xl font-black text-black dark:text-white uppercase tracking-tight">Hapus Foto Profil</h3>
                    <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 mt-2 leading-relaxed">
                        Apakah Anda yakin ingin menghapus foto profil ini? Foto yang dihapus tidak dapat dikembalikan.
                    </p>
                </div>
                
                <div class="flex items-center gap-2 mt-6">
                    <button type="button" @click="open = false" class="flex-1 h-10 px-3 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 text-black dark:text-white border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all duration-150 rounded-lg cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="open = false; $wire.deleteAvatar()" class="flex-1 h-10 px-3 bg-rose-500 hover:bg-rose-400 text-white border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:hover:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all duration-150 rounded-lg cursor-pointer">
                        Ya, Hapus
                    </button>
                </div>
            </div>
                </div>
            </template>
        </div>
    </div>
</div>
