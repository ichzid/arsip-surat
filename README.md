# Arsip Surat Laravel - Sistem Arsip Surat dan Disposisi

Arsip Surat Laravel adalah aplikasi pengelolaan surat masuk, surat keluar, disposisi, dan arsip dokumen internal instansi dalam satu sistem terintegrasi.

Proyek ini dirancang untuk membantu instansi atau organisasi mengelola alur persuratan secara lebih rapi, terdokumentasi, dan mudah ditelusuri melalui dashboard admin berbasis web.

---

## Fitur Utama

- Dashboard ringkas untuk memantau jumlah surat masuk, surat keluar, dan disposisi yang masih pending.
- Manajemen surat masuk lengkap dengan nomor agenda otomatis, asal surat, tanggal surat, tanggal diterima, status, dan lampiran PDF.
- Manajemen surat keluar lengkap dengan tujuan surat, nomor surat, divisi pembuat, tanggal surat, dan lampiran PDF.
- Disposisi surat masuk antar divisi dengan catatan, instruksi, tenggat waktu, dan status tindak lanjut.
- Notifikasi disposisi berbasis database untuk pengguna pada divisi tujuan.
- Cetak lembar disposisi surat ke format PDF.
- Laporan arsip surat berdasarkan periode, jenis surat, dan filter divisi tertentu.
- Export laporan surat ke file Excel.
- Manajemen pengguna beserta pembagian role dan divisi.
- Manajemen divisi/unit kerja.
- Pengaturan identitas aplikasi dan instansi yang dapat diubah dari panel admin.
- Hak akses berbasis role menggunakan permission system.
- Tampilan admin responsif berbasis Inertia.js dan Vue.

---

## Modul Sistem

- Login dan autentikasi pengguna
- Dashboard
- Surat Masuk
- Detail Surat Masuk
- Disposisi Surat
- Cetak Lembar Disposisi
- Surat Keluar
- Detail Surat Keluar
- Laporan Arsip Surat
- Export Excel
- Notifikasi
- Manajemen Pengguna
- Manajemen Divisi
- Pengaturan Aplikasi dan Instansi
- Profil Pengguna

---

## Role Pengguna

- Admin
- Operator Divisi Umum
- Operator Divisi
- Pimpinan

Setiap role memiliki cakupan akses berbeda. Admin dan pimpinan memiliki akses paling luas, sedangkan operator divisi hanya melihat data yang relevan dengan divisinya.

---

## Tech Stack

- Backend: Laravel 12
- Frontend: Vue 3 + Inertia.js 2
- Styling: Tailwind CSS
- Build Tool: Vite 7
- Database Lokal: SQLite
- Authentication Starter: Laravel Breeze
- Authorization: Spatie Laravel Permission
- Export Excel: Laravel Excel
- Generate PDF: Laravel DomPDF

---

## Cara Menjalankan Secara Lokal

Opsi cepat sekali jalan:

```bash
composer run setup
```

Jika ingin setup manual, ikuti langkah berikut.

### 1. Clone repo ini

```bash
git clone https://github.com/ichzid/arsip-surat.git
cd arsip-surat
```

### 2. Install dependensi backend dan frontend

```bash
composer install
npm install
```

### 3. Siapkan file environment

```bash
cp .env.example .env
```

Jika menggunakan Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Lalu generate application key:

```bash
php artisan key:generate
```

### 4. Siapkan database SQLite

Buat file database SQLite:

```bash
touch database/database.sqlite
```

Jika menggunakan Windows PowerShell:

```powershell
New-Item -ItemType File -Path database/database.sqlite -Force
```

Pastikan konfigurasi database di file `.env` menggunakan SQLite, misalnya:

```env
DB_CONNECTION=sqlite
```

### 5. Jalankan migrasi, seeder, dan storage link

```bash
php artisan migrate --seed
php artisan storage:link
```

### 6. Jalankan aplikasi

Opsi paling praktis:

```bash
composer run dev
```

Atau jalankan manual:

```bash
php artisan serve
npm run dev
```

Aplikasi akan berjalan di `http://localhost:8000` untuk server Laravel dan Vite untuk asset development.

---

## Akun Demo Seeder

Setelah menjalankan `php artisan migrate --seed`, akun demo yang tersedia adalah:

```txt
Admin
Email    : admin@kominfo.go.id
Password : password

Operator Divisi Umum
Email    : sekretariat@kominfo.go.id
Password : password

Operator APTIKA
Email    : aptika@kominfo.go.id
Password : password

Operator IKP
Email    : ikp@kominfo.go.id
Password : password

Operator Statistik
Email    : statistik@kominfo.go.id
Password : password

Pimpinan
Email    : kadis@kominfo.go.id
Password : password
```

---

## Catatan Penggunaan

- Upload file surat dibatasi untuk file PDF dengan ukuran maksimal 5 MB.
- Surat masuk memiliki nomor agenda otomatis dengan format harian.
- Surat keluar otomatis mengikuti divisi pengguna yang membuat surat.
- Notifikasi disposisi disimpan ke database dan muncul untuk pengguna pada divisi tujuan.
- Lembar disposisi dapat dicetak ke PDF dari detail surat masuk.
- Modul laporan mendukung tampilan cetak dan export Excel berdasarkan filter periode.
- Jika menggunakan file upload, jalankan `php artisan storage:link` agar file dapat diakses dari browser.

---

## Testing

Untuk menjalankan pengujian:

```bash
php artisan test
```

Project ini sudah memiliki feature test untuk autentikasi, surat masuk, surat keluar, disposisi, laporan, notifikasi, pengaturan, dan profil.

---

## Pengembangan Lanjutan

Beberapa area yang masih bisa dikembangkan lebih lanjut:

- Approval workflow disposisi yang lebih detail per level jabatan.
- Pencarian lanjutan dan filter arsip yang lebih kompleks.
- Dukungan upload format dokumen selain PDF bila dibutuhkan instansi.
- Audit log aktivitas pengguna.
- Integrasi email atau notifikasi real-time.
- Dashboard statistik yang lebih lengkap untuk kebutuhan pimpinan.

---

## Dukung Pengembangan

Jika proyek ini bermanfaat dan Anda ingin mendukung pengembangan selanjutnya, Anda dapat memberikan dukungan melalui Saweria:

https://saweria.co/ichzid

---

Dibuat untuk membantu instansi mengelola persuratan internal, disposisi, dan arsip digital secara lebih rapi, cepat, dan terstruktur.
