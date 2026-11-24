# Sistem-Informasi-MONITORIS-NTF

**Sistem Monitoring & Operasional Sales Motoris — CV. Netafarm Indolestari**  
*Distributor Sarana Pertanian, Hidroponik, Perikanan, Peternakan & Greenhouse (Sidoarjo, Jawa Timur)*

---

## 🌟 Fitur Utama

1. **Monitoring & Tracking Kunjungan GPS**
   - Check-in dan check-out berbasis GPS & Geofence di outlet pelanggan.
   - Deteksi presisi titik lokasi toko/kios mitra dan validasi jarak.
   - Histori kunjungan dan visualisasi rute jalan raya lapangan.

2. **Manajemen Outlet & Pelanggan**
   - Master data toko mitra, kontak pemilik, dan pemetaan wilayah.
   - Sales penanggung jawab per outlet dan pemantauan riwayat transaksi.

3. **Pencatatan Pesanan (Sales Order)**
   - Input pesanan langsung dari lapangan dengan katalog produk pertanian/hidroponik.
   - Manajemen pembayaran tunai, tempo, serta batas piutang (*credit limit*).
   - Sinkronisasi stok bawaan sales motoris.

4. **Progressive Web App (PWA) Ready**
   - Mendukung instalasi aplikasi di smartphone (Android/iOS) dan desktop.
   - Dilengkapi service worker caching dan manifest aplikasi.

5. **Akses Multi-Peran (Role-Based Access Control)**
   - **Admin**: Akses penuh operasional, master data, konfigurasi harga, dan laporan.
   - **Supervisor**: Monitoring tim sales motoris, approval plafon piutang, dan evaluasi KPI.
   - **Sales Motoris**: Ruang kerja mobile untuk kunjungan outlet, katalog, dan input penjualan.

---

## 🔐 Kredensial Pengujian (Demo)

| Peran | Email | Kata Sandi |
|---|---|---|
| **Admin** | `admin@motoris.test` | `password` |
| **Sales Motoris** | `sofan@motoris.test` | `password` |
| **Supervisor** | `supervisor@motoris.test` | `password` |

---

## 🚀 Panduan Menjalankan Aplikasi (Lokal)

### 1. Prasyarat
- PHP >= 8.2
- Composer
- Node.js & NPM
- SQLite / MySQL

### 2. Instalasi
```bash
# Clone repository
git clone https://github.com/sxxiklma/Sistem-Informasi-MONITORIS-NTF.git
cd Sistem-Informasi-MONITORIS-NTF

# Install dependensi backend & frontend
composer install
npm install

# Siapkan file environment & database
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Build asset frontend
npm run build

# Jalankan server lokal
php artisan serve
```

---

## 🏢 CV. Netafarm Indolestari
- **Alamat Kantor**: Jl. Raya Sawunggaling No.150 A, Jemundo, Kec. Taman, Kabupaten Sidoarjo, Jawa Timur
- **Sistem**: MotorisTrack Operational Platform
