<div class="min-h-screen bg-[#f8f9fa] dark:bg-zinc-950 bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] dark:bg-[linear-gradient(to_right,#27272a_1px,transparent_1px),linear-gradient(to_bottom,#27272a_1px,transparent_1px)] bg-[size:24px_24px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-8">

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
                    <button wire:click="setFilter('unanswered')" class="px-4 py-2 border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg inline-flex items-center gap-1.5 {{ $filter === 'unanswered' ? 'bg-amber-400 text-black' : 'bg-white dark:bg-zinc-900 text-black dark:text-white hover:bg-amber-100 dark:hover:bg-zinc-800' }}">
                        <x-icon name="lucide-clock" class="w-4 h-4 stroke-[2.5]" />
                        <span>Belum Dibalas ({{ $counts['unanswered'] }})</span>
                    </button>
                    <button wire:click="setFilter('open')" class="px-4 py-2 border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg inline-flex items-center gap-1.5 {{ $filter === 'open' ? 'bg-emerald-400 text-black' : 'bg-white dark:bg-zinc-900 text-black dark:text-white hover:bg-emerald-100 dark:hover:bg-zinc-800' }}">
                        <x-icon name="lucide-message-circle" class="w-4 h-4 stroke-[2.5]" />
                        <span>Aktif ({{ $counts['open'] }})</span>
                    </button>
                    <button wire:click="setFilter('history')" class="px-4 py-2 border-2 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg inline-flex items-center gap-1.5 {{ $filter === 'history' ? 'bg-zinc-400 text-black' : 'bg-white dark:bg-zinc-900 text-black dark:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800' }}">
                        <x-icon name="lucide-history" class="w-4 h-4 stroke-[2.5]" />
                        <span>Riwayat ({{ $counts['history'] }})</span>
                    </button>
                </div>

                <div class="space-y-3">
                    @forelse ($conversations as $conversation)
                        <button
                            type="button"
                            wire:click="openConversation({{ $conversation->id }})"
                            class="w-full text-left p-4 border-3 border-black dark:border-zinc-700 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] transition-all cursor-pointer {{ $selected && $selected->id === $conversation->id ? 'bg-yellow-300 dark:bg-yellow-400 text-black [&_*]:!text-black translate-x-0.5 translate-y-0.5 shadow-none ring-2 ring-black' : 'bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800' }}"
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
                                <span class="px-2 py-0.5 bg-lime-300 border-2 border-black dark:border-zinc-700 text-[9px] font-black text-black dark:text-black uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)]">
                                    {{ \App\Models\AdminConversation::categoryLabel($conversation->category) }}
                                </span>
                                @if ($conversation->isOpen())
                                    @if ($conversation->awaiting_reply_at)
                                        <span class="px-2 py-0.5 bg-amber-400 border-2 border-black dark:border-zinc-700 text-[9px] font-black text-black dark:text-black uppercase rounded animate-pulse">Menunggu Balasan</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-emerald-300 border-2 border-black dark:border-zinc-700 text-[9px] font-black text-black dark:text-black uppercase rounded">Menunggu User</span>
                                    @endif
                                @else
                                    <span class="px-2 py-0.5 bg-rose-300 border-2 border-black dark:border-zinc-700 text-[9px] font-black text-black dark:text-black uppercase rounded">
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
                                    <span class="px-2 py-0.5 bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 text-[9px] font-black text-black dark:text-white uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)]">
                                        {{ \App\Models\AdminConversation::categoryLabel($selected->category) }}
                                    </span>
                                    @if ($selected->isOpen())
                                        @if ($selected->awaiting_reply_at)
                                            <span class="px-2 py-0.5 bg-amber-400 border-2 border-black dark:border-zinc-700 text-[9px] font-black text-black uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1 animate-pulse">
                                                <x-icon name="lucide-clock" class="w-3 h-3 stroke-[2.5]" />
                                                Menunggu Balasan Admin
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 bg-emerald-400 border-2 border-black dark:border-zinc-700 text-[9px] font-black text-black uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)]">Aktif</span>
                                        @endif
                                    @else
                                        <span class="px-2 py-0.5 bg-rose-400 border-2 border-black dark:border-zinc-700 text-[9px] font-black text-black uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)]">
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
                                            @click="window.dispatchEvent(new CustomEvent('open-confirm', { detail: { title: 'Tutup Percakapan', message: 'Tutup percakapan ini? Pengguna tidak dapat membalas lagi.', confirmLabel: 'Ya, Tutup', danger: true, action: () => $wire.closeConversation({{ $selected->id }}) } }))"
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
                                        @click="window.dispatchEvent(new CustomEvent('open-confirm', { detail: { title: 'Hapus Percakapan', message: 'Hapus percakapan ini? Riwayat akan tersimpan 30 hari sebelum dibersihkan otomatis.', confirmLabel: 'Ya, Hapus', danger: true, action: () => $wire.deleteConversation({{ $selected->id }}) } }))"
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
