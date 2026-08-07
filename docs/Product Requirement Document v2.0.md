# Product Requirement Document (PRD)

## Platform Directory Kost Hyper-Local Bandung

Versi Dokumen: 2.1  
Status: Disesuaikan dengan implementasi aplikasi saat ini  
Platform: Web Application Mobile-Responsive

## 1. Visi & Lingkup Produk

Platform Directory Kost Hyper-Local Bandung adalah aplikasi web untuk membantu pencari kost menemukan listing kost valid di Kota Bandung dan membantu pemilik kost mengelola listing secara mandiri. Produk mengutamakan pencarian cepat, peta interaktif, dashboard pemilik, chat internal, verifikasi dokumen, dan moderasi admin.

Produk saat ini bukan marketplace transaksi sewa penuh. Fokusnya adalah direktori, discovery, verifikasi, dan komunikasi awal antara pencari kost dan pemilik.

## 2. Tech Stack Aktual

| Lapisan | Teknologi | Fungsi |
| --- | --- | --- |
| Backend | Laravel 13 | Routing, model, validasi, auth, middleware, database, notification |
| Interaktivitas Server UI | Livewire 4 | Komponen pencarian, dashboard, form, chat, admin panel |
| Auth & Security | Laravel Fortify | Login, register, reset password, email verification, 2FA, passkeys |
| Frontend Build | Vite 8 | Build asset production |
| Styling | Tailwind CSS 4 | UI mobile-first, dark mode, Neo-Brutalism |
| Client Interaction | Alpine.js | Interaksi ringan, peta, modal, state UI |
| Maps | Google Maps API | Peta katalog dan pemilihan lokasi kost |
| Database | MySQL/PostgreSQL compatible | Penyimpanan user, kost, fasilitas, chat, verifikasi |
| Testing & Quality | Pest, Pint, PHPStan/Larastan | Test suite, lint, type check |

## 3. Role & Hak Akses

### 3.1 Guest

- Melihat beranda dan hasil pencarian kost.
- Melihat detail kost yang sudah published.
- Mengakses login dan register.
- Jika mencoba memulai chat dari detail kost, diarahkan ke login.

### 3.2 User / Pencari Kost

- Mengelola profil dan keamanan akun.
- Mengirim pesan ke pemilik kost dari halaman detail.
- Membuka dashboard chat pencari.
- Mengarsipkan percakapan dari sisi pencari.
- Menghubungi admin melalui fitur Hubungi Admin.

### 3.3 Owner / Pemilik Kost

- Mengakses dashboard pemilik setelah login dan email verified.
- Membuat, mengedit, menghapus, dan mengubah status ketersediaan kost miliknya.
- Mengelola fasilitas, aturan, foto, harga, periode sewa, lokasi, dan dokumen kepemilikan.
- Membalas chat dari pencari kost.
- Menghubungi admin melalui fitur Hubungi Admin.

### 3.4 Admin

- Mengakses dashboard moderasi dan inbox bantuan admin.
- Approve/reject listing kost.
- Approve/reject fasilitas custom.
- Approve/reject dokumen identitas owner.
- Approve/reject dokumen kepemilikan kost.
- Membuka dokumen verifikasi privat.
- Membalas, menutup, dan menghapus percakapan bantuan.

## 4. Routing Produk

| Route | Akses | Fungsi |
| --- | --- | --- |
| `/` | Public | Beranda dan pencarian kost |
| `/kost/{slug}` | Public terbatas | Detail kost published; non-published hanya admin/pemilik |
| `/owner/{user}` | Public | Profil publik pemilik |
| `/login` | Guest | Login Livewire |
| `/register` | Guest | Register user/owner |
| `/dashboard` | Auth + verified + owner | Dashboard pemilik |
| `/dashboard/kost/create` | Auth + verified + owner | Form tambah kost |
| `/dashboard/kost/{slug}/edit` | Auth + verified + owner | Form edit kost milik owner |
| `/dashboard/chats` | Auth + verified + owner | Chat pemilik |
| `/dashboard/user/chats` | Auth + verified | Chat pencari |
| `/profil` | Auth + verified | Profil dan keamanan akun |
| `/hubungi-admin` | Auth + verified, non-admin | Chat bantuan ke admin |
| `/admin/moderation` | Auth + verified + admin | Dashboard moderasi |
| `/admin/messages` | Auth + verified + admin | Inbox bantuan admin |
| `/admin/verification-document/{kind}/{id}` | Auth + verified + admin | Akses dokumen verifikasi privat |

