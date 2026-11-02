# 📚 Aplikasi Manajemen Perpustakaan Berbasis Web (PHP & MySQL)
### Bahan Belajar Lengkap Persiapan Uji Kompetensi Keahlian (UKK) Jurusan PPLG / RPL

Selamat datang di proyek referensi standar industri untuk **Uji Kompetensi Keahlian (UKK)** konsentrasi keahlian **Pengembangan Perangkat Lunak dan GIM (PPLG)** atau **Rekayasa Perangkat Lunak (RPL)**.

Aplikasi ini dirancang khusus oleh **Senior PHP Developer & Instruktur UKK** agar memiliki struktur yang **sangat bersih, mudah dipahami dari atas ke bawah (Native PHP Procedural Modular)**, aman dari celah keamanan dasar, dan memenuhi seluruh kriteria rubrik penilaian UKK.

---

## 🗂️ Arsitektur Direktori Proyek

Proyek ini menyediakan **dua versi implementasi tampilan** dengan struktur file dan logika PHP backend yang **100% IDENTIK**:

```text
PerpusUKK/
├── database/
│   └── db_perpustakaan.sql      # Dump skema tabel InnoDB + relasi FK + Seeding dummy data
│
├── versi-polos/                 # Versi 1: HTML5 Semantik murni + 1 file CSS mandiri
│   ├── assets/
│   │   └── style.css            # Styling responsif murni tanpa framework eksternal
│   ├── config/
│   │   ├── database.php         # Koneksi PDO, exception handling & helper functions
│   │   └── auth.php             # Session guard middleware
│   ├── includes/
│   │   ├── header.php           # Template navbar dan navigasi
│   │   └── footer.php           # Template penutup
│   ├── login.php                # Autentikasi dengan Prepared Statement & password_verify
│   ├── logout.php               # Penghancuran sesi aman
│   ├── index.php                # Dashboard statistik 4 indikator utama
│   ├── buku.php                 # CRUD Buku + Pencarian + Validasi stok + Proteksi FK
│   ├── anggota.php              # CRUD Anggota + Pencarian + Proteksi FK
│   ├── peminjaman.php           # Transaksi Peminjaman (Database Transaction & Lock)
│   ├── pengembalian.php         # Transaksi Pengembalian + Hitung Keterlambatan Otomatis
│   └── laporan.php              # Rekapitulasi 3 Kategori + Filter Tanggal + Cetak Resmi
│
├── versi-bootstrap/             # Versi 2: Logika PHP identik menggunakan Bootstrap 5.3 CDN
│   ├── config/
│   │   ├── database.php
│   │   └── auth.php
│   ├── includes/
│   │   ├── header.php
│   │   └── footer.php
│   ├── login.php
│   ├── logout.php
│   ├── index.php
│   ├── buku.php
│   ├── anggota.php
│   ├── peminjaman.php
│   ├── pengembalian.php
│   └── laporan.php
│
└── README.md                    # Dokumentasi lengkap, panduan setup, dan skenario uji
```

---

## 🔑 Akun Login Administrator Default

Setelah mengimpor database, Anda dapat langsung masuk menggunakan akun default berikut:

| Parameter | Kredensial Default | Catatan |
| :--- | :--- | :--- |
| **URL Login** | `http://localhost/PerpusUKK/versi-polos/login.php` <br>atau `http://localhost/PerpusUKK/versi-bootstrap/login.php` | Sesuaikan path di folder `htdocs` |
| **Username** | `admin` | Disimpan di tabel `users` |
| **Password** | `admin123` | Terenkripsi dengan `password_hash()` (Bcrypt) |

---

## 🚀 Panduan Instalasi (XAMPP / Laragon)

### 1. Letakkan Folder di Web Server Root
1. Salin folder `PerpusUKK` ke direktori root web server Anda:
   - **XAMPP (Windows):** `C:\xampp\htdocs\PerpusUKK`
   - **Laragon (Windows):** `C:\laragon\www\PerpusUKK`
   - **Linux / Termux:** `/var/www/html/PerpusUKK` atau jalankan via PHP Built-in Server.

### 2. Impor Database MySQL
1. Buka **phpMyAdmin** di browser (`http://localhost/phpmyadmin`).
2. Buat database baru bernama `db_perpustakaan` (atau biarkan query otomatis membuatnya).
3. Klik tab **Import** -> Pilih file `database/db_perpustakaan.sql` -> Klik **Go / Kirim**.
4. Database akan otomatis membuat 4 tabel berelasi (`users`, `buku`, `anggota`, `peminjaman`) beserta indeks dan data pengujian awal.

