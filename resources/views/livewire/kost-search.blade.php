<div>
    <!-- Filter Section (Sticky) -->
    <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-md border-b border-slate-200 py-4 shadow-sm mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-4 items-center">
                <!-- Search Input -->
                <div class="w-full md:w-1/3 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama kost atau jalan..." class="w-full pl-10 pr-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow bg-white/90">
                </div>

                <!-- Filters -->
                <div class="w-full md:w-2/3 flex flex-wrap lg:flex-nowrap gap-3">
                    <select wire:model.live="gender" class="flex-1 min-w-[120px] py-2 px-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white/90">
                        <option value="">Semua Tipe</option>
                        <option value="putra">Putra</option>
                        <option value="putri">Putri</option>
                        <option value="campur">Campur</option>
                    </select>

                    <select wire:model.live="district" class="flex-1 min-w-[140px] py-2 px-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white/90">
                        <option value="">Semua Kecamatan</option>
                        @foreach($districts as $dist)
                            <option value="{{ $dist }}">{{ $dist }}</option>
                        @endforeach
                    </select>

                    <input wire:model.live.debounce.500ms="price_min" type="number" placeholder="Min Harga" class="flex-1 min-w-[110px] py-2 px-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white/90">
                    <input wire:model.live.debounce.500ms="price_max" type="number" placeholder="Max Harga" class="flex-1 min-w-[110px] py-2 px-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white/90">
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-12">
        <!-- Grid List Kost -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 relative">
            <div wire:loading class="absolute inset-0 bg-white/50 backdrop-blur-sm z-10 rounded-2xl"></div>
            
            @forelse($kosts as $kost)
                <div class="bg-white rounded-2xl border {{ $kost->boosted_at ? 'border-amber-400 shadow-amber-100' : 'border-slate-200' }} overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col group translate-y-0 hover:-translate-y-1">
                    <!-- Thumbnail -->
                    <div class="h-52 bg-slate-200 relative overflow-hidden">
                        @if($kost->primaryImage)
                            <img src="{{ Storage::url($kost->primaryImage->image_path) }}" alt="{{ $kost->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-400">
                                <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                        @endif

                        <!-- Badges -->
                        <div class="absolute top-3 left-3 flex flex-col gap-2">
                            @if($kost->boosted_at)
                                <span class="px-3 py-1 bg-gradient-to-r from-amber-500 to-orange-500 shadow-md shadow-amber-500/30 rounded-full text-xs font-bold text-white flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z"/></svg>
                                    Super
                                </span>
                            @endif
                            <span class="px-3 py-1 bg-white/90 backdrop-blur-md rounded-full text-xs font-semibold text-slate-700 capitalize shadow-sm">
                                {{ $kost->gender_type }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-bold text-indigo-600 uppercase tracking-wider">{{ $kost->district }}</span>
                            <h2 class="text-lg font-bold text-slate-900 mt-1 line-clamp-1 group-hover:text-indigo-600 transition-colors">
                                <a href="{{ route('kost.show', $kost->slug) }}" class="focus:outline-none">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    {{ $kost->name }}
                                </a>
                            </h2>
                            <p class="text-sm text-slate-500 mt-1 line-clamp-1 flex items-center gap-1">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ $kost->address }}
                            </p>
                            
                            <!-- Facilities Preview -->
                            @if($kost->facilities && $kost->facilities->count() > 0)
                            <div class="mt-3 flex flex-wrap gap-1">
                                @foreach($kost->facilities->take(3) as $facility)
                                    <span class="text-[10px] font-medium px-2 py-1 bg-slate-100 text-slate-600 rounded-md truncate max-w-full">
                                        {{ $facility->name }}
                                    </span>
                                @endforeach
                                @if($kost->facilities->count() > 3)
                                    <span class="text-[10px] font-medium px-2 py-1 bg-slate-100 text-slate-600 rounded-md">
                                        +{{ $kost->facilities->count() - 3 }}
                                    </span>
                                @endif
                            </div>
                            @endif
                        </div>
                        
                        <!-- Price & Action -->
                        <div class="mt-5 pt-4 border-t border-slate-100 flex items-end justify-between">
                            <div>
                                <p class="text-xs text-slate-500 font-medium mb-0.5">Mulai dari</p>
                                <span class="text-lg font-extrabold text-slate-900 leading-none">Rp {{ number_format($kost->price_monthly, 0, ',', '.') }}</span>
                                <span class="text-xs text-slate-500 font-medium">/bln</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 flex flex-col items-center justify-center text-center">
                    <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">Tidak ada kost ditemukan</h3>
                    <p class="text-slate-500 mt-1 max-w-sm">Coba sesuaikan filter atau kata kunci pencarian Anda untuk menemukan kost yang sesuai.</p>
                    <button wire:click="$set('search', ''); $set('gender', ''); $set('district', ''); $set('price_min', ''); $set('price_max', '');" class="mt-4 px-4 py-2 bg-indigo-50 text-indigo-600 font-medium rounded-lg hover:bg-indigo-100 transition">Reset Filter</button>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $kosts->links() }}
        </div>
    </div>
</div>
