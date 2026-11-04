-- ==============================================================================
-- DATABASE: db_ninemart
-- PROYEK  : Aplikasi Penjualan Online NineMart - SMK Negeri 9 Semarang
-- STANDAR : Soal Tryout UKK 2026 / 2024 Klaster Pemrograman Web
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS `db_ninemart` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `db_ninemart`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `tblbarang`;
DROP TABLE IF EXISTS `tblpemasok`;
DROP TABLE IF EXISTS `tblkelompok`;
DROP TABLE IF EXISTS `tblpengguna`;
SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------------------------
-- 1. TABEL: tblpengguna
-- ------------------------------------------------------------------------------
CREATE TABLE `tblpengguna` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `user` VARCHAR(15) NOT NULL UNIQUE,
    `password` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. TABEL: tblkelompok
-- ------------------------------------------------------------------------------
CREATE TABLE `tblkelompok` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `kode_kelompok` VARCHAR(15) NOT NULL UNIQUE,
    `nama_kelompok` VARCHAR(35) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. TABEL: tblpemasok
-- ------------------------------------------------------------------------------
CREATE TABLE `tblpemasok` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `kode_pemasok` VARCHAR(15) NOT NULL UNIQUE,
    `nama_pemasok` VARCHAR(35) NOT NULL,
    `alamat` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 4. TABEL: tblbarang
-- ------------------------------------------------------------------------------
CREATE TABLE `tblbarang` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `kode_barang` VARCHAR(15) NOT NULL UNIQUE,
    `nama_barang` VARCHAR(35) NOT NULL,
    `kode_kelompok` VARCHAR(15) NOT NULL,
    `harga_beli` INT(11) NOT NULL,
    `harga_jual` INT(11) NOT NULL,
    `stock` INT(11) NOT NULL,
    KEY `idx_kode_kelompok` (`kode_kelompok`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- DUMMY SEED DATA (SESUAI DOKUMEN INSTRUMEN SOAL TRYOUT UKK)
-- ==============================================================================

-- Data Pengguna Default
-- Password terenkripsi dengan password_hash (default: admin, user, adi)
-- Logika autentikasi mendukung verifikasi hash dan kompatibilitas plain text
INSERT INTO `tblpengguna` (`id`, `user`, `password`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'), -- password: password (atau admin)
(2, 'user', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
(3, 'adi', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Data Kelompok Barang
INSERT INTO `tblkelompok` (`id`, `kode_kelompok`, `nama_kelompok`) VALUES
(1, 'KLP-1', 'MAKANAN'),
(2, 'KLP-2', 'BARANG HARIAN'),
(3, 'KLP-3', 'MNUMAN');

-- Data Pemasok
INSERT INTO `tblpemasok` (`id`, `kode_pemasok`, `nama_pemasok`, `alamat`) VALUES
(1, 'PMS-1', 'Toko Jaya Makmur', 'PASAR SAMPANGAN'),
(2, 'PMS-2', 'Toko Maju Mundur', 'PASAR DARGO'),
(3, 'PMS-3', 'Toko Maju Abadi', 'PASAR KOBONG'),
(4, 'PMS-4', 'Toko Maju Selamat', 'PASAR BULU'),
(5, 'PMS-5', 'Toko Maju Sejahtera', 'PASAR PETERONGAN');

-- Data Barang
INSERT INTO `tblbarang` (`id`, `kode_barang`, `nama_barang`, `kode_kelompok`, `harga_beli`, `harga_jual`, `stock`) VALUES
(1, 'BRG-1', 'SABUN', 'KLP-2', 15000, 17500, 50),
(2, 'BRG-2', 'SAMPO', 'KLP-2', 32500, 40000, 100),
(3, 'BRG-3', 'SIKAT GIGI', 'KLP-2', 22500, 30000, 100);
