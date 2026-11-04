# Panduan Teknis & Presentasi NineMart UKK 2026 (Edisi Lengkap Bahasa Indonesia)

Dokumen ini memuat panduan lengkap presentasi alur teknis aplikasi kasir dan inventaris NineMart dengan **seluruh 11 tangkapan layar asli lembar soal UKK tanpa terpotong (uncropped)**, skrip presentasi di hadapan asesor, serta tautan Canva.

---

## 1. Berkas Presentasi Lokal (12 Slide Lengkap + 100% Uncropped)

* **Lokasi Berkas:**  
  [PRESENTASI_NINEMART_CANVA.pptx](file:///home/murtix/belajar/BelajarUKK/NineMart/dokumen/PRESENTASI_NINEMART_CANVA.pptx) (1.35 MB)
* **Karakteristik Desain & Tata Letak:**  
  - Format Widescreen 16:9 beresolusi tinggi.
  - **Palet Warna SMKN 9 & Vokasi:** Royal Purple (`#3B185F`), Warm Amber Gold (`#D97706`), dan Slate Canvas (`#F8FAFC`).
  - **Tipografi Bersih & Elegan:** Menggunakan font *Segoe UI* dengan hierarki keterbacaan tinggi.
  - **Mockup Jendela Peramban Web:** Setiap tangkapan layar dibingkai rapi dengan header browser macOS/Chrome style lengkap dengan alamat URL lokal (`http://localhost/ninemart/...`).
  - **100% Zero Crop:** Dihitung aspek rasionya secara matematis tanpa memotong header, tombol aksi, atau footer tabel.
  - **100% Bahasa Indonesia Formal:** Murni penjelasan alur kerja aplikasi dan kesesuaian rubrik ujian tanpa kalimat promosi atau marketing.

### Panduan 1-Klik Impor ke Canva Web:
Jika ingin mengedit berkas PPTX ini langsung di Canva:
1. Buka [https://www.canva.com](https://www.canva.com) di peramban web.
2. Klik tombol **Unggah** (*Upload*) di kanan atas atau cukup **tarik dan lepas (drag-and-drop)** file `PRESENTASI_NINEMART_CANVA.pptx` ke beranda Canva.
3. Canva otomatis mengonversi seluruh 12 slide PPTX menjadi presentasi Canva yang dapat diedit langsung.

---

## 2. Tautan Langsung Desain Canva (Live & Editable)

Desain presentasi Canva terhubung langsung di akun Canva (**Heilen Audio**):
* **Tautan Presentasi Canva Utama (Live Edit):**  
  👉 **[Buka Presentasi di Canva Editor](https://www.canva.com/api/design/eyJhbGciOiJkaXIiLCJlbmMiOiJBMjU2R0NNIiwiZXhwaXJ5IjoxNzk2Mzg4MTk1NDY3fQ..MRReN4Kt3_5egBfC.DM_JqX4JU39uPpNuzg-aQ7eizXVDqkztLbXLPceO7wT0FeAfHw4nZU7--kOZeGZN69q2I81wYi1DhGBgLHjLpvrROfUfOQKakf4JqHxHeMX54po.5-TESTrd2fNGpixRNiFqCw/edit)**

---

## 3. Rincian 12 Slide Presentasi & Tangkapan Layar Soal UKK

| Slide | Modul & Judul Alur | Screenshot Terpasang (100% Uncropped) | Rincian Alur Teknis & Validasi Soal |
|---|---|---|---|
| **01** | **Sampul: Identitas Peserta & Aplikasi** | `page1_Image8.jpeg` & `page1_Image9.png` | Identitas peserta Sabil Murti, SMKN 9 Semarang, RPL, Pemrograman Web PHP & MySQL. |
| **02** | **Modul 01: Rancangan Skema Basis Data** | `page1_Image7.png` (Skema Relasi 4 Tabel) | Skema relasi 4 tabel InnoDB (`tblpengguna`, `tblkelompok`, `tblpemasok`, `tblbarang`). |
| **03** | **Modul 02: Halaman Masuk (Login) Sistem** | `page1_Image8.jpeg` (Login Lawang Sewu) | Otentikasi username & password dengan PDO prepared statement, perlindungan anti-SQLi. |
| **04** | **Modul 03: Dashboard Utama (Sugeng Rawuh)** | `page1_Image9.png` (Dashboard Beranda) | Ucapan sambutan khas Semarang, navigasi menu terpusat, session guard middleware. |
| **05** | **Modul 04: Menu Pengguna (Dropdown Aktif)** | `page2_Image12.png` (Menu Pengguna Terbuka) | Dropdown bertingkat mempermudah akses ke Data Pengguna dan Ubah Password. |
| **06** | **Modul 05: Manajemen Data Pengguna** | `page2_Image13.png` (Tabel Akun Petugas) | Pengelolaan akun kasir/admin, tombol tambah perak, proteksi anti-hapus akun sendiri. |
| **07** | **Modul 06: Formulir Ubah Password** | `page2_Image14.png` (Form Ubah Sandi) | Pembaruan kata sandi mandiri dengan batasan ketat maksimal 10 karakter. |
| **08** | **Modul 07: Menu Master Data (Dropdown Aktif)**| `page3_Image17.png` (Menu Data Terbuka) | Sentralisasi akses data master (Data Barang, Data Pemasok, Data Kelompok). |
| **09** | **Modul 08: Master Data Kelompok Barang** | `page4_Image22.png` (Tabel Kelompok) | Klasifikasi kategori retail, tombol tambah & ubah, integritas kunci tamu foreign key. |
| **10** | **Modul 09: Master Data Pemasok (Distributor)**| `page3_Image19.png` (Tabel Mitra Pemasok) | Pencatatan distributor (kode PMS, nama, alamat, telepon) sebagai rujukan barang. |
| **11** | **Modul 10: Master Data Barang & Stok Toko** | `page3_Image18.png` (Katalog Produk & Stok) | Katalog produk retail, harga beli modal, harga jual kasir, validasi angka positif. |
| **12** | **Modul 11: Alur Selesai Sesi (Logout)** | `page4_Image23.jpeg` (Pengalihan Logout) | Pembersihan sesi `session_unset()`, `session_destroy()`, pembersihan cookie browser. |

---

## 4. Naskah Presentasi Lisan di Hadapan Asesor UKK

1. **Slide 1 (Pembuka):**
   > *"Selamat pagi/siang Bapak/Ibu Asesor. Saya Sabil Murti dari Kompetensi Keahlian Rekayasa Perangkat Lunak SMK Negeri 9 Semarang. Pada kesempatan ini saya mempresentasikan alur teknis dan arsitektur aplikasi kasir & inventaris NineMart yang dibangun menggunakan PHP 8 Native dan basis data relasional MySQL."*

2. **Slide 2 (Skema Basis Data):**
   > *"Aplikasi NineMart dirancang menggunakan 4 entitas tabel yang saling berelasi pada basis data MySQL InnoDB, yaitu tabel pengguna untuk otentikasi, tabel kelompok dan pemasok sebagai data induk, serta tabel barang yang mereferensikan keduanya melalui relasi Foreign Key untuk menjamin integritas data."*

3. **Slide 3 (Login Sistem):**
   > *"Proses masuk sistem menggunakan query terparameter PDO prepared statements pada tabel tblpengguna. Hal ini memastikan otentikasi kasir terlindungi dari ancaman injeksi SQL dan session fixation."*

4. **Slide 4 (Dashboard):**
   > *"Setelah verifikasi berhasil, kasir disambut oleh halaman dashboard dengan ucapan kearifan lokal 'Sugeng Rawuh'. Seluruh modul di dashboard dipagari oleh middleware session guard sehingga akses URL tanpa login otomatis ditolak."*

5. **Slide 5 & 6 (Pengguna):**
   > *"Menu Pengguna memiliki navigasi dropdown bertingkat untuk mengelola akun kasir. Pada tabel pengguna, terdapat sistem proteksi khusus di mana petugas yang sedang aktif login tidak dapat menghapus akunnya sendiri guna mencegah sistem terkunci."*

6. **Slide 7 (Ubah Password):**
   > *"Formulir ubah password menerapkan validasi sesuai instruksi lembar soal UKK, yaitu panjang kata sandi baru dibatasi maksimal 10 karakter."*

7. **Slide 8, 9, 10 (Kelompok & Pemasok):**
   > *"Menu Data menaungi data kelompok barang dan data rekanan distributor pemasok. Relasi kunci tamu memastikan kategori atau pemasok yang sudah tercatat pada data barang tidak dapat dihapus sembarangan."*

8. **Slide 11 (Katalog Barang & Stok):**
   > *"Katalog barang mengelola kode barang, nama produk, harga beli modal, harga jual kasir, serta stok fisik. Sistem memvalidasi bahwa seluruh input harga dan kuantitas wajib bernilai positif."*

9. **Slide 12 (Selesai Sesi / Logout):**
   > *"Saat pergantian shift kasir, tombol logout mengeksekusi session_unset() dan session_destroy() untuk menghapus sesi server dan menghapus token cookie peramban, lalu mengalihkan layar kembali ke halaman login."*
