<div>
    <!-- Filter Section (Floating) -->
    <div class="relative z-10 bg-white shadow-lg rounded-2xl p-4 border border-gray-100 mb-10">
        <div class="flex flex-col md:flex-row gap-4 items-center">
            <!-- Search Input -->
            <div class="w-full md:w-1/3 relative h-11">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama kost atau jalan..." class="w-full h-full pl-10 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all outline-none">
            </div>

            <!-- Filters -->
            <div class="w-full md:w-2/3 flex flex-wrap md:flex-nowrap gap-3 items-center">
                <select wire:model.live="gender" class="flex-1 min-w-[120px] h-11 py-2 px-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all outline-none">
                    <option value="">Semua Tipe</option>
                    <option value="putra">Putra</option>
                    <option value="putri">Putri</option>
                    <option value="campur">Campur</option>
                </select>

                <select wire:model.live="district" class="flex-1 min-w-[140px] h-11 py-2 px-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all outline-none">
                    <option value="">Semua Kecamatan</option>
                    @foreach($districts as $dist)
                        <option value="{{ $dist }}">{{ $dist }}</option>
                    @endforeach
                </select>

                <input wire:model.live.debounce.500ms="price_min" type="number" placeholder="Min Harga" class="flex-1 min-w-[110px] h-11 py-2 px-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all outline-none">
                <input wire:model.live.debounce.500ms="price_max" type="number" placeholder="Max Harga" class="flex-1 min-w-[110px] h-11 py-2 px-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all outline-none">
            </div>
        </div>
    </div>

    <!-- Grid List Kost -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 relative">
        <div wire:loading class="absolute inset-0 bg-white/50 backdrop-blur-sm z-10 rounded-2xl"></div>
        
        @forelse($kosts as $kost)
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-shadow duration-300 flex flex-col group relative">
                <!-- Thumbnail -->
                <div class="aspect-[4/3] bg-gray-200 relative overflow-hidden flex-shrink-0">
                    @if($kost->primaryImage)
                        <img src="{{ Storage::url($kost->primaryImage->image_path) }}" alt="{{ $kost->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-200 animate-pulse text-gray-400">
                            <svg class="w-12 h-12 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    @endif

                    <!-- Badges -->
                    <div class="absolute top-3 left-3 flex flex-col gap-2">
                        @if($kost->boosted_at)
                            <span class="px-3 py-1 bg-gradient-to-r from-amber-500 to-orange-500 shadow-md rounded-full text-xs font-semibold text-white flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.381z"/></svg>
                                Super
                            </span>
                        @endif
                        <span class="px-3 py-1 bg-white/95 backdrop-blur-sm rounded-full text-xs font-semibold text-gray-800 capitalize shadow-sm">
                            {{ $kost->gender_type }}
                        </span>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-bold text-purple-600 uppercase tracking-wider">{{ $kost->district }}</span>
                        <h2 class="text-lg font-bold text-gray-800 mt-1 line-clamp-1 group-hover:text-purple-600 transition-colors">
                            <a href="{{ route('kost.show', $kost->slug) }}" class="focus:outline-none">
                                <span class="absolute inset-0" aria-hidden="true"></span>
                                {{ $kost->name }}
                            </a>
                        </h2>
                        <p class="text-sm text-gray-500 mt-1 line-clamp-1 flex items-center gap-1">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="truncate">{{ $kost->address }}</span>
                        </p>
                        
                        <!-- Facilities Preview -->
                        @if($kost->facilities && $kost->facilities->count() > 0)
                        <div class="mt-3 flex flex-wrap gap-1 relative z-10">
                            @foreach($kost->facilities->take(3) as $facility)
                                <span class="text-[10px] font-medium px-2 py-1 bg-gray-100 text-gray-600 rounded-md truncate max-w-full">
                                    {{ $facility->name }}
                                </span>
                            @endforeach
                            @if($kost->facilities->count() > 3)
                                <span class="text-[10px] font-medium px-2 py-1 bg-gray-100 text-gray-600 rounded-md">
                                    +{{ $kost->facilities->count() - 3 }}
                                </span>
                            @endif
                        </div>
                        @endif
                    </div>
                    
                    <!-- Price -->
                    <div class="mt-5 pt-4 border-t border-gray-100 flex items-end justify-between">
                        <div>
                            <p class="text-xs text-gray-500 font-medium mb-0.5">Mulai dari</p>
                            <span class="text-lg font-extrabold text-gray-900 leading-none">Rp {{ number_format($kost->price_monthly, 0, ',', '.') }}</span>
                            <span class="text-xs text-gray-500 font-medium">/bln</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 flex flex-col items-center justify-center text-center">
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Tidak ada kost ditemukan</h3>
                <p class="text-gray-500 mt-1 max-w-sm">Coba sesuaikan filter atau kata kunci pencarian Anda untuk menemukan kost yang sesuai.</p>
                <button wire:click="$set('search', ''); $set('gender', ''); $set('district', ''); $set('price_min', ''); $set('price_max', '');" class="mt-4 px-4 py-2 bg-purple-50 text-purple-600 font-medium rounded-lg hover:bg-purple-100 transition relative z-10">Reset Filter</button>
            </div>
        @endforelse
    </div>

    <div class="mt-8 relative z-10">
        {{ $kosts->links() }}
    </div>
</div>
