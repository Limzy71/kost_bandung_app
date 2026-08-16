<x-mail::message>
# Halo {{ $ownerName }},

Kami menginformasikan bahwa masa aktif Boost Kost untuk **{{ $kostName }}** akan berakhir dalam 3 hari, tepatnya pada {{ $expiryDate }}.

Agar kost Anda terus mendapatkan prioritas tampilan di hasil pencarian, lakukan perpanjangan sebelum masa aktif berakhir.

Biaya perpanjangan: {{ $price }} untuk 30 hari aktif.

<x-mail::button :url="route('dashboard')">
Perpanjang Sekarang
</x-mail::button>

Jika Anda memiliki pertanyaan, jangan ragu untuk menghubungi kami melalui halaman Hubungi Admin di aplikasi kami.

Terima kasih,<br>
Tim KostBandung
</x-mail::message>
