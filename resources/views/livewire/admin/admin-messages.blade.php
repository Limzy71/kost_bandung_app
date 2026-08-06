<div class="min-h-screen bg-[#f8f9fa] dark:bg-zinc-950 bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] dark:bg-[linear-gradient(to_right,#27272a_1px,transparent_1px),linear-gradient(to_bottom,#27272a_1px,transparent_1px)] bg-[size:24px_24px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-8">

        <!-- Floating Auto-Dismiss Toast Notification -->
        <div
            x-data="{
                show: false,
                message: '',
                timer: null,
                trigger(msg) {
                    this.message = msg;
                    this.show = true;
                    if (this.timer) clearTimeout(this.timer);
                    this.timer = setTimeout(() => { this.show = false; }, 4000);
                }
            }"
            x-on:show-toast.window="trigger($event.detail.message)"
            x-show="show"
            x-cloak
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            class="fixed bottom-6 right-6 z-50 bg-lime-300 border-4 border-black dark:border-zinc-700 p-4 rounded-xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] text-black flex items-center gap-3 max-w-md"
        >
            <div class="w-8 h-8 rounded-full bg-black text-lime-300 flex items-center justify-center text-xs font-black shrink-0">✓</div>
            <p class="text-xs sm:text-sm font-black text-black leading-snug"><span x-text="message"></span></p>
            <button type="button" @click="show = false" class="ml-auto text-black hover:bg-black/10 p-1 rounded font-black text-xs cursor-pointer transition-colors">✕</button>
        </div>

        <!-- Page Header Neo-Brutalist -->
        <div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 p-6 sm:p-8 rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)]">
            <div class="flex flex-wrap items-center gap-2 mb-3">
                <span class="px-3 py-1 bg-yellow-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                    Control Panel Admin
                </span>
                <span class="px-3 py-1 bg-emerald-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1.5">
                    <x-icon name="lucide-clock" class="w-3.5 h-3.5 stroke-[2.5]" />
                    Dibalas maksimal 1x24 jam
                </span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-black dark:text-white tracking-tight uppercase leading-none">
                Inbox Bantuan Admin
            </h1>
            <p class="text-zinc-700 dark:text-zinc-300 text-sm sm:text-base font-bold mt-2">
                Komplain, pertanyaan, dan masukan dari pencari kost serta pemilik kost. Percakapan yang tak dibalas 24 jam akan otomatis ditutup.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">

            <!-- Left: Conversation List -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <button wire:click="setFilter('unanswered')" class="px-4 py-2 border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg {{ $filter === 'unanswered' ? 'bg-amber-400 text-black' : 'bg-white dark:bg-zinc-900 text-black dark:text-white hover:bg-amber-100 dark:hover:bg-amber-950/40' }}">
                        <span class="relative inline-flex h-2 w-2 -mt-0.5 mr-1">
                            @if ($counts['unanswered'] > 0)
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-600 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-700"></span>
                            @else
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-zinc-400"></span>
                            @endif
                        </span>
                        Belum Dibalas ({{ $counts['unanswered'] }})
                    </button>
                    <button wire:click="setFilter('open')" class="px-4 py-2 border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg {{ $filter === 'open' ? 'bg-emerald-400 text-black' : 'bg-white dark:bg-zinc-900 text-black dark:text-white hover:bg-emerald-100 dark:hover:bg-emerald-950/40' }}">
                        <x-icon name="lucide-circle" class="w-3.5 h-3.5 inline -mt-0.5 mr-1 {{ $filter === 'open' ? 'fill-black' : '' }}" />
                        Aktif ({{ $counts['open'] }})
                    </button>
                    <button wire:click="setFilter('history')" class="px-4 py-2 border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg {{ $filter === 'history' ? 'bg-zinc-400 text-black' : 'bg-white dark:bg-zinc-900 text-black dark:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800' }}">
                        <x-icon name="lucide-history" class="w-3.5 h-3.5 inline -mt-0.5 mr-1" />
                        Riwayat ({{ $counts['history'] }})
                    </button>
                </div>

                <div class="space-y-3">
                    @forelse ($conversations as $conversation)
                        <button
                            type="button"
                            wire:click="openConversation({{ $conversation->id }})"
                            class="w-full text-left p-4 border-3 border-black dark:border-zinc-700 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] transition-all cursor-pointer {{ $selected && $selected->id === $conversation->id ? 'bg-yellow-200 dark:bg-yellow-950/40 translate-x-0.5 translate-y-0.5 shadow-none ring-2 ring-black' : 'bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800' }}"
                        >
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-8 h-8 shrink-0 rounded-lg border-2 border-black dark:border-zinc-700 flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] {{ $conversation->sender_role === 'owner' ? 'bg-emerald-300' : 'bg-yellow-300' }}">
                                        <x-icon name="{{ $conversation->sender_role === 'owner' ? 'lucide-building-2' : 'lucide-user' }}" class="w-4 h-4 text-black stroke-[2.5]" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-black text-black dark:text-white truncate">{{ $conversation->user->name ?? 'Pengguna Terhapus' }}</p>
                                        <p class="text-[10px] font-black uppercase text-zinc-500 dark:text-zinc-400">
                                            {{ $conversation->sender_role === 'owner' ? 'Pemilik Kost' : 'Pencari Kost' }}
                                        </p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase shrink-0">{{ $conversation->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 mt-2">
                                <span class="px-2 py-0.5 bg-lime-300 border-2 border-black dark:border-zinc-700 text-[9px] font-black uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)]">
                                    {{ \App\Models\AdminConversation::categoryLabel($conversation->category) }}
                                </span>
                                @if ($conversation->isOpen())
                                    @if ($conversation->awaiting_reply_at)
                                        <span class="px-2 py-0.5 bg-amber-400 border-2 border-black dark:border-zinc-700 text-[9px] font-black uppercase rounded animate-pulse">Menunggu Balasan</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-emerald-300 border-2 border-black dark:border-zinc-700 text-[9px] font-black uppercase rounded">Menunggu User</span>
                                    @endif
                                @else
                                    <span class="px-2 py-0.5 bg-rose-300 border-2 border-black dark:border-zinc-700 text-[9px] font-black uppercase rounded">
                                        {{ $conversation->closed_reason === 'expired' ? 'Ditutup Otomatis' : 'Ditutup Admin' }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs font-bold text-zinc-700 dark:text-zinc-300 mt-2 line-clamp-2">
                                {{ $conversation->latestMessage?->body ?? 'Belum ada pesan.' }}
                            </p>
                        </button>
                    @empty
                        <div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 rounded-2xl p-10 text-center shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] space-y-3">
                            <div class="w-16 h-16 bg-emerald-200 border-3 border-black dark:border-zinc-700 rounded-2xl flex items-center justify-center mx-auto shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                                <x-icon name="lucide-message-circle" class="w-8 h-8 text-black stroke-[2.5]" />
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-black dark:text-white uppercase">
                                    @if ($filter === 'unanswered')
                                        Semua Pesan Telah Dibalas
                                    @elseif ($filter === 'open')
                                        Tidak Ada Percakapan Aktif
                                    @else
                                        Belum Ada Riwayat
                                    @endif
                                </h3>
                                <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 mt-1">
                                    @if ($filter === 'unanswered')
                                        Bagus! Tidak ada pesan yang menunggu balasan Anda.
                                    @elseif ($filter === 'open')
                                        Percakapan baru dari pengguna akan muncul di sini.
                                    @else
                                        Percakapan yang sudah ditutup akan muncul di sini.
                                    @endif
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
                        <div class="p-5 border-b-4 border-black dark:border-zinc-700 bg-yellow-300 flex items-center justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-10 h-10 shrink-0 rounded-xl border-2 border-black dark:border-zinc-700 flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] {{ $selected->sender_role === 'owner' ? 'bg-emerald-300' : 'bg-yellow-300' }}">
                                        <x-icon name="{{ $selected->sender_role === 'owner' ? 'lucide-building-2' : 'lucide-user' }}" class="w-5 h-5 text-black stroke-[2.5]" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-lg font-black text-black truncate">{{ $selected->user->name ?? 'Pengguna Terhapus' }}</p>
                                        <p class="text-[10px] font-black uppercase text-black/70">
                                            {{ $selected->sender_role === 'owner' ? 'Pemilik Kost' : 'Pencari Kost' }} &middot; {{ $selected->user->email ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-2 mt-3">
                                    <span class="px-2 py-0.5 bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 text-[9px] font-black uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)]">
                                        {{ \App\Models\AdminConversation::categoryLabel($selected->category) }}
                                    </span>
                                    @if ($selected->isOpen())
                                        @if ($selected->awaiting_reply_at)
                                            <span class="px-2 py-0.5 bg-amber-400 border-2 border-black dark:border-zinc-700 text-[9px] font-black uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1 animate-pulse">
                                                <x-icon name="lucide-clock" class="w-3 h-3 stroke-[2.5]" />
                                                Menunggu Balasan Admin
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 bg-emerald-400 border-2 border-black dark:border-zinc-700 text-[9px] font-black uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)]">Aktif</span>
                                        @endif
                                    @else
                                        <span class="px-2 py-0.5 bg-rose-400 border-2 border-black dark:border-zinc-700 text-[9px] font-black uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)]">
                                            {{ $selected->closed_reason === 'expired' ? 'Ditutup Otomatis (1x24 jam)' : 'Ditutup Admin' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div class="p-5 space-y-4 max-h-[520px] overflow-y-auto bg-[#f8f9fa] dark:bg-zinc-950">
                            @forelse ($selected->messages as $message)
                                @if ($message->sender_type === 'admin')
                                    <div class="flex items-start justify-end gap-3">
                                        <div class="max-w-[80%] text-right">
                                            <div class="bg-lime-300 border-2 border-black dark:border-zinc-700 p-3 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                                                <p class="text-sm font-bold text-black whitespace-pre-wrap">{{ $message->body }}</p>
                                            </div>
                                            <p class="text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase mt-1.5 mr-1">
                                                Anda (Admin) &middot; {{ $message->created_at->format('d M Y, H:i') }}
                                            </p>
                                        </div>
                                        <div class="w-9 h-9 shrink-0 bg-lime-400 border-2 border-black dark:border-zinc-700 rounded-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                                            <x-icon name="lucide-shield" class="w-4 h-4 text-black stroke-[2.5]" />
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 shrink-0 border-2 border-black dark:border-zinc-700 rounded-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] {{ $selected->sender_role === 'owner' ? 'bg-emerald-300' : 'bg-yellow-300' }}">
                                            <x-icon name="{{ $selected->sender_role === 'owner' ? 'lucide-building-2' : 'lucide-user' }}" class="w-4 h-4 text-black stroke-[2.5]" />
                                        </div>
                                        <div class="max-w-[80%]">
                                            <div class="bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 p-3 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                                                <p class="text-sm font-bold text-black dark:text-white whitespace-pre-wrap">{{ $message->body }}</p>
                                            </div>
                                            <p class="text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase mt-1.5 ml-1">
                                                {{ $selected->user->name ?? 'Pengguna' }} &middot; {{ $message->created_at->format('d M Y, H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <p class="text-center text-xs font-black text-zinc-500 dark:text-zinc-400 uppercase py-8">Belum ada pesan.</p>
                            @endforelse
                        </div>

                        <!-- Reply / Actions -->
                        <div class="p-5 border-t-4 border-black dark:border-zinc-700 bg-white dark:bg-zinc-900">
                            @if ($selected->isOpen())
                                <form wire:submit.prevent="replyConversation" class="space-y-3">
                                    <label class="block text-[10px] font-black uppercase text-black dark:text-white mb-1">Balasan Anda</label>
                                    <textarea wire:model="replyBody" rows="3"
                                        class="w-full bg-zinc-100 dark:bg-zinc-800 border-3 border-black dark:border-zinc-700 rounded-xl px-4 py-3 text-sm font-bold text-black dark:text-white focus:outline-none focus:ring-0 focus:bg-white dark:focus:bg-zinc-900 focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] transition-all resize-none"
                                        placeholder="Tulis balasan untuk {{ $selected->user->name ?? 'pengguna' }}..."></textarea>
                                    @error('replyBody')
                                        <p class="text-xs font-black text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                    @enderror
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <button
                                            type="button"
                                            wire:click="closeConversation({{ $selected->id }})"
                                            wire:confirm="Tutup percakapan ini? Pengguna tidak dapat membalas lagi."
                                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-400 hover:bg-rose-300 text-black border-3 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer">
                                            <x-icon name="lucide-circle-slash" class="w-4 h-4 stroke-[2.5]" />
                                            Tutup Percakapan
                                        </button>
                                        <button type="submit"
                                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-400 hover:bg-emerald-300 text-black border-3 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer">
                                            <x-icon name="lucide-send" class="w-4 h-4 stroke-[2.5]" />
                                            Kirim Balasan
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                                    <div class="flex items-center gap-2 text-sm font-black text-black dark:text-white">
                                        <x-icon name="lucide-circle-slash" class="w-4 h-4 stroke-[2.5]" />
                                        Percakapan sudah ditutup. Pengguna tidak dapat membalas lagi.
                                    </div>
                                    <button
                                        type="button"
                                        wire:click="deleteConversation({{ $selected->id }})"
                                        wire:confirm="Hapus percakapan ini? Riwayat akan tersimpan 30 hari sebelum dibersihkan otomatis."
                                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-zinc-800 hover:bg-black text-white border-3 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer">
                                        <x-icon name="lucide-trash-2" class="w-4 h-4 stroke-[2.5]" />
                                        Hapus Percakapan
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
                            <p class="text-sm font-bold text-zinc-600 dark:text-zinc-400 mt-1">Klik salah satu percakapan di sebelah kiri untuk membaca pesan dan membalasnya.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
