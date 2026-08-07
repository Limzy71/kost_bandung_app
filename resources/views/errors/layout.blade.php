<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <title>@yield('title') · {{ config('app.name', 'KostBandung') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('partials.appearance')
    </head>
    <body class="min-h-screen antialiased bg-[#f8f9fa] bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] bg-[size:24px_24px] dark:bg-zinc-950 dark:bg-[linear-gradient(to_right,#27272a_1px,transparent_1px),linear-gradient(to_bottom,#27272a_1px,transparent_1px)]">

        <div class="flex min-h-screen flex-col items-center justify-center gap-8 p-6 md:p-10">

            {{-- Brand --}}
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                <span class="text-xl font-black text-black uppercase tracking-tight flex items-center leading-none dark:text-white">
                    KostBandung<span class="bg-[#FFE500] border-2 border-black px-1.5 py-0.5 rounded text-xs shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] ml-1 font-black text-black dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">.web.id</span>
                </span>
            </a>

            {{-- ===== Neo-Brutalist Error Card ===== --}}
            <div class="bg-white border-4 border-black p-8 md:p-10 shadow-[12px_12px_0px_0px_rgba(0,0,0,1)] rounded-lg w-full max-w-lg dark:bg-zinc-900 dark:border-zinc-700 dark:shadow-[12px_12px_0px_0px_rgba(255,255,255,0.25)]">

                {{-- Code Badge --}}
                <div class="inline-flex items-center gap-2 bg-[#FFE500] border-2 border-black px-4 py-1.5 rounded shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] mb-4 dark:border-zinc-700 dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)]">
                    <span class="text-xs font-black uppercase tracking-widest text-black">Error @yield('code')</span>
                </div>

                {{-- Title --}}
                <h1 class="text-3xl sm:text-4xl font-black text-black uppercase tracking-tight leading-tight dark:text-white">
                    @yield('title')
                </h1>

                {{-- Message --}}
                <p class="mt-3 text-sm font-bold text-zinc-600 dark:text-zinc-400">
                    @yield('message')
                </p>

                {{-- Divider --}}
                <div class="border-t-4 border-black my-8 dark:border-zinc-700"></div>

                {{-- Home Button --}}
                <a href="{{ url('/') }}"
                    class="w-full py-4 px-6 bg-[#FFE500] hover:bg-yellow-400 text-black border-4 border-black font-black text-sm uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] active:translate-x-1 active:translate-y-1 active:shadow-none transition-all duration-75 rounded-lg flex items-center justify-center gap-2 cursor-pointer dark:border-zinc-600 dark:shadow-[4px_4px_0px_0px_rgba(255,255,255,0.25)] dark:hover:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)]">
                    <x-icon name="lucide-chevron-left" class="w-5 h-5 stroke-[3]" />
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </body>
</html>
