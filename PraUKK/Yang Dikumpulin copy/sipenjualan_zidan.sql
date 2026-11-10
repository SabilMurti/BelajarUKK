-- ==========================================================
-- PRA UJI KOMPETENSI KEAHLIAN (PRA UKK) KELAS XII RPL
-- Sistem Penjualan Online
-- Database: sipenjualan_zidan
-- Peserta: Zidan Fathul (SMK Negeri 9 Semarang)
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `sipenjualan_zidan`
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE `sipenjualan_zidan`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `transaksi`;
DROP TABLE IF EXISTS `barang`;
SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------
-- 1. Struktur Tabel `barang`
-- ----------------------------------------------------------
CREATE TABLE `barang` (
  `kode_brg` VARCHAR(6) NOT NULL,
  `nama_brg` VARCHAR(100) NOT NULL,
  `merk` VARCHAR(30) NOT NULL,
  `harga` BIGINT(10) NOT NULL,
  `jumlah` INT(4) NOT NULL,
  PRIMARY KEY (`kode_brg`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------------------------------------
-- 2. Struktur Tabel `transaksi`
-- ----------------------------------------------------------
CREATE TABLE `transaksi` (
  `kode_transaksi` VARCHAR(10) NOT NULL,
  `kode_brg` VARCHAR(6) NOT NULL,
  `nama_brg` VARCHAR(60) NOT NULL,
  `harga` BIGINT(10) NOT NULL,
  `jumlah` INT(4) NOT NULL,
  `total_bayar` BIGINT(10) NOT NULL,
  `tanggal` DATE NOT NULL,
  PRIMARY KEY (`kode_transaksi`),
  KEY `kode_brg` (`kode_brg`),
  CONSTRAINT `fk_transaksi_barang` FOREIGN KEY (`kode_brg`) 
    REFERENCES `barang` (`kode_brg`) 
    ON UPDATE CASCADE 
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------------------------------------
-- Data Awal Pengujian Sistem
-- ----------------------------------------------------------
INSERT INTO `barang` (`kode_brg`, `nama_brg`, `merk`, `harga`, `jumlah`) VALUES
('BRG001', 'Keyboard Mechanical RGB', 'Logitech', 450000, 25),
('BRG002', 'Mouse Wireless Ergonomic', 'Razer', 250000, 40),
('BRG003', 'Headset Gaming 7.1 Surround', 'Fantech', 320000, 15),
('BRG004', 'Flashdisk 64GB USB 3.0', 'SanDisk', 85000, 50),
('BRG005', 'Webcam Full HD 1080p', 'A4Tech', 375000, 12);

INSERT INTO `transaksi` (`kode_transaksi`, `kode_brg`, `nama_brg`, `harga`, `jumlah`, `total_bayar`, `tanggal`) VALUES
('TRX0001', 'BRG001', 'Keyboard Mechanical RGB', 450000, 2, 900000, '2026-11-08'),
('TRX0002', 'BRG002', 'Mouse Wireless Ergonomic', 250000, 1, 250000, '2026-11-09'),
('TRX0003', 'BRG004', 'Flashdisk 64GB USB 3.0', 85000, 3, 255000, '2026-11-10');
