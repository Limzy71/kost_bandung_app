<x-mail::message>
# Halo {{ $ownerName }},

Pengajuan perubahan data utama untuk kost **{{ $kostName }}** **tidak disetujui** oleh admin.

@if($note)
**Alasan penolakan:** {{ $note }}
@endif

Anda dapat mengajukan perubahan kembali melalui menu **Edit Properti** di dashboard.

Salam hangat,<br>
Tim KostBandung
</x-mail::message>
