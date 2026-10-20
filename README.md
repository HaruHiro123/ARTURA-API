# ARTURA Backend API — Evaluasi 2

Repositori ini merupakan sistem **Backend REST API berbasis Laravel dan MySQL** yang terhubung langsung dengan website Frontend **ARTURA (Art Gallery & E-Commerce)** yang telah saya kembangkan sebelumnya. 

Pada tahap **Evaluasi 2** ini, website ARTURA yang semula bersifat statis (*localStorage*) telah ditingkatkan menjadi aplikasi **Full-Stack** yang terintegrasi dengan database MySQL (`artura_db`), manajemen penyimpanan file gambar (*Laravel Storage*), operasi **CRUD API Resource**, serta sistem keamanan autentikasi berbasis token menggunakan **Laravel Sanctum ( hingga tahap Auth)**.

**Link Website Frontend ARTURA (React + Vite):** https://haruhiro123.github.io/ARTURA/)  
**Link Repository Frontend:** (https://github.com/HaruHiro123/ARTURA)

---

## Rincian Pengerjaan Modul

Berikut adalah tahapan pengembangan yang telah diselesaikan di dalam repositori ini:

### 1. Konfigurasi Database, Migration, & Seeder (Bab 3.4.1 – 3.4.3)
- Membangun skema database **`artura_db`** menggunakan *Laravel Migration* yang terdiri dari:
  - Tabel **`categories`**: Menyimpan kategori karya seni (*Digital Art* dan *Traditional Art*).
  - Tabel **`artworks`**: Menyimpan 20 data karya seni yang berelasi (*Foreign Key* `category_id`) dengan tabel `categories`, mendukung tipe koleksi `portfolio` (tidak dijual / harga `nullable`) maupun `shop` (dijual dengan status `available` / `sold`).
  - Tabel **`users`** & **`personal_access_tokens`**: Digunakan untuk manajemen akun admin dan autentikasi token Laravel Sanctum.
- Mengisi 20 data awal karya seni secara otomatis menggunakan **Database Seeder** agar selaras dengan katalog asli pada website ARTURA.

### 2. Eloquent ORM & Relasi Antar Tabel (Bab 3.4.4)
- Mengimplementasikan model **`Category`** (`app/Models/Category.php`) dan **`Artwork`** (`app/Models/Artwork.php`) menggunakan relasi *One-to-Many* (`hasMany` dan `belongsTo`).
- Mengaktifkan trait **`HasApiTokens`** dari Laravel Sanctum pada model **`User`** (`app/Models/User.php`).

### 3. API Resource & Full CRUD Controller (Bab 3.4.5)
- Membuat **`ArtworkResource`** (`app/Http/Resources/ArtworkResource.php`) untuk memformat *response* JSON secara konsisten beserta URL gambar dari storage server.
- Membangun **`ArtworkController`** (`app/Http/Controllers/Api/ArtworkController.php`) yang menangani seluruh operasi **CRUD**:
  - `index()` (`GET /api/artworks`): Menampilkan seluruh daftar karya seni beserta kategorinya.
  - `show()` (`GET /api/artworks/{id_atau_slug}`): Menampilkan detail spesifik satu karya seni berdasarkan ID maupun *slug*.
  - `store()` (`POST /api/artworks`): Menambahkan karya seni baru ke database disertai validasi input.
  - `update()` (`PUT/PATCH /api/artworks/{id_atau_slug}`): Memperbarui data karya seni yang sudah ada di database.
  - `destroy()` (`DELETE /api/artworks/{id_atau_slug}`): Menghapus data karya seni dari database.

### 4. Manajemen Upload File Gambar & Storage Link (Bab 3.4.6)
- Mengonfigurasi *symbolic link* melalui `php artisan storage:link` sehingga seluruh file gambar karya seni disajikan langsung dari server Laravel melalui `http://127.0.0.1:8000/storage/artworks/...`.
- Mendukung fitur unggah gambar baru maupun pembaruan gambar baik melalui **Postman (`multipart/form-data` key `img`)** maupun langsung dari **Halaman Admin React**.

### 5. Authentication & Route Protection dengan Laravel Sanctum (Bab 3.4.7)
- Membuat **`AuthController`** (`app/Http/Controllers/Api/AuthController.php`) yang mencakup:
  - **`register`** (`POST /api/register`): Registrasi akun baru dengan validasi dan enkripsi password (`bcrypt`).
  - **`login`** (`POST /api/login`): Verifikasi kredensial pengguna dan penerbitan **Bearer Token** (`plainTextToken`).
  - **`profile`** (`GET /api/profile`): Mengambil informasi pengguna yang sedang terautentikasi.
  - **`logout`** (`POST /api/logout`): Mencabut/menghapus *access token* yang sedang aktif (`currentAccessToken()->delete()`).
- Mengamankan rute manipulasi data (`store`, `update`, `destroy`, `profile`, dan `logout`) pada **`routes/api.php`** menggunakan middleware **`auth:sanctum`**.

---

## Cara Kerja Integrasi Frontend React & Backend Laravel

1. **Saat Server Lokal Laravel Aktif (`php artisan serve` + `npm run dev`):**
   - Website Frontend React ARTURA secara otomatis mengambil data katalog karya seni dan memuat gambar langsung dari endpoint `http://127.0.0.1:8000/api/artworks` dan `http://127.0.0.1:8000/storage/artworks/...` tanpa mengubah desain UI.
   - Seluruh aksi pada **Admin Panel** di website (Tambah Karya, Edit Karya, dan Hapus Karya) terhubung langsung ke database MySQL menggunakan *Bearer Token* Sanctum.
2. **Mekanisme Fallback (Saat Diakses via GitHub Pages):**
   - Apabila website dibuka melalui link statis GitHub Pages ketika server lokal Laravel sedang tidak dinyalakan, sistem secara otomatis menggunakan data *fallback* sehingga tampilan website tetap berjalan normal tanpa error.

---

## Panduan Pengujian Endpoint di Postman

Seluruh endpoint diuji menggunakan **Postman** dengan menyertakan Header **`Accept: application/json`**:

| No | Nama Request | Method | Endpoint URL | Auth / Proteksi |
| :-: | :--- | :---: | :--- | :--- |
| 1 | **Auth - Register** | `POST` | `http://127.0.0.1:8000/api/register` | Publik (`form-data`: `name`, `email`, `password`) |
| 2 | **Auth - Login** | `POST` | `http://127.0.0.1:8000/api/login` | Publik (Menghasilkan **Bearer Token**) |
| 3 | **Auth - Get Profile** | `GET` | `http://127.0.0.1:8000/api/profile` | **Bearer Token (`auth:sanctum`)** |
| 4 | **Artworks - Get All** | `GET` | `http://127.0.0.1:8000/api/artworks` | Publik |
| 5 | **Artworks - Get Detail** | `GET` | `http://127.0.0.1:8000/api/artworks/1` | Publik |
| 6 | **Artworks - Create New** | `POST` | `http://127.0.0.1:8000/api/artworks` | **Bearer Token** + Upload File `img` |
| 7 | **Artworks - Update Artwork** | `POST` | `http://127.0.0.1:8000/api/artworks/{id}` | **Bearer Token** (`form-data` dengan `_method: PUT`) |
| 8 | **Artworks - Delete Artwork** | `DELETE` | `http://127.0.0.1:8000/api/artworks/{id}` | **Bearer Token (`auth:sanctum`)** |
| 9 | **Auth - Logout** | `POST` | `http://127.0.0.1:8000/api/logout` | **Bearer Token (`auth:sanctum`)** |

### Akun Pengujian Default:
- **Name:** `ADMIN`
- **Email (Postman / API):** `admin@artura.com`
- **Username (Panel Admin Web):** `admin`
- **Password:** `artura123`

---

## Cara Menjalankan Proyek di Komputer Lokal

```bash
# 1. Masuk ke direktori backend Laravel & install dependensi
composer install
cp .env.example .env
php artisan key:generate

# 2. Jalankan migrasi tabel, seeder 20 karya seni, dan symbolic link storage
php artisan migrate:fresh --seed
php artisan storage:link

# 3. Jalankan server Laravel ([http://127.0.0.1:8000](http://127.0.0.1:8000))
php artisan serve
