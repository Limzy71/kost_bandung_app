<div class="min-h-screen bg-[#f8f9fa] bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] bg-[size:24px_24px]">
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
            class="fixed bottom-6 right-6 z-50 bg-lime-300 border-4 border-black p-4 rounded-xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] text-black flex items-center gap-3 max-w-md"
        >
            <div class="w-8 h-8 rounded-full bg-black text-lime-300 flex items-center justify-center text-xs font-black shrink-0">✓</div>
            <p class="text-xs sm:text-sm font-black text-black leading-snug"><span x-text="message"></span></p>
            <button type="button" @click="show = false" class="ml-auto text-black hover:bg-black/10 p-1 rounded font-black text-xs cursor-pointer transition-colors">✕</button>
        </div>

        <!-- Page Header Neo-Brutalist -->
        <div class="bg-white border-4 border-black p-6 sm:p-8 rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="px-3 py-1 bg-yellow-300 text-black border-2 border-black font-black text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        Hubungi Admin
                    </span>
                    <span class="px-3 py-1 bg-cyan-300 text-black border-2 border-black font-black text-xs uppercase tracking-wider shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] inline-flex items-center gap-1.5">
                        <x-icon name="lucide-clock" class="w-3.5 h-3.5 stroke-[2.5]" />
                        Dibalas maksimal 1x24 jam
                    </span>
                </div>
                <div>
                    <h1 class="text-3xl sm:text-4xl font-black text-black tracking-tight uppercase leading-none">
                        Bantuan &amp; Layanan
                    </h1>
                    <p class="text-zinc-700 text-sm sm:text-base font-bold mt-2">
                        Kirim komplain, pertanyaan, atau masukan. Admin akan membalas Anda dalam waktu maksimal 1x24 jam.
                    </p>
                </div>
            </div>
            <button wire:click="openCompose"
                class="shrink-0 inline-flex items-center gap-2 px-5 py-3 bg-black hover:bg-zinc-800 text-white border-3 border-black font-black text-xs uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer">
                <x-icon name="lucide-message-circle-plus" class="w-4 h-4 stroke-[2.5]" />
                Pesan Baru
            </button>
        </div>

        @if (session()->has('success'))
            <div class="bg-lime-300 border-4 border-black rounded-2xl p-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-sm font-black text-black">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 items-start">

            <!-- Left: Conversation List -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex flex-wrap items-center gap-2">
                    <button wire:click="$set('tab', 'active')" class="px-4 py-2 border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg {{ $tab === 'active' ? 'bg-cyan-400 text-black' : 'bg-white text-black hover:bg-cyan-100' }}">
                        <x-icon name="lucide-message-circle" class="w-3.5 h-3.5 inline -mt-0.5 mr-1 {{ $tab === 'active' ? 'fill-black' : '' }}" />
                        Percakapan Aktif
                    </button>
                    <button wire:click="$set('tab', 'history')" class="px-4 py-2 border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg {{ $tab === 'history' ? 'bg-zinc-400 text-black' : 'bg-white text-black hover:bg-zinc-100' }}">
                        <x-icon name="lucide-history" class="w-3.5 h-3.5 inline -mt-0.5 mr-1" />
                        Riwayat
                    </button>
                </div>

                <div class="space-y-3">
                    @forelse ($conversations as $conversation)
                        <button
                            type="button"
                            wire:click="openConversation({{ $conversation->id }})"
                            class="w-full text-left p-4 border-3 border-black rounded-xl shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all cursor-pointer {{ $selected && $selected->id === $conversation->id ? 'bg-yellow-200 translate-x-0.5 translate-y-0.5 shadow-none ring-2 ring-black' : 'bg-white hover:bg-zinc-50' }}"
                        >
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="px-2 py-0.5 bg-lime-300 border-2 border-black text-[9px] font-black uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)] shrink-0">
                                        {{ \App\Models\AdminConversation::categoryLabel($conversation->category) }}
                                    </span>
                                    @if ($conversation->isOpen())
                                        <span class="px-2 py-0.5 bg-cyan-300 border-2 border-black text-[9px] font-black uppercase rounded shrink-0">Aktif</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-rose-300 border-2 border-black text-[9px] font-black uppercase rounded shrink-0">Ditutup</span>
                                    @endif
                                </div>
                                <span class="text-[10px] font-black text-zinc-500 uppercase shrink-0">{{ $conversation->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm font-black text-black mt-2 line-clamp-2">
                                {{ $conversation->messages()->latest()->value('body') ?? 'Belum ada pesan.' }}
                            </p>
                        </button>
                    @empty
                        <div class="bg-white border-4 border-black rounded-2xl p-10 text-center shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] space-y-3">
                            <div class="w-16 h-16 bg-cyan-200 border-3 border-black rounded-2xl flex items-center justify-center mx-auto shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                                <x-icon name="lucide-message-circle" class="w-8 h-8 text-black stroke-[2.5]" />
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-black uppercase">
                                    {{ $tab === 'active' ? 'Belum Ada Percakapan Aktif' : 'Belum Ada Riwayat' }}
                                </h3>
                                <p class="text-xs font-bold text-zinc-600 mt-1">
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
                    <div class="bg-white border-4 border-black rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] overflow-hidden flex flex-col">
                        <!-- Thread Header -->
                        <div class="p-5 border-b-4 border-black bg-yellow-300 flex items-center justify-between gap-4">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2 py-0.5 bg-white border-2 border-black text-[9px] font-black uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                                        {{ \App\Models\AdminConversation::categoryLabel($selected->category) }}
                                    </span>
                                    @if ($selected->isOpen())
                                        <span class="px-2 py-0.5 bg-cyan-400 border-2 border-black text-[9px] font-black uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">Aktif</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-rose-400 border-2 border-black text-[9px] font-black uppercase rounded shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                                            {{ $selected->closed_reason === 'expired' ? 'Ditutup Otomatis (1x24 jam)' : 'Ditutup Admin' }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[11px] font-black text-black/70 uppercase mt-1.5">
                                    Dibuka {{ $selected->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </div>

                        <!-- Messages -->
                        <div class="p-5 space-y-4 max-h-[520px] overflow-y-auto bg-[#f8f9fa]">
                            @forelse ($selected->messages as $message)
                                @if ($message->sender_type === 'admin')
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 shrink-0 bg-lime-400 border-2 border-black rounded-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                            <x-icon name="lucide-shield" class="w-4 h-4 text-black stroke-[2.5]" />
                                        </div>
                                        <div class="max-w-[80%]">
                                            <div class="bg-white border-2 border-black p-3 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                                                <p class="text-sm font-bold text-black whitespace-pre-wrap">{{ $message->body }}</p>
                                            </div>
                                            <p class="text-[10px] font-black text-zinc-500 uppercase mt-1.5 ml-1">
                                                Admin &middot; {{ $message->created_at->format('d M Y, H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <div class="flex items-start justify-end gap-3">
                                        <div class="max-w-[80%] text-right">
                                            <div class="bg-cyan-300 border-2 border-black p-3 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                                                <p class="text-sm font-bold text-black whitespace-pre-wrap">{{ $message->body }}</p>
                                            </div>
                                            <p class="text-[10px] font-black text-zinc-500 uppercase mt-1.5 mr-1">
                                                Anda &middot; {{ $message->created_at->format('d M Y, H:i') }}
                                            </p>
                                        </div>
                                        <div class="w-9 h-9 shrink-0 bg-yellow-300 border-2 border-black rounded-lg flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                            <x-icon name="lucide-user" class="w-4 h-4 text-black stroke-[2.5]" />
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <p class="text-center text-xs font-black text-zinc-500 uppercase py-8">Belum ada pesan.</p>
                            @endforelse
                        </div>

                        <!-- Follow-up / Closed Notice -->
                        <div class="p-5 border-t-4 border-black bg-white">
                            @if ($selected->isOpen())
                                <form wire:submit.prevent="sendFollowUp" class="space-y-3">
                                    <label class="block text-[10px] font-black uppercase text-black mb-1">Balas / Lanjutkan Percakapan</label>
                                    <textarea wire:model="followUpBody" rows="3"
                                        class="w-full bg-zinc-100 border-3 border-black rounded-xl px-4 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:bg-white focus:shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all resize-none"
                                        placeholder="Tulis pesan lanjutan Anda untuk Admin..."></textarea>
                                    @error('followUpBody')
                                        <p class="text-xs font-black text-rose-600">{{ $message }}</p>
                                    @enderror
                                    <div class="flex items-center justify-between gap-3">
                                        <p class="text-[10px] font-black text-zinc-500 uppercase">Balasan maksimal 1x24 jam</p>
                                        <button type="submit"
                                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-400 hover:bg-emerald-300 text-black border-3 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer">
                                            <x-icon name="lucide-send" class="w-4 h-4 stroke-[2.5]" />
                                            Kirim
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div class="text-center space-y-3 py-2">
                                    <p class="text-sm font-black text-black">
                                        Percakapan ini sudah ditutup.
                                    </p>
                                    <button wire:click="openCompose"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-cyan-300 hover:bg-cyan-200 text-black border-3 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-xl cursor-pointer">
                                        <x-icon name="lucide-message-circle-plus" class="w-4 h-4 stroke-[2.5]" />
                                        Buka Percakapan Baru
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-white border-4 border-black rounded-2xl p-12 text-center shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] space-y-4">
                        <div class="w-20 h-20 bg-zinc-200 border-3 border-black rounded-2xl flex items-center justify-center mx-auto text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] -rotate-3">
                            <x-icon name="lucide-message-square-text" class="w-10 h-10 stroke-[2.5]" />
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-black uppercase">Pilih Percakapan</h3>
                            <p class="text-sm font-bold text-zinc-600 mt-1">Klik salah satu percakapan di sebelah kiri untuk melihat pesan dan balasan.</p>
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
            <div class="relative bg-white border-4 border-black rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] w-full max-w-lg p-6 space-y-5">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-xl font-black text-black uppercase">Pesan Baru ke Admin</h2>
                    <button wire:click="closeCompose" class="p-1.5 bg-zinc-200 hover:bg-zinc-300 border-2 border-black rounded text-black cursor-pointer">
                        <x-icon name="lucide-x" class="w-4 h-4 stroke-[2.5]" />
                    </button>
                </div>

                <div class="bg-yellow-200 border-2 border-black p-3 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] text-xs font-black text-black">
                    <p class="inline-flex items-center gap-1.5">
                        <x-icon name="lucide-clock" class="w-3.5 h-3.5 stroke-[2.5]" />
                        Pesan Anda akan dibalas Admin maksimal 1x24 jam.
                    </p>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-black mb-1">Kategori</label>
                    <select wire:model="category"
                        class="w-full bg-zinc-100 border-3 border-black rounded-xl px-4 py-3 text-sm font-black uppercase text-black focus:outline-none focus:ring-0 focus:bg-white transition-all cursor-pointer">
                        <option value="" class="font-black uppercase">PILIH KATEGORI...</option>
                        @foreach (\App\Models\AdminConversation::CATEGORIES as $cat)
                            <option value="{{ $cat }}" class="font-black uppercase">{{ mb_strtoupper(\App\Models\AdminConversation::categoryLabel($cat)) }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="text-xs font-black text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-black mb-1">Isi Pesan</label>
                    <textarea wire:model="newBody" rows="5"
                        class="w-full bg-zinc-100 border-3 border-black rounded-xl px-4 py-3 text-sm font-bold text-black focus:outline-none focus:ring-0 focus:bg-white transition-all resize-none"
                        placeholder="Tulis komplain, pertanyaan, atau masukan Anda..."></textarea>
                    @error('newBody')
                        <p class="text-xs font-black text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-wrap items-center justify-end gap-3 pt-1">
                    <button wire:click="closeCompose" class="px-4 py-2.5 bg-white hover:bg-zinc-100 text-black border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg cursor-pointer">
                        Batal
                    </button>
                    <button wire:click="sendNewConversation" class="px-5 py-2.5 bg-cyan-400 hover:bg-cyan-300 text-black border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg inline-flex items-center gap-2 cursor-pointer">
                        <x-icon name="lucide-send" class="w-4 h-4 stroke-[2.5]" />
                        Kirim ke Admin
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
