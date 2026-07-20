<div>
    <h1>{{ $kost->name }}</h1>
    <p>Harga: Rp {{ number_format($kost->price_monthly, 0, ',', '.') }} / bulan</p>
    <p>Deskripsi: {{ $kost->description }}</p>

    <h3>Fasilitas:</h3>
    <ul>
        @foreach($kost->facilities as $facility)
            <li>{{ $facility->name }}</li>
        @endforeach
    </ul>

    <a href="{{ route('home') }}">← Kembali</a>
</div>