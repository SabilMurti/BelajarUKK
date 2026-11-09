# 🚗 NineRental — Aplikasi Rental Mobil SMK Negeri 9 Semarang
### Bahan Uji Kompetensi Keahlian (UKK) LSP P1 SMK Negeri 9 Semarang

Aplikasi web manajemen rental mobil standar LSP P1 yang dibangun sesuai dengan **Instrumen Soal UKK LSP P1 SMKN 9 Semarang: Aplikasi Nine Rental Mobil**.

---

## 📸 Presisi UI / UX Sesuai Lembar Soal PDF
Seluruh tata letak, skema warna, dan elemen visual dibuat **identik dan presisi** dengan screenshot dokumen soal:
1. **Layar Dashboard:**
   - Outer frame kuning terang (`#ffff00`).
   - Header hijau cerah (`#00e600`) dengan tipografi serif besar `SMK` dan teks `SEKOLAH MENENGAH KEJURUAN KOTA SEMARANG`.
   - Subheader oranye (`#ff9900`) bertuliskan `APLIKASI RENTAL MOBIL "NINE RENTAL"`.
   - Kanvas gradasi ungu (`#59477d` ke lavender) dengan 3 menu modular:
     - **Master** &rarr; Kotak kuning bertuliskan **`A`** (bergaris bawah biru) &rarr; Sub-label `Kendaraan`.
     - **Transaksi** &rarr; Kotak kuning bertuliskan **`B`** (bergaris bawah biru) &rarr; Sub-label `Sewa`.
     - **Customer** &rarr; Kotak kuning bertuliskan **`C`** (bergaris bawah biru) &rarr; Sub-label `Pelanggan`.
   - Footer biru royal (`#0000cc`) bertuliskan `COPYRIGHT @ SMK NEGERI 9 SEMARANG`.
2. **Master Kendaraan (CRUD):**
   - Header hijau 3 baris: `NINE RENTAL`, `RENTAL MOBIL SEGALA MERK`, `JL.PETERONGANSARI NO 2 SEMARANG`.
   - Subheader abu-abu bertuliskan `DATA KENDARAAN`.
   - Ikon navigasi kiri atas: `≤` (kembali ke dashboard) dan `±` (tambah data).
   - Tabel header ungu (`#7b5297`) dengan teks kuning terang (`#ffff00`), baris data teks kuning di atas latar abu-abu.
   - Kolom: `No`, `No Polisi`, `Nama Kendaraan`, `Tipe`, `Warna`, `No Mesin`, `No Rangka`, `Bahan Bakar`, `Tahun`, `Status`, `Aksi` (edit ✏️, hapus ❌).
   - Kotak jumlah data putih di bawah tabel: `Record : 2`.
   - Form input kendaraan dengan field lengkap, select tipe (AT/MT), upload foto, dan tombol `Simpan`.
3. **Transaksi Sewa (CRUD):**
   - Subheader `DATA TRANSAKSI SEWA`.
   - Kolom tabel: `No`, `No Sewa`, `Tgl Sewa`, `Tgl Kembali`, `No Polisi`, `No KTP`, `Biaya`, `Catatan`, `Status`, `Aksi`.
   - Form input transaksi dengan autofill: Memilih `No KTP` otomatis memuat `Nama Pelanggan`, memilih `No Polisi` otomatis memuat `Nama Kendaraan`.
   - Sinkronisasi status kendaraan: Saat transaksi sewa dibuat, status unit otomatis menjadi `Sewa`. Saat tombol `Kembali` ditekan, status unit otomatis kembali menjadi `Ready`.
4. **Customer Pelanggan (CRUD):**
   - Subheader `DATA PELANGGAN`.
   - Kolom tabel: `No`, `No KTP`, `Nama Pelanggan`, `Alamat`, `Telepon`, `Foto`, `Aksi`.
   - Form input data pelanggan lengkap dengan upload foto dan tombol `Simpan`.

---

## 🗂️ Arsitektur Direktori Proyek

```text
NineRental/
├── config/
│   └── database.php        # Koneksi PDO MySQL (auto-fallback SQLite) & helper escape()
├── database/
│   ├── db_rental.sql       # Skema database MySQL & seeding data awal sesuai soal
│   └── rental.db           # SQLite database bawaan (zero-config testing)
├── assets/
│   └── css/
│       └── style.css       # Styling presisi persis lembar soal PDF
├── uploads/
│   ├── kendaraan/          # Direktori berkas foto kendaraan
│   └── pelanggan/          # Direktori berkas foto pelanggan
├── dokumen/
│   ├── DOKUMENTASI_WEB_NINERENTAL.pdf  # Dokumen laporan resmi pengujian sistem
│   ├── PRESENTASI_NINERENTAL.pptx      # Slide presentasi PowerPoint
│   └── PRESENTASI_NINERENTAL.pdf       # Versi PDF dari slide presentasi
├── Yang Dikumpulin/
│   ├── db_rental.sql                   # SQL dump untuk pengumpulan asesor
│   ├── DOKUMENTASI_WEB_NINERENTAL.pdf  # PDF dokumentasi resmi
│   ├── PRESENTASI_NINERENTAL.pptx      # Slide presentasi PPTX
│   ├── PRESENTASI_NINERENTAL.pdf       # Slide presentasi PDF
│   └── NineRental.zip                  # Paket zip source code siap kumpul
├── index.php               # Dashboard utama aplikasi
├── kendaraan.php           # Modul Master Kendaraan (List, Tambah, Edit, Hapus)
├── sewa.php                # Modul Transaksi Sewa (List, Tambah, Edit, Hapus, Selesai)
├── pelanggan.php           # Modul Customer Pelanggan (List, Tambah, Edit, Hapus)
├── test_queries.php        # Skrip pengujian otomatis database & transaksi
├── SOAL WEB UKK LSP P1 SMK 9 SMG11.pdf # Berkas soal asli UKK
└── README.md               # Dokumentasi modul
```

---

## 🚀 Panduan Instalasi & Menjalankan

### 1. Impor Database MySQL (XAMPP / Laragon)
1. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`).
2. Buat database bernama `rental`.
3. Impor berkas `database/db_rental.sql`.
4. Tabel yang akan terbentuk: `kendaraantbl`, `pelanggantbl`, `sewatbl`.

### 2. Jalankan Server Web
Salin folder `BelajarUKK` ke direktori web server Anda (`htdocs` atau `www`), lalu akses:
- **URL Dashboard NineRental:** `http://localhost/BelajarUKK/NineRental/`
- Atau via PHP built-in server:
  ```bash
  cd BelajarUKK/NineRental
  php -S 127.0.0.1:8080
  ```
  Akses di browser: `http://localhost:8080/`

---

## 🧪 Validasi Pengujian
Jalankan pengujian sintaks PHP:
```bash
find NineRental/ -name "*.php" -exec php -l {} +
```
Jalankan pengujian integritas query & transaksi:
```bash
php NineRental/test_queries.php
```
