<div class="min-h-[calc(100vh-4rem)] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-md w-full bg-white rounded-3xl p-8 md:p-10 shadow-2xl shadow-gray-200/50 border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-950 tracking-tight leading-tight">
                Selamat Datang Kembali
            </h1>
            <p class="mt-2 text-sm text-gray-500 font-medium">
                Masuk ke akun Anda untuk melanjutkan akses platform KostBandung.id
            </p>
        </div>

        <form wire:submit.prevent="login" class="space-y-5">
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
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-gray-700">
                        Kata Sandi
                    </label>
                </div>
                <input wire:model="password" type="password" id="password" 
                       class="w-full px-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-2xl text-gray-950 placeholder-gray-400 focus:bg-white focus:border-gray-950 focus:outline-none transition-all"
                       placeholder="••••••••">
                @error('password')
                    <p class="mt-1.5 text-xs font-medium text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input wire:model="remember" type="checkbox" class="rounded border-gray-300 text-gray-950 focus:ring-0">
                    <span class="text-gray-600">Ingat Saya</span>
                </label>
            </div>

            <button type="submit" 
                    class="w-full py-3.5 px-6 bg-gray-950 hover:bg-gray-800 text-white font-semibold text-sm rounded-full shadow-sm transition-all focus:outline-none">
                Masuk Akun
            </button>
        </form>

        <div class="mt-8 text-center text-xs text-gray-500">
            Belum memiliki akun? 
            <a href="{{ route('register') }}" class="font-bold text-gray-950 hover:underline">
                Daftar Sekarang
            </a>
        </div>
    </div>
</div>
