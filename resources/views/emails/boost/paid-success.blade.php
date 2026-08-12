<x-mail::message>
# Halo {{ $ownerName }},

Kabar baik! Pembayaran Boost Kost untuk **{{ $kostName }}** telah kami terima dan dikonfirmasi.

**Detail Transaksi:**
- Order ID: {{ $orderId }}
- Metode: {{ $paymentMethod }}
- Jumlah: {{ $amount }}
- Tanggal: {{ $paymentDate }}

Boost Kost Anda kini aktif hingga **{{ $expiryDate }}**.
Selama masa aktif ini, kost Anda akan mendapatkan prioritas tampilan di halaman pencarian KostBandung.web.id.

<x-mail::button :url="route('dashboard')">
Kelola Kost Saya
</x-mail::button>

Terima kasih telah menggunakan layanan Boost Kost.
Kami berharap kost Anda semakin mudah ditemukan pencari kost.

Salam hangat,<br>
Tim KostBandung.web.id
</x-mail::message>
