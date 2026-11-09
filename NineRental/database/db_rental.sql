-- ==============================================================================
-- DATABASE: rental
-- SOAL UKK LSP P1 SMK NEGERI 9 SEMARANG - APLIKASI NINE RENTAL MOBIL
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS `rental`;
USE `rental`;

-- ------------------------------------------------------------------------------
-- 1. Struktur Tabel: kendaraantbl
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `kendaraantbl`;
CREATE TABLE `kendaraantbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_polisi` varchar(12) NOT NULL,
  `nama_kendaraan` varchar(35) NOT NULL,
  `tipe` varchar(2) NOT NULL,
  `warna` varchar(15) NOT NULL,
  `no_rangka` varchar(75) NOT NULL,
  `no_mesin` varchar(75) NOT NULL,
  `bahan_bakar` varchar(10) NOT NULL,
  `thn_kendaraan` int(11) NOT NULL,
  `foto` text DEFAULT NULL,
  `status` varchar(10) NOT NULL DEFAULT 'Ready',
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_polisi` (`no_polisi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data Awal: kendaraantbl (sesuai screenshot soal PDF)
INSERT INTO `kendaraantbl` (`id`, `no_polisi`, `nama_kendaraan`, `tipe`, `warna`, `no_mesin`, `no_rangka`, `bahan_bakar`, `thn_kendaraan`, `foto`, `status`) VALUES
(1, 'BG 1726 B', 'SUPRA X', 'AT', 'Hitam', 'AV-2013-098', '123456789', 'Bensin', 2013, '', 'Ready'),
(2, 'BG 6587 B', 'MIO', 'MT', 'Putih', 'XN-765-88', '0987654321', 'Bensin', 2014, '', 'Ready');

-- ------------------------------------------------------------------------------
-- 2. Struktur Tabel: pelanggantbl
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `pelanggantbl`;
CREATE TABLE `pelanggantbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `no_ktp` varchar(75) NOT NULL,
  `nama_pelanggan` varchar(35) NOT NULL,
  `alamat` text NOT NULL,
  `telepon` varchar(20) NOT NULL,
  `foto` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_ktp` (`no_ktp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data Awal: pelanggantbl (sesuai screenshot soal PDF)
INSERT INTO `pelanggantbl` (`id`, `no_ktp`, `nama_pelanggan`, `alamat`, `telepon`, `foto`) VALUES
(1, '3579518264', 'Maxikom', 'Jln. Residen H. Abdul Rozak No. 33 Palembang', '0711-717598', ''),
(2, '3579518284', 'Budi Santoso', 'Jl. Pemuda No. 45 Semarang', '081234567890', '');

-- ------------------------------------------------------------------------------
-- 3. Struktur Tabel: sewatbl
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS `sewatbl`;
CREATE TABLE `sewatbl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `no_sewa` varchar(20) NOT NULL,
  `no_ktp` varchar(25) NOT NULL,
  `no_polisi` varchar(12) NOT NULL,
  `tgl_sewa` date NOT NULL,
  `tgl_kembali` date NOT NULL,
  `biaya` int(11) NOT NULL,
  `catatan` text DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Sewa',
  PRIMARY KEY (`id`),
  UNIQUE KEY `no_sewa` (`no_sewa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Data Awal: sewatbl (sesuai screenshot soal PDF)
INSERT INTO `sewatbl` (`id`, `tanggal`, `no_sewa`, `no_ktp`, `no_polisi`, `tgl_sewa`, `tgl_kembali`, `biaya`, `catatan`, `status`) VALUES
(1, '2014-03-15', 'S,14-03,1', '3579518264', 'BG 6587 B', '2014-03-15', '2014-03-17', 300000, '-', 'Kembali'),
(2, '2014-03-23', 'S,14-03,2', '3579518284', 'BG 1726 B', '2014-03-23', '2014-03-25', 350000, 'Penyewaan', 'Kembali');
