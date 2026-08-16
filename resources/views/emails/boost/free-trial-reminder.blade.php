<x-mail::message>
# Halo {{ $ownerName }},

Masa percobaan gratis Boost Kost untuk **{{ $kostName }}** akan berakhir besok, pada {{ $expiryDate }}.

Setelah masa percobaan berakhir, kost Anda akan kembali ke urutan listing reguler dan tidak lagi mendapatkan prioritas di hasil pencarian.

Untuk menjaga posisi prioritas kost Anda, perpanjang Boost Kost selama 30 hari dengan biaya {{ $price }}.

<x-mail::button :url="route('dashboard')">
Perpanjang Boost Kost
</x-mail::button>

Terima kasih telah mencoba layanan Boost Kost kami.
Kami berharap fitur ini membantu meningkatkan visibilitas kost Anda.

Salam hangat,<br>
Tim KostBandung
</x-mail::message>
