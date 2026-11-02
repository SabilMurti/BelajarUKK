-- ==============================================================================
-- DATABASE: db_perpustakaan
-- PROYEK  : Aplikasi Manajemen Perpustakaan Berbasis Web (PHP & MySQL)
-- TUJUAN  : Standar Uji Kompetensi Keahlian (UKK) Jurusan PPLG / RPL
-- ENGINE  : InnoDB dengan Foreign Key Constraint (RESTRICT & CASCADE)
-- ==============================================================================

-- 1. Inisialisasi Database (Aman untuk phpMyAdmin & CLI)
CREATE DATABASE IF NOT EXISTS `db_perpustakaan` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `db_perpustakaan`;

-- Matikan foreign key check sementara agar drop table bersih tanpa konflik constraint
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `peminjaman`;
DROP TABLE IF EXISTS `anggota`;
DROP TABLE IF EXISTS `buku`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================================================
-- 2. TABEL: users (Petugas / Administrator Perpustakaan)
-- ==============================================================================
CREATE TABLE `users` (
    `id_user` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL COMMENT 'Disimpan dalam format bcrypt password_hash',
    `nama_lengkap` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- 3. TABEL: buku (Katalog Koleksi Buku Perpustakaan)
-- ==============================================================================
CREATE TABLE `buku` (
    `id_buku` INT AUTO_INCREMENT PRIMARY KEY,
    `kode_buku` VARCHAR(20) NOT NULL UNIQUE,
    `judul_buku` VARCHAR(150) NOT NULL,
    `pengarang` VARCHAR(100) NOT NULL,
    `penerbit` VARCHAR(100) NOT NULL,
    `tahun_terbit` YEAR NOT NULL,
    `kategori` VARCHAR(50) NOT NULL,
    `stok` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_buku_judul` (`judul_buku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- 4. TABEL: anggota (Data Pengunjung & Peminjam Terdaftar)
-- ==============================================================================
CREATE TABLE `anggota` (
    `id_anggota` INT AUTO_INCREMENT PRIMARY KEY,
    `nomor_anggota` VARCHAR(20) NOT NULL UNIQUE,
    `nama_lengkap` VARCHAR(100) NOT NULL,
    `jenis_kelamin` ENUM('Laki-laki', 'Perempuan') NOT NULL,
    `alamat` TEXT NOT NULL,
    `no_telepon` VARCHAR(20) NOT NULL,
    `tanggal_pendaftaran` DATE NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_anggota_nama` (`nama_lengkap`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- 5. TABEL: peminjaman (Transaksi Peminjaman & Pengembalian Buku)
--    Relasi: 
--    - id_anggota -> anggota(id_anggota) [ON DELETE RESTRICT ON UPDATE CASCADE]
--    - id_buku    -> buku(id_buku)       [ON DELETE RESTRICT ON UPDATE CASCADE]
-- ==============================================================================
CREATE TABLE `peminjaman` (
    `id_peminjaman` INT AUTO_INCREMENT PRIMARY KEY,
    `id_anggota` INT NOT NULL,
    `id_buku` INT NOT NULL,
    `tanggal_pinjam` DATE NOT NULL,
    `tanggal_rencana_kembali` DATE NOT NULL,
    `tanggal_kembali_aktual` DATE NULL DEFAULT NULL,
    `jumlah_pinjam` INT NOT NULL DEFAULT 1,
    `status` ENUM('Dipinjam', 'Dikembalikan') NOT NULL DEFAULT 'Dipinjam',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_peminjaman_status` (`status`),
    KEY `idx_peminjaman_tgl_pinjam` (`tanggal_pinjam`),
    CONSTRAINT `fk_peminjaman_anggota` 
        FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT `fk_peminjaman_buku` 
        FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`) 
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- 6. DUMMY DATA AWAL (SEEDING)
-- ==============================================================================

-- Akun Administrator Default:
-- Username: admin
-- Password: admin123
INSERT INTO `users` (`id_user`, `username`, `password`, `nama_lengkap`) VALUES
(1, 'admin', '$2y$12$ms6ZU8Q/lq.j0P6upWS.YuyOnfQY1yjZ4k6usWej2TBPY13Cij71O', 'Administrator Utama');

-- Data Katalog Buku
INSERT INTO `buku` (`id_buku`, `kode_buku`, `judul_buku`, `pengarang`, `penerbit`, `tahun_terbit`, `kategori`, `stok`) VALUES
(1, 'BK-001', 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 2005, 'Fiksi / Sastra', 4),
(2, 'BK-002', 'Pemrograman Web dengan PHP dan MySQL', 'Budi Raharjo', 'Informatika', 2021, 'Teknologi Informasi', 6),
(3, 'BK-003', 'Clean Code: A Handbook of Agile Craftsmanship', 'Robert C. Martin', 'Prentice Hall', 2008, 'Teknologi Informasi', 3),
(4, 'BK-004', 'Bumi Manusia', 'Pramoedya Ananta Toer', 'Hasta Mitra', 1980, 'Sejarah / Novel', 5),
(5, 'BK-005', 'Belajar Jaringan Komputer Berbasis Mikrotik', 'Iwan Sofana', 'Informatika', 2020, 'Teknologi Informasi', 2);

-- Data Anggota Perpustakaan
INSERT INTO `anggota` (`id_anggota`, `nomor_anggota`, `nama_lengkap`, `jenis_kelamin`, `alamat`, `no_telepon`, `tanggal_pendaftaran`) VALUES
(1, 'AG-2026-001', 'Ahmad Fauzi', 'Laki-laki', 'Jl. Merdeka No. 10, Jakarta Pusat', '081234567890', '2026-01-10'),
(2, 'AG-2026-002', 'Siti Nurhaliza', 'Perempuan', 'Jl. Sudirman No. 45, Bandung', '082345678901', '2026-01-15'),
(3, 'AG-2026-003', 'Budi Santoso', 'Laki-laki', 'Jl. Diponegoro No. 12, Surabaya', '083456789012', '2026-02-01'),
(4, 'AG-2026-004', 'Dewi Lestari', 'Perempuan', 'Jl. Malioboro No. 88, Yogyakarta', '085678901234', '2026-02-10');

-- Data Transaksi Peminjaman (Berisi status 'Dipinjam' dan 'Dikembalikan' untuk pengujian)
INSERT INTO `peminjaman` (`id_peminjaman`, `id_anggota`, `id_buku`, `tanggal_pinjam`, `tanggal_rencana_kembali`, `tanggal_kembali_aktual`, `jumlah_pinjam`, `status`) VALUES
(1, 1, 1, '2026-10-10', '2026-10-17', NULL, 1, 'Dipinjam'),
(2, 2, 2, '2026-10-30', '2026-11-06', NULL, 1, 'Dipinjam'),
(3, 3, 3, '2026-10-01', '2026-10-08', '2026-10-07', 1, 'Dikembalikan'),
(4, 4, 4, '2026-09-15', '2026-09-22', '2026-09-25', 1, 'Dikembalikan');
