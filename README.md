# Sistem Monitoring Kendaraan

Aplikasi monitoring dan manajemen pemesanan kendaraan dengan sistem approval bertingkat.

## Persyaratan Sistem

-   PHP 8.3 atau lebih tinggi
-   Laravel 12
-   MySQL 8 atau lebih tinggi
-   Composer 2.0+

## Instalasi

1. Clone project ini

```bash
git clone https://github.com/aripzz/monitoring-kendaraan.git
cd monitoring-kendaraan
```

2. Install dependensi PHP

```bash
composer install
```

3. Salin file .env.example menjadi .env dan sesuaikan konfigurasi database

```bash
cp .env.example .env
```

4. Generate application key

```bash
php artisan key:generate
```

5. Jalankan migrasi database

```bash
php artisan migrate
```

6. Jalankan seeder untuk data awal

```bash
php artisan db:seed
```

## Data Login Default

Setelah menjalankan seeder, Anda dapat login menggunakan akun berikut:

-   **Admin**: admin@example.com
-   **Supervisor**: supervisor@example.com
-   **Manager**: manager@example.com
-   **Employee**: employee@example.com
-   **Driver 1**: driver1@example.com
-   **Driver 2**: driver2@example.com

**Password untuk semua akun**: 123123

## Panduan Penggunaan

### 1. Login Sistem

-   Buka aplikasi melalui browser
-   Masukkan email dan password
-   Sistem akan mengarahkan ke dashboard sesuai role pengguna

### 2. Pengaturan Awal (WAJIB - Login sebagai Admin)

**PENTING**: Sebelum melakukan pemesanan kendaraan, admin HARUS mengatur hierarki atasan terlebih dahulu:

-   Login menggunakan akun admin (admin@example.com)
-   Klik menu "Master User"
-   Untuk setiap user, klik tombol "Edit"
-   Pilih atasan/superior untuk user tersebut
-   Simpan pengaturan

**Contoh hierarki yang disarankan:**

-   Employee → Supervisor (sebagai atasan)
-   Manager → Supervisor (sebagai atasan)
-   Driver → Supervisor (sebagai atasan)
-   Admin → Supervisor (sebagai atasan)
-   Supervisor → Manager (sebagai atasan)

### 3. Pemesanan Kendaraan

-   Login dengan akun user yang sudah memiliki atasan
-   Klik menu "Pemesanan"
-   Klik tombol "Tambah Pemesanan"
-   Isi form pemesanan:
    -   Pilih kendaraan
    -   Pilih driver
    -   Isi tujuan penggunaan
    -   Tentukan waktu mulai dan selesai
-   Klik "Simpan" untuk mengajukan pemesanan

### 4. Approval Pemesanan

-   Atasan langsung dapat menyetujui/menolak pemesanan
-   Admin dapat mengubah driver sebelum approval level 0/admin
-   Status pemesanan:
    -   Pending: Menunggu persetujuan
    -   Approved: Disetujui
    -   Rejected: Ditolak
    -   Completed: Selesai

### 5. Manajemen Master Data (Admin)

-   Kelola Data User
    -   Tambah/edit/hapus user
    -   Atur atasan user
-   Kelola Data Kendaraan
    -   Tambah/edit/hapus kendaraan
    -   Lihat riwayat penggunaan

### 6. Laporan

-   Lihat laporan penggunaan kendaraan
-   Filter berdasarkan periode
-   Export data ke Excel

## Role dan Hak Akses

### Admin

-   Akses ke semua fitur
-   Manajemen master data
-   Approval pemesanan
-   Pengaturan driver

### User

-   Pengajuan pemesanan
-   Lihat status pemesanan
-   Lihat riwayat pemesanan

### Driver

-   Lihat jadwal tugas
-   Update status pemesanan

## Troubleshooting

### Masalah Umum

1. **Error 500**

    - Periksa log di `storage/logs/laravel.log`
    - Pastikan permission folder storage sudah benar

2. **Gagal Login**

    - Pastikan email dan password benar
    - Cek status user aktif/non-aktif

3. **Gagal Approval**

    - Pastikan hirarki atasan sudah dikonfigurasi
    - Cek role dan permission user

4. **Gagal Membuat Pemesanan**
    - Pastikan user sudah memiliki atasan yang ditentukan oleh admin
    - Jika muncul pesan "Tidak dapat membuat pemesanan karena user tidak memiliki atasan", hubungi admin untuk mengatur atasan
