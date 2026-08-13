<div class="min-h-screen bg-[#f8f9fa] dark:bg-zinc-950 bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] dark:bg-[linear-gradient(to_right,#27272a_1px,transparent_1px),linear-gradient(to_bottom,#27272a_1px,transparent_1px)] bg-[size:24px_24px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-8">

        <!-- Page Header Neo-Brutalist -->
        <div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 p-6 sm:p-8 rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 bg-yellow-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                        Obrolan Kost
                    </span>
                    <span class="px-3 py-1 bg-emerald-300 text-black border-2 border-black dark:border-zinc-700 font-black text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1.5">
                        <x-icon name="lucide-zap" class="w-3.5 h-3.5 stroke-[2.5]" />
                        Real-time
                    </span>
                </div>
                <div>
                    <h1 class="text-3xl sm:text-4xl font-black text-black dark:text-white tracking-tight uppercase leading-none">
                        Inbox Chat Penyewa
                    </h1>
                    <p class="text-zinc-700 dark:text-zinc-300 text-sm sm:text-base font-bold mt-2">
                        Buka percakapan untuk melihat pesan pencari kost dan membalasnya secara langsung.
                    </p>
                </div>
            </div>
            <a href="{{ route('dashboard') }}"
                class="shrink-0 inline-flex items-center gap-2 px-5 py-3 bg-cyan-300 hover:bg-cyan-200 text-black border-3 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer">
                <x-icon name="lucide-layout-grid" class="w-4 h-4 stroke-[2.5]" />
                Dashboard Pemilik
            </a>
        </div>

        @if (session()->has('success'))
            <div class="bg-lime-300 border-4 border-black dark:border-zinc-700 rounded-2xl p-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] text-sm font-black text-black">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">

            <!-- Left: Conversation List -->
            <div class="lg:col-span-2 space-y-4">
                <div class="space-y-3">
                    @forelse ($conversations as $conversation)
                        <button
                            type="button"
                            wire:click="openConversation({{ $conversation->id }})"
                            class="w-full text-left p-4 border-3 border-black dark:border-zinc-700 rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] transition-all cursor-pointer {{ $selected && $selected->id === $conversation->id ? 'bg-yellow-200 dark:bg-yellow-950/40 translate-x-0.5 translate-y-0.5 shadow-none ring-2 ring-black' : 'bg-white dark:bg-zinc-900 hover:bg-zinc-50 dark:hover:bg-zinc-800' }}"
                        >
                            <div class="flex items-center justify-between gap-3">
                                <p class="font-black text-black dark:text-white uppercase text-sm truncate">
                                    {{ $conversation->seeker?->name ?? 'Pengguna Terhapus' }}
                                </p>
                                <span class="text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase shrink-0">
                                    {{ $conversation->latestMessage?->created_at?->diffForHumans() ?? $conversation->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-[10px] font-black uppercase text-cyan-700 dark:text-cyan-400 mt-1 truncate">
                                {{ $conversation->kost?->name ?? 'Kost telah dihapus' }}
                            </p>
                            <div class="flex items-center justify-between gap-3 mt-1.5">
                                <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 line-clamp-1">
                                    {{ $conversation->latestMessage?->body ?? 'Belum ada pesan.' }}
                                </p>
                                @php $unread = $unreadCounts[$conversation->id] ?? 0; @endphp
                                @if ($unread > 0)
                                    <span class="shrink-0 bg-rose-500 text-white border-2 border-black dark:border-zinc-700 rounded-full px-1.5 py-0.5 text-[9px] font-black min-w-[20px] text-center">
                                        {{ $unread }}
                                    </span>
                                @endif
                            </div>
                        </button>
                    @empty
                        <div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 rounded-2xl p-10 text-center shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] space-y-3">
                            <div class="w-16 h-16 bg-emerald-200 dark:bg-emerald-950/50 border-3 border-black dark:border-zinc-700 rounded-2xl flex items-center justify-center mx-auto shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                                <x-icon name="lucide-message-circle" class="w-8 h-8 text-black dark:text-white stroke-[2.5]" />
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-black dark:text-white uppercase">Belum Ada Percakapan</h3>
                                <p class="text-xs font-bold text-zinc-600 dark:text-zinc-400 mt-1">
                                    Pesan pertama dari pencari kost akan muncul di sini secara real-time.
                                </p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right: Thread -->
            <div class="lg:col-span-3">
                @if ($selected)
                    <div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] overflow-hidden flex flex-col">
                        <!-- Thread Header -->
                        <div class="p-5 border-b-4 border-black dark:border-zinc-700 bg-yellow-300 flex items-center justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-lg font-black text-black uppercase truncate">
                                    {{ $selected->seeker?->name ?? 'Pengguna Terhapus' }}
                                </p>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    @if ($selected->kost)
                                        <a href="{{ route('kost.show', $selected->kost->slug) }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 text-[9px] font-black text-black dark:text-white uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)] hover:bg-cyan-200 transition-all">
                                            <x-icon name="lucide-building-2" class="w-3 h-3 stroke-[2.5]" />
                                            {{ $selected->kost->name }}
                                        </a>
                                    @endif
                                    <span class="px-2 py-0.5 bg-emerald-400 border-2 border-black dark:border-zinc-700 text-[9px] font-black uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] dark:shadow-[1px_1px_0px_0px_rgba(255,255,255,0.25)]">
                                        Chat Terhubung
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                @if ($selected->seeker?->phone_number)
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', $selected->seeker->phone_number) }}?text={{ rawurlencode('Halo ' . ($selected->seeker->name ?? '') . ', saya pemilik kost ' . ($selected->kost?->name ?? 'tersebut') . ' dari KostBandung.') }}"
                                        target="_blank" rel="noopener"
                                        class="inline-flex items-center gap-1.5 bg-white dark:bg-zinc-900 hover:bg-emerald-100 text-black dark:text-white border-2 border-black dark:border-zinc-700 font-black text-[10px] uppercase px-3 py-1.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded cursor-pointer"
                                        title="Hubungi via WhatsApp">
                                        <x-icon name="lucide-phone" class="w-3.5 h-3.5 stroke-[2.5]" />
                                        WhatsApp
                                    </a>
                                @endif
                                <button wire:click="toggleArchive({{ $selected->id }})"
                                    class="inline-flex items-center gap-1.5 bg-zinc-200 dark:bg-zinc-800 hover:bg-zinc-300 dark:hover:bg-zinc-700 text-black dark:text-white border-2 border-black dark:border-zinc-700 font-black text-[10px] uppercase px-3 py-1.5 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded cursor-pointer"
                                    title="Arsipkan percakapan">
                                    <x-icon name="lucide-archive" class="w-3.5 h-3.5 stroke-[2.5]" />
                                    Arsip
                                </button>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div class="p-5 space-y-4 max-h-[520px] overflow-y-auto bg-[#f8f9fa] dark:bg-zinc-950"
                            data-selected="{{ $selected->id }}"
                            x-data="{ current: {{ $selected->id }} }"
                            x-ref="thread"
                            wire:poll.visible="markSelectedRead"
                            x-on:livewire:update.window="() => { const t = $refs.thread; const nearBottom = t.scrollHeight - t.scrollTop - t.clientHeight < 120; if (nearBottom || t.dataset.selected !== String(current)) { current = Number(t.dataset.selected); $nextTick(() => t.scrollTop = t.scrollHeight); } }">
                            @forelse ($selected->messages as $message)
                                @if ($message->sender_id !== Auth::id())
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 shrink-0 bg-cyan-300 border-2 border-black dark:border-zinc-700 rounded-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                                            <x-icon name="lucide-user" class="w-4 h-4 text-black stroke-[2.5]" />
                                        </div>
                                        <div class="max-w-[80%]">
                                            <div class="bg-white dark:bg-zinc-900 border-2 border-black dark:border-zinc-700 p-3 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                                                <p class="text-sm font-bold text-black dark:text-white whitespace-pre-wrap">{{ $message->body }}</p>
                                            </div>
                                            <p class="text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase mt-1.5 ml-1">
                                                {{ $message->sender?->name ?? 'Pengguna Terhapus' }} &middot; {{ $message->created_at->format('d M Y, H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-start justify-end gap-3">
                                        <div class="max-w-[80%] text-right">
                                            <div class="bg-emerald-300 border-2 border-black dark:border-zinc-700 p-3 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)]">
                                                <p class="text-sm font-bold text-black whitespace-pre-wrap">{{ $message->body }}</p>
                                            </div>
                                            <p class="text-[10px] font-black text-zinc-500 dark:text-zinc-400 uppercase mt-1.5 mr-1">
                                                Anda &middot; {{ $message->created_at->format('d M Y, H:i') }}
                                                @if ($message->read_at)
                                                    &middot; <span class="inline-flex items-center gap-0.5 text-emerald-700 dark:text-emerald-400"><x-icon name="lucide-check-check" class="w-3 h-3 stroke-[2.5]" />Dibaca</span>
                                                @endif
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

                        <!-- Composer -->
                        <div class="p-5 border-t-4 border-black dark:border-zinc-700 bg-white dark:bg-zinc-900">
                            <form wire:submit.prevent="sendMessage" class="space-y-3">
                                <label class="block text-[10px] font-black uppercase text-black dark:text-white mb-1">Balas Pesan</label>
                                <textarea wire:model="newBody" rows="3"
                                    class="w-full bg-zinc-100 dark:bg-zinc-800 border-3 border-black dark:border-zinc-700 rounded-xl px-4 py-3 text-sm font-bold text-black dark:text-white focus:outline-none focus:ring-0 focus:bg-white dark:focus:bg-zinc-900 focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:focus:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] transition-all resize-none"
                                    placeholder="Tulis balasan untuk pencari kost..."></textarea>
                                @error('newBody')
                                    <p class="text-xs font-black text-rose-600 dark:text-rose-400">{{ $message }}</p>
                                @enderror
                                <div class="flex items-center justify-end gap-3">
                                    <button type="submit" wire:loading.attr="disabled"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-400 hover:bg-emerald-300 text-black border-3 border-black dark:border-zinc-700 font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] dark:shadow-[3px_3px_0px_0px_rgba(255,255,255,0.25)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer disabled:opacity-50">
                                        <x-icon name="lucide-send" class="w-4 h-4 stroke-[2.5]" />
                                        Kirim
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="bg-white dark:bg-zinc-900 border-4 border-black dark:border-zinc-700 rounded-2xl p-12 text-center shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] dark:shadow-[8px_8px_0px_0px_rgba(255,255,255,0.25)] space-y-4">
                        <div class="w-20 h-20 bg-zinc-200 dark:bg-zinc-800 border-3 border-black dark:border-zinc-700 rounded-2xl flex items-center justify-center mx-auto text-black dark:text-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] -rotate-3">
                            <x-icon name="lucide-message-square-text" class="w-10 h-10 stroke-[2.5]" />
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-black dark:text-white uppercase">Pilih Percakapan</h3>
                            <p class="text-sm font-bold text-zinc-600 dark:text-zinc-400 mt-1">Klik salah satu percakapan di sebelah kiri untuk membalas pesan pencari kost.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
