<div>
    <!-- Floating Filter Pill -->
    <div class="relative z-20 bg-white/80 backdrop-blur-md shadow-xl shadow-gray-200/50 rounded-2xl md:rounded-full border border-gray-100 mb-12 p-2">
        <div class="flex flex-col md:flex-row items-center divide-y md:divide-y-0 md:divide-x divide-gray-200 w-full">
            <!-- Search Input -->
            <div class="w-full md:w-1/3 relative flex items-center px-4 py-2">
                <svg class="h-5 w-5 text-zinc-400 flex-shrink-0" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="9" r="6"></circle>
                    <path d="M15 15l4 4"></path>
                </svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama kost atau jalan..." class="w-full h-10 pl-3 pr-2 bg-transparent border-none focus:ring-0 text-slate-800 placeholder-zinc-400 outline-none">
            </div>

            <!-- Gender -->
            <div class="w-full md:w-auto flex-1 px-4 py-2">
                <select wire:model.live="gender" class="w-full h-10 bg-transparent border-none focus:ring-0 text-slate-800 outline-none cursor-pointer appearance-none md:bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%239CA3AF%22%20stroke-width%3D%222%22%20fill%3D%22none%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-right">
                    <option value="">Semua Tipe</option>
                    <option value="putra">Putra</option>
                    <option value="putri">Putri</option>
                    <option value="campur">Campur</option>
                </select>
            </div>

            <!-- District -->
            <div class="w-full md:w-auto flex-1 px-4 py-2">
                <select wire:model.live="district" class="w-full h-10 bg-transparent border-none focus:ring-0 text-slate-800 outline-none cursor-pointer appearance-none md:bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%239CA3AF%22%20stroke-width%3D%222%22%20fill%3D%22none%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-right">
                    <option value="">Kecamatan</option>
                    @foreach($districts as $dist)
                        <option value="{{ $dist }}">{{ $dist }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Min Price -->
            <div class="w-full md:w-auto flex-1 px-4 py-2">
                <select wire:model.live="price_min" class="w-full h-10 bg-transparent border-none focus:ring-0 text-slate-800 outline-none cursor-pointer appearance-none md:bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%239CA3AF%22%20stroke-width%3D%222%22%20fill%3D%22none%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-right">
                    <option value="">Min Harga</option>
                    <option value="500000">Rp 500.000</option>
                    <option value="1000000">Rp 1.000.000</option>
                    <option value="1500000">Rp 1.500.000</option>
                    <option value="2000000">Rp 2.000.000</option>
                    <option value="3000000">Rp 3.000.000</option>
                    <option value="5000000">Rp 5.000.000</option>
                </select>
            </div>

            <!-- Max Price -->
            <div class="w-full md:w-auto flex-1 px-4 py-2">
                <select wire:model.live="price_max" class="w-full h-10 bg-transparent border-none focus:ring-0 text-slate-800 outline-none cursor-pointer appearance-none md:bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20width%3D%2220%22%20height%3D%2220%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M5%208l5%205%205-5%22%20stroke%3D%22%239CA3AF%22%20stroke-width%3D%222%22%20fill%3D%22none%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%2F%3E%3C%2Fsvg%3E')] bg-no-repeat bg-right">
                    <option value="">Max Harga</option>
                    <option value="500000">Rp 500.000</option>
                    <option value="1000000">Rp 1.000.000</option>
                    <option value="1500000">Rp 1.500.000</option>
                    <option value="2000000">Rp 2.000.000</option>
                    <option value="3000000">Rp 3.000.000</option>
                    <option value="5000000">Rp 5.000.000</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Grid List Kost -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 relative z-10">
        <div wire:loading class="absolute inset-0 bg-slate-50/50 backdrop-blur-sm z-20 rounded-3xl"></div>
        
        @forelse($kosts as $kost)
            <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group flex flex-col border border-transparent">
                <!-- Image Container -->
                <div class="aspect-[4/3] bg-zinc-200 relative overflow-hidden flex-shrink-0 cursor-pointer" onclick="window.location.href='{{ route('kost.show', $kost->slug) }}'">
                    @if($kost->primaryImage)
                        <img src="{{ Storage::url($kost->primaryImage->image_path) }}" alt="{{ $kost->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-zinc-100">
                            <svg class="w-10 h-10 text-zinc-300 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    @endif
                    
                    <!-- Gradient Overlay -->
                    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/60 via-black/10 to-transparent pointer-events-none"></div>

                    <!-- Badges -->
                    <div class="absolute top-4 left-4 flex flex-col gap-2 pointer-events-none">
                        @if($kost->boosted_at)
                            <span class="px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-[11px] font-bold text-white flex items-center gap-1.5 border border-white/30 shadow-sm uppercase tracking-wider">
                                <svg class="w-3 h-3 text-amber-300" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z"/></svg>
                                Super
                            </span>
                        @endif
                    </div>
                    
                    <!-- Gender Badge (Bottom Right of Image for balance) -->
                    <div class="absolute bottom-4 right-4 pointer-events-none">
                         <span class="px-3 py-1 bg-black/40 backdrop-blur-md rounded-full text-[11px] font-semibold text-white uppercase tracking-wider border border-white/20 shadow-sm">
                            {{ $kost->gender_type }}
                        </span>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="p-6 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-widest">{{ $kost->district }}</span>
                        </div>
                        
                        <h2 class="text-xl font-bold text-slate-900 leading-tight group-hover:text-indigo-600 transition-colors line-clamp-1">
                            <a href="{{ route('kost.show', $kost->slug) }}" class="focus:outline-none">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                {{ $kost->name }}
                            </a>
                        </h2>
                        
                        <p class="text-sm text-zinc-500 mt-2 line-clamp-1 flex items-center gap-1.5">
                            <svg class="w-4 h-4 flex-shrink-0 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            <span class="truncate">{{ $kost->address }}</span>
                        </p>
                        
                        <!-- Facilities Info -->
                        @if($kost->facilities && $kost->facilities->count() > 0)
                        <div class="mt-4 flex flex-wrap items-center gap-3 relative z-10">
                            @foreach($kost->facilities->take(3) as $facility)
                                <div class="flex items-center gap-1.5 text-xs font-medium text-zinc-600">
                                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
                                    <span class="truncate max-w-[80px]">{{ $facility->name }}</span>
                                </div>
                            @endforeach
                            @if($kost->facilities->count() > 3)
                                <span class="text-xs font-medium text-zinc-400">
                                    +{{ $kost->facilities->count() - 3 }}
                                </span>
                            @endif
                        </div>
                        @endif
                    </div>
                    
                    <!-- Price Footer -->
                    <div class="mt-6 pt-5 border-t border-gray-100 flex items-baseline gap-1">
                        <span class="text-xl font-extrabold text-slate-900 tracking-tight">Rp {{ number_format($kost->price_monthly, 0, ',', '.') }}</span>
                        <span class="text-sm font-medium text-zinc-500">/ bulan</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-900">Tidak ada hunian ditemukan</h3>
                <p class="text-zinc-500 mt-2 max-w-md font-light">Coba ubah filter atau kata kunci pencarian Anda untuk menemukan hunian yang sesuai.</p>
                <button wire:click="$set('search', ''); $set('gender', ''); $set('district', ''); $set('price_min', ''); $set('price_max', '');" class="mt-6 px-6 py-2.5 bg-slate-900 text-white font-medium rounded-full hover:bg-slate-800 transition-colors shadow-md relative z-10">Reset Filter</button>
            </div>
        @endforelse
    </div>

    <div class="mt-12 relative z-10">
        {{ $kosts->links() }}
    </div>
</div>