## 5. User Stories

### 5.1 Pencari Kost

- Sebagai pencari kost, saya ingin mencari kost berdasarkan kata kunci agar menemukan listing yang relevan.
- Sebagai pencari kost, saya ingin memfilter kost berdasarkan gender, kecamatan, harga, periode sewa, dan status verifikasi.
- Sebagai pencari kost, saya ingin melihat lokasi kost pada peta agar memahami posisi relatifnya.
- Sebagai pencari kost, saya ingin melihat foto, fasilitas, aturan, harga, dan ketersediaan kamar sebelum menghubungi pemilik.
- Sebagai pencari kost, saya ingin mengirim pesan ke pemilik dari halaman detail kost.
- Sebagai pencari kost, saya ingin melihat riwayat chat dan mengarsipkan percakapan.
- Sebagai pencari kost, saya ingin menghubungi admin jika ada pertanyaan atau komplain.

### 5.2 Pemilik Kost

- Sebagai pemilik, saya ingin mendaftar akun owner dengan data bisnis dan nomor WhatsApp.
- Sebagai pemilik, saya ingin membuat listing kost lengkap dengan foto, fasilitas, aturan, harga, lokasi, dan dokumen kepemilikan.
- Sebagai pemilik, saya ingin mengedit listing hanya jika listing tersebut milik saya.
- Sebagai pemilik, saya ingin mengubah status ketersediaan kamar secara cepat dari dashboard.
- Sebagai pemilik, saya ingin menerima dan membalas pesan dari pencari kost.
- Sebagai pemilik, saya ingin mengunggah dokumen identitas dan kepemilikan agar listing saya dapat dipercaya.

### 5.3 Admin

- Sebagai admin, saya ingin meninjau listing pending sebelum tayang publik.
- Sebagai admin, saya ingin menolak listing bermasalah.
- Sebagai admin, saya ingin memverifikasi identitas owner dan dokumen kepemilikan kost.
- Sebagai admin, saya ingin mengelola fasilitas custom agar daftar fasilitas tetap berkualitas.
- Sebagai admin, saya ingin melihat inbox bantuan berdasarkan status belum dibalas, aktif, dan riwayat.
- Sebagai admin, saya ingin menutup dan menghapus percakapan bantuan yang sudah selesai.

## 6. Spesifikasi Fungsional

### 6.1 Auth & Account Security

- Login dan register berbasis Livewire.
- Register mendukung role `user` dan `owner`; role admin tidak dibuka dari form publik.
- Password minimal 8 karakter, mengandung huruf dan angka.
- Register dibatasi rate limiter maksimal 5 akun valid per IP per jam.
- Fortify mengaktifkan:
  - registration;
  - reset password;
  - email verification;
  - two-factor authentication dengan `confirm` dan `confirmPassword`;
  - passkeys dengan `confirmPassword`.
- Profil mendukung update nama, email, nomor WhatsApp, business name, avatar, dan dokumen identitas.
- Jika email berubah, `email_verified_at` direset dan notifikasi dikirim.

### 6.2 Katalog & Pencarian Kost

- Komponen utama: `KostSearch`.
- Query hanya menampilkan kost dengan `status = published` dan `is_available = true`.
- Filter:
  - search keyword;
  - gender type;
  - district;
  - rent period;
  - minimum price;
  - maximum price;
  - verified only.
- District count dihitung sebelum filter district diterapkan.
- Sorting memprioritaskan `boosted_at` kemudian `created_at` terbaru.
- Data map disusun dari hasil pagination agar marker selaras dengan listing.
- Relasi eager loaded: `primaryImage`, `user`, dan `facilities` approved.

