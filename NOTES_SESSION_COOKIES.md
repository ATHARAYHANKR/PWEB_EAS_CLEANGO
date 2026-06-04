# SESSION & COOKIES CONFIGURATION NOTES

## 📋 File yang Dikonfigurasi

### 1. `.env` (Root Project)
**Status:** ✅ DIUPDATE
- Menambahkan lengkap konfigurasi SESSION di `.env`
- Semua opsi session & cookies sekarang bisa di-customize via `.env`

### 2. `config/session.php`
**Status:** ✅ SUDAH ADA (built-in Laravel)
- File ini sudah menggunakan environment variables dari `.env`
- Tidak perlu diubah, semua sudah fleksibel

---

## 🔧 KONFIGURASI SESSION & COOKIES DI `.env`

### Session Driver Options
```env
# Pilihan: file, cookie, database, memcached, redis, dynamodb, array
SESSION_DRIVER=database
```
- **database** → Menyimpan session di tabel `sessions` (recommended untuk production)
- **file** → Menyimpan session di file (gunakan untuk development ringan)
- **cookie** → Session di cookie (tidak secure)
- **redis** → Menyimpan di Redis (fast, scalable)

### Session Lifetime
```env
SESSION_TABLE=sessions
SESSION_LIFETIME=120           # Menit (120 = 2 jam)
SESSION_EXPIRE_ON_CLOSE=false  # true = expire saat browser ditutup
```

### Session Encryption
```env
SESSION_ENCRYPT=false  # true untuk enkripsi session data
```

---

## 🍪 COOKIE CONFIGURATION

| Opsi | Nilai | Penjelasan |
|------|-------|-----------|
| `SESSION_COOKIE` | `cleango-session` | Nama cookie yang disimpan di browser |
| `SESSION_PATH` | `/` | Path dimana cookie berlaku |
| `SESSION_DOMAIN` | (kosong) | Domain cookie - kosong = semua subdomain |
| `SESSION_SECURE_COOKIE` | `false` | true = hanya dikirim via HTTPS (production) |
| `SESSION_HTTP_ONLY` | `true` | true = prevent JavaScript access (keamanan) |
| `SESSION_SAME_SITE` | `lax` | Opsi: `lax`, `strict`, `none`, `null` |

---

## 📌 REKOMENDASI SETUP

### Development (localhost)
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_EXPIRE_ON_CLOSE=false
SESSION_SECURE_COOKIE=false
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

### Production
```env
SESSION_DRIVER=redis          # atau database
SESSION_LIFETIME=60           # lebih pendek
SESSION_EXPIRE_ON_CLOSE=true  # logout saat browser ditutup
SESSION_ENCRYPT=true          # enkripsi data
SESSION_SECURE_COOKIE=true    # HTTPS only
SESSION_HTTP_ONLY=true        # prevent JS access
SESSION_SAME_SITE=strict      # keamanan maksimal
```

---

## 🗄️ CREATE SESSIONS TABLE

Jika menggunakan `SESSION_DRIVER=database`, pastikan tabel `sessions` sudah ada:

```bash
php artisan session:table
php artisan migrate
```

---

## 🚀 TESTING SESSION

Di controller, test dengan:
```php
Session::put('test', 'value');
echo Session::get('test');  // output: value
Session::forget('test');
```

Di view Blade:
```blade
{{ session('test') }}
```

---

## ⚙️ CARA CUSTOMIZE

1. Edit `.env` dengan opsi yang diinginkan
2. Clear cache: `php artisan config:clear`
3. Restart server jika diperlukan

**Contoh:** Mengubah session lifetime menjadi 30 menit
```env
SESSION_LIFETIME=30
```
Lalu jalankan: `php artisan config:clear`

---

## 🔐 SECURITY NOTES

✅ **Sudah Aman:**
- `SESSION_HTTP_ONLY=true` → JS tidak bisa akses cookie
- `SESSION_SAME_SITE=lax` → CSRF protection

⚠️ **Untuk Production:**
- Set `SESSION_SECURE_COOKIE=true`
- Set `SESSION_ENCRYPT=true`
- Set `SESSION_SAME_SITE=strict`
- Gunakan `SESSION_DRIVER=redis` untuk performance

---

**Updated:** Juni 4, 2026
**File Config:** `config/session.php`, `.env`
