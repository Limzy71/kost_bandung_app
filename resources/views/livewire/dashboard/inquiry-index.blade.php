<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
    
    <!-- Page Header Neo-Brutalist -->
    <div class="bg-white border-4 border-black p-6 rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-black uppercase tracking-tight">Inbox Pesan</h1>
            <p class="text-sm font-bold text-zinc-600 mt-1">Kelola pertanyaan calon penyewa untuk properti Anda.</p>
        </div>
        
        <div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0">
            <button wire:click="$set('filter', 'all')" class="px-4 py-2 border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg {{ $filter === 'all' ? 'bg-black text-white' : 'bg-white text-black hover:bg-zinc-100' }}">Semua</button>
            <button wire:click="$set('filter', 'unread')" class="px-4 py-2 border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg {{ $filter === 'unread' ? 'bg-yellow-400 text-black' : 'bg-white text-black hover:bg-yellow-100' }}">Belum Dibaca</button>
            <button wire:click="$set('filter', 'read')" class="px-4 py-2 border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg {{ $filter === 'read' ? 'bg-emerald-400 text-black' : 'bg-white text-black hover:bg-emerald-100' }}">Sudah Dibaca</button>
            <button wire:click="$set('filter', 'archived')" class="px-4 py-2 border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg {{ $filter === 'archived' ? 'bg-zinc-400 text-black' : 'bg-white text-black hover:bg-zinc-100' }}">Diarsipkan</button>
        </div>
    </div>

    <!-- Inbox List -->
    <div class="space-y-6">
        @forelse ($inquiries as $inquiry)
            <div class="bg-white border-4 border-black rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] overflow-hidden transition-all {{ $inquiry->status === 'unread' ? 'border-l-8 border-l-yellow-400' : '' }}">
                <div class="p-6 md:p-8 flex flex-col md:flex-row gap-6">
                    
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
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
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
                                <svg class="w-4 h-4 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                Hubungi via WhatsApp &rarr;
                            </a>
                            
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
                    <svg class="w-10 h-10 stroke-[2.5]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-black uppercase">Belum Ada Pesan</h3>
                    <p class="text-sm font-bold text-zinc-700 max-w-md mx-auto mt-2">
                        @if($filter === 'all')
                            Anda belum menerima pesan pertanyaan apapun dari calon penyewa kost.
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
</div>
