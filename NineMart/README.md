# 🛒 NineMart — Aplikasi Penjualan Online SMK Negeri 9 Semarang
### Bahan Tryout Uji Kompetensi Keahlian (UKK) Klaster Pemrograman Web

Aplikasi web referensi standar industri yang dibangun sesuai dengan **Instrumen Soal Tryout UKK Klaster Pemrograman Web (SMK Negeri 9 Semarang)**.

---

## 📸 Presisi UI / UX Sesuai Lembar Uji
Antarmuka sistem ini dibuat **100% identik dan presisi** dengan screenshot dokumen soal:
1. **Layar Login:** Background Lawang Sewu Semarang (`banner.jpeg`), kartu login melayang dengan header mini-banner, judul `LOGIN SISTEM`, ikon 3D kunci emas (`key-icon.png`), input username/password, dan footer `NINE MART TOKO NYA SMKN 9 SEMARANG | 2024`.
2. **Layar Dashboard:** Kotak salam kuning terang `#ffff00` berisi teks *SUGENG RAWUH ... |!!!!*, dengan latar belakang kanvas hijau terang `#00ff00`.
3. **Menu Pengguna:** Dropdown menu *Data Pengguna* dan *Ubah Password*.
4. **Data Pengguna:** Tabel pengguna dengan header slate blue `#8da0c4`, tombol tambah plus bulat perak (`icon-plus.png`), dan tombol aksi hapus silang merah (`icon-delete.png`).
5. **Ubah Password:** Form validasi password lama dan password baru maksimal 10 karakter.
6. **Menu Data:** Dropdown menu *Data Barang*, *Data Pemasok*, dan *Data Kelompok*.
7. **Data Barang, Pemasok, Kelompok:** Header kolom `Aksi` berwarna biru `#0000ff`, ikon edit pensil kuning (`icon-edit.png`) dan tombol hapus silang merah (`icon-delete.png`).
8. **Footer Sistem:** Baris teks putih di bagian bawah: `| APLIKASI PENJUALAN ONLINE | NINE MART | SMK NEGERI 9 SEMARANG - 2024 |`.

---

## 🗂️ Arsitektur Direktori Proyek

```text
NineMart/
├── config/
│   ├── database.php        # Konfigurasi PDO MySQL & helper escape()
│   └── auth.php            # Session guard middleware
├── database/
│   └── db_ninemart.sql     # Skema database & seeding data awal sesuai soal
├── assets/
│   ├── css/
│   │   └── style.css       # Styling kustom anti-slop persis lembar soal
│   └── img/
│       ├── banner.jpeg     # Background Lawang Sewu (Login)
│       ├── header-banner.png # Banner ungu NineMart & garis kuning
│       ├── key-icon.png    # Ikon kunci login
│       ├── icon-plus.png   # Ikon tambah bulat perak
│       ├── icon-edit.png   # Ikon pensil edit
│       └── icon-delete.png # Ikon silang hapus
├── includes/
│   ├── header.php          # Template header banner & navbar menu
│   └── footer.php          # Template footer & script modal
├── login.php               # Halaman autentikasi sistem
├── logout.php              # Penghancuran sesi dan redirect ke login
├── index.php               # Dashboard ucapan Sugeng Rawuh
├── pengguna.php            # Master Data Pengguna (CRUD)
├── ubah_password.php       # Form perubahan kata sandi
├── barang.php              # Master Data Barang (CRUD + Dropdown Kelompok)
├── pemasok.php             # Master Data Pemasok (CRUD)
├── kelompok.php            # Master Data Kelompok (CRUD)
├── test_queries.php        # Skrip pengujian otomatis database
└── README.md               # Dokumentasi modul
```

---

## 🔑 Akun Login Administrator Default

Setelah mengimpor database, Anda dapat login menggunakan salah satu akun berikut:

| Username | Password Default | Hak Akses |
| :--- | :--- | :--- |
| `admin` | `password` (atau `admin`) | Administrator |
| `user` | `password` (atau `user`) | Operator |
| `adi` | `password` (atau `adi`) | Operator |

---

## 🚀 Panduan Instalasi & Menjalankan

### 1. Impor Database MySQL
1. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Buat database `db_ninemart`.
3. Impor berkas `database/db_ninemart.sql`.
4. Tabel yang akan terbentuk: `tblpengguna`, `tblkelompok`, `tblpemasok`, `tblbarang`.

### 2. Jalankan Server Web
Jalankan melalui XAMPP/Laragon atau jalankan PHP built-in server dari root `BelajarUKK`:
```bash
cd /home/murtix/belajar/BelajarUKK
php -S 127.0.0.1:8080
```
Buka browser pada: [`http://localhost:8080/NineMart/`](http://localhost:8080/NineMart/)

---

## 🧪 Validasi Pengujian
Jalankan pengujian sintaks PHP:
```bash
find NineMart/ -name "*.php" -exec php -l {} +
```
Jalankan pengujian query otomatis:
```bash
php NineMart/test_queries.php
```

