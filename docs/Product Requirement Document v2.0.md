# **PRODUCT REQUIREMENT DOCUMENT (PRD)** 

## **Nama Produk: Platform Pencarian & Manajemen Kost Hyper-Local Bandung** 

Platform: Web Application (Mobile-Responsive / PWA) | Versi Dokumen: 2.0 (Updated Stack) 

## **1. Visi & Lingkup Produk** 

Membangun platform direktori kost berbasis peta interaktif yang berfokus secara eksklusif di wilayah administratif Kota Bandung. Sistem dirancang untuk memberikan kemudahan bagi mahasiswa dalam mencari dan menghubungi pemilik kost, serta menyediakan portal _self-service_ yang intuitif bagi pemilik properti. Pengembangan menggunakan arsitektur monolith modern untuk memastikan kecepatan _development_ , kinerja SEO yang unggul, dan kemudahan skalabilitas. 

## **2. Arsitektur Sistem & Tech Stack** 

Sistem dikembangkan menggunakan perpaduan teknologi yang mengutamakan kecepatan interaksi (layaknya _Single Page Application_ ) namun tetap mempertahankan keunggulan _Server-Side Rendering_ (SSR).

|**Lapisan Sistem**|**Teknologi**<br>**Pilihan**|**Peran & Fungsi Utama**|
|---|---|---|
|Back-end & Core<br>Engine|Laravel 12|Menangani logika bisnis, database, ru-<br>te, dan autentikasi.|
|Interaktivitas &<br>Logika UI|Laravel Livewire<br>3|Menjalankan pencarian, flter, dan pro-<br>ses form tanpa_page reload_.|
|Interaksi Klien &|Alpine.js +|Menangani_drag-and-drop_pin peta, mo-|
|Peta|Google Maps API|dal, dan_dropdown_di_browser_.|
|Styling &|Tailwind CSS|Menyediakan utilitas desain responsif|
|Tampilan|v4.1|dengan pendekatan_mobile-frst_.|
|Lingkungan|Laravel Herd|Menyediakan server lokal yang sangat|
|Pengembangan|(Windows)|cepat, bersih, dan terisolasi.|
|Database|PostgreSQL /<br>MySQL|Menyimpan relasi entitas pengguna,<br>data kost, dan transaksi ftur sundul.|



## **3. Profil Pengguna (User Personas)** 

1 

- **Pencari Kost (Mahasiswa):** Pengguna utama, khususnya mahasiswa Teknik Informatika atau fakultas lainnya yang membutuhkan kost strategis, memprioritaskan fitur pencarian berbasis lokasi, filter harga, dan kejelasan status kamar. 

- **Pemilik Kost (** **_Landlord_ ):** Pengguna mitra yang membutuhkan portal sederhana tanpa kerumitan teknis untuk mendaftarkan properti, memperbarui ketersediaan kamar, dan memanfaatkan fitur promosi. 

- **Administrator Web:** Pengelola sistem yang bertugas memoderasi iklan baru, mencegah penipuan, dan memantau performa pendapatan dari fitur promosi. 

## **4. User Stories (Kebutuhan Pengguna)** 

- Sebagai mahasiswa pencari kost, saya ingin memfilter daftar kost menggunakan Livewire tanpa harus memuat ulang halaman agar proses pencarian lebih cepat. 

- Sebagai mahasiswa pencari kost, saya ingin melihat informasi kost yang dirender di sisi server agar tautan yang saya bagikan memunculkan _preview_ (SEO/Open Graph) yang akurat. 

- Sebagai pemilik kost, saya ingin menandai lokasi properti saya di Google Maps secara presisi agar mahasiswa mudah menemukan alamat saya. 

- Sebagai pemilik kost, saya ingin menekan satu tombol _toggle_ untuk mengubah status sisa kamar agar data selalu _up-to-date_ . 

- Sebagai admin, saya ingin meninjau draf pendaftaran kost baru dan menyetujuinya sebelum tampil di halaman utama untuk menjaga keamanan platform. 

## **5. Spesifikasi Fungsional Utama** 

## **Modul Pemetaan & Geofencing** 

- **Fungsi** **_Bounding Box_ :** Alpine.js akan menangkap koordinat dari Google Maps API dan mengirimkannya ke Livewire untuk divalidasi apakah titik tersebut berada di dalam batas Kota Bandung. 

- **_Clustering Marker_ :** Menyatukan pin penanda kost yang berdekatan menjadi satu indikator angka agar tampilan peta tidak menumpuk saat _zoomout_ . 

## **Manajemen Iklan Kost (Listing)** 

2 

- **Alur Moderasi Draf:** Kost yang baru didaftarkan masuk dengan status _Pending Review_ dan baru dapat diakses publik setelah admin mengubah statusnya menjadi _Published_ . 

- **Form Ketersediaan Dinamis:** Komponen antarmuka yang memungkinkan pemilik menginput sisa jumlah kamar spesifik atau sekadar menyalakan opsi “Masih Ada/Penuh” menggunakan komponen _toggle_ . 

## **Modul Komunikasi (Dual Interaksi)** 

- **Tanya via WhatsApp:** Tombol CTA yang melakukan _redirect_ otomatis ke aplikasi WhatsApp pemilik dengan membawa parameter teks sapaan dan URL kost terkait. 

- **Kirim Pesan /** **_Booking Internal_ :** Fitur modal _pop-up_ berbasis Alpine.js yang memungkinkan calon penyewa mengirim pesan langsung ke _dashboard_ sistem pemilik kost. 

## **Modul Fitur Sundul (Iklan Premium)** 

- **Mekanisme** **_Boost_ :** Sistem memperbarui data _timestamp_ khusus di database saat pemilik kost berhasil mengaktifkan paket iklan. 

- **Logika Pengurutan Pencarian:** _Query builder_ Laravel akan secara otomatis memprioritaskan properti dengan status _boost_ aktif untuk tampil di urutan teratas hasil pencarian Livewire. 

## **6. Persyaratan Non-Fungsional & SEO** 

- **Optimalisasi Mesin Pencari (SEO):** Detail halaman kost di-render menggunakan sintaks Blade standar untuk memastikan Google Bot dapat mengindeks nama kost, fasilitas, dan harga secara instan. 

- **Performa & Kecepatan Load:** Penggunaan Tailwind v4.1 mengeliminasi file CSS yang membengkak, memastikan skor performa _Core Web Vitals_ yang tinggi di perangkat seluler. 

- **Keamanan Form:** Seluruh pengiriman data dari form pendaftaran dan _login_ dilindungi oleh proteksi CSRF bawaan Laravel dan validasi sisi server. 

## **7. Strategi Peluncuran (** **_Rollout Phase_ )** 

- **Fase 1 (Pilot Project Lokasi Spesifik):** Validasi sistem secara _hyper-local_ dengan mengakuisisi data kost di sekitar kampus prioritas, seperti lingkungan area Universitas Pasundan, untuk memastikan geofencing dan manajemen database berjalan tanpa _bug_ . 

3 

- **Fase 2 (Ekspansi Kota Bandung):** Membuka pendaftaran publik secara masif melalui pemasaran komunitas dan grup mahasiswa se-Bandung untuk mengumpulkan _density_ (kepadatan data). 

- **Fase 3 (Monetisasi Aktif):** Mengaktifkan modul pembelian Iklan Premium (“Sundul Iklan”) bagi pemilik kost setelah mendapatkan _traffic_ organik yang stabil. 

4 