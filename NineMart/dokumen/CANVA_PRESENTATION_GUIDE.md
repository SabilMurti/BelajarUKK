# 🎯 Panduan Presentasi Canva & Naskah Uji Kompetensi Keahlian (UKK)
### Aplikasi Penjualan Online NineMart — SMK Negeri 9 Semarang

---

## 🚀 Cara Import Slide Presentasi ke Canva (1 Klik)

File presentasi telah disiapkan dalam format standar PowerPoint 16:9 widescreen di:
📁 **[`NineMart/dokumen/PRESENTASI_NINEMART_CANVA.pptx`](file:///home/murtix/belajar/BelajarUKK/NineMart/dokumen/PRESENTASI_NINEMART_CANVA.pptx)**

### Langkah-langkah Membuka di Canva:
1. Buka browser dan login ke akun **[Canva](https://www.canva.com/)**.
2. Pada pojok kanan atas beranda Canva, klik tombol **"Unggah / Upload"** atau **"Buat Desain"** $\rightarrow$ **"Impor File"**.
3. Pilih file `PRESENTASI_NINEMART_CANVA.pptx`.
4. Canva akan secara otomatis mengonversi seluruh slide menjadi format presentasi Canva yang 100% dapat diedit (teks, warna, animasi transisi, dan template).
5. Tambahkan logo atau foto pendukung dari folder `NineMart/img/` jika diinginkan.

---

## 🗣️ Naskah Presentasi & Panduan Bicara per Slide (Untuk Asesor)

### Slide 1: Cover Judul
- **Judul Slide:** NineMart: Aplikasi Penjualan Online SMK Negeri 9 Semarang
- **Poin Bicara Siswa:**
  > *"Selamat pagi/siang Bapak/Ibu Asesor. Terima kasih atas kesempatan yang diberikan. Pada hari ini saya akan mempresentasikan hasil pengerjaan proyek Uji Kompetensi Keahlian (UKK) Klaster Pemrograman Web, yaitu Aplikasi Penjualan Online NineMart milik SMK Negeri 9 Semarang."*

### Slide 2: Latar Belakang & Masalah
- **Poin Bicara Siswa:**
  > *"Aplikasi ini dilatarbelakangi oleh kebutuhan digitalisasi pencatatan mini-market sekolah. Selama ini pendataan barang, stok, dan pemasok masih rentan terjadi kesalahan jika dicatat manual. NineMart hadir sebagai solusi web terintegrasi yang memudahkan petugas mengelola katalog dan persediaan barang secara terpusat."*

### Slide 3: Arsitektur & Teknologi (Tech Stack)
- **Poin Bicara Siswa:**
  > *"Untuk backend, aplikasi ini dibangun menggunakan Native PHP secara modular dan bersih dengan driver PDO (PHP Data Objects) tanpa framework berat, sehingga sangat ringan dan mudah dirawat. Pada sisi basis data, kami menggunakan MariaDB InnoDB yang menjamin konsistensi data. Serta untuk tampilan, kami menggunakan Semantic HTML5 dan Custom Pure CSS tanpa ketergantungan CDN eksternal, sehingga sistem dapat berjalan 100% offline di server lokal sekolah."*

### Slide 4: Desain Basis Data (db_ninemart)
- **Poin Bicara Siswa:**
  > *"Basis data db_ninemart terdiri dari 4 tabel utama: tabel pengguna untuk kredensial login, tabel kelompok untuk kategori barang, tabel pemasok untuk direktori vendor, dan tabel barang untuk katalog produk lengkap dengan harga beli, harga jual, dan stok."*

### Slide 5: Fitur Utama Aplikasi
- **Poin Bicara Siswa:**
  > *"Fitur utama mencakup autentikasi sistem dengan proteksi session guard, dashboard ucapan selamat datang 'Sugeng Rawuh', modul kelola pengguna, fitur ubah kata sandi dengan validasi keamanan, serta operasi CRUD lengkap untuk data kelompok, data pemasok, dan data barang."*

### Slide 6: Keamanan & Integritas Rekayasa Perangkat Lunak
- **Poin Bicara Siswa:**
  > *"Dari sisi keamanan, aplikasi menerapkan prinsip industri: pertama, kebal SQL Injection karena seluruh query memakai PDO Prepared Statements. Kedua, kebal XSS karena seluruh data luaran disanitasi menggunakan htmlspecialchars. Ketiga, proteksi session fixation dan proteksi relasi integritas di mana kelompok barang tidak boleh dihapus jika masih terkait dengan data barang."*

### Slide 7: Hasil Pengujian Fungsional
- **Poin Bicara Siswa:**
  > *"Seluruh skenario pengujian mulai dari validasi login, proteksi rute tanpa sesi, manipulasi data master, hingga penghancuran sesi saat logout telah diuji dan menghasilkan status 100% PASS."*

### Slide 8: Penutup & Tanya Jawab
- **Poin Bicara Siswa:**
  > *"Demikian presentasi dari saya. Selanjutnya saya siap untuk mendemonstrasikan sistem secara langsung dan menjawab pertanyaan dari Bapak/Ibu Asesor. Terima kasih."*
