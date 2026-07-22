# Taki Course - Modern LMS & Online Learning Platform

<p align="center">
  <img src="public/assets/logo/logo.svg" alt="Taki Course Logo" width="200">
</p>

<p align="center">
  Platform pembelajaran online (*Learning Management System*) modern berbasis <b>Laravel 11</b> dan <b>Tailwind CSS</b> yang dirancang untuk memberikan pengalaman belajar interaktif, manajemen kursus berbasis peran (RBAC), serta transaksi berlangganan secara seamless.
</p>

---

## 🚀 Fitur Utama

### 🌐 Public & Student Experience
* **Interactive Landing Page**: Tampilan depan modern dengan banner dinamis, daftar kategori populer, carousel kursus favorit (Flickity), dan slider testimoni.
* **Course Catalog & Detail Page**: Eksplorasi materi kursus lengkap dengan daftar video pembelajaran, manfaat kelas, dan portofolio siswa (Fancybox).
* **Protected Video Player**: Halaman *Learning Experience* berbasis video player (Plyr) yang terproteksi sesuai dengan hak akses dan status berlangganan.
* **Subscription & Checkout**: Alur transaksi checkout langganan yang fleksibel untuk pengguna dengan role `student`.

### 🛡️ Admin & Role-Based Access Control (RBAC)
Menggunakan **Spatie Laravel Permission** dengan 3 tingkatan peran (*Role*):
* **Owner (Administrator Utama)**:
  * Manajemen Kategori (*Categories*)
  * Manajemen Pengajar (*Teachers*)
  * Manajemen Kursus & Video Materi (*Courses & Course Videos*)
  * Verifikasi & Manajemen Transaksi Berlangganan (*Subscribe Transactions*)
* **Teacher (Pengajar)**:
  * Mengelola kursus yang diampu.
  * Menambahkan dan mengedit video materi pembelajaran.
* **Student (Siswa)**:
  * Menjelajahi katalog kursus, melakukan transaksi langganan, dan mengakses materi kelas.

---

## 🛠️ Teknologi & Stack

* **Backend**: PHP 8.2+, [Laravel 11](https://laravel.com/)
* **Frontend**: Blade Templates, [Tailwind CSS](https://tailwindcss.com/)
* **Database**: MySQL / PostgreSQL / SQLite
* **Authentication & Authorization**: Laravel Breeze / Sanctum, [Spatie Laravel-Permission](https://spatie.be/docs/laravel-permission)
* **Libraries & Plugins**:
  * [Flickity](https://flickity.metafizzy.co/) – Interactive Carousel Slider
  * [Plyr](https://plyr.io/) – HTML5 Media Player
  * [Fancybox UI](https://fancyapps.com/fancybox/) – Student Portfolio Lightbox

---

## 📋 Persyaratan Sistem

Sebelum menjalankan proyek ini, pastikan sistem Anda telah memenuhi persyaratan berikut:
* **PHP** >= 8.2
* **Composer** >= 2.0
* **Node.js** >= 18.x & **NPM**
* **MySQL** >= 8.0 / **MariaDB** / **SQLite**

---

## ⚙️ Panduan Instalasi

Ikuti langkah-langkah berikut untuk menjalankan proyek di lingkungan lokal (*Local Development*):

### 1. Clone Repository
```bash
git clone https://github.com/Ridhsuki/taki-course.git
cd taki-course
```

### 2. Install Dependency PHP & Node.js
```bash
composer install
npm install
```

### 3. Konfigurasi Environment (`.env`)
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Buka file `.env` dan atur konfigurasi database sesuai environment Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taki_course
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Jalankan Migrasi & Database Seeder
Jalankan migrasi tabel dan isi data awal (kategori, kursus, role, dan user demo):
```bash
php artisan migrate --seed
```

### 6. Hubungkan Storage Link
Buat shortcut folder storage agar aset gambar (icon kategori & thumbnail) dapat diakses di publik:
```bash
php artisan storage:link
```

### 7. Jalankan Server Lokal & Asset Builder

Jalankan Tailwind CSS builder (pada terminal terpisah):
```bash
npm run dev
```

Jalankan server aplikasi Laravel:
```bash
php artisan serve
```

Buka browser Anda dan akses: `http://127.0.0.1:8000`

---

## 📁 Struktur Direktori Utama

```text
taki-course/
├── app/
│   ├── Http/Controllers/       # Controller logika bisnis (Front, Admin, Auth)
│   └── Models/                 # Eloquent Models (Category, Course, Teacher, Transaction, dll)
├── database/
│   ├── migrations/             # Struktur skema tabel database
│   └── seeders/                # Seeder data awal (Role, Category, Course)
├── public/
│   ├── css/                    # Compiled CSS (Tailwind output)
│   ├── js/                     # Custom JavaScript (main.js)
│   └── storage/ -> storage/app/public # Symlink media uploads
├── resources/
│   └── views/                  # Blade Templates (Front & Admin layouts)
└── routes/
    ├── web.php                 # Rute aplikasi utama & middleware RBAC
    └── auth.php                # Rute otentikasi login/register
```

---

## 🤝 Kontribusi

Kontribusi selalu terbuka! Jika Anda ingin berkontribusi:
1. Fork repository ini.
2. Buat feature branch baru (`git checkout -b feature/FiturBaru`).
3. Commit perubahan Anda (`git commit -m 'Menambahkan FiturBaru'`).
4. Push ke branch tersebut (`git push origin feature/FiturBaru`).
5. Buat **Pull Request**.

---

## 📄 Lisensi

Proyek ini dikembangkan di bawah lisensi [MIT License](LICENSE).
