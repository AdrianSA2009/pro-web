<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>
<div align="center">
  <img src="https://img.shields.io/badge/Laravel_13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13" />
  <img src="https://img.shields.io/badge/PHP_8.3-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3" />
  <img src="https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
  <img src="https://img.shields.io/badge/Vite-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite" />
  <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" />
  <img src="https://img.shields.io/badge/Flowbite-1E429F?style=for-the-badge&logo=flowbite&logoColor=38B2AC" alt="Flowbite" />
  <img src="https://img.shields.io/badge/Pusher-300D4F?style=for-the-badge&logo=pusher&logoColor=white" alt="Pusher" />
</div>

# Inviniux — Aplikasi Stok Gudang Elektronik

Sistem informasi berbasis web untuk pengelolaan dan pemantauan persediaan barang elektronik di gudang. Mendukung pencatatan stok masuk/keluar, manajemen supplier & kategori, *real-time* notifikasi stok rendah, serta ekspor laporan ke Excel.

Dikembangkan sebagai tugas *Project Based Learning* (PBL) Program Studi Teknik Informatika, Politeknik Negeri Batam.

## Daftar Isi

- [Tim Pengembang](#-tim-pengembang-pbl-if-2pc-05)
- [Fitur](#-fitur-utama)
- [Teknologi](#-teknologi-yang-digunakan)
- [Instalasi](#-cara-instalasi-local-development)
- [Struktur Proyek](#-struktur-proyek)
- [Hak Akses](#-hak-akses-role)
- [License](#-license)

## 👥 Tim Pengembang (PBL IF-2PC-05)

| Peran | Nama |
|-------|------|
| **Manajer Proyek** | Dwi Amalia Purnamasari, S.T., M.Cs |
| **Ketua Kelompok** | Adrian Septiaji (3312501064) |
| **Anggota** | Cindo Maulina (3312501070) |
| **Anggota** | Taqiyyah Aufaa Nabiilah (3312501084) |

## 🚀 Fitur Utama

### Admin Gudang
* **Manajemen Data Barang** — CRUD barang beserta gambar, satuan (UnitBarang), dan kategori.
* **Barang Masuk** — Pencatatan inventaris masuk dengan validasi nomor seri unik.
* **Barang Keluar** — Pencatatan barang keluar dengan pengecekan stok otomatis.
* **Manajemen Supplier** — Kelola data pemasok barang.
* **Manajemen Kategori** — Kelola kategori produk elektronik.
* **Manajemen Pengguna** — Tambah, edit, dan hapus akun pengguna sistem.
* **Ekspor Laporan** — Unduh data barang dalam format Excel (.xlsx) via PhpSpreadsheet.
* **Notifikasi Stok Rendah** — *Real-time* alert via Pusher ketika stok di bawah batas minimum.

### Manajer
* **Dashboard Monitoring** — Ringkasan stok dan statistik persediaan.
* **Lihat Data Barang** — Akses *read-only* ke daftar barang beserta detail stok.
* **Ekspor Laporan** — Unduh laporan persediaan dalam format Excel.
* **Notifikasi Stok Rendah** — *Real-time* alert via Pusher ketika stok di bawah batas minimum.

### Umum
* **Autentikasi** — Login, lupa password (OTP via email), dan reset password.
* **Pengaturan Profil** — Edit nama, email, dan ubah password.
* **Role-based Access Control** — Middleware `role` membatasi akses berdasarkan peran.

## 🛠️ Teknologi yang Digunakan

| Layer | Teknologi |
|-------|-----------|
| **Framework** | [Laravel 13](https://laravel.com) (PHP ≥ 8.3) |
| **Frontend** | Blade, Tailwind CSS 3, Flowbite 4, JavaScript |
| **Bundler** | [Vite 8](https://vite.dev) |
| **Database** | MySQL |
| **Real-time** | Pusher + Laravel Echo |
| **Export Excel** | PhpSpreadsheet |
| **Email** | SMTP (OTP untuk reset password) |

## ⚙️ Cara Instalasi (Local Development)

### Prasyarat
- PHP ≥ 8.3
- Composer
- Node.js & npm
- MySQL
- Laragon / XAMPP / Valet (atau server lokal lainnya)

### Langkah Instalasi

1. **Clone repositori:**
   ```bash
   git clone https://github.com/adriansa2009/pro-web.git
   cd pro-web
   ```

2. **Instal dependensi PHP:**
   ```bash
   composer install
   ```

3. **Instal dependensi Node.js:**
   ```bash
   npm install
   npm run build
   ```

4. **Salin file environment dan atur konfigurasi:**
   ```bash
   cp .env.example .env
   ```
   Sesuaikan nilai berikut di `.env`:
   - `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` — kredensial database MySQL
   - `MAIL_*` — konfigurasi SMTP untuk OTP reset password
   - `PUSHER_*` — kredensial Pusher (opsional, untuk notifikasi *real-time*)

5. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

6. **Jalankan migrasi dan seeder:**
   ```bash
   php artisan migrate --seed
   ```

7. **Jalankan server pengembangan:**
   ```bash
   npm run dev
   php artisan serve
   ```

8. Buka browser → `http://localhost:8000`

### Akun Default (Seeder)

| Role | Email | Password |
|------|-------|----------|
| Admin Gudang | *(lihat seeder)* | *(lihat seeder)* |
| Manajer | *(lihat seeder)* | *(lihat seeder)* |

> Jalankan `php artisan db:seed` untuk membuat akun default. Periksa file seeder di `database/seeders/` untuk kredensial.

## 📁 Struktur Proyek

```
pro-web/
├── app/
│   ├── Http/Controllers/
│   │   ├── admin/          # Controller Admin Gudang
│   │   ├── manajer/        # Controller Manajer
│   │   └── Auth/           # Autentikasi & Reset Password
│   ├── Models/             # Eloquent Models (Barang, BarangMasuk, BarangKeluar, Supplier, ...)
│   ├── Events/             # Event Pusher (LowStockNotification)
│   ├── Mail/               # Mailable (SendOtpMail)
│   └── Traits/             # HasStockAlert (trait notifikasi stok)
├── database/
│   ├── migrations/         # Skema database
│   └── seeders/            # Data awal
├── resources/views/        # Blade templates
├── routes/
│   └── web.php             # Definisi route
└── public/                 # Asset publik (build, images)
```

## 🔐 Hak Akses (Role)

| Fitur | Admin Gudang | Manajer |
|-------|:---:|:---:|
| Dashboard | ✅ | ✅ |
| CRUD Barang | ✅ | ❌ (read-only) |
| Barang Masuk / Keluar | ✅ | ❌ |
| Supplier & Kategori | ✅ | ❌ |
| Manajemen Pengguna | ✅ | ❌ |
| Ekspor Laporan | ✅ | ✅ |
| Notifikasi Stok Rendah | ✅ | ❌ |
| Pengaturan Profil | ✅ | ✅ |

## 📄 License

Proyek ini dilisensikan di bawah [MIT License](LICENSE).


