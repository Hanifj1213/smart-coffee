# 🚀 Tutorial Deploy Smart Coffee ke VPS (HestiaCP)

> **Subdomain:** `kelas-c-8.informatika-unjedir.web.id`
> **Server:** `informatika-unjedir.web.id`
> **Panel:** `https://43.134.97.197:8083/`
> **Repo GitHub:** `https://github.com/Hanifj1213/smart-coffee.git`

---

## LANGKAH 1 — SSH ke VPS

Buka **PowerShell** atau **CMD**, lalu ketik:

```bash
ssh kelas-c@informatika-unjedir.web.id
```

Masukkan password saat diminta:
```
Kk3#ktFSW|/td+B6
```

> Setelah berhasil login, kamu akan melihat prompt seperti `kelas-c@server:~$`

---

## LANGKAH 2 — Cek Versi PHP & Composer

Jalankan satu per satu:

```bash
php -v
composer --version
node -v
npm -v
```

> Pastikan PHP >= 8.2, Composer tersedia, dan Node.js + npm tersedia.
> Jika `node` atau `npm` tidak ditemukan, lihat **Langkah 2B** di bawah.

### Langkah 2B — Install Node.js (jika belum ada)

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs
```

Atau jika tidak punya akses sudo, pakai NVM:

```bash
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.3/install.sh | bash
source ~/.bashrc
nvm install 20
```

---

## LANGKAH 3 — Buat Database MySQL di HestiaCP

1. Buka HestiaCP Panel (`https://43.134.97.197:8083/`)
2. Masuk ke menu **DB** (Database).
3. Klik **Add Database**.
4. Isi data berikut:
   - **Database**: `smartcoffee` (nanti jadinya misal `kelas-c_smartcoffee`)
   - **User**: `smartcoffee` (nanti jadinya misal `kelas-c_smartcoffee`)
   - **Password**: (Generate password dan simpan baik-baik)
5. Klik **Save**.

---

## LANGKAH 4 — Masuk ke Folder Web

```bash
cd /home/kelas-c/web/kelas-c-8.informatika-unjedir.web.id
ls
```

> Kamu akan melihat folder seperti `public_html`, `private`, dll.

---

## LANGKAH 5 — Clone Project dari GitHub

```bash
# Hapus isi public_html default
rm -rf public_html
mkdir public_html

# Clone project ke folder terpisah
git clone https://github.com/Hanifj1213/smart-coffee.git app
```

> Project sekarang ada di folder `app/`

---

## LANGKAH 6 — Install Dependencies

```bash
cd app

# Install PHP dependencies
composer install --no-dev --optimize-autoloader --ignore-platform-req=php

# Install Node dependencies & build assets
npm install
npm run build
```

> Tunggu sampai selesai. Proses `npm run build` akan menghasilkan folder `public/build/`.

---

## LANGKAH 7 — Setup Environment

```bash
# Copy file .env
cp .env.example .env

# Generate APP_KEY
php artisan key:generate
```

Sekarang edit file `.env`:

```bash
nano .env
```

Ubah baris-baris berikut (sesuaikan DB_* dengan data dari Langkah 3):

```env
APP_NAME="Smart Coffee"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://kelas-c-8.informatika-unjedir.web.id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kelas-c_smartcoffee
DB_USERNAME=kelas-c_smartcoffee
DB_PASSWORD=password_yang_digenerate_tadi
```

> Tekan `Ctrl+O` lalu `Enter` untuk save, `Ctrl+X` untuk keluar dari nano.

---

## LANGKAH 8 — Jalankan Migrasi Database MySQL

```bash
# Jalankan migrasi + seeder
php artisan migrate --seed
```

> Ini akan membuat semua tabel di MySQL HestiaCP dan mengisi data awal (menu, reward, user admin, dll).

---

## LANGKAH 9 — Set Permissions

```bash
chmod -R 775 storage bootstrap/cache database
```

---

## LANGKAH 10 — Hubungkan ke public_html

Laravel mengharuskan web server mengarah ke folder `public/` saja (bukan root project).
Kita gunakan **symlink** agar HestiaCP bisa mengarah ke folder yang benar.