### 6.3 Detail Kost

- Komponen utama: `KostDetail`.
- Relasi dimuat: facilities, rules, images, user, prices.
- Detail non-published tidak dapat dilihat publik.
- Admin dan pemilik listing tetap dapat melihat preview listing non-published.
- User login dapat membuat percakapan dengan pemilik.
- Sistem menolak chat jika:
  - kost tidak published;
  - kost penuh;
  - user adalah pemilik kost;
  - rate limit terlampaui.

### 6.4 Form Create/Edit Kost

- Komponen utama: `CreateKost` dan `EditKost`.
- Required data:
  - nama;
  - deskripsi;
  - district;
  - alamat;
  - latitude/longitude;
  - gender type;
  - harga utama;
  - rent period;
  - total rooms;
  - available rooms;
  - minimal 4 foto dan maksimal 10 foto.
- Optional data:
  - deposit;
  - include utilities;
  - WhatsApp contact;
  - nearby landmarks;
  - additional rules note;
  - extra rent periods and prices;
  - ownership document.
- Foto wajib image `jpeg`, `png`, `jpg`, atau `webp`, maksimal 2MB per file.
- Dokumen ownership saat ini berupa image dengan tipe dokumen `pbb` atau `surat_kuasa`.
- Input teks utama disimpan dengan `strip_tags()`.
- Create kost menghasilkan slug unik dan status awal `pending`.
- Edit kost memvalidasi kepemilikan pada `mount()`.
- Delete dan toggle availability menggunakan query kost milik user untuk mencegah IDOR.

### 6.5 Dashboard Pemilik

- Menampilkan total properti, total kamar tersedia, dan pesan masuk belum dibaca.
- Listing owner dipaginasi 9 item.
- Pencarian dashboard berdasarkan nama, kecamatan, dan alamat.
- Query eager load `primaryImage` dan `facilities`, serta `withCount('conversations')`.
- Delete membutuhkan konfirmasi teks `HAPUS`.
- Delete dilakukan permanen (`forceDelete`) dan file foto ikut dibersihkan melalui lifecycle model.

### 6.6 Chat Kost

- Model utama: `KostConversation` dan `KostMessage`.
- Percakapan memiliki status:
  - `open`;
  - `archived_by_owner`;
  - `archived_by_seeker`.
- Owner hanya dapat membuka percakapan pada kost miliknya.
- Seeker hanya dapat membuka percakapan miliknya.
- Pesan dibatasi maksimal 2000 karakter.
- Rate limiter pengiriman pesan: 20 pesan per menit per user untuk dashboard chat.
- Badge unread dihitung berdasarkan pesan yang belum `read_at` dan bukan dikirim oleh user aktif.

### 6.7 Admin Moderation

- Komponen utama: `ModerationDashboard`.
- Tab:
  - pending;
  - published;
  - rejected;
  - all;
  - facilities;
  - verification.
- Admin dapat:
  - approve listing menjadi published;
  - reject listing;
  - approve/reject fasilitas pending;
  - approve/reject identitas owner;
  - approve/reject dokumen kepemilikan kost.
- Verification tab menampilkan owner dengan identitas pending dan kost dengan ownership pending.
- Rejection note default digunakan jika admin tidak mengisi alasan.

### 6.8 Hubungi Admin

- Model utama: `AdminConversation` dan `AdminMessage`.
- Kategori: `komplain`, `pertanyaan`, `masukan`, `lainnya`.
- User dapat membuka percakapan baru dan follow-up pada percakapan open.
- Admin tidak dapat membuka percakapan sebagai pengirim user.
- Pesan user mengisi `awaiting_reply_at`; admin reply mengosongkannya.
- Percakapan open dengan `awaiting_reply_at` lebih dari 24 jam otomatis ditutup sebagai expired.
- Soft-deleted conversation dipruning setelah 30 hari.
- Admin inbox menyediakan filter unanswered, open, dan history.

### 6.9 Verifikasi Dokumen

