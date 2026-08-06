<div class="min-h-screen bg-[#f8f9fa] bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] bg-[size:24px_24px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-8">

    <!-- Page Header Neo-Brutalist -->
    <div class="bg-white border-4 border-black p-6 rounded-2xl shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-black uppercase tracking-tight">Pesan Terkirim</h1>
            <p class="text-sm font-bold text-zinc-600 mt-1">Pantau pertanyaan yang Anda kirim ke pemilik kost.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 mt-4 md:mt-0">
            <button wire:click="$set('filter', 'all')" class="px-4 py-2 border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg {{ $filter === 'all' ? 'bg-black text-white' : 'bg-white text-black hover:bg-zinc-100' }}">Semua</button>
            <button wire:click="$set('filter', 'waiting')" class="px-4 py-2 border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg {{ $filter === 'waiting' ? 'bg-yellow-400 text-black' : 'bg-white text-black hover:bg-yellow-100' }}">Menunggu Balasan</button>
            <button wire:click="$set('filter', 'replied')" class="px-4 py-2 border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg {{ $filter === 'replied' ? 'bg-emerald-400 text-black' : 'bg-white text-black hover:bg-emerald-100' }}">Sudah Dibalas</button>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-lime-300 border-4 border-black rounded-2xl p-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] text-sm font-black text-black">
            {{ session('success') }}
        </div>
    @endif

    <!-- Sent Messages List -->
    <div class="space-y-6">
        @forelse ($inquiries as $inquiry)
            <div class="bg-white border-4 border-black rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] overflow-hidden {{ $inquiry->owner_reply ? '' : 'border-l-8 border-l-yellow-400' }}">
                <div class="p-6 md:p-8 flex flex-col md:flex-row gap-6">

                    <!-- Left: Kost Info -->
                    <div class="md:w-1/3 shrink-0 space-y-4">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                @if($inquiry->owner_reply)
                                    <span class="bg-emerald-400 text-black px-2 py-0.5 border-2 border-black text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] rounded">Sudah Dibalas</span>
                                @else
                                    <span class="bg-yellow-400 text-black px-2 py-0.5 border-2 border-black text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] rounded">Menunggu Balasan</span>
                                @endif
                                <span class="text-[10px] font-black text-zinc-500 uppercase">{{ $inquiry->created_at->diffForHumans() }}</span>
                            </div>
                            <h3 class="text-xl font-black text-black uppercase">{{ $inquiry->kost?->name ?? 'Kost telah dihapus' }}</h3>
                            <p class="text-sm font-bold text-zinc-600 mt-1">Dikirim atas nama {{ $inquiry->name }}</p>
                        </div>

                        @if ($inquiry->kost)
                            <a href="{{ route('kost.show', $inquiry->kost->slug) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-300 hover:bg-cyan-200 text-black border-2 border-black font-black text-xs uppercase shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg">
                                <x-icon name="lucide-eye" class="w-4 h-4 stroke-[2.5]" />
                                Lihat Kost
                            </a>
                        @else
                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-200 text-zinc-600 border-2 border-zinc-400 font-black text-xs uppercase rounded-lg">Kost telah dihapus</span>
                        @endif
                    </div>

                    <!-- Right: Message & Reply -->
                    <div class="md:w-2/3 flex flex-col justify-between space-y-6">
                        <div class="bg-white border-2 border-black p-4 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] relative">
                            <div class="absolute -top-3 left-4 bg-white px-2 text-[10px] font-black uppercase border-2 border-black rounded text-black">Pesan Anda</div>
                            <p class="text-sm font-bold text-black whitespace-pre-wrap">{{ $inquiry->message }}</p>
                        </div>

                        @if($inquiry->owner_reply)
                            <div class="bg-lime-50 border-2 border-black p-4 rounded-xl shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] relative">
                                <div class="absolute -top-3 left-4 bg-lime-300 px-2 text-[10px] font-black uppercase border-2 border-black rounded text-black">Balasan Pemilik Kost</div>
                                <p class="text-sm font-bold text-black whitespace-pre-wrap">{{ $inquiry->owner_reply }}</p>
                                @if($inquiry->replied_at)
                                    <p class="text-[10px] font-black text-zinc-500 uppercase mt-2">Dibalas {{ $inquiry->replied_at->diffForHumans() }}</p>
                                @endif
                            </div>
                        @else
                            <div class="bg-zinc-100 border-2 border-dashed border-zinc-400 p-4 rounded-xl text-center">
                                <p class="text-xs font-black uppercase text-zinc-500">Belum ada balasan dari pemilik kost. Anda bisa mengirim pesan baru dari halaman Kost kapan saja.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-yellow-100 border-4 border-black rounded-2xl p-12 text-center shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] space-y-4">
                <div class="w-20 h-20 bg-white border-3 border-black rounded-2xl flex items-center justify-center mx-auto text-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] -rotate-3">
                    <x-icon name="lucide-send" class="w-10 h-10 stroke-[2.5]" />
                </div>
                <div>
                    <h3 class="text-2xl font-black text-black uppercase">Belum Ada Pesan</h3>
                    <p class="text-sm font-bold text-zinc-700 max-w-xl mx-auto mt-2">
                        @if($filter === 'all')
                            Anda belum mengirim pesan pertanyaan apapun ke pemilik kost.
                        @else
                            Tidak ada pesan dalam kategori ini.
                        @endif
                    </p>
                </div>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-black hover:bg-zinc-800 text-white border-2 border-black font-black text-xs uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all rounded-lg">
                    <x-icon name="lucide-search" class="w-4 h-4 stroke-[2.5]" />
                    Cari Kost Sekarang
                </a>
            </div>
        @endforelse
    </div>

    <div>
        {{ $inquiries->links() }}
    </div>
</div>
</div>