```bash
# Kembali ke folder web domain
cd /home/kelas-c/web/kelas-c-8.informatika-unjedir.web.id

# Hapus public_html yang kosong
rm -rf public_html

# Buat symlink dari public_html ke folder public Laravel
ln -s /home/kelas-c/web/kelas-c-8.informatika-unjedir.web.id/app/public public_html
```

### Verifikasi:

```bash
ls -la public_html
```

> Harus muncul: `public_html -> /home/kelas-c/web/.../app/public`

---

## LANGKAH 11 — Optimize untuk Production

```bash
cd /home/kelas-c/web/kelas-c-8.informatika-unjedir.web.id/app

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

## LANGKAH 12 — Test!

Buka browser dan akses:

```
https://kelas-c-8.informatika-unjedir.web.id
```

### Login Admin:
- **Email:** `admin@coffee.com`
- **Password:** `password`

### Login Member (contoh):
- **Email:** `budi@member.com`
- **Password:** `password`

---

## ⚠️ TROUBLESHOOTING

### Error 403 Forbidden
Symlink mungkin tidak diizinkan oleh Apache. Coba cara alternatif:

```bash
# Hapus symlink
cd /home/kelas-c/web/kelas-c-8.informatika-unjedir.web.id
rm public_html

# Copy semua isi public ke public_html
mkdir public_html
cp -r app/public/* public_html/
cp app/public/.htaccess public_html/

# Edit index.php agar mengarah ke folder app
nano public_html/index.php
```

Di `public_html/index.php`, ubah 2 baris path:

```php
// SEBELUM (baris paling bawah file):
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// SESUDAH:
require __DIR__.'/../app/vendor/autoload.php';
$app = require_once __DIR__.'/../app/bootstrap/app.php';
```

Simpan (`Ctrl+O`, `Enter`, `Ctrl+X`).

### Error 500 Internal Server Error
```bash
# Cek log error
tail -50 /home/kelas-c/web/kelas-c-8.informatika-unjedir.web.id/app/storage/logs/laravel.log

# Pastikan permission benar
cd /home/kelas-c/web/kelas-c-8.informatika-unjedir.web.id/app
chmod -R 775 storage bootstrap/cache database
```

### Error "Class not found" atau Autoload
```bash
cd /home/kelas-c/web/kelas-c-8.informatika-unjedir.web.id/app
composer dump-autoload --ignore-platform-req=php
php artisan optimize:clear
php artisan optimize
```

### Halaman Blank / CSS Tidak Muncul
```bash
# Pastikan build sudah jalan
cd /home/kelas-c/web/kelas-c-8.informatika-unjedir.web.id/app
npm run build

# Cek apakah file build ada
ls public/build/
```

### Database Error / Table Not Found
```bash
cd /home/kelas-c/web/kelas-c-8.informatika-unjedir.web.id/app
php artisan migrate:fresh --seed
```

---

## 🔄 CARA UPDATE SETELAH ADA PERUBAHAN

Setiap kali kamu push perubahan baru ke GitHub:

```bash
ssh kelas-c@informatika-unjedir.web.id
cd /home/kelas-c/web/kelas-c-8.informatika-unjedir.web.id/app

git pull origin main
composer install --no-dev --optimize-autoloader --ignore-platform-req=php
npm install && npm run build
php artisan migrate --force
php artisan optimize
```

---

## ✅ CHECKLIST FINAL

- [ ] SSH ke VPS berhasil
- [ ] PHP, Composer, Node.js tersedia
- [ ] Database MySQL berhasil dibuat via HestiaCP
- [ ] Project di-clone dari GitHub
- [ ] `composer install` berhasil
- [ ] `npm run build` berhasil
- [ ] File `.env` sudah dikonfigurasi dengan kredensial MySQL
- [ ] Database berhasil di-migrate+seed
- [ ] Permission storage/bootstrap/database sudah 775
- [ ] public_html mengarah ke folder public Laravel
- [ ] Website bisa diakses dari browser
- [ ] Login admin berhasil
