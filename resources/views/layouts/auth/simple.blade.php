<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen antialiased bg-[#f8f9fa] bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] bg-[size:24px_24px]">

        {{-- ===== Neo-Brutalist Auth Top Bar ===== --}}
        <header class="bg-white border-b-4 border-black shadow-[0_4px_0_0_rgba(0,0,0,1)] sticky top-0 z-50">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
                {{-- Logo & Brand --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group" wire:navigate>
                    <span class="text-lg font-black text-black uppercase tracking-tight flex items-center leading-none">
                        KostBandung<span class="bg-[#FFE500] border-2 border-black px-1.5 py-0.5 rounded text-xs shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] ml-1 font-black text-black">.id</span>
                    </span>
                </a>

                {{-- Back to Home button --}}
                <a href="{{ route('home') }}" wire:navigate
                    class="inline-flex items-center gap-1.5 bg-white hover:bg-[#FFE500] text-black border-2 border-black font-black text-xs uppercase px-3.5 py-2 rounded-lg shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all cursor-pointer">
                    <x-icon name="lucide-chevron-left" class="w-4 h-4 stroke-[3]" />
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </header>
        {{-- ===== End Top Bar ===== --}}

        <div class="flex min-h-[calc(100vh-4rem)] flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-lg flex-col gap-6">
                {{ $slot }}
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
