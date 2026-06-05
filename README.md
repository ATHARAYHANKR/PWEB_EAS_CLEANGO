# 📖 Tutorial Setup CleanGo Project

Panduan lengkap untuk download, setup, dan menjalankan project CleanGo dari GitHub.

---

## 📋 Prerequisites (Persyaratan)

Sebelum memulai, pastikan sudah terinstall:

1. **XAMPP** (atau PHP environment lainnya)
   - PHP 8.2.12 atau lebih tinggi
   - MySQL/MariaDB
   - [Download XAMPP](https://www.apachefriends.org/)

2. **Composer** (PHP Package Manager)
   - [Download Composer](https://getcomposer.org/)
   - Gunakan untuk install dependencies Laravel

3. **Node.js & npm** (untuk frontend tools)
   - [Download Node.js](https://nodejs.org/)
   - npm akan terinstall otomatis bersama Node.js

4. **Git** (Version Control)
   - [Download Git](https://git-scm.com/)
   - Untuk clone repository dari GitHub

5. **Text Editor/IDE** (opsional tapi recommended)
   - Visual Studio Code
   - atau editor lainnya pilihan Anda

---

## 🚀 Step 1: Clone Repository dari GitHub

### 1.1 Buka Terminal/Command Prompt

Navigasi ke folder tempat Anda ingin menyimpan project:

```bash
cd D:\Xampp\htdocs
```

### 1.2 Clone Repository

```bash
git clone https://github.com/ATHARAYHANKR/PWEB_EAS_CLEANGO.git
cd PWEB_EAS_CLEANGO
```

**Hasil:**
- Project akan di-download ke folder `PWEB_EAS_CLEANGO`
- Semua file dari GitHub akan tersalin ke komputer Anda

---

## 🔧 Step 2: Install PHP Dependencies

Pastikan Anda sudah di dalam folder project:

```bash
cd D:\Xampp\htdocs\PWEB_EAS_CLEANGO
```

Jalankan Composer untuk install semua package PHP yang dibutuhkan:

```bash
composer install
```

**Penjelasan:**
- Command ini membaca file `composer.json`
- Mengunduh semua library PHP (Laravel, validation, excel export, dll)
- Membuat folder `vendor/` yang berisi semua dependencies
- Membutuhkan waktu beberapa menit

**Output yang diharapkan:**
```
Loading composer repositories with package information
Updating dependencies
...
Installing dependencies from lock file
...
✓ 100+ packages installed
```

---

## 📦 Step 3: Install Node Dependencies

Untuk frontend tools (Tailwind CSS, Vite, dll):

```bash
npm install
```

**Penjelasan:**
- Membaca file `package.json`
- Install semua JavaScript dependencies
- Membuat folder `node_modules/`

**Output yang diharapkan:**
```
added 500+ packages
```

---

## ⚙️ Step 4: Setup Environment File

Buat file `.env` dengan meng-copy `.env.example`:

### Option 1: Manual (Windows Command Prompt)
```cmd
copy .env.example .env
```

### Option 2: Manual (PowerShell)
```powershell
Copy-Item .env.example .env
```

### Option 3: Menggunakan Git Bash atau Terminal Linux/Mac
```bash
cp .env.example .env
```

---

## 🔐 Step 5: Generate Application Key

Jalankan command Laravel untuk generate encryption key:

```bash
php artisan key:generate
```

**Penjelasan:**
- Membuat APP_KEY di file `.env`
- Encryption key ini penting untuk security aplikasi
- Jika file `.env` belum ada, command akan membuat error

**Output yang diharapkan:**
```
Application key set successfully.
```

---

## 🗄️ Step 6: Konfigurasi Database

### 6.1 Buat Database Baru di MySQL

**Cara 1: Menggunakan phpMyAdmin (XAMPP)**
1. Buka browser, akses http://localhost/phpmyadmin
2. Login dengan user root (default: no password)
3. Klik menu "Databases"
4. Input nama: `cleango` (atau nama lain sesuai keinginan)
5. Klik "Create"

**Cara 2: Menggunakan MySQL Command Line**
```bash
mysql -u root -p
CREATE DATABASE cleango;
EXIT;
```

### 6.2 Update File `.env`

Edit file `.env` dan sesuaikan database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cleango
DB_USERNAME=root
DB_PASSWORD=
```

**Penjelasan konfigurasi:**
- `DB_CONNECTION=mysql` - Gunakan MySQL/MariaDB
- `DB_HOST=127.0.0.1` - Server lokal
- `DB_PORT=3306` - Port default MySQL
- `DB_DATABASE=cleango` - Nama database (sesuai yang dibuat)
- `DB_USERNAME=root` - User default XAMPP
- `DB_PASSWORD=` - Kosong (default XAMPP tidak pakai password)

---

## 🗄️ Step 7: Jalankan Database Migration

Buat semua table database secara otomatis:

```bash
php artisan migrate
```

**Penjelasan:**
- Membaca folder `database/migrations/`
- Membuat semua table di database secara berurutan
- Contoh table yang dibuat: users, orders, katalog, staff, dll
- Proses ini akan membuat struktur database lengkap

**Output yang diharapkan:**
```
Creating table cache
Creating table jobs
Creating table job_batches
Creating table users
Creating table owners
Creating table staff
Creating table layanan
Creating table katalog
Creating table orders
Creating table order_details
Creating table pembayaran
Creating table tracking
Creating table app_settings
...
Migration table created successfully.
✓ Migrated in 2.34s (Database)
```

---

## 🌱 Step 8: Seed Database (Opsional)

Isi database dengan data dummy/contoh:

```bash
php artisan db:seed
```

**Penjelasan:**
- Menjalankan file `database/seeders/`
- Insert data contoh ke database (users, services, packages, dll)
- Memudahkan testing tanpa harus input manual
- **Opsional** - bisa skip jika ingin database kosong

---

## 🔗 Step 9: Create Storage Symlink

Buat symlink agar foto yang diupload bisa diakses:

```bash
php artisan storage:link
```

**Penjelasan:**
- Membuat link dari `public/storage` ke `storage/app/public`
- Memungkinkan file yang diupload bisa diakses via HTTP
- Penting untuk menampilkan foto katalog, antar jemput, dll

**Output yang diharapkan:**
```
The [public/storage] link has been connected to [storage/app/public]. ✓
```

---

## 🎨 Step 10: Build Frontend Assets (Optional)

Compile Tailwind CSS dan JavaScript:

### Option 1: Build Production
```bash
npm run build
```

### Option 2: Development Mode dengan Auto-Reload
```bash
npm run dev
```

**Penjelasan:**
- `npm run build` - Compile assets sekali (production)
- `npm run dev` - Jalankan development server dengan hot reload
- Tidak wajib dilakukan untuk development awal, tapi recommended

---

## ✅ Step 11: Jalankan Development Server

Mulai Laravel development server:

```bash
php artisan serve
```

**Output yang diharapkan:**
```
   INFO  Server running on [http://127.0.0.1:8000].

  Press Ctrl+C to quit
```

**Sekarang project siap diakses!**
- Buka browser: http://localhost:8000
- Atau: http://127.0.0.1:8000

---

## 📝 Akun Login Default

Setelah seed database, gunakan akun berikut untuk login:

### Owner/Admin Login
```
Email: owner@cleango.com
Password: password
```

### Staff Login
```
Email: staff@cleango.com
Password: password
```

### Customer Login
```
Email: customer@cleango.com
Password: password
```

> **Catatan:** Ubah password setelah login pertama untuk keamanan lebih baik

---

## 🛠️ Session & Cookies Configuration

Project ini sudah dikonfigurasi dengan session database yang customizable.

### File Konfigurasi
- File `.env` - Setting environment variables
- File `config/session.php` - Konfigurasi Laravel session default

### Environment Variables untuk Session

Edit `.env` sesuai kebutuhan:

```env
# Session Driver (database, file, cookie, memcached, redis)
SESSION_DRIVER=database

# Nama tabel sessions di database
SESSION_TABLE=sessions

# Durasi session (dalam menit)
SESSION_LIFETIME=120

# Expire saat browser ditutup? (true/false)
SESSION_EXPIRE_ON_CLOSE=false

# Encrypt session payload? (true/false)
SESSION_ENCRYPT=false

# Nama cookie session
SESSION_COOKIE=cleango-session

# Path untuk cookie
SESSION_PATH=/

# Hanya kirim via HTTPS? (development: false, production: true)
SESSION_SECURE_COOKIE=false

# Cookie hanya accessible via HTTP (tidak via JavaScript)
SESSION_HTTP_ONLY=true

# SameSite policy (lax, strict, none)
SESSION_SAME_SITE=lax
```

**Setting yang Recommended:**

**Untuk Development:**
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=false
```

**Untuk Production:**
```env
SESSION_DRIVER=database
SESSION_LIFETIME=1440
SESSION_SECURE_COOKIE=true
```

---

## 🎯 Features yang Sudah Tersedia

✅ **Owner/Admin Dashboard**
- Manajemen Jenis Layanan (CRUD)
- Manajemen Katalog Layanan (CRUD)
- Manajemen Staff (CRUD)
- View Pesanan dan Status
- Pembatalan Order

✅ **Staff Dashboard**
- Ambil Order yang masuk
- Verifikasi Berat/Quantity
- Kirim Tagihan ke Customer
- Update Status Laundry
- Konfirmasi Pembayaran
- Update Profil Pribadi (nama, telepon, alamat, password)

✅ **Customer Dashboard**
- Booking/Pesan Layanan
- **Edit & Hapus Booking** (sebelum dikonfirmasi)
- Upload Foto Barang
- Pilih Jenis Layanan & Katalog
- Lihat Status Order Realtime
- Upload Bukti Pembayaran
- Tracking Pesanan

✅ **Fitur Global**
- Storage untuk Upload Foto
- Session Database untuk Tracking Pengguna
- Validation dan Error Handling
- Confirmation Modal untuk Aksi Penting
- Responsive Design dengan Tailwind CSS

---

## 🚨 Troubleshooting

### Error: "No application encryption key has been specified"
**Solusi:**
```bash
php artisan key:generate
```

### Error: "SQLSTATE[HY000]: General error: 1030"
**Solusi:** Database belum dibuat atau konfigurasi `.env` salah
1. Pastikan database `cleango` sudah dibuat di MySQL
2. Cek konfigurasi DB di file `.env`
3. Jalankan `php artisan migrate` ulang

### Error: "File not found 404" untuk foto
**Solusi:** Jalankan storage link
```bash
php artisan storage:link
```

### Error: Permission denied pada `storage/` atau `bootstrap/cache/`
**Solusi (Windows):** Berikan permission ke folder
- Klik kanan folder `storage` → Properties
- Klik tab "Security" → Edit
- Pilih user Anda → Check "Full Control"
- Apply dan OK

### Error saat `npm install`
**Solusi:**
1. Hapus folder `node_modules` dan file `package-lock.json`
2. Jalankan `npm install` lagi

---

## 📊 Struktur Database Utama

```
users (Pengguna)
├── id, name, email, password, role
└── role: owner, staff, customer

owners (Pemilik/Admin)
├── id, user_id, nama, notelp, alamat
└── Foreign key ke users

staff (Staf Laundry)
├── id, user_id, nama, username, notelp, alamat, is_active
└── Foreign key ke users

layanan (Jenis Layanan)
├── id, nama, deskripsi, harga_default
└── Contoh: Cuci, Setrika, Lengkap

katalog (Paket Layanan)
├── id, id_layanan, nama, deskripsi, harga, foto
└── Contoh: Cuci Cepat (2 hari), Cuci Standar (3 hari)

orders (Pesanan)
├── id, user_id, id_staff, tanggal, status
└── status: pending, pickup, process, ready, done

pembayaran (Pembayaran)
├── id, id_order, jumlah, status, bukti_upload
└── status: pending, confirmed, completed

tracking (Pelacakan)
├── id, id_order, status, waktu
└── Log perubahan status order
```

---

## 🔒 Security Notes

### Development vs Production

**Development (.env):**
```env
APP_DEBUG=true
SESSION_SECURE_COOKIE=false
MAIL_MAILER=log
```

**Production (.env):**
```env
APP_DEBUG=false
SESSION_SECURE_COOKIE=true
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
# ... konfigurasi email lainnya
```

### Password Security
- Database menggunakan bcrypt hashing
- Password tidak tersimpan dalam plain text
- Semua form menggunakan CSRF protection (token)
- HTTP-only cookies untuk session

---

## 📞 Support

Jika ada pertanyaan atau error:

1. **Check Laravel Documentation:** https://laravel.com/docs
2. **Check GitHub Issues:** https://github.com/ATHARAYHANKR/PWEB_EAS_CLEANGO/issues
3. **Review Console Errors:** 
   - Buka DevTools (F12) di browser
   - Lihat tab "Console" untuk JavaScript errors
4. **Check Laravel Logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

---

## ✨ Next Steps

Setelah project running:

1. **Explore Admin Dashboard**
   - Lihat menu Jenis Layanan, Katalog, Staff
   - Coba CRUD operations (Create, Read, Update, Delete)

2. **Test Booking Process**
   - Login sebagai customer
   - Buat booking baru
   - Upload foto dan bukti pembayaran

3. **Test Staff Dashboard**
   - Login sebagai staff
   - Lihat order yang masuk
   - Process order dari pickup sampai delivery

4. **Customize untuk Production**
   - Update database credentials
   - Setup email configuration
   - Enable HTTPS dan secure cookies
   - Setup backup system

---

## 📦 Project Info

- **Nama:** CleanGo - Laundry Service Management System
- **Framework:** Laravel 11.54.0
- **PHP Version:** 8.2.12+
- **Database:** MySQL/MariaDB
- **Frontend:** Blade, Tailwind CSS, JavaScript
- **Repository:** https://github.com/ATHARAYHANKR/PWEB_EAS_CLEANGO

---

**Happy Coding! 🎉**

Terakhir update: Juni 2026
