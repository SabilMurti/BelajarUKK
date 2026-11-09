<?php
// Konfigurasi database MySQL
$db_host = '127.0.0.1';
$db_port = '3306';
$db_name = 'rental';
$db_user = 'root';
$db_pass = '';

try {
    // Coba koneksi MySQL via PDO
    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // Jika MySQL lokal belum aktif, gunakan SQLite bawaan agar web langsung jalan
    $sqlite_file = __DIR__ . '/../database/rental.db';
    $dsn = "sqlite:" . $sqlite_file;
    $pdo = new PDO($dsn, null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Inisialisasi tabel SQLite otomatis
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS kendaraantbl (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            no_polisi TEXT NOT NULL UNIQUE,
            nama_kendaraan TEXT NOT NULL,
            tipe TEXT NOT NULL,
            warna TEXT NOT NULL,
            no_rangka TEXT NOT NULL,
            no_mesin TEXT NOT NULL,
            bahan_bakar TEXT NOT NULL,
            thn_kendaraan INTEGER NOT NULL,
            foto TEXT DEFAULT NULL,
            status TEXT NOT NULL DEFAULT 'Ready'
        );

        CREATE TABLE IF NOT EXISTS pelanggantbl (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            no_ktp TEXT NOT NULL UNIQUE,
            nama_pelanggan TEXT NOT NULL,
            alamat TEXT NOT NULL,
            telepon TEXT NOT NULL,
            foto TEXT DEFAULT NULL
        );

        CREATE TABLE IF NOT EXISTS sewatbl (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            tanggal TEXT NOT NULL,
            no_sewa TEXT NOT NULL UNIQUE,
            no_ktp TEXT NOT NULL,
            no_polisi TEXT NOT NULL,
            tgl_sewa TEXT NOT NULL,
            tgl_kembali TEXT NOT NULL,
            biaya INTEGER NOT NULL,
            catatan TEXT DEFAULT NULL,
            status TEXT NOT NULL DEFAULT 'Sewa'
        );
    ");

    // Seed data default jika tabel kosong
    $cek = $pdo->query("SELECT COUNT(*) FROM kendaraantbl")->fetchColumn();
    if ($cek == 0) {
        $pdo->exec("
            INSERT INTO kendaraantbl (id, no_polisi, nama_kendaraan, tipe, warna, no_mesin, no_rangka, bahan_bakar, thn_kendaraan, foto, status) VALUES
            (1, 'BG 1726 B', 'SUPRA X', 'AT', 'Hitam', 'AV-2013-098', '123456789', 'Bensin', 2013, '', 'Ready'),
            (2, 'BG 6587 B', 'MIO', 'MT', 'Putih', 'XN-765-88', '0987654321', 'Bensin', 2014, '', 'Ready');

            INSERT INTO pelanggantbl (id, no_ktp, nama_pelanggan, alamat, telepon, foto) VALUES
            (1, '3579518264', 'Maxikom', 'Jln. Residen H. Abdul Rozak No. 33 Palembang', '0711-717598', ''),
            (2, '3579518284', 'Budi Santoso', 'Jl. Pemuda No. 45 Semarang', '081234567890', '');

            INSERT INTO sewatbl (id, tanggal, no_sewa, no_ktp, no_polisi, tgl_sewa, tgl_kembali, biaya, catatan, status) VALUES
            (1, '2014-03-15', 'S,14-03,1', '3579518264', 'BG 6587 B', '2014-03-15', '2014-03-17', 300000, '-', 'Kembali'),
            (2, '2014-03-23', 'S,14-03,2', '3579518284', 'BG 1726 B', '2014-03-23', '2014-03-25', 350000, 'Penyewaan', 'Kembali');
        ");
    }
}

// Fungsi sanitasi output HTML
function escape($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}
