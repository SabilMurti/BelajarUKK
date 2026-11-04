# 🎓 BelajarUKK — Bahan Belajar & Proyek Uji Kompetensi Keahlian (UKK)
### Standar Kurikulum Merdeka & Industri — Konsentrasi Keahlian PPLG / RPL

Selamat datang di repositori monorepo **BelajarUKK**. Repositori ini didedikasikan sebagai pusat kode sumber, dokumentasi arsitektur, dan bahan ajar persiapan **Uji Kompetensi Keahlian (UKK)** untuk siswa SMK jurusan **Pengembangan Perangkat Lunak dan GIM (PPLG)** serta **Rekayasa Perangkat Lunak (RPL)**.

Setiap modul dirancang dengan standar koding bersih, aman dari celah dasar (SQL Injection, XSS, Session Hijacking), dan dilengkapi pengujian terstruktur untuk memudahkan verifikasi di hadapan Asesor / Penguji UKK.

---

## 🗂️ Daftar Paket / Modul UKK

| Direktori Modul | Nama Aplikasi / Studi Kasus | Teknologi Utama | Status Modul |
| :--- | :--- | :--- | :---: |
| [**`perpus/`**](./perpus/) | **Aplikasi Manajemen Perpustakaan**<br>Sistem sirkulasi peminjaman, pengembalian, katalog buku, anggota, dan rekapitulasi laporan resmi. | Native PHP Procedural, MySQL/MariaDB (InnoDB), Dual-UI (Vanilla CSS & Bootstrap 5.3 CDN) | **Ready / Tested** ✅ |
| [**`NineMart/`**](./NineMart/) | **NineMart — Kasir & Point of Sale (POS)**<br>Bahan Tryout UKK 2026 Klaster Pemrograman Web SMK Negeri 9 Semarang. | Native PHP / Web Tech, PDF Panduan & Aset Identitas Visual | **Bahan Soal & Aset** 📦 |

---

## 🚀 Panduan Ringkas Memulai

### 1. Struktur Folder Repositori
```text
BelajarUKK/
├── perpus/                          # Modul 1: Aplikasi Perpustakaan Berbasis Web
│   ├── database/                    # Skema database InnoDB & Seeding data
│   ├── versi-polos/                 # Versi Semantic HTML5 & CSS mandiri
│   ├── versi-bootstrap/             # Versi Bootstrap 5.3 CDN modern
│   ├── index.php                    # Portal pemilih versi perpus
│   ├── test_queries.php             # Skrip automated query & transaction test
│   └── README.md                    # Dokumentasi lengkap modul perpustakaan
│
├── NineMart/                        # Modul 2: Bahan Tryout UKK Kasir / POS
│   ├── SOAL TRYOUT UKK 2026...      # Dokumen panduan & instrumen soal
│   └── img/                         # Asset logo & banner
│
├── index.php                        # Hub portal utama BelajarUKK
├── .gitignore                       # Konfigurasi ignoransi file sampah
└── README.md                        # Dokumentasi utama repositori
```

### 2. Jalankan Server Lokal (XAMPP / Laragon / PHP CLI)
Salin atau clone folder repositori ke root web server Anda:
- **XAMPP (Windows):** `C:\xampp\htdocs\BelajarUKK`
- **Laragon (Windows):** `C:\laragon\www\BelajarUKK`
- **Linux / Termux:** `/var/www/html/BelajarUKK` atau jalankan PHP built-in server:
  ```bash
  cd BelajarUKK
  php -S 127.0.0.1:8080
  ```

### 3. Akses Modul Melalui Browser
- **Portal Utama BelajarUKK:** [`http://localhost:8080/`](http://localhost:8080/)
- **Modul Perpustakaan:** [`http://localhost:8080/perpus/`](http://localhost:8080/perpus/)

---

## 🛡️ Prinsip Desain & Koding Standar UKK
1. **PDO Prepared Statements:** Parameter binding tanpa interpolasi string langsung untuk menjamin keamanan dari SQL Injection.
2. **Output Sanitization (XSS):** Seluruh variabel dinamis pengguna di-escape via `htmlspecialchars()`.
3. **Database Transaction (ACID):** Menjamin konsistensi stok buku dan transaksi keuangan melalui `$pdo->beginTransaction()`, `$pdo->commit()`, dan `$pdo->rollBack()`.
4. **Komentar Edukatif:** Setiap blok query dan middleware dilengkapi komentar penjelasan alur logika untuk mempermudah saat diuji asesor.

---

&copy; Repositori BelajarUKK &bull; Disusun untuk persiapan Uji Kompetensi Keahlian PPLG/RPL.
