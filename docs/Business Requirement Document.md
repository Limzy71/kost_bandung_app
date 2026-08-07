# Business Requirement Document (BRD)

## Platform Directory Kost Hyper-Local Bandung

Versi Dokumen: 1.1  
Status: Disesuaikan dengan implementasi aplikasi saat ini  
Target Lokasi: Kota Bandung

## 1. Pendahuluan & Tujuan Bisnis

### 1.1 Latar Belakang

Pasar pencarian kost di Kota Bandung sangat aktif, terutama di area kampus dan pusat aktivitas mahasiswa. Informasi kost masih banyak tersebar di grup media sosial, chat pribadi, dan platform umum yang tidak selalu memiliki data lokasi, harga, ketersediaan kamar, atau status verifikasi yang jelas.

Platform ini dibangun sebagai direktori kost hyper-local Bandung yang menghubungkan pencari kost, pemilik kost, dan administrator dalam satu sistem terverifikasi. Fokus utama produk adalah akurasi lokasi, transparansi informasi, keamanan data listing, komunikasi langsung, serta proses moderasi untuk mengurangi risiko listing palsu.

### 1.2 Tujuan Utama

- **Pencari Kost:** Menyediakan pencarian kost berbasis daftar dan peta interaktif yang cepat, mobile-first, dan fokus pada wilayah Kota Bandung.
- **Pemilik Kost:** Menyediakan portal self-service untuk mendaftarkan, mengedit, menghapus, dan memperbarui status ketersediaan kost.
- **Administrator:** Menyediakan panel moderasi untuk menjaga kualitas listing, memverifikasi identitas pemilik, memverifikasi dokumen kepemilikan kost, serta menangani pesan bantuan pengguna.
- **Tujuan Bisnis:** Membangun densitas data kost Bandung melalui pendaftaran gratis pada tahap awal, dengan fondasi monetisasi melalui prioritas listing/boost atau fitur premium di tahap berikutnya.

## 2. Cakupan Wilayah & Pembatasan

### 2.1 In-Scope

- Seluruh kecamatan dalam wilayah administratif Kota Bandung.
- Area sekitar kampus dan pusat aktivitas mahasiswa di Kota Bandung.
- Listing kost dengan titik koordinat yang berada dalam batas konfigurasi kecamatan Bandung.

### 2.2 Out-of-Scope

- Wilayah luar Kota Bandung seperti Kabupaten Bandung, Cimahi, Lembang, Soreang, dan wilayah sekitarnya.
- Transaksi pembayaran sewa kamar secara langsung di platform.
- Booking otomatis dengan pembayaran internal.

### 2.3 Geofencing

Sistem memvalidasi koordinat latitude dan longitude pada form pembuatan/edit kost berdasarkan konfigurasi batas wilayah kecamatan Bandung. Jika titik lokasi berada di luar batas kecamatan yang dipilih, penyimpanan listing ditolak.

## 3. Profil Pengguna

### 3.1 Pencari Kost

Pengguna yang mencari kost berdasarkan lokasi, harga, tipe hunian, periode sewa, status ketersediaan, dan status verifikasi. Pencari kost dapat menghubungi pemilik melalui chat internal dan dapat menghubungi admin melalui fitur bantuan.

### 3.2 Pemilik Kost

Mitra penyedia kost yang mendaftarkan data properti, mengelola foto, fasilitas, aturan, harga, periode sewa, dokumen kepemilikan, dan status ketersediaan kamar. Pemilik juga menerima dan membalas chat dari pencari kost.

### 3.3 Administrator

Pengelola internal platform yang bertanggung jawab melakukan moderasi listing, approval/rejection fasilitas custom, verifikasi identitas pemilik, verifikasi dokumen kepemilikan kost, dan pengelolaan inbox bantuan admin.

## 4. Persyaratan Fitur Utama

### 4.1 Modul Pencarian Kost Publik

