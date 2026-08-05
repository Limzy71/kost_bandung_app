# **RANCANGAN STRUKTUR DATABASE (ERD)** 

## **Platform Pencarian & Manajemen Kost Hyper-Local Bandung** 

Status: Final Version | Kompatibilitas: Laravel 12 (Eloquent ORM) 

Rancangan Struktur Database atau _Entity Relationship Diagram_ (ERD) ini merupakan versi akhir ( _Final Version_ ) yang telah diperbarui secara penuh. Skema ini telah mencakup seluruh penyesuaian terkait manajemen fasilitas, tipe kost, serta aturan khusus, dan dirancang agar 100% kompatibel dengan standar _migration_ dan Eloquent ORM pada _framework_ Laravel 12. 

## **1. Tabel** **`users`** 

Menyimpan semua data pengguna (Admin, Mahasiswa/Pencari Kost, dan Pemilik Kost). 

|**Nama Kolom**|**Tipe Data / Key**|**Keterangan**|
|---|---|---|
|`id`|Primary Key, BigInt||
|`name`|String||
|`email`|String, Unique||
|`avatar`|String, Nullable|Path berkas foto profil di storage|
|`phone_number`|String, Unique||
|`password`|String||
|`role`|Enum|’admin’, ’owner’, ’student’|
|`timestamps`|Timestamps|created_at, updated_at|



## **2. Tabel** **`kosts`** 

Tabel utama untuk menyimpan data detail properti kost. 

## **3. Tabel** **`kost_images`** 

Menyimpan galeri foto untuk setiap kost (Relasi _One-to-Many_ ). 

## **4. Tabel** **`facilities` (Master Data)** 

Menyimpan daftar fasilitas baku (termasuk kelonggaran akses). 

**5. Tabel** **`facility_kost` (Pivot Table)** 

Tabel relasi _Many-to-Many_ antara Kost dan Fasilitas. 

1 

|**Nama Kolom**|**Tipe Data / Key**|**Keterangan**|
|---|---|---|
|`id`|Primary Key, BigInt||
|`user_id`|Foreign Key|Merujuk ke`users.id`|
|`name`|String||
|`description`|Text||
|`type`|Enum|’putra’, ’putri’, ’campur’|
|`price_per_month`|Integer||
|`total_rooms`|Integer||
|`available_rooms`|Integer||
|`is_available`|Boolean||
|`latitude`|Decimal (10, 8)||
|`longitude`|Decimal (11, 8)||
|`address_detail`|Text||
|`additional_rules_n`|`ote`Text, Nullable|Catatan aturan spesifk manual (cth:<br>”Tamu kena charge Rp 50.000”)|
|`status`|Enum|’pending’, ’published’, ’rejected’|
|`boosted_at`|Timestamp,<br>Nullable||
|`timestamps`|Timestamps|created_at, updated_at|
|`softDeletes`|SoftDeletes|deleted_at|



|**Nama Kolom**|**Tipe Data / Key**|**Keterangan**|
|---|---|---|
|`id`|Primary Key, BigInt||
|`kost_id`|Foreign Key|Merujuk ke`kosts.id`|
|`image_path`|String||
|`is_primary`|Boolean|Indikator foto sampul (thumbnail) di<br>pencarian|
|`timestamps`|Timestamps|created_at, updated_at|
|**Nama Kolom**|**Tipe Data / Key**|**Keterangan**|
|`id`|Primary Key, BigInt||
|`name`|String|Contoh: ”WiFi”, ”AC”, ”Akses Kunci 24<br>Jam”|
|`icon`|String, Nullable||
|`timestamps`|Timestamps|created_at, updated_at|



2 

|**Nama Kolom**|**Tipe Data / Key**|**Keterangan**|
|---|---|---|
|`facility_id`|Foreign Key|Merujuk ke`facilities.id`|
|`kost_id`|Foreign Key|Merujuk ke`kosts.id`|



## **6. Tabel** **`rules` (Master Data)** 

Menyimpan daftar aturan baku (larangan/batasan) yang bisa dicentang pemilik kost. 

|**Nama Kolom**|**Tipe Data / Key**|**Keterangan**|
|---|---|---|
|`id`|Primary Key, BigInt||
|`name`|String|Contoh: ”Ada Jam Malam”, ”Dilarang<br>Bawa Hewan”|
|`icon`|String, Nullable||
|`timestamps`|Timestamps|created_at, updated_at|



