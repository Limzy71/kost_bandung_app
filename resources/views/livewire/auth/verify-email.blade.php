<div class="w-full">
    {{-- ===== Neo-Brutalist Verify Email Card ===== --}}
    <div class="bg-white border-4 border-black p-8 md:p-10 shadow-[12px_12px_0px_0px_rgba(0,0,0,1)] rounded-lg w-full dark:bg-zinc-900 dark:border-zinc-700 dark:shadow-[12px_12px_0px_0px_rgba(255,255,255,0.25)]">

        {{-- Card Header --}}
        <div class="mb-8 text-center sm:text-left">
            <div class="inline-flex items-center gap-2 bg-[#FFE500] border-2 border-black px-3 py-1 rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mb-4 dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                <x-icon name="lucide-mail" class="w-4 h-4 text-black stroke-[3]" />
                <span class="text-[10px] font-black uppercase tracking-widest text-black">Verifikasi Alamat Email</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-black uppercase tracking-tight leading-tight dark:text-white">
                Cek Kotak Masuk Anda!
            </h1>
            <p class="mt-2 text-sm font-bold text-zinc-600 dark:text-zinc-400">
                Terima kasih telah mendaftar. Silakan klik tautan verifikasi yang baru saja kami kirimkan ke email Anda.
            </p>
        </div>

        {{-- Success Banner --}}
        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 flex items-center gap-3 bg-lime-300 border-4 border-black p-4 rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:border-zinc-700 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]">
                <x-icon name="lucide-check-circle" class="w-5 h-5 text-black shrink-0 stroke-[3]" />
                <span class="text-xs font-black text-black uppercase">Tautan verifikasi baru telah berhasil dikirimkan ke alamat email Anda.</span>
            </div>
        @endif

        {{-- Divider --}}
        <div class="border-t-4 border-black mb-8 dark:border-zinc-700"></div>

        <div class="space-y-4">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                    class="w-full py-4 px-6 bg-cyan-300 hover:bg-cyan-400 active:translate-x-1 active:translate-y-1 active:shadow-none text-black border-3 border-black font-black text-sm uppercase tracking-wider rounded-lg shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all flex items-center justify-center gap-2 cursor-pointer dark:border-zinc-700 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)]">
                    <x-icon name="lucide-send" class="w-4 h-4 text-black stroke-[3]" />
                    <span>Kirim Ulang Email Verifikasi</span>
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full py-3 px-6 bg-white hover:bg-zinc-100 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none text-zinc-800 border-2 border-black font-black text-xs uppercase tracking-wider rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transition-all flex items-center justify-center gap-2 cursor-pointer dark:bg-zinc-900 dark:hover:bg-zinc-800 dark:text-zinc-200 dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                    <x-icon name="lucide-log-out" class="w-4 h-4 text-black stroke-[2.5] dark:text-white" />
                    <span>Keluar Akun</span>
                </button>
            </form>
        </div>
    </div>
</div>
