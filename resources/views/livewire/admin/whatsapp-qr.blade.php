<x-layouts::auth.simple :title="'Pairing WhatsApp Gateway — Admin KostBandung'">
    <div class="w-full">
        {{-- ===== Neo-Brutalist WhatsApp Gateway Pairing Hub ===== --}}
        <div class="bg-white border-4 border-black p-6 sm:p-10 shadow-[10px_10px_0px_0px_rgba(0,0,0,1)] rounded-2xl w-full dark:bg-zinc-900 dark:border-zinc-700 dark:shadow-[10px_10px_0px_0px_rgba(255,255,255,0.25)] space-y-6">

            {{-- Header Badge & Title --}}
            <div>
                <div class="inline-flex items-center gap-2 bg-[#FFE500] border-2 border-black px-3.5 py-1.5 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mb-3 dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                    <x-icon name="lucide-shield-alert" class="w-4 h-4 text-black stroke-[3]" />
                    <span class="text-xs font-black uppercase tracking-widest text-black">Khusus Administrator</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-black uppercase tracking-tight leading-tight dark:text-white">
                    Scan WhatsApp Gateway
                </h1>
                <p class="mt-2 text-xs sm:text-sm font-bold text-zinc-600 dark:text-zinc-400 leading-relaxed">
                    Tautkan nomor WhatsApp resmi KostBandung agar sistem dapat mengirimkan kode OTP secara otomatis dan gratis ke nomor pengguna.
                </p>
            </div>

            {{-- Instruction Steps Banner --}}
            <div class="p-4 bg-zinc-100 dark:bg-zinc-800 border-2 border-black dark:border-zinc-700 rounded-xl space-y-2 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                <div class="text-xs font-black uppercase text-black dark:text-white flex items-center gap-1.5">
                    <x-icon name="lucide-smartphone" class="w-4 h-4 text-black dark:text-white stroke-[2.5]" />
                    <span>Petunjuk Scan dari HP:</span>
                </div>
                <ol class="text-xs font-bold text-zinc-700 dark:text-zinc-300 list-decimal list-inside space-y-1">
                    <li>Buka aplikasi <span class="text-black dark:text-white font-extrabold">WhatsApp</span> di HP pengirim.</li>
                    <li>Ketuk menu <span class="text-black dark:text-white font-extrabold">Titik Tiga</span> / <span class="text-black dark:text-white font-extrabold">Pengaturan</span> &rarr; pilih <span class="text-black dark:text-white font-extrabold">Perangkat Tertaut</span>.</li>
                    <li>Ketuk tombol <span class="text-black dark:text-white font-extrabold">Tautkan Perangkat</span> dan arahkan kamera ke QR Code di bawah.</li>
                </ol>
            </div>

            {{-- QR Code Display Area --}}
            <div id="unconnected-section" class="text-center space-y-4 pt-2">
                <div class="inline-block p-4 bg-white border-3 border-black dark:border-zinc-700 rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.2)]">
                    <div id="qr-loading-spinner" class="w-60 h-60 flex flex-col items-center justify-center gap-3 text-zinc-500 font-bold text-xs uppercase">
                        <x-icon name="lucide-refresh-cw" class="w-8 h-8 stroke-[2.5] animate-spin text-black" />
                        <span>Memuat QR Code...</span>
                    </div>
                    <img id="qr-image" src="" alt="WhatsApp QR Code" class="w-60 h-60 hidden mx-auto block rounded-lg" />
                </div>

                {{-- Visual Animated Countdown Badge --}}
                <div>
                    <div class="inline-flex items-center gap-2 bg-[#FFE500] border-2 border-black px-4 py-2 rounded-xl text-xs font-black uppercase text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        <x-icon name="lucide-timer" class="w-4 h-4 stroke-[2.5]" />
                        <span>Refresh QR dalam:</span>
                        <span id="countdown-text" class="font-mono text-sm px-1.5 py-0.5 bg-black text-white rounded">20s</span>
                    </div>
                </div>
            </div>

            {{-- Connected Success Card --}}
            <div id="connected-section" class="hidden p-6 bg-lime-200 dark:bg-lime-950/60 border-3 border-black dark:border-lime-700 rounded-2xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-center space-y-3">
                <div class="w-14 h-14 mx-auto bg-lime-400 border-2 border-black rounded-2xl flex items-center justify-center text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                    <x-icon name="lucide-check-circle" class="w-8 h-8 stroke-[3]" />
                </div>
                <h2 class="text-xl font-black text-black dark:text-white uppercase flex items-center justify-center gap-2">
                    <span>WhatsApp Gateway Terhubung!</span>
                    <x-icon name="lucide-check-circle-2" class="w-6 h-6 text-emerald-600 dark:text-emerald-400 stroke-[2.5]" />
                </h2>
                <p class="text-xs sm:text-sm font-bold text-zinc-800 dark:text-zinc-200 max-w-md mx-auto">
                    Nomor WhatsApp pengirim telah berhasil ditautkan dan siap mengirimkan pesan OTP otomatis ke seluruh pengguna.
                </p>
                <div class="pt-2">
                    <a href="{{ route('admin.moderation') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-black text-white hover:bg-zinc-800 border-2 border-black font-black text-xs uppercase rounded-xl shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] cursor-pointer">
                        <x-icon name="lucide-arrow-left" class="w-4 h-4 stroke-[2.5]" />
                        <span>Kembali ke Panel Admin</span>
                    </a>
                </div>
            </div>

            {{-- Footer Action --}}
            <div class="text-center pt-3 border-t-2 border-zinc-200 dark:border-zinc-800">
                <a href="{{ route('admin.moderation') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-black text-zinc-600 dark:text-zinc-400 hover:text-black dark:hover:text-white uppercase cursor-pointer transition-colors">
                    <x-icon name="lucide-arrow-left" class="w-3.5 h-3.5 stroke-[2.5]" />
                    <span>Kembali ke Dashboard Moderasi</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Interactive Live Poller Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let countdown = 20;
            const countdownEl = document.getElementById('countdown-text');
            const qrImage = document.getElementById('qr-image');
            const qrSpinner = document.getElementById('qr-loading-spinner');
            const unconnSection = document.getElementById('unconnected-section');
            const connSection = document.getElementById('connected-section');

            async function fetchStatus() {
                try {
                    const res = await fetch('{{ route("admin.whatsapp.qr.data") }}');
                    const data = await res.json();

                    if (data.connected || data.status === 'connected') {
                        unconnSection.classList.add('hidden');
                        connSection.classList.remove('hidden');
                        return true;
                    }

                    // Only update QR image when explicitly requested
                    if (data.qrBase64 && updateQrImage) {
                        qrImage.src = data.qrBase64;
                        qrImage.classList.remove('hidden');
                        qrSpinner.classList.add('hidden');
                    }
                } catch (err) {
                    console.error('Fetch QR Error:', err);
                }
                return false;
            }

            // Track whether to update the QR image in this call
            let updateQrImage = true;

            // Initial fetch — always update QR image on first load
            fetchStatus();
            updateQrImage = false;

            // 1s countdown timer
            setInterval(async () => {
                countdown--;
                if (countdownEl) {
                    countdownEl.textContent = countdown + 's';
                }

                // Every 3s: ONLY check connection status (do NOT refresh QR image)
                if (countdown % 3 === 0 && countdown > 0) {
                    updateQrImage = false;
                    const connected = await fetchStatus();
                    if (connected) return;
                }

                // Every 20s: refresh QR image + check status
                if (countdown <= 0) {
                    countdown = 20;
                    updateQrImage = true;
                    await fetchStatus();
                    updateQrImage = false;
                }
            }, 1000);
        });
    </script>
</x-layouts::auth.simple>
