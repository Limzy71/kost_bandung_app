# KostBandung

Platform direktori kost hyper-local untuk wilayah Kota Bandung. Aplikasi ini membantu pencari kost menemukan listing berdasarkan lokasi, harga, tipe hunian, status verifikasi, dan ketersediaan kamar; sekaligus menyediakan dashboard bagi pemilik kost dan admin untuk mengelola data, chat, moderasi, serta verifikasi dokumen.

## Fitur Utama

- Katalog kost publik dengan pencarian dan filter.
- Peta interaktif menggunakan Google Maps API.
- Detail kost dengan foto, fasilitas, aturan, harga, periode sewa, lokasi, dan kontak.
- Dashboard pemilik untuk membuat, mengedit, menghapus, dan mengubah status ketersediaan kost.
- Upload 4-10 foto kost dan dokumen kepemilikan.
- Chat internal antara pencari kost dan pemilik.
- Halaman Hubungi Admin untuk komplain, pertanyaan, masukan, dan bantuan.
- Admin panel untuk moderasi listing, fasilitas custom, identitas owner, dan dokumen kepemilikan.
- Email verification, 2FA, dan passkeys melalui Laravel Fortify.
- Dark mode dengan gaya UI Neo-Brutalism.

## Tech Stack

- Laravel 13
- Livewire 4
- Laravel Fortify
- Tailwind CSS 4
- Vite 8
- Alpine.js
- MySQL/PostgreSQL compatible
- Pest, Pint, PHPStan/Larastan

## Requirement

- PHP 8.3+
- Composer
- Node.js dan npm
- Database MySQL/PostgreSQL
- Google Maps API key
- SMTP mail provider untuk email verification/reset password

## Dokumentasi Proyek

Dokumen requirement tersedia di folder `docs/`:

- `docs/Business Requirement Document.md`
- `docs/Product Requirement Document v2.0.md`
- `docs/Rancangan Wireframe Aplikasi Kost.md`
- `docs/Struktur Database ERD.md`

## Role Aplikasi

- **Guest:** melihat katalog dan detail kost published.
- **User/Pencari:** mengirim chat ke pemilik, mengelola profil, dan menghubungi admin.
- **Owner/Pemilik:** mengelola listing kost dan membalas chat pencari.
- **Admin:** moderasi listing, verifikasi dokumen, dan menangani inbox bantuan.

## Keamanan

- Route sensitif dilindungi middleware `auth`, `verified`, `owner`, dan `admin`.
- Aksi pemilik divalidasi dengan ownership check.
- Form dilindungi CSRF dan validasi server-side.
- Input teks listing disanitasi saat penyimpanan.
- Dokumen verifikasi disimpan pada disk privat.
- Rate limiter diterapkan pada register, pembuatan kost, dan chat.
