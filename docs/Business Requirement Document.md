# **BUSINESS REQUIREMENT DOCUMENT (BRD)** 

## **Platform Pencarian & Manajemen Kost Hyper-Local Bandung** 

Versi Dokumen: 1.0 | Target Lokasi: Kota Bandung 

## **1. Pendahuluan & Tujuan Bisnis (Business Goals)** 

## **1.1 Latar Belakang** 

Pasar pencarian kost di area sekitar kampus Kota Bandung memiliki volume yang sangat besar, namun saat ini informasinya masih sangat tersebar di berbagai grup media sosial dan platform tidak resmi. Di sisi lain, pemilik kost membutuhkan platform yang efisien dan terarah untuk memasarkan kamar mereka. Sementara itu, mahasiswa sebagai pencari kost sangat membutuhkan transparansi data terkait lokasi presisi, ketersediaan kamar secara berkala, serta kemudahan untuk menghubungi langsung pihak pemilik tanpa perantara yang rumit. 

## **1.2 Tujuan Utama (Objectives)** 

Untuk memfasilitasi kebutuhan pasar tersebut, platform ini menetapkan tiga pilar tujuan utama: 

- **Pencari Kost (Mahasiswa):** Menyediakan platform pencarian berbasis peta interaktif ( _interactive map_ ) yang akurat, cepat, transparan, dan terfokus pada wilayah Kota Bandung. 

- **Pemilik Kost (Mitra):** Menyediakan portal pendaftaran _self-service_ yang praktis dan mudah digunakan untuk mengelola detail properti serta status ketersediaan kamar. 

- **Tujuan Bisnis:** Membangun densitas ( _density_ ) data kost yang tinggi di Kota Bandung melalui strategi pendaftaran tanpa biaya (gratis) di tahap awal, sekaligus memvalidasi model bisnis melalui fitur Iklan Premium (Sundul). 

## **2. Cakupan Wilayah & Pembatasan (Scope & Geofencing)** 

- **_In-Scope_ :** Seluruh wilayah administratif Kota Bandung. Hal ini mencakup seluruh area sekitar kampus-kampus besar di Bandung, seperti Universitas Pasundan (Unpas), Institut Teknologi Bandung (ITB), Universitas Padjadjaran (Unpad) Dipatiukur, Telkom University (area kota), Universitas Katolik Parahyangan (Unpar), dan universitas lain di dalam batas kota. 

- **_Out-of-Scope_ :** Seluruh wilayah yang berada di luar batas administratif resmi Kota Bandung, seperti Soreang, Kabupaten Bandung, Cimahi, Lembang, 

1 

dan sekitarnya. 

- **Teknis** **_Geofencing_ :** Sistem akan secara otomatis melakukan validasi koordinat geografis ( _Latitude_ dan _Longitude_ ) pemilik kost saat mendaftarkan propertinya. Validasi menggunakan parameter batas kotak ( _Bounding Box_ ) resmi Kota Bandung untuk memastikan penanda lokasi berada di dalam cakupan wilayah yang ditentukan. 

## **3. Profil Pengguna (User Personas)** 

Dokumen ini mendefinisikan tiga aktor utama yang akan berinteraksi di dalam sistem: 

1. **Pencari Kost (Mahasiswa):** Pengguna aktif yang membutuhkan hunian sementara. Kriteria pencarian mereka didasarkan pada kedekatan lokasi kampus, kesesuaian harga, ketersediaan fasilitas penunjang, dan kemudahan dalam membangun komunikasi dengan pemilik. 

2. **Pemilik Kost (** **_Landlord_ ):** Mitra penyedia properti yang bertanggung jawab mendaftarkan data kost, memperbarui jumlah kamar kosong, serta merespons pesan atau minat dari calon penghuni. 

3. **Administrator Web:** Tim internal pengelola platform yang bertugas memverifikasi kelayakan data kost, memitigasi risiko penipuan, mengelola fitur iklan premium, dan memantau stabilitas aktivitas sistem secara keseluruhan. 

## **4. Persyaratan Fitur Utama (Functional Requirements)** 

## **4.1 Modul Pencari Kost (Mahasiswa)** 

Pencari kost difasilitasi dengan antarmuka pencarian yang intuitif melalui fiturfitur berikut: 

- **Pencarian Berbasis Peta Interaktif (Google Maps API):** 

   - Menampilkan penanda posisi ( _pin marker_ ) kost secara akurat di wilayah Kota Bandung. 

   - Menyediakan filter pencarian instan berdasarkan: rentang harga (minimum hingga maksimum), fasilitas utama (WiFi, Kamar Mandi Dalam, AC, dll.), tipe hunian (Putra, Putri, Campur), serta jarak radius dari kampus terdekat. 

## • **Halaman Detail Kost:** 

- Menyediakan galeri foto dan video representatif untuk setiap kamar dan area fasilitas bersama. 

2 