## **7. Tabel** **`kost_rule` (Pivot Table)** 

Tabel relasi _Many-to-Many_ antara Kost dan Aturan. 

|**Nama Kolom**|**Tipe Data / Key**|**Keterangan**|
|---|---|---|
|`kost_id`|Foreign Key|Merujuk ke`kosts.id`|
|`rule_id`|Foreign Key|Merujuk ke`rules.id`|



## **8. Tabel** **`inquiries`** 

Untuk mencatat riwayat pesan/pengajuan _booking_ internal dari pencari kost. 

## **Ringkasan Relasi Database (Model Laravel Eloquent)** 

- **`User`** 

   - `hasMany(Kost::class)` 

   - `hasMany(Inquiry::class)` 

   - `hasMany(AdminConversation::class)`

- **`Kost`** 

   - `belongsTo(User::class)` 

   - `hasMany(KostImage::class)` 

   - `hasMany(Inquiry::class)` 

   - `belongsToMany(Facility::class)` _(Melalui tabel pivot:_ _`facility_kost` )_ 

3 

|**Nama Kolom**|**Tipe Data / Key**|**Keterangan**|
|---|---|---|
|`id`|Primary Key, BigInt||
|`kost_id`|Foreign Key|Merujuk ke`kosts.id`|
|`student_id`|Foreign Key|Merujuk ke`users.id`|
|`message`|Text||
|`status`|Enum|’unread’, ’read’, ’replied’|
|`timestamps`|Timestamps|created_at, updated_at|



## **9. Tabel** **`admin_conversations`** (Thread CS / Hubungi Admin)

Menyimpan thread percakapan antara pengguna (Pencari Kost / Pemilik Kost) dengan Admin (fitur Hubungi Admin / CS & pengaduan).

|**Nama Kolom**|**Tipe Data / Key**|**Keterangan**|
|---|---|---|
|`id`|Primary Key, BigInt||
|`user_id`|Foreign Key, Nullable|Merujuk ke`users.id` (nullOnDelete)|
|`sender_role`|Enum/String|’user’, ’owner’ — peran pengirim saat membuka thread|
|`category`|Enum/String|’komplain’, ’pertanyaan’, ’masukan’, ’lainnya’|
|`status`|Enum/String|’open’, ’closed’ (default: ’open’)|
|`closed_reason`|String, Nullable|’admin’ atau ’expired’|
|`awaiting_reply_at`|Timestamp, Nullable|Batas SLA 1x24 jam; direset setiap user mengirim pesan|
|`closed_at`|Timestamp, Nullable||
|`deleted_at`|SoftDeletes|Retensi 30 hari sebelum dihapus permanen|
|`timestamps`|Timestamps|created_at, updated_at|
|Index|Composite|(status, awaiting_reply_at), (user_id, status)|

## **10. Tabel** **`admin_messages`**

Menyimpan pesan per thread percakapan CS (Relasi _One-to-Many_ ke `admin_conversations`).

|**Nama Kolom**|**Tipe Data / Key**|**Keterangan**|
|---|---|---|
|`id`|Primary Key, BigInt||
|`conversation_id`|Foreign Key|Merujuk ke`admin_conversations.id` (cascadeOnDelete)|
|`sender_type`|Enum/String|’user’, ’admin’|
|`sender_id`|Foreign Key, Nullable|Merujuk ke`users.id` (nullOnDelete)|
|`body`|Text|Maksimal 2.000 karakter|
|`read_at`|Timestamp, Nullable|Ditandai saat penerima membuka thread|
|`timestamps`|Timestamps|created_at, updated_at|
|Index|Composite|(conversation_id, created_at)|



   - `belongsToMany(Rule::class)` _(Melalui tabel pivot:_ _`kost_rule` )_ 

- **`Facility`** 

   - `belongsToMany(Kost::class)` 

- **`Rule`** 

   - `belongsToMany(Kost::class)` 

- **`AdminConversation`** 

   - `belongsTo(User::class)` 

   - `hasMany(AdminMessage::class)` 

- **`AdminMessage`** 

   - `belongsTo(AdminConversation::class)` 

   - `belongsTo(User::class)` _(Melalui_ _`sender_id` )_ 

4 