- Halaman beranda menampilkan listing kost yang sudah berstatus `published` dan tersedia.
- Filter pencarian meliputi kata kunci, tipe gender (`putra`, `putri`, `campur`), kecamatan, rentang harga, periode sewa, dan filter hanya terverifikasi.
- Listing diprioritaskan berdasarkan `boosted_at` jika fitur boost aktif pada data.
- Peta interaktif menampilkan marker kost menggunakan Google Maps API.
- Data marker memuat nama, slug, kecamatan, alamat, tipe gender, harga, koordinat, gambar utama, dan status boost.
- Sistem menggunakan eager loading untuk relasi gambar utama, pemilik, dan fasilitas agar performa listing tetap baik.

### 4.2 Modul Detail Kost

- Menampilkan informasi lengkap kost: nama, deskripsi, alamat, kecamatan, tipe gender, harga utama, periode sewa, harga deposit, fasilitas, aturan, foto, lokasi, dan kontak.
- Kost non-published hanya dapat dilihat oleh admin atau pemilik kost terkait.
- Pencari kost login dapat memulai chat ke pemilik melalui detail kost.
- Pemilik tidak dapat mengirim pesan ke listing miliknya sendiri.
- Chat awal dilindungi rate limiter untuk mengurangi spam.
- Kost penuh atau non-published tidak menerima pesan baru.

### 4.3 Modul Portal Pemilik Kost

- Pemilik wajib login, email verified, dan memiliki role owner untuk mengakses dashboard.
- Dashboard menampilkan total properti, jumlah kamar tersedia, jumlah pesan masuk, daftar kost, dan pencarian internal.
- Pemilik dapat membuat, mengedit, menghapus permanen, dan mengubah status ketersediaan kost.
- Aksi edit dan delete dilindungi ownership check berdasarkan `auth()->id() === kost.user_id` atau query melalui relasi kost milik user.
- Form kost mendukung:
  - nama, deskripsi, alamat, kecamatan, koordinat peta;
  - tipe gender;
  - harga bulanan/utama dan periode sewa (`daily`, `weekly`, `monthly`, `three_monthly`, `six_monthly`, `yearly`);
  - harga deposit dan opsi include utilities;
  - total kamar dan kamar tersedia;
  - WhatsApp kontak;
  - landmark terdekat;
  - fasilitas standar dan fasilitas custom;
  - aturan standar dan aturan custom;
  - catatan aturan tambahan;
  - upload 4-10 foto;
  - pemilihan foto utama;
  - upload dokumen kepemilikan kost.
- Input teks disanitasi saat penyimpanan untuk mengurangi risiko XSS tersimpan.
- Pembuatan kost dilindungi rate limiter.

### 4.4 Modul Chat Pencari Kost & Pemilik

- Pencari kost dan pemilik memiliki dashboard chat masing-masing.
- Percakapan dibuat per kombinasi kost dan pencari.
- Pesan memiliki status terbaca (`read_at`) dan badge jumlah pesan belum dibaca.
- Pengguna dapat mengarsipkan percakapan dari sisi masing-masing.
- Pengiriman pesan dilindungi validasi server-side dan rate limiter.
- Akses percakapan dilindungi ownership check:
  - pencari hanya melihat percakapan dengan `seeker_id` miliknya;
  - pemilik hanya melihat percakapan pada kost yang dimilikinya.

### 4.5 Modul Profil, Keamanan Akun, dan Verifikasi

- Pengguna dapat mengelola profil, avatar, nomor WhatsApp, dan data bisnis untuk pemilik.
- Perubahan email memicu reset verifikasi email dan notifikasi perubahan email.
- Sistem mendukung email verification.
- Sistem mendukung two-factor authentication (2FA) Fortify dengan konfirmasi password.
- Sistem mendukung passkeys/WebAuthn dengan konfirmasi password.
- Pemilik dapat mengunggah dokumen identitas dan dokumen kepemilikan kost untuk diverifikasi admin.
- Dokumen verifikasi disimpan pada disk privat dan hanya dapat diakses oleh admin melalui route khusus.

### 4.6 Modul Admin & Moderasi

