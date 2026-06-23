# ☕ Smart Coffee CRM

Sistem Customer Relationship Management (CRM) berbasis web untuk kedai kopi, dibangun dengan **Laravel 13**, **Livewire 4**, dan **Flux UI**. Dilengkapi dengan algoritma **K-Nearest Neighbors (KNN)** untuk segmentasi perilaku pelanggan secara otomatis.

---

## 📋 Fitur Utama

### 👨‍💼 Admin
| Fitur | Deskripsi |
|---|---|
| **Dashboard Analitik** | Ringkasan metrik CRM: total member, revenue, distribusi tier, scatter-plot clustering pelanggan |
| **Manajemen Menu** | CRUD menu kopi, minuman non-kopi, dan makanan |
| **Manajemen Akun** | Kelola akun admin, kasir, & member — edit tier, label perilaku, dan poin |
| **Katalog Reward** | Kelola reward (voucher, merchandise, produk gratis) |
| **Broadcast Campaign** | Kirim promo massal ke semua member via simulasi WhatsApp & Email |
| **Export Laporan** | Download laporan member, transaksi (Excel), dan rekap penjualan (PDF) |

### 🖥️ Kasir
| Fitur | Deskripsi |
|---|---|
| **Kasir POS** | Point of Sale: pencarian member, keranjang belanja, kupon promo, tukar poin, dan cetak struk |
| **Riwayat Transaksi** | Lihat semua transaksi yang telah diproses dengan filter tanggal dan pencarian member |
| **Cari Member** | Lookup profil member (read-only): tier, poin, pengeluaran, segmen KNN, dan 5 transaksi terakhir |

### 👤 Member
| Fitur | Deskripsi |
|---|---|
| **Loyalty Dashboard** | Info poin, tier, rekomendasi menu berbasis KNN, dan promo personal |
| **Pesan Menu** | Lihat katalog menu dan pesan langsung |
| **Katalog Reward** | Tukar poin loyalitas dengan reward |

---

## ⚙️ Persyaratan Sistem

- **PHP** >= 8.4
- **Composer** >= 2.x
- **Node.js** >= 18.x & **npm** >= 9.x
- **SQLite** (default, sudah built-in di PHP)

> [!NOTE]
> Project ini menggunakan SQLite sebagai database default, sehingga tidak perlu setup MySQL/PostgreSQL.

---

## 🚀 Cara Menjalankan

### 1. Clone Repository

```bash
git clone https://github.com/username/smart-coffee-crm.git
cd smart-coffee-crm
```

### 2. Install Dependency PHP

```bash
composer install
```

### 3. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Buat Database & Jalankan Migrasi + Seeder

```bash
php artisan migrate --seed
```

> [!TIP]
> Perintah `--seed` akan mengisi database dengan data sample: 1 admin, 1 kasir, 1 member test (Rian), dan 45 pelanggan berlabel untuk dataset training KNN.

### 5. Install Dependency Frontend

```bash
npm install
```

### 6. Jalankan Aplikasi (Development)

Cara termudah: jalankan semua service sekaligus dengan satu perintah:

```bash
composer dev
```

Perintah ini akan menjalankan secara bersamaan:
- 🌐 **Laravel Server** di `http://localhost:8000`
- 📡 **Queue Worker** untuk background jobs
- ⚡ **Vite Dev Server** untuk hot-reload asset frontend

> [!IMPORTANT]
> Pastikan port `8000` (Laravel) dan port Vite tidak sedang digunakan oleh aplikasi lain.

### Alternatif: Jalankan Manual Terpisah

Jika `composer dev` tidak bekerja, jalankan masing-masing di terminal terpisah:

```bash
# Terminal 1 — Laravel Server
php artisan serve

# Terminal 2 — Vite Dev Server
npm run dev

# Terminal 3 — Queue Worker (opsional, untuk notifikasi)
php artisan queue:listen --tries=1
```

---

## 🔑 Akun Default

Setelah menjalankan seeder, gunakan akun berikut untuk login:

| Role | Email | Password |
|------|-------|----------|
| **Admin** | `admin@coffee.com` | `password` |
| **Kasir** | `kasir@coffee.com` | `password` |
| **Member** | `member@coffee.com` | `password` |

---

## 🔐 Hak Akses (Role-Based Access Control)

```
Admin   → /admin/*   (Dashboard, Menu, Akun, Reward, Laporan)
Kasir   → /kasir/*   (POS, Riwayat Transaksi, Cari Member)
Member  → /member/*  (Loyalty Dashboard, Pesan Menu, Reward)
```

Setiap role hanya bisa mengakses halaman sesuai hak aksesnya. Jika mencoba mengakses halaman role lain, akan di-redirect otomatis.

---

## 📁 Struktur Folder Penting

```
smart-coffee-crm/
├── app/
│   ├── Livewire/
│   │   ├── Admin/          # Komponen admin: Dashboard, Menu, User, Reward
│   │   ├── Kasir/          # Komponen kasir: Cashier, TransactionHistory, MemberLookup
│   │   └── Member/         # Komponen member: Dashboard, Order, Reward
│   ├── Models/             # Eloquent models (User, Transaction, Reward, dll)
│   ├── Http/
│   │   └── Middleware/
│   │       └── CheckRole.php  # Middleware hak akses multi-role
│   └── Services/
│       └── KNearestNeighborsService.php  # Implementasi algoritma KNN
├── database/
│   ├── migrations/         # Skema tabel database
│   └── seeders/            # Data sample & training dataset KNN
├── resources/views/
│   ├── livewire/           # Blade templates untuk komponen Livewire
│   └── layouts/            # Layout utama aplikasi
├── routes/
│   └── web.php             # Definisi rute aplikasi
└── .env.example            # Template konfigurasi environment
```

---

## 🔄 Reset Database

Jika ingin mengulang dari awal (hapus semua data dan isi ulang):

```bash
php artisan migrate:fresh --seed
```

---

## 🛠️ Build untuk Produksi

```bash
npm run build
```

---

## 📝 Tech Stack

- **Backend**: Laravel 13, Livewire 4
- **Frontend**: Flux UI (Livewire), Tailwind CSS 4, Vite 8
- **Database**: SQLite
- **Algoritma**: K-Nearest Neighbors (KNN) untuk segmentasi pelanggan
- **Auth**: Laravel Fortify

---

## 📄 Lisensi

Project ini dibuat untuk keperluan tugas mata kuliah CRM.
