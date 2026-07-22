@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Navigasi Halaman" class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <!-- Mobile Pagination Buttons -->
            <div class="flex items-center justify-between gap-2 sm:hidden w-full">
                @if ($paginator->onFirstPage())
                    <span class="px-4 py-2 text-xs font-black uppercase text-zinc-400 bg-zinc-100 border-2 border-black/40 cursor-not-allowed rounded-lg">
                        Sebelumnya
                    </span>
                @else
                    <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="px-4 py-2 text-xs font-black uppercase text-black bg-white hover:bg-yellow-100 border-2 border-black rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer">
                        Sebelumnya
                    </button>
                @endif

                <span class="text-xs font-black text-black bg-yellow-300 border-2 border-black px-3 py-1.5 rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                    {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
                </span>

                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="px-4 py-2 text-xs font-black uppercase text-black bg-white hover:bg-yellow-100 border-2 border-black rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer">
                        Selanjutnya
                    </button>
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
                    <span class="bg-yellow-300 border border-black px-1.5 py-0.5 rounded mx-0.5">{{ $paginator->count() }}</span>
                    dari
                    <span class="bg-cyan-300 border border-black px-1.5 py-0.5 rounded mx-0.5">{{ $paginator->total() }}</span>
                    Properti Kost
                    @if ($paginator->firstItem())
                        <span class="text-zinc-600 font-bold tracking-normal lowercase ml-1">
                            (no. {{ $paginator->firstItem() }} – {{ $paginator->lastItem() }})
                        </span>
                    @endif
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
                        <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="w-9 h-9 flex items-center justify-center text-xs font-black text-black bg-white hover:bg-yellow-100 border-2 border-black rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer" aria-label="Sebelumnya">
                            <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
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
                                <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}">
                                    @if ($page == $paginator->currentPage())
                                        <span class="w-9 h-9 flex items-center justify-center text-xs font-black text-black bg-yellow-400 border-2 border-black rounded-lg shadow-[3px_3px_0px_0px_rgba(0,0,0,1)]">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="w-9 h-9 flex items-center justify-center text-xs font-black text-black bg-white hover:bg-yellow-100 border-2 border-black rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer" aria-label="Ke Halaman {{ $page }}">
                                            {{ $page }}
                                        </button>
                                    @endif
                                </span>
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="w-9 h-9 flex items-center justify-center text-xs font-black text-black bg-white hover:bg-yellow-100 border-2 border-black rounded-lg shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer" aria-label="Selanjutnya">
                            <svg class="w-4 h-4 stroke-[3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
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
</div>
