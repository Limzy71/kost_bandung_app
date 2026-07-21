<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-md w-full bg-white rounded-3xl p-8 md:p-10 shadow-2xl shadow-gray-200/50 border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-950 tracking-tight leading-tight">
                Buat Akun Baru
            </h1>
            <p class="mt-2 text-sm text-gray-500 font-medium">
                Daftar sekarang untuk mencari kost atau mempublikasikan properti Anda
            </p>
        </div>

        <form wire:submit.prevent="register" class="space-y-5">
            <!-- Role Selector -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-2">
                    Tipe Akun
                </label>
                <div class="grid grid-cols-2 gap-3">
                    <button type="button" wire:click="$set('role', 'user')"
                            class="py-3 px-4 rounded-2xl text-xs font-semibold border transition-all flex items-center justify-center gap-2 {{ $role === 'user' ? 'bg-gray-950 text-white border-gray-950 shadow-sm' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                        Pencari Kost
                    </button>
                    <button type="button" wire:click="$set('role', 'owner')"
                            class="py-3 px-4 rounded-2xl text-xs font-semibold border transition-all flex items-center justify-center gap-2 {{ $role === 'owner' ? 'bg-gray-950 text-white border-gray-950 shadow-sm' : 'bg-gray-50 text-gray-700 border-gray-200 hover:bg-gray-100' }}">
                        Pemilik Kost
                    </button>
                </div>
                @error('role')
                    <p class="mt-1.5 text-xs font-medium text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Nama Lengkap
                </label>
                <input wire:model="name" type="text" id="name" 
                       class="w-full px-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-2xl text-gray-950 placeholder-gray-400 focus:bg-white focus:border-gray-950 focus:outline-none transition-all"
                       placeholder="Nama lengkap Anda">
                @error('name')
                    <p class="mt-1.5 text-xs font-medium text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Alamat Email
                </label>
                <input wire:model="email" type="email" id="email" 
                       class="w-full px-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-2xl text-gray-950 placeholder-gray-400 focus:bg-white focus:border-gray-950 focus:outline-none transition-all"
                       placeholder="nama@email.com">
                @error('email')
                    <p class="mt-1.5 text-xs font-medium text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Kata Sandi
                </label>
                <input wire:model="password" type="password" id="password" 
                       class="w-full px-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-2xl text-gray-950 placeholder-gray-400 focus:bg-white focus:border-gray-950 focus:outline-none transition-all"
                       placeholder="Minimal 8 karakter">
                @error('password')
                    <p class="mt-1.5 text-xs font-medium text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 mb-1.5">
                    Konfirmasi Kata Sandi
                </label>
                <input wire:model="password_confirmation" type="password" id="password_confirmation" 
                       class="w-full px-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-2xl text-gray-950 placeholder-gray-400 focus:bg-white focus:border-gray-950 focus:outline-none transition-all"
                       placeholder="Ulangi kata sandi">
            </div>

            <button type="submit" 
                    class="w-full py-3.5 px-6 bg-gray-950 hover:bg-gray-800 text-white font-semibold text-sm rounded-full shadow-sm transition-all focus:outline-none mt-2">
                Daftar Sekarang
            </button>
        </form>

        <div class="mt-8 text-center text-xs text-gray-500">
            Sudah memiliki akun? 
            <a href="{{ route('login') }}" class="font-bold text-gray-950 hover:underline">
                Masuk Di Sini
            </a>
        </div>
    </div>
</div>