### 3. Konfigurasi Koneksi (Jika Diperlukan)
File konfigurasi berada di:
- `versi-polos/config/database.php`
- `versi-bootstrap/config/database.php`

Secara default, konfigurasi telah disetel untuk XAMPP standar:
```php
$db_host = '127.0.0.1';
$db_port = '3306';
$db_name = 'db_perpustakaan';
$db_user = 'root';
$db_pass = ''; // Kosong untuk default XAMPP
```

### 4. Akses Aplikasi di Browser
- **Versi Semantic HTML & CSS Murni:**  
  `http://localhost/PerpusUKK/versi-polos/`
- **Versi Modern Bootstrap 5 CDN:**  
  `http://localhost/PerpusUKK/versi-bootstrap/`

---

## 💡 Konsep Krusial PHP yang Dipelajari untuk UKK

### 1. Keamanan Mutlak dengan PDO Prepared Statements
Mengapa tidak menggunakan `mysqli_query("SELECT ... WHERE user = '$user'")`?  
Karena interpolasi string rentan terhadap **SQL Injection**. Dengan PDO:
```php
// Prepared Statement memisahkan struktur query SQL dari data masukan user
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
$stmt->execute([':username' => $username]);
$user = $stmt->fetch();
```

### 2. Otentikasi Password dengan Hash Standar Industri
Password tidak boleh disimpan dalam bentuk teks biasa (plain text) maupun MD5 (karena sudah usang dan rentan rainbow table).
```php
// Saat verifikasi login
if ($user && password_verify($password, $user['password'])) {
    session_regenerate_id(true); // Proteksi dari Session Fixation
    $_SESSION['user_id'] = $user['id_user'];
}
```

### 3. Database Transaction (Atomicity ACID)
Digunakan pada **Peminjaman** dan **Pengembalian** buku:
- Saat meminjam: Simpan riwayat pinjam DAN kurangi stok buku.
- Jika stok habis atau terjadi error server di tengah proses, transaksi otomatis dibatalkan (`rollBack()`), sehingga stok buku tidak berkurang tanpa ada data peminjaman.
```php
$pdo->beginTransaction();
try {
    // 1. Kunci baris buku untuk update (cegah race condition)
    $stmt = $pdo->prepare("SELECT stok FROM buku WHERE id_buku = :id FOR UPDATE");
    $stmt->execute([':id' => $id_buku]);
    $buku = $stmt->fetch();

    if ($buku['stok'] < $jumlah_pinjam) {
        throw new Exception("Stok tidak mencukupi!");
    }

    // 2. Simpan peminjaman
    $pdo->prepare("INSERT INTO peminjaman (...) VALUES (...)")->execute([...]);

    // 3. Kurangi stok buku
    $pdo->prepare("UPDATE buku SET stok = stok - :jml WHERE id_buku = :id")->execute([...]);

    // Jika semua berhasil, simpan permanen
    $pdo->commit();
} catch (Exception $e) {
    // Batalkan seluruh perubahan jika ada satu saja query yang gagal
    $pdo->rollBack();
    $error = $e->getMessage();
}
```

### 4. Perhitungan Keterlambatan Otomatis (`DateTime::diff`)
Perhitungan keterlambatan dilakukan secara matematis menggunakan objek bawaan PHP `DateTime`:
```php
$targetRencana = new DateTime($tglRencana);
$targetAktual  = !empty($tglAktual) ? new DateTime($tglAktual) : new DateTime(date('Y-m-d'));

if ($targetAktual > $targetRencana) {
    $selisih = $targetRencana->diff($targetAktual)->days;
    $status  = "Terlambat {$selisih} Hari"; // Badge Merah
} else {
    $status  = "Tepat Waktu";               // Badge Hijau
}
```

### 5. Format Cetak Resmi Menggunakan CSS `@media print`
Saat tombol "Cetak Laporan" ditekan, fungsi JavaScript `window.print()` akan dipanggil. Melalui aturan CSS `@media print` (atau class `.d-print-none` pada Bootstrap):
- Elemen interaktif seperti Navbar, tombol aksi, dan form filter otomatis disembunyikan.
- Kop surat resmi instansi dan tanda tangan penanggung jawab perpustakaan otomatis dimunculkan.

---

## 🧪 Lembar Kerja & Skenario Pengujian UKK (Test Scenarios)

