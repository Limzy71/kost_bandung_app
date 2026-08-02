@props(['status' => ''])

@php
    $config = [
        'published' => ['label' => 'Tayang', 'class' => 'bg-lime-300'],
        'pending' => ['label' => 'Menunggu', 'class' => 'bg-yellow-300'],
        'rejected' => ['label' => 'Ditolak', 'class' => 'bg-rose-400'],
        'unread' => ['label' => 'Belum dibaca', 'class' => 'bg-rose-400'],
        'read' => ['label' => 'Sudah dibaca', 'class' => 'bg-cyan-300'],
        'archived' => ['label' => 'Diarsipkan', 'class' => 'bg-zinc-200'],
        'available' => ['label' => 'Tersedia', 'class' => 'bg-lime-300'],
        'full' => ['label' => 'Penuh', 'class' => 'bg-zinc-300'],
    ];

    $item = $config[$status] ?? ['label' => ucfirst($status), 'class' => 'bg-zinc-200'];
@endphp

<span class="px-2.5 py-1 {{ $item['class'] }} border-2 border-black text-[10px] font-black uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] inline-flex items-center gap-1">
    {{ $item['label'] }}
</span>
