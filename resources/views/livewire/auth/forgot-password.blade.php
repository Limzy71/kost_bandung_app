<x-layouts::auth title="Lupa Kata Sandi">
    <div class="w-full">
        {{-- ===== Neo-Brutalist Card ===== --}}
        <div class="bg-white border-4 border-black p-8 md:p-10 shadow-[12px_12px_0px_0px_rgba(0,0,0,1)] rounded-lg w-full dark:bg-zinc-900 dark:border-zinc-700 dark:shadow-[12px_12px_0px_0px_rgba(255,255,255,0.25)]">

            {{-- Card Header --}}
            <div class="mb-8">
                <div class="inline-flex items-center gap-2 bg-[#FFE500] border-2 border-black px-3 py-1 rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mb-4 dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                    <span class="text-[10px] font-black uppercase tracking-widest text-black">Pemulihan Akun</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black text-black uppercase tracking-tight leading-tight dark:text-white">
                    Lupa Kata Sandi?
                </h1>
                <p class="mt-2 text-sm font-bold text-zinc-600 dark:text-zinc-400">
                    Lupa kata sandi Anda? Tidak masalah. Masukkan alamat email Anda, dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
                </p>
            </div>

            {{-- Divider --}}
            <div class="border-t-4 border-black mb-8 dark:border-zinc-700"></div>

            {{-- Session Status --}}
            @if (session('status'))
                <div class="mb-6 flex items-center gap-3 bg-emerald-400 border-4 border-black p-4 rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:border-zinc-700 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]">
                    <x-icon name="lucide-check-circle" class="w-5 h-5 text-black shrink-0 stroke-[3]" />
                    <span class="text-xs font-black uppercase text-black">{{ session('status') }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs font-black uppercase tracking-wider text-black mb-2 dark:text-white">
                        Alamat Email
                    </label>
                    <input name="email" type="email" id="email" autocomplete="email" required autofocus
                        class="w-full px-4 py-3 text-sm bg-zinc-50 border-3 border-black rounded-lg text-black placeholder-zinc-400 focus:outline-none focus:ring-0 focus:bg-[#FFE500]/10 focus:border-black transition-all shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:bg-zinc-900 dark:border-zinc-700 dark:text-white dark:placeholder-zinc-500 dark:focus:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] dark:focus:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]"
                        placeholder="nama@email.com"
                        value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-2 text-xs font-bold text-rose-600 flex items-center gap-1 dark:text-rose-400">
                            <span class="font-black">✕</span> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <button type="submit"
                    class="w-full py-4 px-6 bg-[#FFE500] hover:bg-yellow-400 text-black border-4 border-black font-black text-sm uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] active:translate-x-1 active:translate-y-1 active:shadow-none transition-all duration-75 rounded-lg flex items-center justify-center gap-2 cursor-pointer mt-2 dark:border-zinc-600 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)]">
                    Kirim Tautan Reset
                </button>
            </form>

            {{-- Divider --}}
            <div class="my-7 border-t-4 border-dashed border-black dark:border-zinc-700"></div>

            {{-- Login Link --}}
            <p class="text-center text-xs font-bold text-black dark:text-white">
                Sudah ingat kata sandi Anda?
                <a href="{{ route('login') }}" wire:navigate
                    class="font-black text-black underline decoration-2 underline-offset-2 hover:bg-[#FFE500] hover:no-underline px-1 rounded transition-all dark:text-white">
                    Kembali ke Masuk
                </a>
            </p>
        </div>
    </div>
</x-layouts::auth>