Gunakan tabel pengujian ini saat mempresentasikan proyek di hadapan Asesor / Penguji UKK:

| No | Modul / Fitur | Langkah Pengujian | Hasil yang Diharapkan | Status |
| :---: | :--- | :--- | :--- | :---: |
| **1** | **Autentikasi** | Buka halaman `index.php` tanpa login terlebih dahulu. | Otomatis di-redirect ke `login.php` dengan pesan peringatan. | [PASS] |
| **2** | **Autentikasi** | Masukkan password salah pada form login. | Sistem menolak login dan menampilkan alert error. | [PASS] |
| **3** | **Autentikasi** | Masukkan username `admin` dan password `admin123`. | Berhasil masuk ke Dashboard dan session tersimpan. | [PASS] |
| **4** | **Autentikasi** | Klik tombol "Keluar / Logout". | Session dihapus dan kembali ke form login dengan notifikasi sukses logout. | [PASS] |
| **5** | **Dashboard** | Periksa 4 indikator statistik (Total Buku, Anggota, Transaksi, Sedang Dipinjam). | Angka pada kartu statistik cocok dengan isi database real-time. | [PASS] |
| **6** | **Katalog Buku** | Tambah buku baru dengan kode yang sudah ada sebelumnya. | Muncul validasi error bahwa kode buku sudah terdaftar (Unik). | [PASS] |
| **7** | **Katalog Buku** | Tambah atau edit buku dengan stok `-5`. | Form menolak dan menampilkan pesan bahwa stok tidak boleh negatif. | [PASS] |
| **8** | **Katalog Buku** | Cari buku menggunakan kata kunci pengarang atau judul. | Tabel memfilter data buku sesuai kata kunci pencarian. | [PASS] |
| **9** | **Katalog Buku** | Hapus buku yang sedang/pernah tercatat dalam tabel peminjaman. | Sistem menangkap `PDOException (23000)` dan menampilkan peringatan ramah bahwa data tidak boleh dihapus. | [PASS] |
| **10** | **Data Anggota** | Tambah anggota baru, edit nomor telepon, dan cari berdasarkan nama. | Data berhasil dimanipulasi dan dicari secara instan. | [PASS] |
| **11** | **Data Anggota** | Hapus anggota yang memiliki riwayat transaksi peminjaman. | Sistem mencegah penghapusan dengan alert Foreign Key yang aman. | [PASS] |
| **12** | **Peminjaman** | Lakukan peminjaman dengan jumlah buku melebihi sisa stok di database. | Transaksi di-rollback, muncul pesan bahwa stok tidak mencukupi, stok tidak berkurang. | [PASS] |
| **13** | **Peminjaman** | Lakukan peminjaman valid (jumlah pinjam <= stok). | Transaksi di-commit, riwayat baru tersimpan, stok buku otomatis berkurang. | [PASS] |
| **14** | **Pengembalian** | Proses buku yang telah melewati batas rencana kembali. | Sistem mendeteksi keterlambatan dan menampilkan label "Terlambat X Hari". | [PASS] |
| **15** | **Pengembalian** | Klik tombol "Kembalikan" pada salah satu buku. | Status transaksi berubah menjadi 'Dikembalikan', tanggal kembali tercatat, stok buku otomatis bertambah kembali. | [PASS] |
| **16** | **Laporan** | Ganti jenis laporan ke Buku, Anggota, atau Transaksi. | Tabel berganti menampilkan data sesuai kategori yang dipilih. | [PASS] |
| **17** | **Laporan** | Filter transaksi peminjaman berdasarkan rentang tanggal. | Hanya transaksi di dalam rentang tanggal tersebut yang ditampilkan. | [PASS] |
| **18** | **Cetak Dokumen** | Klik tombol "Cetak Laporan (Print)". | Pratinjau cetak browser bersih: Navbar & form filter hilang, kop surat & tanda tangan muncul sempurna. | [PASS] |

---

## 👨‍💻 Standar Koding untuk Asesor & Siswa
- **Modular & Konsisten:** Seluruh logika terpusat dan mudah dilacak alurnya tanpa abstraksi berlebihan.
- **Komentar Edukatif:** Kode dilengkapi komentar berbahasa Indonesia pada baris-baris kritis sehingga memudahkan siswa memahami *mengapa* suatu fungsi ditulis seperti itu.
- **Zero Configuration:** Menggunakan standar default environment XAMPP tanpa perlu setup composer atau npm tambahan.