- Menampilkan status ketersediaan kamar secara _real-time_ atau status indikator umum (“Tersedia” / “Penuh”). 

- Menyajikan lokasi presisi pada peta beserta estimasi jarak tempuh nyata ke kampus terdekat. 

## • **Interaksi Fleksibel (Opsi Dual Interaksi):** 

- **Opsi A (** **_Direct WhatsApp_ ):** Tombol CTA ( _Call to Action_ ) khusus untuk menghubungi pemilik secara langsung melalui WhatsApp dengan pesan pembuka otomatis ( _pre-filled message_ ). 

- **Opsi B (** **_In-App Booking/Chat_ ):** Formulir pengajuan minat atau pemesanan internal di dalam platform untuk mencatat rekam ketertarikan calon penghuni. 

## **4.2 Modul Portal Pemilik Kost (** **_Landlord Portal_ )** 

Portal khusus yang didedikasikan bagi para pemilik kost untuk mengelola aset digital mereka: 

- **Autentikasi & Registrasi:** Proses pendaftaran akun yang aman dan cepat menggunakan alamat email aktif atau verifikasi nomor WhatsApp menggunakan sistem _One-Time Password_ (OTP). 

- **Manajemen Properti (CRUD):** Formulir pendaftaran properti baru yang lengkap untuk mengisi nama kost, alamat fisik, deskripsi, fasilitas, penentuan titik koordinat via fitur _drag-and-drop pin_ Google Maps, serta mengunggah galeri foto properti. 

## • **Kelola Ketersediaan Kamar (Dual Mode):** 

   - **Mode Sederhana:** Pemilik cukup menggeser tombol alih ( _toggle status_ ) umum antara “Masih Ada Kamar” atau “Kamar Penuh”. 

   - **Mode Rinci:** Pengaturan jumlah kamar secara mendetail (contoh: Total 10 Kamar, Terisi 8, Sisa 2 Kamar). 

- **Dashboard Pemilik:** Panel analisis ringkas untuk memantau performa tayangan iklan kost, jumlah klik, serta mengajukan aktivasi fitur Iklan Premium (Sundul). 

## **4.3 Modul Admin & Verifikasi (** **_Admin Panel_ )** 

Modul khusus kontrol internal untuk menjaga kualitas dan keamanan ekosistem platform: 

- **Moderasi Data & Pencegahan Penipuan:** Menerapkan sistem _Draf & Review_ . Setiap iklan kost baru yang diajukan pemilik tidak akan langsung dita- 

3 

yangkan kepada publik sebelum melalui pemeriksaan manual dan persetujuan ( _Approve_ ) oleh Admin. Admin berhak menolak ( _Reject_ ) iklan dengan menyertakan alasan yang jelas. 

- **Manajemen Fitur Sundul / Iklan Premium:** Pengelolaan persetujuan manual atau otomatis atas pengajuan fitur “Sundul” agar posisi kost berada pada urutan teratas hasil pencarian. 

## **5. Model Bisnis & Fitur Monetisasi (Monetization Strategy)** 

Platform ini mengadopsi model pendapatan hibrida yang mengutamakan akuisisi data di tahap awal: 

- **Pendaftaran Gratis (** **_Freemium Model_ ):** Seluruh pemilik kost dibebaskan dari biaya registrasi akun dan penayangan iklan tingkat dasar (100% gratis). Hal ini ditujukan untuk membangun basis data kost yang padat ( _supply density_ ) di area Kota Bandung secara cepat. 

- **Fitur Iklan Premium / Sundul (** **_Operational Revenue_ ):** Sebagai sumber pendapatan utama, pemilik kost dapat membayar biaya nominal tertentu untuk mengaktifkan paket “Sundul”. Fitur ini memungkinkan properti mereka berada di daftar teratas hasil pencarian ( _Top List_ ) dan memperoleh lencana verifikasi khusus (badge “Rekomendasi” / “Verified”). 

## **6. Persyaratan Non-Fungsional (Non-Functional Requirements)** 

- **Performa & Kecepatan:** Mengingat mayoritas mahasiswa mengakses platform melalui ponsel pintar, desain web wajib menerapkan pendekatan _mobile-first_ yang responsif dan memiliki waktu muat ( _page load time_ ) yang sangat cepat demi menjaga kenyamanan pengguna. 

- **Keamanan Data:** Perlindungan ketat pada enkripsi data pribadi sensitif milik pengguna dan pemilik kost (terutama nomor telepon) guna mencegah aktivitas pengambilan data massal secara ilegal ( _data scraping_ ) oleh pihak ketiga. 

- **Kemudahan Penggunaan (** **_Usability_ ):** Antarmuka dan alur pendaftaran properti pada Portal Pemilik dirancang sesederhana mungkin agar mudah digunakan secara intuitif oleh pemilik kost dari berbagai latar belakang usia dan literasi teknologi. 

4 

