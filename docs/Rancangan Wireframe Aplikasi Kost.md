## Rancangan Wireframe Menyeluruh: Sisi Pencari Kost dan Pemilik Kost

Rancangan wireframemenyeluruh (teks dan struktur visual) untukkedua sisi peng- guna: Pencari Kost (Mahasiswa) dan Pemilik Kost (Mitra). Rancangan ini menggunak- an pendekatan mobile-first (berbasis Tailwind CSS) dengan antarmuka yang modern,

bersih, dan fungsional.

## [Mobile] 1. WIREFRAME: SISI PENCARI KOST (MAHASISWA)

Fokus utama di sisi ini adalah kecepatan pencarian, eksplorasi peta, dan konversi

(menghubungi pemilik).

- A. Halaman Beranda & Eksplorasi (Search & Map View)

Halaman ini dibagi menjadi dua mode tampilan yang bisa diubah pengguna di layar

mobile, dan tampil berdampingan di desktop.

- \- [Bagian Header / Navigasi Atas]

(Kiri) Logo Platform. (Tengah) Kolom Pencarian Cepat: “Cari nama kost atau jalan...”

(Kanan) Tombol “Masuk/Daftar” & “Iklankan Kost”.

- \- [Bagian Filter Bar - Sticky (Menempel saat di-scroll)]

Dropdown Tipe Kost: [Semua] | [Putra] | [Putri] | [Campur] Dropdown Rentang Harga: [Min Harga] - [Max Harga] Tombol Filter Lanjutan: [Fasilitas & Aturan Khusus] → Membuka modal pop-up.

Toggle View (Khusus Mobile): [Lihat Daftar] ↔ [Lihat Peta]

- \- [Area Konten Kiri: Daftar Kost (List View)]

Card Kost 1 (Berlabel Sundul/Iklan - Warna Vibrant/Mencolok) [Thumbnail Foto Utama] Tag: [Bintang] Pilihan Super | [Tag] Kost Putra Judul: Kost Lengkong Besar 10A Lokasi: 500m dari Unpas Harga: Rp 1.200.000 / bulan Fasilitas Ringkas: [Sinyal] WiFi | [AC] AC | [Shower] KM Dalam. Card Kost 2, 3, dst. (Organik - Struktur sama seperti Card 1)

- \- [Area Konten Kanan: Peta Interaktif (Map View)]

Tampilan Google Maps area Bandung. Marker/Pin: Berupa kotak kecil berisi harga (Misal: “1.2Jt”). Jika pin diklik → Muncul Pop-up Card kecil berisi foto dan nama kost.


## B. Halaman Detail Kost

Halaman ini adalah penentu sebelum mahasiswa menekan tombol hubungi.

- \- [Header Galeri Foto]

1 Foto Sampul Besar (Penuh) di atas. 3 Foto Kecil (Thumbnail) di bawahnya dengan tombol efek overlay “+5 Foto Lain-

nya”.

- \- [Area Konten Utama]

## Bagian Judul & Harga:

[Badge Tipe Kost: Putri] Judul: Kost Setiabudi Asri Harga: Rp 1.500.000 / bulan

Status Ketersediaan: [Tersedia] Sisa 2 Kamar

## Bagian Lokasi & Jarak:

Teks alamat lengkap.

[Peta Mini] dengan radius lingkaran.

## Bagian Deskripsi:

Teks paragraf dari pemilik kost.

## Bagian Fasilitas (Clean Grid 2 Kolom):

[Icon WiFi] WiFi Kencang [Icon Kunci] Akses 24 Jam

[Icon AC] AC

## Bagian Aturan Kost (List Vertikal dengan Icon):

[Icon Silang] Dilarang bawa hewan.

[Icon Jam] Ada Jam Malam (22.00 WIB).

- \- [Floating Action Bar - Sticky di Layar Bawah]

Tombol Primer (Hijau): “Tanya via WhatsApp”

Tombol Sekunder (Garis Tepi): “Kirim Pesan Internal”

## [Desktop] 2. WIREFRAME: SISI PEMILIK KOST (DASHBOARD ADMIN)

Fokus utama di sisi ini adalah manajemen data yang mudah dengan tata letak dashbo- ard yang terstruktur rapi, metrik yang jelas, dan kemudahan navigasi.

- A. Struktur Rangka Global (Layout)

- \- [Top Bar / Header]:

Hamburger Menu (Untuk menyembunyikan/menampilkan sidebar). Kotak Pencarian Global. Ikon Lonceng Notifikasi & Foto Profil Pemilik.


- \- [Sidebar Kiri - Fixed/Terkunci]:

Menampilkan deretan menu dengan ikon: [Beranda] Ringkasan (Dashboard) [Gedung] Kelola Kost Saya [Pesan] Pesan Masuk [Roket] Sundul Iklan (Premium)

[Pengaturan] Pengaturan Profil

- B. Halaman Ringkasan (Dashboard Utama)

- \- [Statistik Utama - Clean Grid 4 Kolom]

Kartu 1: Total Kost (Angka tebal, misal: 2 Properti) Kartu 2: Kamar Tersedia (Warna vibrant hijau, misal: 5 Kamar) Kartu 3: Total Dilihat (Angka kunjungan bulan ini)

Kartu 4: Pesan Masuk (Angka notifikasi yang belum dibaca)

- \- [Aktivitas Terbaru]

Daftar singkat (list) riwayat seperti: “Kost Lengkong berhasil dipublikasikan admin.”

- C. Halaman Kelola Kost (Properti Saya)

- \- [Header Area]

Judul Halaman: “Daftar Properti Kost”

Tombol CTA Utama (Warna Kontras/Vibrant): “+ Tambah Kost Baru”

- \- [Tabel Data / List View]

Kolom Tabel: Foto | Nama Kost | Alamat | Tipe | Harga | Status Kamar | Aksi Baris Data: [Thumbnail] | Kost Lengkong | Jl. Lengkong... | Putra | Rp1.2Jt | [Aktif] Toggle:

Tersedia/Penuh | [Edit] [Hapus]

- D. Halaman Form “Tambah/Edit Kost”

- \- [Form Informasi Dasar]

Input: Nama Kost Dropdown: Tipe Kost (Putra/Putri/Campur) Input: Harga per Bulan (Hanya Angka)

- \- [Form Lokasi & Peta Interaktif]

Input Teks: Alamat Lengkap. Peta Google Maps (Area Lebar): “Geser Pin (Marker) merah ini ke titik persis kost Anda.” (Berjalan dengan Alpine.js untuk menangkap latitude/longitude).


- \- [Form Fasilitas & Aturan]

Deretan Checkbox Master Fasilitas (WiFi, AC, dll). Deretan Checkbox Master Aturan (Jam Malam, Dilarang Bawa Hewan, dll).

Text Area: “Aturan Tambahan (Opsional)”.

- \- [Area Upload Galeri]

Kotak unggah drag-and-drop besar. Tampilan pratinjau (preview) foto-foto yang sudah dipilih. Opsi “Jadikan Foto Uta-

ma” pada salah satu gambar.

- \- [Tombol Submit]

Tombol “Simpan & Ajukan Publikasi” (Di-submit menggunakan Livewire tanpa me-

muat ulang layar).
