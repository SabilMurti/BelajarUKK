# Panduan Teknis & Presentasi Canva NineMart UKK 2026 (Executive Dark-Tech Edition)

Dokumen ini memuat panduan lengkap akses presentasi Canva, struktur alur teknis aplikasi NineMart per slide, dan panduan presentasi di hadapan asesor Uji Kompetensi Keahlian (UKK) 2026 Klaster Pemrograman Web SMK Negeri 9 Semarang.

---

## 1. Tautan Langsung Desain Canva (Live & Editable)

Desain presentasi telah dibuat dan terhubung di akun Canva (**Heilen Audio**) dengan gaya visual modern dan screenshot terpasang:
* **Tautan Presentasi Canva Utama (Gaya Modern Visual - 9 Slide):**  
  👉 **[Buka Presentasi di Canva Editor (Desain Baru)](https://www.canva.com/api/design/eyJhbGciOiJkaXIiLCJlbmMiOiJBMjU2R0NNIiwiZXhwaXJ5IjoxNzk2Mzg3NDkxNjk0fQ..CEdRauOwD72N4cdY.mGIFX2g-4T0lUMwfe-aeipP6EIrL83cKS2BT-pZpLs2jM4S-SErYh8PIOvj0wcgGYDKwaXn6Be-qZXdWMNZRAt3RRB7a7tpRAEoz1aI30c4UsPc.ZlrLr8RRFmD5C80KAbVdkA/edit)**  
  *(Tautan pendek tampilan:* `https://www.canva.com/d/becZGQ2V2BC5-9Z`*)*
* **Tautan Alternatif (Gaya Minimalis - 9 Slide):**  
  👉 **[https://www.canva.com/d/ujASB880Ox-F6ak](https://www.canva.com/d/ujASB880Ox-F6ak)**

---

## 2. Berkas Presentasi Lokal Siap Pakai (10 Slide Lengkap + Browser Window Mockup)

* **Lokasi Berkas:**  
  `NineMart/dokumen/PRESENTASI_NINEMART_CANVA.pptx` (2.96 MB)
* **Karakteristik Desain Baru (Executive Dark-Tech):**  
  - Tema elegan gelap (*Deep Obsidian `#0B0F19`* & *Elevated Slate Container `#131B2E`*).
  - Aksen visual *Electric Cyan (`#06B6D4`)* dan *Indigo (`#6366F1`)*.
  - Setiap screenshot dibingkai dalam **Browser Window Mockup** dengan tombol window 🔴🟡🟢 dan path URL aktif (`https://localhost/NineMart/...`).
  - Label badge spesifikasi teknis di setiap alur (`[Tech Stack] PDO Prepared Statements | Session Guard...`).
  - Murni penjelasan alur teknis sistem tanpa basa-basi marketing.

### Panduan 1-Klik Impor ke Canva Web:
Jika ingin mengedit berkas PPTX ini di Canva:
1. Buka [https://www.canva.com](https://www.canva.com) di peramban web.
2. Klik tombol **Unggah** (*Upload*) di kanan atas atau cukup **tarik dan lepas (drag-and-drop)** file `NineMart/dokumen/PRESENTASI_NINEMART_CANVA.pptx` ke layar Canva.
3. Canva otomatis mengonversi slide PPTX menjadi presentasi Canva yang 100% editable.

---

## 3. Rincian Struktur 10 Slide Presentasi Teknis NineMart

| Slide | Modul & Alur Sistem | Screenshot & Bingkai Mockup | Poin Penjelasan Teknis Utama |
|---|---|---|---|
| **1** | **Judul & Identitas Ujian** | Mockup `01_halaman_login.png` | Identitas peserta Sabil Murti, SMKN 9 Semarang, PHP 8 Native & PDO MySQL. |
| **2** | **Modul 01: Autentikasi (Login)** | Mockup `login.php` | Verifikasi kredensial PDO prepared statement, anti-SQLi, mitigasi session fixation. |
| **3** | **Modul 02: Dashboard Sugeng Rawuh** | Mockup `index.php` | Sambutan khas SMKN 9, navigasi menu terpusat, session guard middleware. |
| **4** | **Modul 03: Kelola Data Pengguna** | Mockup `pengguna.php` | CRUD akun kasir & admin, proteksi akun aktif tidak boleh dihapus sendiri (anti-lockout). |
| **5** | **Modul 04: Fitur Ubah Password** | Mockup `ubah_password.php` | Validasi batasan panjang kata sandi baru maksimal 10 karakter sesuai soal UKK. |
| **6** | **Modul 05: Kelompok Barang (Kategori)** | Mockup `kelompok.php` | Klasifikasi kategori retail, aksi tambah perak, pensil edit, silang hapus, integritas FK. |
| **7** | **Modul 06: Data Pemasok (Supplier)** | Mockup `pemasok.php` | Pencatatan vendor supplier rekanan toko (nama, alamat, no telp) sebagai rujukan barang. |
| **8** | **Modul 07: Katalog Barang & Stok** | Mockup `barang.php` | Barcode unik, dropdown dinamis kelompok & pemasok, validasi harga & stok positif. |
| **9** | **Modul 08: Logout (Terminasi Sesi)** | Mockup `logout.php` | `session_unset()`, `session_destroy()`, pembersihan cookie browser, redirect login. |
| **10** | **Spesifikasi Database (4 Tabel)** | Grid 4 Kartu Entitas Relasi | Penjelasan 4 tabel relasional InnoDB (`tblpengguna`, `tblkelompok`, `tblpemasok`, `tblbarang`). |

---

## 4. Naskah Singkat Penjelasan di Hadapan Asesor

1. **Slide 1 (Pembuka):**
   > *"Selamat pagi/siang Bapak/Ibu Asesor. Saya Sabil Murti. Hari ini saya mempresentasikan alur teknis dan arsitektur aplikasi kasir & inventaris NineMart yang dibangun dengan PHP 8 Native dan PDO MySQL."*

2. **Slide 2 (Login):**
   > *"Sistem diawali dengan autentikasi login. Input diverifikasi menggunakan query PDO prepared statement terhadap tabel tblpengguna untuk menangkal SQL injection."*

3. **Slide 3 (Dashboard):**
   > *"Setelah login sukses, pengguna masuk ke dashboard Sugeng Rawuh. Sistem menerapkan session guard pada seluruh file; jika sesi kosong, otomatis diredirect ke login."*

4. **Slide 4 (Pengguna):**
   > *"Menu Pengguna mengelola akun kasir dan admin. Sistem memiliki proteksi agar akun yang sedang aktif digunakan login tidak dapat dihapus."*

5. **Slide 5 (Ubah Password):**
   > *"Fitur ubah password memvalidasi kata sandi baru dengan batasan maksimal 10 karakter sesuai spesifikasi soal UKK."*

6. **Slide 6 (Kelompok):**
   > *"Menu Kelompok mengklasifikasikan jenis produk dan dilindungi Foreign Key agar kategori yang berelasi dengan data barang tidak dapat dihapus sembarangan."*

7. **Slide 7 (Pemasok):**
   > *"Menu Pemasok mencatat vendor supplier rekanan toko yang menjadi pilihan dropdown dinamis pada master barang."*

8. **Slide 8 (Barang & Stok):**
   > *"Menu Barang mencatat barcode produk, relasi dinamis ke kelompok dan pemasok, serta validasi bahwa harga dan stok wajib bilangan positif."*

9. **Slide 9 (Logout):**
   > *"Fitur logout membersihkan seluruh variabel $_SESSION dan cookie sesi browser secara aman."*

10. **Slide 10 (Database):**
    > *"Seluruh sistem didukung oleh 4 tabel InnoDB yang ternormalisasi dan telah lulus pengujian integritas 100% pada test_queries.php."*