- User/owner identity verification disimpan di data user.
- Ownership verification disimpan per kost.
- Status verifikasi: unverified, pending, verified, rejected.
- Kost dianggap verified jika identitas owner verified dan ownership kost verified.
- Dokumen disimpan pada disk privat `verification_docs`.
- Akses dokumen hanya melalui controller admin yang dilindungi middleware admin.

### 6.10 UI/UX

- Desain memakai gaya Neo-Brutalism dengan border tebal, shadow, warna kontras, dan dark mode.
- Komponen UI utama memakai Blade components seperti brutal card, brutal button, badge, navbar, dan layout app/auth.
- Layout mobile-first.
- Pagination menggunakan view kustom agar konsisten dengan desain.
- Validasi dan feedback menggunakan teks Bahasa Indonesia.

## 7. Data Model Ringkas

| Model | Fungsi |
| --- | --- |
| `User` | Akun user, owner, admin; profil; verifikasi identitas; 2FA/passkeys |
| `Kost` | Listing kost, lokasi, harga utama, status, verifikasi ownership, boost |
| `KostImage` | Foto listing dan foto utama |
| `KostPrice` | Harga tambahan per periode sewa |
| `Facility` | Fasilitas approved/pending/rejected, standar maupun custom |
| `Rule` | Aturan kost |
| `KostConversation` | Thread chat pencari-owner |
| `KostMessage` | Pesan pada chat pencari-owner |
| `AdminConversation` | Thread Hubungi Admin |
| `AdminMessage` | Pesan pada thread Hubungi Admin |

## 8. Non-Functional Requirements

### 8.1 Security

- Route sensitif dilindungi `auth`, `verified`, `owner`, dan `admin`.
- Ownership check diterapkan pada edit/delete/toggle/chat.
- CSRF Laravel/Livewire aktif pada form.
- Validasi server-side digunakan pada seluruh input penting.
- Sanitasi input teks dilakukan pada penyimpanan listing.
- Rate limiter diterapkan pada register, create kost, start chat, dan pengiriman pesan.
- Dokumen verifikasi tidak disimpan di public disk.

### 8.2 Performance

- Eager loading digunakan pada listing, detail, dashboard, chat, dan admin inbox.
- Pagination digunakan pada katalog, dashboard owner, dan admin inbox.
- Asset production dibuild menggunakan Vite.
- Production harus menggunakan cache Laravel (`config`, `route`, `view`, `event`).

### 8.3 Reliability

- Test suite menggunakan Pest.
- Static analysis menggunakan PHPStan/Larastan.
- Code style menggunakan Pint.
- Deployment final wajib menjalankan test dan build.

### 8.4 SEO

- Detail kost dirender server-side melalui Blade/Livewire layout.
- URL detail menggunakan slug.
- Halaman publik dapat diindeks untuk konten listing yang sudah published.

## 9. Deployment Requirements

### 9.1 Environment Production

Wajib disiapkan pada server production:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-production
DB_CONNECTION=mysql
DB_HOST=...
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
QUEUE_CONNECTION=database
CACHE_STORE=database
SESSION_SECURE_COOKIE=true
```

### 9.2 Command Deployment

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan optimize
```

### 9.3 Command Verifikasi Sebelum Deploy

```bash
composer lint:check
composer types:check
php artisan test
npm run build
```

## 10. Batasan & Roadmap

### 10.1 Batasan Versi Saat Ini

- Belum ada pembayaran booking/sewa internal.
- Belum ada OTP WhatsApp.
- Belum ada dashboard analitik tayangan dan klik yang lengkap.
- Belum ada sistem pembelian boost otomatis.
- Belum ada PWA/offline mode penuh.

### 10.2 Roadmap Lanjutan

- Paket boost/iklan premium dengan transaksi dan invoice.
- Analytics pemilik: view, click, chat conversion.
- Sistem laporan listing bermasalah dari pencari.
- Radius kampus dan estimasi jarak tempuh.
- Notifikasi email/queue untuk chat dan moderation update.
- Optimasi SEO detail kost dengan metadata Open Graph yang lebih lengkap.
