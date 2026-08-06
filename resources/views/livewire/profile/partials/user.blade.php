<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <x-stat-card label="Total Obrolan" :value="$stats['totalChats']" hint="Percakapan dengan pemilik kost" icon="lucide-message-circle" color="bg-cyan-300" />
    <x-stat-card label="Pesan Baru" :value="$stats['unreadChats']" hint="Balasan yang belum Anda baca" icon="lucide-inbox" color="bg-pink-300" />
</div>

<!-- Chat History -->
<div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] overflow-hidden">
    <div class="bg-yellow-300 border-b-4 border-black dark:border-zinc-700 px-6 py-4 flex items-center gap-3">
        <div class="w-9 h-9 bg-black rounded flex items-center justify-center shrink-0">
            <x-icon name="lucide-history" class="w-5 h-5 text-yellow-300 stroke-[2.5]" />
        </div>
        <div>
            <h2 class="text-xl font-black text-black dark:text-white uppercase tracking-tight">Riwayat Obrolan Saya</h2>
            <p class="text-xs font-bold text-black dark:text-white/70">Daftar percakapan yang telah Anda kirim ke pemilik kost.</p>
        </div>
    </div>

    <div class="divide-y divide-zinc-200">
        @forelse ($stats['chats'] as $chat)
            <a href="{{ route('user.chats', ['conversation' => $chat->id]) }}"
                class="p-5 flex flex-col sm:flex-row sm:items-center gap-3 transition-colors hover:bg-cyan-50 group">
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        @if ($chat->kost)
                            <span class="font-black text-black dark:text-white uppercase text-sm group-hover:text-yellow-600 transition-colors truncate">
                                {{ $chat->kost->name }}
                            </span>
                        @else
                            <span class="font-black text-black dark:text-white uppercase text-sm">Kost dihapus</span>
                        @endif
                        @if ($chat->latestMessage && $chat->latestMessage->sender_id !== auth()->id() && ! $chat->latestMessage->read_at)
                            <span class="bg-rose-500 text-white border-2 border-black dark:border-zinc-700 rounded-full px-1.5 py-0.5 text-[9px] font-black min-w-[20px] text-center">Baru</span>
                        @endif
                    </div>
                    <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 mt-1 line-clamp-2">{{ $chat->latestMessage?->body ?? 'Belum ada pesan.' }}</p>
                </div>
                <div class="flex items-center gap-4 shrink-0">
                    @if ($chat->kost)
                        <span class="text-[10px] font-black uppercase text-zinc-500 dark:text-zinc-400 inline-flex items-center gap-1">
                            <x-icon name="lucide-map-pin" class="w-3.5 h-3.5 stroke-[2.5]" />
                            {{ $chat->kost->district }}
                        </span>
                    @endif
                    <span class="text-[10px] font-black uppercase text-zinc-500 dark:text-zinc-400">{{ $chat->updated_at?->translatedFormat('d M Y, H:i') }}</span>
                </div>
            </a>
        @empty
            <div class="p-10 text-center space-y-3">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-zinc-100 dark:bg-zinc-800 border-3 border-black dark:border-zinc-700 flex items-center justify-center">
                    <x-icon name="lucide-inbox" class="w-8 h-8 stroke-[2]" />
                </div>
                <p class="text-sm font-black uppercase text-zinc-500 dark:text-zinc-400">Belum ada obrolan</p>
                <p class="text-xs font-bold text-zinc-500 dark:text-zinc-400">Temukan kost impian Anda lalu kirim pesan untuk mulai berkomunikasi dengan pemilik.</p>
                <a href="{{ route('home') }}"
                    class="inline-flex items-center gap-2 bg-yellow-400 hover:bg-yellow-300 text-black dark:text-white border-3 border-black dark:border-zinc-700 font-black text-xs uppercase px-5 py-2.5 shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg">
                    <x-icon name="lucide-search" class="w-4 h-4 stroke-[2.5]" />
                    <span>Cari Kost</span>
                </a>
            </div>
        @endforelse
    </div>
</div>
