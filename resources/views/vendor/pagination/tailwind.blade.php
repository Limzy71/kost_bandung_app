@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Navigasi Halaman') }}" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <!-- Mobile Pagination Buttons -->
        <div class="flex items-center justify-between gap-2 sm:hidden w-full">
            @if ($paginator->onFirstPage())
                <span class="px-4 py-2 text-xs font-black uppercase text-zinc-400 bg-zinc-100 border-2 border-black/40 cursor-not-allowed rounded-lg">
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="px-4 py-2 text-xs font-black uppercase text-black bg-white hover:bg-yellow-100 border-2 border-black rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all">
                    Sebelumnya
                </a>
            @endif

            <span class="text-xs font-black text-black bg-yellow-300 border-2 border-black px-3 py-1.5 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="px-4 py-2 text-xs font-black uppercase text-black bg-white hover:bg-yellow-100 border-2 border-black rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all">
                    Selanjutnya
                </a>
            @else
                <span class="px-4 py-2 text-xs font-black uppercase text-zinc-400 bg-zinc-100 border-2 border-black/40 cursor-not-allowed rounded-lg">
                    Selanjutnya
                </span>
            @endif
        </div>

        <!-- Desktop Pagination -->
        <div class="hidden sm:flex sm:items-center sm:justify-between w-full gap-4">
            <!-- Results Counter Badge -->
            <div class="bg-white border-2 border-black px-4 py-2 rounded-lg shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] text-xs font-black uppercase text-black">
                Menampilkan 
                @if ($paginator->firstItem())
                    <span class="bg-yellow-300 border border-black px-1.5 py-0.5 rounded ml-0.5">{{ $paginator->firstItem() }}</span>
                    sampai
                    <span class="bg-yellow-300 border border-black px-1.5 py-0.5 rounded">{{ $paginator->lastItem() }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
                dari
                <span class="bg-cyan-300 border border-black px-1.5 py-0.5 rounded">{{ $paginator->total() }}</span>
                Properti Kost
            </div>

            <!-- Page Number Links -->
            <div class="flex items-center gap-2">

                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <span class="w-9 h-9 flex items-center justify-center text-xs font-black text-zinc-400 bg-zinc-100 border-2 border-black/40 cursor-not-allowed rounded-lg">
                        <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="w-9 h-9 flex items-center justify-center text-xs font-black text-black bg-white hover:bg-yellow-100 border-2 border-black rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all">
                        <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="w-9 h-9 flex items-center justify-center text-xs font-black text-black bg-white border-2 border-black rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                            {{ $element }}
                        </span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="w-9 h-9 flex items-center justify-center text-xs font-black text-black bg-yellow-400 border-2 border-black rounded-lg shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="w-9 h-9 flex items-center justify-center text-xs font-black text-black bg-white hover:bg-yellow-100 border-2 border-black rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="w-9 h-9 flex items-center justify-center text-xs font-black text-black bg-white hover:bg-yellow-100 border-2 border-black rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all">
                        <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @else
                    <span class="w-9 h-9 flex items-center justify-center text-xs font-black text-zinc-400 bg-zinc-100 border-2 border-black/40 cursor-not-allowed rounded-lg">
                        <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif
