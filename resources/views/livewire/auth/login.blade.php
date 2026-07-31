<div class="w-full">
    {{-- ===== Neo-Brutalist Login Card ===== --}}
    <div class="bg-white border-4 border-black p-8 md:p-10 shadow-[12px_12px_0px_0px_rgba(0,0,0,1)] rounded-lg w-full">

        {{-- Card Header --}}
        <div class="mb-8">
            <div class="inline-flex items-center gap-2 bg-[#FFE500] border-2 border-black px-3 py-1 rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mb-4">
                <span class="text-[10px] font-black uppercase tracking-widest text-black">Masuk Akun</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-black uppercase tracking-tight leading-tight">
                Selamat Datang Kembali!
            </h1>
            <p class="mt-2 text-sm font-bold text-zinc-600">
                Masuk ke akun KostBandung.id Anda untuk melanjutkan.
            </p>
        </div>

        {{-- Divider --}}
        <div class="border-t-4 border-black mb-8"></div>

        {{-- Rate Limit / General Error Banner --}}
        @if ($errors->has('email') && str_contains($errors->first('email'), 'TERLALU BANYAK'))
            <div class="mb-6 flex items-center gap-3 bg-rose-400 border-4 border-black p-4 rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                <x-icon name="lucide-triangle-alert" class="w-5 h-5 text-black shrink-0 stroke-[3]" />
                <span class="text-xs font-black uppercase text-black">{{ $errors->first('email') }}</span>
            </div>
        @endif

        <form wire:submit="login" class="space-y-5">
            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs font-black uppercase tracking-wider text-black mb-2">
                    Alamat Email
                </label>
                <input wire:model="email" type="email" id="email" autocomplete="email"
                    class="w-full px-4 py-3 text-sm bg-zinc-50 border-3 border-black rounded-lg text-black placeholder-zinc-400 focus:outline-none focus:ring-0 focus:bg-[#FFE500]/10 focus:border-black transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]"
                    placeholder="nama@email.com">
                @error('email')
                    @if (!str_contains($message, 'TERLALU BANYAK'))
                        <p class="mt-2 text-xs font-bold text-rose-600 flex items-center gap-1">
                            <span class="font-black">✕</span> {{ $message }}
                        </p>
                    @endif
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-xs font-black uppercase tracking-wider text-black mb-2">
                    Kata Sandi
                </label>
                <div class="relative flex items-center" x-data="{ show: false }">
                    <input wire:model="password" :type="show ? 'text' : 'password'" id="password" autocomplete="current-password"
                        class="block w-full px-4 pr-12 py-3 text-sm bg-zinc-50 border-3 border-black rounded-lg text-black placeholder-zinc-400 focus:outline-none focus:ring-0 focus:bg-[#FFE500]/10 focus:border-black transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]"
                        placeholder="••••••••">
                    <button type="button" @click="show = !show" :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                        class="absolute inset-y-0 right-1 flex items-center px-3 text-zinc-500 hover:text-black transition-colors cursor-pointer focus:outline-none">
                        <x-icon x-show="!show" name="lucide-eye" class="w-5 h-5" />
                        <x-icon x-cloak x-show="show" name="lucide-eye-off" class="w-5 h-5" />
                    </button>
                </div>
                @error('password')
                    <p class="mt-2 text-xs font-bold text-rose-600 flex items-center gap-1">
                        <span class="font-black">✕</span> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Remember Me --}}
            <div class="flex items-center gap-2.5">
                <input wire:model="remember" type="checkbox" id="remember"
                    class="w-4 h-4 border-2 border-black rounded-sm bg-zinc-50 checked:bg-[#FFE500] checked:border-black focus:ring-0 focus:ring-offset-0 cursor-pointer">
                <label for="remember" class="text-xs font-black uppercase text-black cursor-pointer select-none">
                    Ingat Saya
                </label>
            </div>

            {{-- Submit Button --}}
            <button type="submit"
                class="w-full py-4 px-6 bg-[#FFE500] hover:bg-yellow-400 text-black border-4 border-black font-black text-sm uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] active:translate-x-1 active:translate-y-1 active:shadow-none transition-all duration-75 rounded-lg flex items-center justify-center gap-2 cursor-pointer mt-2"
                wire:loading.attr="disabled"
                wire:target="login"
                wire:loading.class="opacity-60 cursor-not-allowed">
                <span wire:loading.remove wire:target="login">Masuk Akun</span>
                <span wire:loading.inline-flex wire:target="login" class="items-center justify-center gap-2" style="display: none;">
                    <x-icon name="lucide-loader-circle" class="animate-spin h-4 w-4 shrink-0" />
                    <span>Memproses...</span>
                </span>
            </button>
        </form>

        {{-- Divider --}}
        <div class="my-7 border-t-4 border-dashed border-black"></div>

        {{-- Register Link --}}
        <p class="text-center text-xs font-bold text-black">
            Belum punya akun?
            <a href="{{ route('register') }}" wire:navigate
                class="font-black text-black underline decoration-2 underline-offset-2 hover:bg-[#FFE500] hover:no-underline px-1 rounded transition-all">
                Daftar Sekarang
            </a>
        </p>
    </div>
</div>
