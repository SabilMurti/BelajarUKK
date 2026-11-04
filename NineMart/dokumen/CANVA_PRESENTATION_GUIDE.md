# Panduan Teknis & Presentasi Canva NineMart UKK 2026

Dokumen ini memuat panduan lengkap akses presentasi Canva, struktur alur teknis aplikasi NineMart per slide, dan panduan presentasi di hadapan asesor Uji Kompetensi Keahlian (UKK) 2026 Klaster Pemrograman Web SMK Negeri 9 Semarang.

---

## 1. Tautan Langsung Desain Canva (Live & Editable)

Desain presentasi telah dibuat dan terhubung di akun Canva (**Heilen Audio**):
* **Tautan Presentasi Canva (Dengan Screenshot Asli Terpasang - 9 Slide):**  
  👉 **[Buka Presentasi di Canva Editor](https://www.canva.com/api/design/eyJhbGciOiJkaXIiLCJlbmMiOiJBMjU2R0NNIiwiZXhwaXJ5IjoxNzk2Mzg2MTc4Mzc5fQ..Sieovg4iVdqzpITe.vH_JrmaMMrGQSWZ6dIWtWT-LkTe8Q0Ngf_OQQnlv1JnE3o4t0Qv94mzUXATpiV8uSRw37JK5aLVv5YKNtaDvxMXLF_FqrYqdTjLDVFSAis_JiaY.fbHQMZ8avxxdZwQx2AanyQ/edit)**  
  *(Atau tautan pendek tampilan:* `https://www.canva.com/d/ujASB880Ox-F6ak`*)*
* **Tautan Alternatif (7 Slide Arsitektur):**  
  👉 **[https://canva.link/8zvjyorssd61tor](https://canva.link/8zvjyorssd61tor)**

---

## 2. Berkas Presentasi Lokal Siap Pakai (10 Slide Lengkap + Screenshot)

* **Lokasi Berkas:**  
  `NineMart/dokumen/PRESENTASI_NINEMART_CANVA.pptx` (2.96 MB)
* **Karakteristik Desain:**  
  - Format Widescreen 16:9 resolusi tinggi.
  - Murni penjelasan teknis alur sistem (Zero marketing fluff).
  - Setiap slide memuat **screenshot asli ukuran besar** yang diekstrak langsung dari lembar kerja soal ujian UKK 2026.

### Panduan 1-Klik Impor ke Canva Web:
Jika ingin mengedit berkas PPTX ini di Canva:
1. Buka [https://www.canva.com](https://www.canva.com) di peramban web.
2. Klik tombol **Unggah** (*Upload*) di kanan atas atau cukup **tarik dan lepas (drag-and-drop)** file `NineMart/dokumen/PRESENTASI_NINEMART_CANVA.pptx` ke layar Canva.
3. Canva otomatis mengonversi slide PPTX menjadi presentasi Canva yang 100% editable.

---

## 3. Rincian Struktur 10 Slide Presentasi Teknis NineMart

| Slide | Judul & Topik | Screenshot Terpasang | Poin Penjelasan Teknis Utama |
|---|---|---|---|
| **1** | **Judul & Identitas Peserta** | Banner Login Lawang Sewu | Identitas peserta Sabil Murti, SMKN 9 Semarang, stack PHP 8 Native & PDO. |
| **2** | **Alur 1: Autentikasi Pengguna (Login)** | `01_halaman_login.png` | Verifikasi kredensial PDO prepared statement, proteksi SQLi, regenerasi ID sesi. |
| **3** | **Alur 2: Dashboard Sugeng Rawuh** | `02_halaman_dashboard_sugeng_rawuh.png` | Navigasi menu atas, sambutan Sugeng Rawuh, proteksi session guard (akses tanpa login ditolak). |
| **4** | **Alur 3: Manajemen Data Pengguna** | `03_halaman_kelola_pengguna.png` | CRUD akun kasir & admin, proteksi akun aktif dilarang dihapus (anti-lockout). |
| **5** | **Alur 4: Fitur Ubah Password** | `04_halaman_ubah_password.png` | Validasi batasan panjang karakter password baru maksimal 10 karakter sesuai soal UKK. |
| **6** | **Alur 5: Kelompok Barang (Kategori)** | `05_halaman_kelompok_barang.png` | Klasifikasi jenis barang retail, tombol tambah perak, pensil edit, silang hapus, integritas FK. |
| **7** | **Alur 6: Data Pemasok (Supplier)** | `06_halaman_data_pemasok.png` | Pencatatan vendor supplier rekanan toko (nama, alamat, no telp) sebagai rujukan barang. |
| **8** | **Alur 7: Katalog Barang & Stok** | `07_halaman_data_barang.png` | Barcode unik, dropdown relasi kelompok & pemasok, validasi harga & stok positif (>= 0). |
| **9** | **Alur 8: Logout (Terminasi Sesi)** | `09_halaman_konfirmasi_logout.png` | session_unset, session_destroy, penghapusan session cookie, redirect ke login. |
| **10** | **Spesifikasi Database (4 Tabel)** | Diagram Kartu Relasi | Penjelasan 4 tabel InnoDB: `tblpengguna`, `tblkelompok`, `tblpemasok`, `tblbarang`. |

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
