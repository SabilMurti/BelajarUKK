# Panduan Presentasi & Dokumentasi Canva NineMart UKK 2026

Dokumen ini berisi tautan langsung ke Canva, panduan import mandiri, dan skrip presentasi teknis di hadapan dewan asesor Uji Kompetensi Keahlian (UKK) 2026 Klaster Pemrograman Web SMK Negeri 9 Semarang.

---

## 1. Tautan Langsung Desain Canva (Live & Editable)

Presentasi telah dibuat dan terhubung langsung di akun Canva (**Heilen Audio**):
* **Tautan Presentasi Canva (Teknis NineMart UKK 2026 - 7 Slide):**  
  👉 **[https://canva.link/8zvjyorssd61tor](https://canva.link/8zvjyorssd61tor)**
* **Tautan Alternatif (Versi Ringkas - 6 Slide):**  
  👉 **[https://canva.link/06ythr8plzs1byl](https://canva.link/06ythr8plzs1byl)**

---

## 2. Berkas Deliverable Offline / Lokal

Seluruh berkas siap dikumpulkan dan diuji di lingkungan lokal:
1. **Aplikasi Web Native PHP:**  
   `NineMart/` (PHP 8 Native, PDO Prepared Statements, Tema Ungu & Kuning SMKN 9 Semarang)
2. **Dokumentasi Web (Word .docx dengan Screenshot Asli):**  
   `NineMart/dokumen/DOKUMENTASI_WEB_NINEMART.docx` (Memuat 8 tangkapan layar asli dari soal UKK, kamus data relasional 4 tabel, dan matriks pengujian)
3. **Presentasi Slide Widescreen 16:9 (.pptx):**  
   `NineMart/dokumen/PRESENTASI_NINEMART_CANVA.pptx` (Desain teknis bersih tanpa marketing, dilengkapi screenshot tiap halaman)
4. **Dump Basis Data (.sql):**  
   `NineMart/database/db_ninemart.sql` (Inisialisasi tabel `tblpengguna`, `tblkelompok`, `tblpemasok`, `tblbarang` beserta data awal)
5. **Aset Tangkapan Layar Soal UKK:**  
   `NineMart/dokumen/screenshots/` (9 file gambar beresolusi tinggi yang diekstrak dari PDF resmi)

---

## 3. Cara Import Berkas ke Canva via Web Browser (1-Click)

Jika ingin mengedit berkas PPTX atau Word secara langsung di dashboard Canva Anda:
1. Buka [https://www.canva.com](https://www.canva.com) di browser dan pastikan sudah masuk akun.
2. Di pojok kanan atas beranda Canva, klik tombol **Unggah** (*Upload*) atau seret (*drag-and-drop*) file:
   * Seret `NineMart/dokumen/PRESENTASI_NINEMART_CANVA.pptx` untuk membuat slide presentasi.
   * Seret `NineMart/dokumen/DOKUMENTASI_WEB_NINEMART.docx` untuk membuka dokumen Canva Docs.
3. Canva akan secara otomatis mengonversi file menjadi dokumen Canva yang dapat disesuaikan 100%.

---

## 4. Skrip Presentasi Teknis di Hadapan Asesor

### Slide 1: Pembukaan & Identitas
> *"Selamat pagi/siang Bapak/Ibu Asesor. Saya Sabil Murti dari Kompetensi Keahlian Rekayasa Perangkat Lunak SMK Negeri 9 Semarang. Hari ini saya mempresentasikan hasil rancang bangun Aplikasi Kasir & Inventaris NineMart untuk UKK Klaster Pemrograman Web."*

### Slide 2: Arsitektur & Teknologi
> *"Aplikasi ini dibangun menggunakan arsitektur PHP 8 Native tanpa framework eksternal untuk menjamin kinerja yang ringan dan cepat. Akses database dikelola penuh oleh PHP Data Objects (PDO) dengan Prepared Statements guna menangkal SQL Injection. Tampilan antarmuka mengadopsi semantic CSS3 yang mereplikasi 100% lembar kerja ujian."*

### Slide 3: Basis Data Relasional
> *"Sistem memiliki 4 tabel utama: tblpengguna untuk otentikasi role kasir dan admin, tblkelompok untuk klasifikasi barang, tblpemasok untuk vendor rekanan, dan tblbarang untuk katalog produk retail. Keempat tabel terhubung dengan Foreign Key constraint untuk menjamin integritas data referensial."*

### Slide 4: Alur Autentikasi & Keamanan Sesi
> *"Proses login memverifikasi password dengan perlindungan session guard. Administrator dilarang menghapus akunnya sendiri saat sedang login untuk mencegah sistem terkunci. Fitur ubah password dibatasi maksimal 10 karakter sesuai petunjuk lembar soal."*

### Slide 5: Manajemen Kelompok & Pemasok
> *"Master data kelompok barang dan pemasok mendukung operasi Tambah, Edit, dan Hapus lengkap dengan validasi integritas relasi agar tidak ada data orphan dalam sistem."*

### Slide 6: Katalog Barang & Stok
> *"Data barang memiliki barcode unik, pemilihan kelompok dan pemasok dinamis melalui dropdown relasional, serta validasi ketat bahwa harga dan kuantitas stok harus berupa bilangan non-negatif."*

### Slide 7: Hasil Pengujian & Verifikasi
> *"Seluruh skenario pengujian unit dan transaksi pada test_queries.php telah lulus 100%. Seluruh kode PHP bebas dari sintaks error (php -l lolos) dan aplikasi siap dioperasikan."*
