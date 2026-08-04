<div class="min-h-screen bg-[#f8f9fa] bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] bg-[size:24px_24px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-8">
    
    <!-- Page Header Neo-Brutalist -->
    <div class="bg-white border-4 border-black p-6 rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-black uppercase tracking-tight">Inbox Pesan</h1>
            <p class="text-sm font-bold text-zinc-600 mt-1">Kelola pertanyaan calon penyewa untuk properti Anda.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-2 mt-4 md:mt-0">
            <button wire:click="$set('filter', 'all')" class="px-4 py-2 border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg {{ $filter === 'all' ? 'bg-black text-white' : 'bg-white text-black hover:bg-zinc-100' }}">Semua</button>
            <button wire:click="$set('filter', 'unread')" class="px-4 py-2 border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg {{ $filter === 'unread' ? 'bg-yellow-400 text-black' : 'bg-white text-black hover:bg-yellow-100' }}">Belum Dibaca</button>
            <button wire:click="$set('filter', 'read')" class="px-4 py-2 border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg {{ $filter === 'read' ? 'bg-emerald-400 text-black' : 'bg-white text-black hover:bg-emerald-100' }}">Sudah Dibaca</button>
            <button wire:click="$set('filter', 'archived')" class="px-4 py-2 border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg {{ $filter === 'archived' ? 'bg-zinc-400 text-black' : 'bg-white text-black hover:bg-zinc-100' }}">Diarsipkan</button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-lime-300 border-4 border-black rounded-2xl p-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-sm font-black text-black">
            {{ session('success') }}
        </div>
    @endif

    <!-- Inbox List -->
    <div class="space-y-6">
        @forelse ($inquiries as $inquiry)
            <div class="bg-white border-4 border-black rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] overflow-hidden transition-all flex">
                @if($inquiry->status === 'unread')
                    <div class="w-1.5 shrink-0 bg-yellow-400"></div>
                @endif
                <div class="flex-1 p-6 md:p-8 flex flex-col md:flex-row gap-6">
                    
                    <!-- Left: Sender Info -->
                    <div class="md:w-1/3 shrink-0 space-y-4">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                @if($inquiry->status === 'unread')
                                    <span class="bg-yellow-400 text-black px-2 py-0.5 border-2 border-black text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] rounded">Unread</span>
                                @elseif($inquiry->status === 'archived')
                                    <span class="bg-zinc-300 text-black px-2 py-0.5 border-2 border-black text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] rounded">Archived</span>
                                @else
                                    <span class="bg-emerald-400 text-black px-2 py-0.5 border-2 border-black text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] rounded">Read</span>
                                @endif
                                <span class="text-[10px] font-black text-zinc-500 uppercase">{{ $inquiry->created_at->diffForHumans() }}</span>
                            </div>
                            <h3 class="text-xl font-black text-black uppercase">{{ $inquiry->name }}</h3>
                            <p class="text-sm font-bold text-zinc-600 flex items-center gap-1 mt-1">
                                <x-icon name="lucide-phone" class="w-4 h-4" />
                                {{ $inquiry->phone_number }}
                            </p>
                        </div>
                        
                        <div class="bg-zinc-100 border-2 border-black p-3 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            <p class="text-[10px] font-black uppercase text-zinc-500 mb-1">Menanyakan Properti:</p>
                            <a href="{{ route('kost.show', $inquiry->kost->slug) }}" target="_blank" class="text-sm font-black text-black hover:underline line-clamp-2">
                                {{ $inquiry->kost->name }}
                            </a>
                        </div>
                    </div>
                    
                    <!-- Right: Message & Actions -->
                    <div class="md:w-2/3 flex flex-col justify-between space-y-6">
                        <div class="bg-white border-2 border-black p-4 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] relative">
                            <div class="absolute -top-3 left-4 bg-white px-2 text-[10px] font-black uppercase border-2 border-black rounded text-black">Pesan</div>
                            <p class="text-sm font-bold text-black whitespace-pre-wrap">{{ $inquiry->message }}</p>
                        </div>
                        
                        @if($inquiry->owner_reply)
                            <div class="bg-lime-50 border-2 border-black p-4 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] relative">
                                <div class="absolute -top-3 left-4 bg-lime-300 px-2 text-[10px] font-black uppercase border-2 border-black rounded text-black">Balasan Anda</div>
                                <p class="text-sm font-bold text-black whitespace-pre-wrap">{{ $inquiry->owner_reply }}</p>
                                @if($inquiry->replied_at)
                                    <p class="text-[10px] font-black text-zinc-500 uppercase mt-2">Dibalas {{ $inquiry->replied_at->diffForHumans() }}</p>
                                @endif
                            </div>
                        @endif
                        
                        <div class="flex flex-wrap items-center gap-3">
                            @php
                                $waText = rawurlencode("Halo " . $inquiry->name . ", saya pemilik kost " . $inquiry->kost->name . ". Membalas pesan Anda: \n\n\"" . Str::limit($inquiry->message, 50) . "\"");
                                $phone = $inquiry->phone_number;
                                if (str_starts_with($phone, '08')) {
                                    $phone = '628' . substr($phone, 2);
                                }
                            @endphp
                            
                            <a href="https://wa.me/{{ $phone }}?text={{ $waText }}" 
                               target="_blank"
                               wire:click="markAsRead({{ $inquiry->id }})"
                               class="px-5 py-2.5 bg-emerald-400 hover:bg-emerald-300 text-black border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg inline-flex items-center gap-2">
                                <x-icon name="lucide-send" class="w-4 h-4 stroke-[2.5]" />
                                Hubungi via WhatsApp &rarr;
                            </a>

                            <button wire:click="openReplyModal({{ $inquiry->id }})" class="px-4 py-2.5 bg-cyan-300 hover:bg-cyan-200 text-black border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg inline-flex items-center gap-2">
                                <x-icon name="lucide-reply" class="w-4 h-4 stroke-[2.5]" />
                                {{ $inquiry->owner_reply ? 'Ubah Balasan' : 'Balas Pesan' }}
                            </button>
                            
                            @if($inquiry->status === 'unread')
                                <button wire:click="markAsRead({{ $inquiry->id }})" class="px-4 py-2.5 bg-white hover:bg-zinc-100 text-black border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg">
                                    Tandai Sudah Dibaca
                                </button>
                            @endif
                            
                            <button wire:click="toggleArchive({{ $inquiry->id }})" class="px-4 py-2.5 {{ $inquiry->status === 'archived' ? 'bg-cyan-300 hover:bg-cyan-200' : 'bg-zinc-200 hover:bg-zinc-300' }} text-black border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg ml-auto">
                                {{ $inquiry->status === 'archived' ? 'Kembalikan dari Arsip' : 'Arsipkan' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-yellow-100 border-4 border-black rounded-2xl p-12 text-center shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] space-y-4">
                <div class="w-20 h-20 bg-white border-3 border-black rounded-2xl flex items-center justify-center mx-auto text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] -rotate-3">
                    <x-icon name="lucide-inbox" class="w-10 h-10 stroke-[2.5]" />
                </div>
                <div>
                    <h3 class="text-2xl font-black text-black uppercase">Belum Ada Pesan</h3>
                    <p class="text-sm font-bold text-zinc-700 max-w-xl mx-auto mt-2">
                        @if($filter === 'all')
                            Anda belum menerima pesan pertanyaan apapun dari calon penyewa&nbsp;kost.
                        @else
                            Tidak ada pesan dalam kategori ini.
                        @endif
                    </p>
                </div>
            </div>
        @endforelse
    </div>
    
    <div>
        {{ $inquiries->links() }}
    </div>

    <!-- Reply Modal -->
    @if($replyingToId)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/60" wire:click="closeReplyModal"></div>
            <div class="relative bg-white border-4 border-black rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] w-full max-w-lg p-6 space-y-4">
                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-xl font-black text-black uppercase">Balas Pesan</h2>
                    <button wire:click="closeReplyModal" class="p-1.5 bg-zinc-200 hover:bg-zinc-300 border-2 border-black rounded text-black cursor-pointer">
                        <x-icon name="lucide-x" class="w-4 h-4 stroke-[2.5]" />
                    </button>
                </div>
                <div class="bg-zinc-100 border-2 border-black p-3 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] text-xs font-bold text-zinc-600">
                    <p class="text-[10px] font-black uppercase text-zinc-500 mb-1">Balasan akan tampil di halaman "Pesan Terkirim" pencari kost.</p>
                </div>
                <div>
                    <label for="replyMessage" class="block text-[10px] font-black uppercase text-black mb-1">Isi Balasan</label>
                    <textarea id="replyMessage" wire:model="replyMessage" rows="5" class="w-full border-2 border-black rounded-lg p-3 text-sm font-bold text-black focus:outline-none focus:ring-2 focus:ring-cyan-300 resize-none" placeholder="Ketik balasan untuk pencari kost..."></textarea>
                    @error('replyMessage')
                        <p class="mt-1 text-xs font-black text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-wrap items-center justify-end gap-3 pt-2">
                    <button wire:click="closeReplyModal" class="px-4 py-2.5 bg-white hover:bg-zinc-100 text-black border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg">
                        Batal
                    </button>
                    <button wire:click="replyInquiry" class="px-5 py-2.5 bg-emerald-400 hover:bg-emerald-300 text-black border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg inline-flex items-center gap-2">
                        <x-icon name="lucide-send" class="w-4 h-4 stroke-[2.5]" />
                        Kirim Balasan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
</div>
