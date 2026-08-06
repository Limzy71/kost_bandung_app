@props(['status' => ''])

@php
    $config = [
        'published' => ['label' => 'Tayang', 'class' => 'bg-lime-300 text-black'],
        'pending' => ['label' => 'Menunggu', 'class' => 'bg-yellow-300 text-black'],
        'rejected' => ['label' => 'Ditolak', 'class' => 'bg-rose-400 text-black'],
        'unread' => ['label' => 'Belum dibaca', 'class' => 'bg-rose-400 text-black'],
        'read' => ['label' => 'Sudah dibaca', 'class' => 'bg-cyan-300 text-black'],
        'archived' => ['label' => 'Diarsipkan', 'class' => 'bg-zinc-200 dark:bg-zinc-700 dark:text-zinc-100'],
        'available' => ['label' => 'Tersedia', 'class' => 'bg-lime-300 text-black'],
        'full' => ['label' => 'Penuh', 'class' => 'bg-zinc-300 dark:bg-zinc-600 dark:text-zinc-100'],
    ];

    $item = $config[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-zinc-200 dark:bg-zinc-700 dark:text-zinc-100'];
@endphp

<span class="px-2.5 py-1 {{ $item['class'] }} border-2 border-black dark:border-zinc-600 text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] dark:shadow-[2px_2px_0px_0px_rgba(255,255,255,0.25)] inline-flex items-center gap-1">
    {{ $item['label'] }}
</span>
