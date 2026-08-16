<div class="min-h-screen bg-[#f8f9fa] dark:bg-zinc-950 bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] dark:bg-[linear-gradient(to_right,#27272a_1px,transparent_1px),linear-gradient(to_bottom,#27272a_1px,transparent_1px)] bg-[size:24px_24px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-8">

        <!-- Page Header Neo-Brutalist -->
        <div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 p-6 sm:p-8 rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 bg-yellow-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                        Hubungi Admin
                    </span>
                    <span class="px-3 py-1 bg-emerald-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1.5">
                        <x-icon name="lucide-clock" class="w-3.5 h-3.5 stroke-[2.5]" />
                        Dibalas maksimal 1x24 jam
                    </span>
                </div>
                <div>
                    <h1 class="text-3xl sm:text-4xl font-black text-black dark:text-white tracking-tight uppercase leading-none">
                        Bantuan &amp; Layanan
                    </h1>
                    <p class="text-zinc-700 dark:text-zinc-300 text-sm sm:text-base font-bold mt-2">
                        Kirim komplain, pertanyaan, atau masukan. Admin akan membalas Anda dalam waktu maksimal 1x24 jam.
                    </p>
                </div>
            </div>
            <button wire:click="openCompose"
                class="shrink-0 inline-flex items-center gap-2 px-5 py-3 bg-yellow-300 hover:bg-yellow-200 text-black border-3 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer">
                <x-icon name="lucide-message-circle-plus" class="w-4 h-4 stroke-[2.5]" />
                Pesan Baru
            </button>
        </div>

        @if (session()->has('success'))
            <div class="bg-lime-300 border-4 border-black dark:border-zinc-700 rounded-2xl p-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] text-sm font-black text-black">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">

            <!-- Left: Conversation List -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <button wire:click="$set('tab', 'active')" class="px-4 py-2 border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg inline-flex items-center gap-1.5 {{ $tab === 'active' ? 'bg-emerald-400 text-black' : 'bg-white dark:bg-zinc-900 text-black dark:text-white hover:bg-emerald-100 dark:hover:bg-zinc-800' }}">
                        <x-icon name="lucide-message-circle" class="w-4 h-4 stroke-[2.5]" />
                        <span>Percakapan Aktif</span>
                    </button>
                    <button wire:click="$set('tab', 'history')" class="px-4 py-2 border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg inline-flex items-center gap-1.5 {{ $tab === 'history' ? 'bg-zinc-400 text-black' : 'bg-white dark:bg-zinc-900 text-black dark:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800' }}">
                        <x-icon name="lucide-history" class="w-4 h-4 stroke-[2.5]" />
                        <span>Riwayat</span>
                    </button>
                </div>

                <div class="space-y-3">
                    @forelse ($conversations as $conversation)
                        <button
                            type="button"
                            wire:click="openConversation({{ $conversation->id }})"
                            class="w-full text-left p-4 border-3 border-black dark:border-zinc-700 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] transition-all cursor-pointer {{ $selected && $selected->id === $conversation->id ? 'bg-yellow-300 dark:bg-zinc-800 text-black dark:text-white translate-x-0.5 translate-y-0.5 shadow-none ring-2 ring-black dark:ring-yellow-400' : 'bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800' }}"
                        >
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="px-2 py-0.5 bg-lime-300 border-2 border-black dark:border-zinc-700 text-[9px] font-black text-black dark:text-black uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)] shrink-0">
                                        {{ \App\Models\AdminConversation::categoryLabel($conversation->category) }}
                                    </span>
                                    @if ($conversation->isOpen())
                                        <span class="px-2 py-0.5 bg-emerald-300 border-2 border-black dark:border-zinc-700 text-[9px] font-black text-black dark:text-black uppercase rounded shrink-0">Aktif</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-rose-300 border-2 border-black dark:border-zinc-700 text-[9px] font-black text-black dark:text-black uppercase rounded shrink-0">Ditutup</span>
                                    @endif
                                </div>
                                <span class="text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase shrink-0">{{ $conversation->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm font-black text-black dark:text-white mt-2 line-clamp-2">
                                {{ $conversation->latestMessage?->body ?? 'Belum ada pesan.' }}
                            </p>
                        </button>
                    @empty
                        <div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 rounded-2xl p-10 text-center shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] space-y-3">
                            <div class="w-16 h-16 bg-emerald-200 dark:bg-emerald-950/50 border-3 border-black dark:border-zinc-700 rounded-2xl flex items-center justify-center mx-auto text-black dark:text-white shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                                <x-icon name="lucide-message-circle" class="w-8 h-8 stroke-[2.5]" />
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-black dark:text-white uppercase">
                                    {{ $tab === 'active' ? 'Belum Ada Percakapan Aktif' : 'Belum Ada Riwayat' }}
                                </h3>
                                <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 mt-1">
                                    {{ $tab === 'active' ? 'Mulai percakapan baru dengan menekan tombol "Pesan Baru".' : 'Percakapan yang sudah ditutup akan muncul di sini.' }}
                                </p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div>
                    {{ $conversations->links() }}
                </div>
            </div>

            <!-- Right: Thread -->
            <div class="lg:col-span-3">
                @if ($selected)
                    <div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] overflow-hidden flex flex-col">
                        <!-- Thread Header -->
                        <div class="p-5 border-b-4 border-black dark:border-zinc-700 bg-yellow-300 dark:bg-zinc-800 flex items-center justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2 py-0.5 bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 text-[9px] font-black text-black dark:text-white uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)]">
                                        {{ \App\Models\AdminConversation::categoryLabel($selected->category) }}
                                    </span>
                                    @if ($selected->isOpen())
                                        <span class="px-2 py-0.5 bg-emerald-400 border-2 border-black dark:border-zinc-700 text-[9px] font-black text-black dark:text-black uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)]">Aktif</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-rose-400 border-2 border-black dark:border-zinc-700 text-[9px] font-black text-black dark:text-black uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)]">
                                            {{ $selected->closed_reason === 'expired' ? 'Ditutup Otomatis (1x24 jam)' : 'Ditutup Admin' }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[11px] font-black text-black/70 dark:text-zinc-400 uppercase mt-1.5">
                                    Dibuka {{ $selected->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div class="p-5 space-y-4 max-h-[520px] overflow-y-auto bg-[#f8f9fa] dark:bg-zinc-950">
                            @forelse ($selected->messages as $message)
                                @if ($message->sender_type === 'admin')
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 shrink-0 bg-lime-400 border-2 border-black dark:border-zinc-700 rounded-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                                            <x-icon name="lucide-shield" class="w-4 h-4 text-black stroke-[2.5]" />
                                        </div>
                                        <div class="max-w-[80%] flex flex-col items-start">
                                            <div class="inline-block bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 p-3 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                                                <p class="text-sm font-bold text-black dark:text-white whitespace-pre-wrap break-words">{{ $message->body }}</p>
                                            </div>
                                            <p class="text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase mt-1.5 ml-1">
                                                Admin &middot; {{ $message->created_at->format('d M Y, H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-start justify-end gap-3">
                                        <div class="max-w-[80%] flex flex-col items-end">
                                            <div class="inline-block text-left bg-emerald-300 border-2 border-black dark:border-zinc-700 p-3 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                                                <p class="text-sm font-bold text-black whitespace-pre-wrap break-words">{{ $message->body }}</p>
                                            </div>
                                            <p class="text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase mt-1.5 mr-1 text-right">
                                                Anda &middot; {{ $message->created_at->format('d M Y, H:i') }}
                                            </p>
                                        </div>
                                        <div class="w-9 h-9 shrink-0 bg-yellow-300 border-2 border-black dark:border-zinc-700 rounded-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                                            <x-icon name="lucide-user" class="w-4 h-4 text-black stroke-[2.5]" />
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <p class="text-center text-xs font-black text-zinc-500 dark:text-zinc-400 uppercase py-8">Belum ada pesan.</p>
                            @endforelse
                        </div>

                        <!-- Follow-up / Closed Notice -->
                        <div class="p-5 border-t-4 border-black dark:border-zinc-700 bg-white dark:bg-zinc-900">
                            @if ($selected->isOpen())
                                <form wire:submit.prevent="sendFollowUp" class="space-y-3">
                                    <label class="block text-[10px] font-black uppercase text-black dark:text-white mb-1">Balas / Lanjutkan Percakapan</label>
                                    <textarea wire:model="followUpBody" rows="3"
                                        class="w-full bg-zinc-100 dark:bg-zinc-800 border-3 border-black dark:border-zinc-700 rounded-xl px-4 py-3 text-sm font-bold text-black dark:text-white focus:outline-none focus:ring-0 focus:bg-white dark:focus:bg-zinc-900 focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] transition-all resize-none"
                                        placeholder="Tulis pesan lanjutan Anda untuk Admin..."></textarea>
                                    @error('followUpBody')
                                        <p class="text-xs font-black text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                    @enderror
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase">Balasan maksimal 1x24 jam</p>
                                        <button type="submit"
                                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-400 hover:bg-emerald-300 text-black border-3 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer">
                                            <x-icon name="lucide-send" class="w-4 h-4 stroke-[2.5]" />
                                            Kirim
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="text-center space-y-3 py-2">
                                    <p class="text-sm font-black text-black dark:text-white">
                                        Percakapan ini sudah ditutup.
                                    </p>
                                    <button wire:click="openCompose"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-300 hover:bg-emerald-200 text-black border-3 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer">
                                        <x-icon name="lucide-message-circle-plus" class="w-4 h-4 stroke-[2.5]" />
                                        Buka Percakapan Baru
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 rounded-2xl p-12 text-center shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] space-y-4">
                        <div class="w-20 h-20 bg-zinc-200 dark:bg-zinc-800 border-3 border-black dark:border-zinc-700 rounded-2xl flex items-center justify-center mx-auto text-black dark:text-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] -rotate-3">
                            <x-icon name="lucide-message-square-text" class="w-10 h-10 stroke-[2.5]" />
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-black dark:text-white uppercase">Pilih Percakapan</h3>
                            <p class="text-sm font-bold text-zinc-600 dark:text-zinc-400 mt-1">Klik salah satu percakapan di sebelah kiri untuk melihat pesan dan balasan.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Compose Modal -->
    @if ($showCompose)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60" wire:click="closeCompose"></div>
            <div class="relative bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] w-full max-w-lg p-6 space-y-5">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-xl font-black text-black dark:text-white uppercase">Pesan Baru ke Admin</h2>
                    <button wire:click="closeCompose" class="p-1.5 bg-rose-500 hover:bg-rose-400 border-2 border-black dark:border-zinc-700 rounded text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer">
                        <x-icon name="lucide-x" class="w-4 h-4 stroke-[2.5]" />
                    </button>
                </div>

                <div class="bg-yellow-200 border-2 border-black dark:border-zinc-700 p-3 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] text-xs font-black text-black">
                    <p class="inline-flex items-center gap-1.5">
                        <x-icon name="lucide-clock" class="w-3.5 h-3.5 stroke-[2.5]" />
                        Pesan Anda akan dibalas Admin maksimal 1x24 jam.
                    </p>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-black dark:text-white mb-1">Kategori</label>
                    <select wire:model="category"
                        class="w-full bg-zinc-100 dark:bg-zinc-800 border-3 border-black dark:border-zinc-700 rounded-xl px-4 py-3 text-sm font-black uppercase text-black dark:text-white focus:outline-none focus:ring-0 focus:bg-white dark:focus:bg-zinc-900 transition-all cursor-pointer">
                        <option value="" class="font-black uppercase">PILIH KATEGORI...</option>
                        @foreach (\App\Models\AdminConversation::CATEGORIES as $cat)
                            <option value="{{ $cat }}" class="font-black uppercase">{{ mb_strtoupper(\App\Models\AdminConversation::categoryLabel($cat)) }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="text-xs font-black text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-black dark:text-white mb-1">Isi Pesan</label>
                    <textarea wire:model="newBody" rows="5"
                        class="w-full bg-zinc-100 dark:bg-zinc-800 border-3 border-black dark:border-zinc-700 rounded-xl px-4 py-3 text-sm font-bold text-black dark:text-white focus:outline-none focus:ring-0 focus:bg-white dark:focus:bg-zinc-900 transition-all resize-none"
                        placeholder="Tulis komplain, pertanyaan, atau masukan Anda..."></textarea>
                    @error('newBody')
                        <p class="text-xs font-black text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap items-center justify-end gap-3 pt-1">
                    <button wire:click="closeCompose" class="px-4 py-2.5 bg-white dark:bg-zinc-900 hover:bg-zinc-100 dark:hover:bg-zinc-800 dark:bg-zinc-800 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer">
                        Batal
                    </button>
                    <button wire:click="sendNewConversation" class="px-5 py-2.5 bg-emerald-400 hover:bg-emerald-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg inline-flex items-center gap-2 cursor-pointer">
                        <x-icon name="lucide-send" class="w-4 h-4 stroke-[2.5]" />
                        Kirim ke Admin
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