- Admin wajib login, email verified, dan memiliki role admin.
- Admin dapat melihat dashboard moderasi dengan tab pending, published, rejected, all, facilities, dan verification.
- Admin dapat approve/reject listing kost.
- Admin dapat approve/reject fasilitas custom.
- Admin dapat approve/reject verifikasi identitas pemilik.
- Admin dapat approve/reject verifikasi kepemilikan kost.
- Rejection dapat menyimpan catatan alasan penolakan.
- Admin dapat membuka dokumen verifikasi melalui route admin yang dilindungi middleware.

### 4.7 Modul Hubungi Admin

- Pengguna non-admin yang login dan verified dapat membuka percakapan bantuan dengan kategori `komplain`, `pertanyaan`, `masukan`, atau `lainnya`.
- Admin tidak dapat menggunakan halaman Hubungi Admin sebagai pengirim.
- Setiap pesan user mengisi atau memperbarui `awaiting_reply_at`.
- Percakapan yang tidak dibalas admin dalam 24 jam otomatis ditutup sebagai expired.
- Admin memiliki inbox bantuan dengan filter belum dibalas, open, dan history.
- Admin dapat membalas, menutup, dan soft delete percakapan yang sudah ditutup.
- Percakapan soft-deleted dipertahankan selama 30 hari sebelum dipruning permanen.

## 5. Model Bisnis & Monetisasi

### 5.1 Tahap Akuisisi

- Pendaftaran akun dan listing dasar gratis.
- Prioritas awal adalah membangun supply density kost Bandung dan meningkatkan kepercayaan melalui moderasi serta verifikasi.

### 5.2 Tahap Monetisasi

- Fondasi data sudah mendukung listing priority melalui kolom `boosted_at`.
- Fitur pembayaran/aktivasi boost belum menjadi transaksi internal penuh pada versi saat ini.
- Monetisasi lanjutan dapat berupa paket boost manual, badge rekomendasi, atau promosi berbayar setelah traffic dan supply stabil.

## 6. Persyaratan Non-Fungsional

### 6.1 Keamanan

- Route sensitif dilindungi middleware `auth`, `verified`, `owner`, dan `admin`.
- Aksi pemilik dilindungi ownership check untuk mencegah IDOR.
- Form menggunakan CSRF Laravel/Livewire dan validasi server-side.
- Input teks penting disanitasi saat penyimpanan.
- Password menggunakan hashing Laravel.
- Login, register, chat, dan pembuatan kost memiliki rate limiting sesuai konteks.
- Dokumen verifikasi disimpan privat.

### 6.2 Performa

- Listing menggunakan eager loading untuk relasi utama.
- Dashboard menggunakan pagination untuk daftar kost dan inbox admin.
- Build frontend diproses melalui Vite/Tailwind.
- Production wajib menggunakan cache config, route, view, dan event.

### 6.3 UX & Aksesibilitas

- Desain mobile-first dengan gaya Neo-Brutalism dan dukungan dark mode.
- Komponen UI konsisten menggunakan Blade components dan styling Tailwind.
- Pesan error validasi ditampilkan dalam bahasa Indonesia.
- Alur dashboard pemilik dan admin dibuat berbasis tab/filter agar mudah dipahami.

## 7. Batasan Versi Saat Ini

- Belum ada pembayaran online atau transaksi booking internal.
- Belum ada OTP WhatsApp; WhatsApp digunakan sebagai nomor kontak dan field profil.
- Belum ada analitik tayangan/klik listing yang lengkap.
- Fitur boost sudah memiliki fondasi pengurutan, tetapi proses pembelian/aktivasi otomatis belum menjadi modul transaksi penuh.
- PWA/offline mode belum menjadi fokus implementasi utama.

## 8. Kriteria Kesiapan Production

- Semua route sensitif harus tetap terlindungi middleware role dan verified.
- `.env` production wajib menggunakan `APP_ENV=production`, `APP_DEBUG=false`, domain production, database production, mailer valid, dan secret yang tidak tersimpan di git.
- Jalankan `composer lint:check`, `composer types:check`, `php artisan test`, dan `npm run build` sebelum deployment.
- Jalankan `php artisan migrate --force` dan `php artisan optimize` di server production.
