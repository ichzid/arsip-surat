<div align="center">

<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="320" alt="Laravel Logo">

# 📬 Sistem Manajemen Arsip Surat

**Aplikasi web berbasis Laravel untuk pengelolaan surat masuk, surat keluar, dan disposisi pada instansi pemerintahan**

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=flat-square&logo=vue.js&logoColor=white)](https://vuejs.org)
[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-2.x-9553E9?style=flat-square)](https://inertiajs.com)
[![License](https://img.shields.io/badge/License-MIT-blue?style=flat-square)](LICENSE)

[Demo Live](http://arsip.ichmal.my.id) · [Laporkan Bug](https://github.com/ichzid/arsip-surat/issues) · [Request Fitur](https://github.com/ichzid/arsip-surat/issues)

</div>

---

## 📋 Tentang Sistem

**Sistem Manajemen Arsip Surat** adalah aplikasi web yang dirancang untuk membantu instansi pemerintahan dalam mengelola administrasi persuratan secara digital. Sistem ini menggantikan proses manual berbasis kertas dengan alur kerja yang terstruktur, transparan, dan mudah dilacak.

### ✨ Fitur Utama

| Fitur | Deskripsi |
|---|---|
| 📥 **Surat Masuk** | Pencatatan, upload dokumen PDF, dan penomoran agenda otomatis |
| 📤 **Surat Keluar** | Pembuatan dan pengarsipan surat keluar per divisi |
| 📋 **Disposisi** | Sistem disposisi surat antar bidang/divisi dengan notifikasi |
| 🖨️ **Cetak Lembar Disposisi** | Generate PDF lembar disposisi langsung dari sistem |
| 📊 **Laporan** | Laporan surat masuk & keluar dengan filter tanggal dan ekspor Excel |
| 👥 **Manajemen Pengguna** | CRUD pengguna dengan sistem role berbasis Spatie Permission |
| 🏢 **Manajemen Divisi** | Pengelolaan struktur organisasi bidang/divisi |
| ⚙️ **Pengaturan Sistem** | Konfigurasi nama instansi, logo, dan informasi kantor |

---

## 🔄 Alur Sistem

```
SURAT MASUK DITERIMA
        │
        ▼
[Operator Sekretariat mencatat surat]
  - Input data surat masuk
  - Upload file PDF
  - Nomor agenda digenerate otomatis
  - Status: NEW
        │
        ▼
[Pimpinan / Operator Sekretariat membuat disposisi]
  - Pilih divisi tujuan
  - Tambah catatan & instruksi
  - Tentukan batas waktu
  - Status surat: DISPOSITION
  - Notifikasi otomatis terkirim ke divisi tujuan
        │
        ▼
[Operator Divisi Penerima menindaklanjuti]
  - Melihat surat & disposisi masuk
  - Memperbarui status disposisi: PENDING → PROCESS → DONE
        │
        ▼
[Seluruh riwayat tersimpan & bisa dicetak]
  - Lembar disposisi bisa dicetak (PDF)
  - Laporan bisa diekspor ke Excel
```

---

## 👤 Role & Hak Akses

| Role | Hak Akses |
|---|---|
| **Admin** | Akses penuh ke seluruh fitur sistem, manajemen user & divisi |
| **Operator Sekretariat** *(Divisi Umum)* | CRUD surat masuk, membuat disposisi, melihat semua surat |
| **Operator Divisi** | Melihat surat & disposisi yang ditujukan ke divisinya, update status |
| **Pimpinan** | Membuat disposisi, melihat semua surat & laporan |

---

## 🔑 Akun Demo

> Password semua akun: **`password`**

| Role | Email |
|---|---|
| Admin | `admin@kominfo.go.id` |
| Operator Sekretariat | `sekretariat@kominfo.go.id` |
| Operator APTIKA | `aptika@kominfo.go.id` |
| Operator IKP | `ikp@kominfo.go.id` |
| Pimpinan | `kadis@kominfo.go.id` |

---

## 🛠️ Tech Stack

- **Backend**: Laravel 12, PHP 8.3
- **Frontend**: Vue.js 3, Inertia.js, Tailwind CSS
- **Database**: MySQL
- **Auth & RBAC**: Laravel Breeze + Spatie Laravel Permission
- **PDF**: barryvdh/laravel-dompdf
- **Excel Export**: Maatwebsite/Laravel-Excel
- **Build Tool**: Vite
- **CI/CD**: GitHub Actions

---

## 🚀 Instalasi Lokal

### Persyaratan
- PHP >= 8.3
- Composer
- Node.js >= 18
- MySQL

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/ichzid/arsip-surat.git
cd arsip-surat

# 2. Install PHP dependencies
composer install

# 3. Install JS dependencies
npm install

# 4. Salin file environment
cp .env.example .env
php artisan key:generate

# 5. Konfigurasi database di .env
# DB_DATABASE=arsip_surat
# DB_USERNAME=root
# DB_PASSWORD=your_password

# 6. Jalankan migrasi & seeder
php artisan migrate --seed

# 7. Buat symlink storage
php artisan storage:link

# 8. Jalankan server
php artisan serve
npm run dev
```

Akses aplikasi di: **http://localhost:8000**

---

## 📁 Struktur Modul

```
app/
├── Http/Controllers/
│   ├── IncomingLetterController.php   # Surat Masuk
│   ├── OutgoingLetterController.php   # Surat Keluar
│   ├── DispositionController.php      # Disposisi
│   ├── ReportController.php           # Laporan & Ekspor Excel
│   ├── UserController.php             # Manajemen Pengguna
│   ├── DivisionController.php         # Manajemen Divisi
│   └── SettingController.php          # Pengaturan Sistem
├── Models/
│   ├── IncomingLetter.php
│   ├── OutgoingLetter.php
│   ├── Disposition.php
│   ├── Division.php
│   ├── User.php
│   └── Setting.php
resources/js/Pages/
│   ├── Dashboard.vue
│   ├── IncomingLetters/
│   ├── OutgoingLetters/
│   ├── Reports/
│   ├── Users/
│   ├── Divisions/
│   └── Settings/
```

---

## 🔁 CI/CD

Sistem ini dilengkapi dengan pipeline **GitHub Actions** yang secara otomatis melakukan deployment ke VPS setiap kali ada push ke branch `main`.

```yaml
Push ke GitHub (main)
       ↓
GitHub Actions berjalan
       ↓
SSH ke VPS → git pull → composer install → npm build → migrate → cache
       ↓
✅ Aplikasi ter-update otomatis!
```

---

## 📄 Lisensi

Distributed under the MIT License. See `LICENSE` for more information.

---

<div align="center">

Dibuat dengan ❤️ menggunakan Laravel & Vue.js

</div>
