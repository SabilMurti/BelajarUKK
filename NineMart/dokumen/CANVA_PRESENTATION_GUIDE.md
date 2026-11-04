# Panduan Teknis & Presentasi Canva NineMart UKK 2026 (Full Uncropped Edition)

Dokumen ini memuat panduan lengkap akses presentasi Canva, rincian 12 slide alur teknis aplikasi NineMart dengan **seluruh 10 tangkapan layar asli soal UKK tanpa terpotong (uncropped)**, dan panduan pengujian di hadapan asesor Uji Kompetensi Keahlian (UKK) 2026 Klaster Pemrograman Web SMK Negeri 9 Semarang.

---

## 1. Tautan Langsung Desain Canva (Live & Editable)

Desain presentasi telah dibuat dan terhubung di akun Canva (**Heilen Audio**) memuat seluruh 10 aset tangkapan layar beresolusi penuh:
* **Tautan Presentasi Canva Utama (13 Halaman Lengkap - Semua Screenshot):**  
  👉 **[Buka Presentasi di Canva Editor (13 Slide)](https://www.canva.com/api/design/eyJhbGciOiJkaXIiLCJlbmMiOiJBMjU2R0NNIiwiZXhwaXJ5IjoxNzk2Mzg4MTk1NDY3fQ..MRReN4Kt3_5egBfC.DM_JqX4JU39uPpNuzg-aQ7eizXVDqkztLbXLPceO7wT0FeAfHw4nZU7--kOZeGZN69q2I81wYi1DhGBgLHjLpvrROfUfOQKakf4JqHxHeMX54po.5-TESTrd2fNGpixRNiFqCw/edit)**
* **Tautan Desain Canva Alternatif (9 Slide):**  
  👉 **[https://www.canva.com/d/becZGQ2V2BC5-9Z](https://www.canva.com/d/becZGQ2V2BC5-9Z)**

---

## 2. Berkas Presentasi Lokal Siap Pakai (12 Slide Lengkap + 100% Uncropped)

* **Lokasi Berkas:**  
  `NineMart/dokumen/PRESENTASI_NINEMART_CANVA.pptx` (1.33 MB)
* **Karakteristik Desain & Tata Letak:**  
  - Format Widescreen 16:9 beresolusi tinggi.
  - Gaya visual *Executive Dark-Tech* (*Deep Obsidian `#0A0E17`* dan *Slate Container `#111827`*).
  - **100% Zero Crop:** Setiap screenshot dihitung aspek rasionya secara presisi dan dibingkai dalam browser container rapi tanpa ada teks tabel, tombol aksi, atau footer yang terpotong.
  - Murni penjelasan alur kerja aplikasi per modul tanpa kalimat promosi atau marketing.

### Panduan 1-Klik Impor ke Canva Web:
Jika ingin mengedit berkas PPTX ini langsung di Canva:
1. Buka [https://www.canva.com](https://www.canva.com) di peramban web.
2. Klik tombol **Unggah** (*Upload*) di kanan atas atau cukup **tarik dan lepas (drag-and-drop)** file `NineMart/dokumen/PRESENTASI_NINEMART_CANVA.pptx` ke beranda Canva.
3. Canva otomatis mengonversi slide PPTX menjadi presentasi Canva yang 100% editable.

---

## 3. Rincian Lengkap 12 Slide Presentasi & Screenshot Terpasang

| Slide | Modul & Alur Sistem | Screenshot Terpasang (Uncropped) | Rincian Alur Teknis & Validasi |
|---|---|---|---|
| **01** | **Judul & Identitas Ujian** | `01_login_sistem.jpeg` | Identitas peserta Sabil Murti, SMKN 9 Semarang, PHP 8 Native & PDO MySQL. |
| **02** | **Modul 01: Autentikasi (Login)** | `01_login_sistem.jpeg` | Verifikasi kredensial PDO prepared statement, anti-SQLi, session regeneration. |
| **03** | **Modul 02: Dashboard Sugeng Rawuh** | `02_dashboard_sugeng_rawuh.png` | Sambutan khas SMKN 9, navigasi menu atas, session guard middleware. |
| **04** | **Modul 03: Menu Pengguna (Dropdown)** | `03_menu_pengguna_dropdown.png` | Dropdown navigasi terbuka menampilkan sub-menu Data Pengguna & Ubah Password. |
| **05** | **Modul 04: Manajemen Data Pengguna** | `04_data_pengguna.png` | Tabel akun user, tombol tambah perak, tombol hapus merah, proteksi akun aktif. |
| **06** | **Modul 05: Formulir Ubah Password** | `05_ubah_password.png` | Form ganti sandi dengan validasi batas panjang karakter `*Maksimal 10 karakter`. |
| **07** | **Modul 06: Menu Master Data (Dropdown)**| `06_menu_data_dropdown.png` | Dropdown master data terbuka (Data Barang, Data Pemasok, Data Kelompok). |
| **08** | **Modul 07: Data Kelompok Barang** | `07_data_kelompok.png` | Klasifikasi kategori retail (Makanan, Minuman, Harian), aksi CRUD, proteksi FK. |
| **09** | **Modul 08: Master Data Pemasok** | `08_data_pemasok.png` | Data supplier vendor rekanan toko (nama, alamat, no telp) sebagai rujukan barang. |
| **10** | **Modul 09: Master Data Barang & Stok**| `09_data_barang.png` | Katalog produk, barcode SKU, harga beli, harga jual, dan stok fisik realtime. |
| **11** | **Modul 10: Terminasi Sesi (Logout)** | `10_logout_sistem.jpeg` | `session_unset()`, `session_destroy()`, pembersihan cookie browser, redirect login. |
| **12** | **Skema Basis Data (4 Tabel)** | Grid 4 Kartu Entitas Relasi | Penjelasan 4 tabel relasional InnoDB (`tblpengguna`, `tblkelompok`, `tblpemasok`, `tblbarang`). |

---

## 4. Naskah Singkat Penjelasan di Hadapan Asesor

1. **Slide 1 (Pembuka):**
   > *"Selamat pagi/siang Bapak/Ibu Asesor. Saya Sabil Murti dari Kompetensi Keahlian RPL SMKN 9 Semarang. Hari ini saya mempresentasikan alur teknis dan arsitektur aplikasi kasir & inventaris NineMart yang dibangun menggunakan PHP 8 Native dan PDO MySQL."*

2. **Slide 2 (Login):**
   > *"Autentikasi login memverifikasi username dan password menggunakan query PDO prepared statement terhadap tabel tblpengguna untuk menjamin keamanan dari serangan SQL Injection."*

3. **Slide 3 (Dashboard):**
   > *"Setelah berhasil masuk, sistem menampilkan dashboard Sugeng Rawuh. Halaman ini dilindungi session guard di mana akses langsung tanpa login otomatis ditolak dan diredirect."*

4. **Slide 4 & 5 (Pengguna):**
   > *"Menu Pengguna memiliki dropdown untuk mengelola akun kasir/admin. Pada tabel pengguna, terdapat proteksi di mana user yang sedang aktif login tidak dapat menghapus akunnya sendiri guna mencegah lockout."*

5. **Slide 6 (Ubah Password):**
   > *"Formulir ubah password menerapkan validasi khusus sesuai lembar kerja ujian, yaitu panjang kata sandi baru dibatasi maksimal 10 karakter."*

6. **Slide 7, 8, 9 (Kelompok & Pemasok):**
   > *"Menu Data menaungi kategori kelompok dan pemasok. Kedua tabel ini terhubung melalui Foreign Key ke tabel barang sehingga kategori atau pemasok yang sedang berelasi tidak dapat dihapus sembarangan."*

7. **Slide 10 (Barang & Stok):**
   > *"Katalog barang mengelola kode barcode, nama produk, harga beli, harga jual, serta stok. Sistem memvalidasi bahwa harga dan stok wajib berupa angka non-negatif."*

8. **Slide 11 (Logout):**
   > *"Fitur logout mengosongkan variabel $_SESSION, menghancurkan sesi di server, dan menghapus cookie browser secara tuntas."*

9. **Slide 12 (Database):**
   > *"Seluruh data dikelola dalam 4 tabel InnoDB yang telah lulus 100% skenario pengujian query dan transaksi ACID pada test_queries.php."*
