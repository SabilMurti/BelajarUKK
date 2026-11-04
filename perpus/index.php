<?php
/**
 * ==============================================================================
 * PORTAL UTAMA APLIKASI PERPUSTAKAAN UKK PPLG
 * ==============================================================================
 * Halaman landing page untuk memilih versi tampilan yang ingin diuji / dipelajari:
 * 1. Versi Polos (Semantic HTML5 & 1 berkas CSS murni)
 * 2. Versi Bootstrap 5 (CDN Framework Modern)
 * Dilengkapi dengan pengecekan koneksi database secara real-time.
 * ==============================================================================
 */

// Cek koneksi ke database db_perpustakaan
$db_connected = false;
$db_error = '';
$total_buku = 0;
$total_anggota = 0;
$total_pinjam = 0;

try {
    $dsn = "mysql:host=127.0.0.1;port=3306;dbname=db_perpustakaan;charset=utf8mb4";
    $pdo = new PDO($dsn, 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 2
    ]);
    $db_connected = true;

    // Ambil ringkasan cepat
    $total_buku = $pdo->query("SELECT COUNT(*) FROM buku")->fetchColumn();
    $total_anggota = $pdo->query("SELECT COUNT(*) FROM anggota")->fetchColumn();
    $total_pinjam = $pdo->query("SELECT COUNT(*) FROM peminjaman")->fetchColumn();

} catch (Exception $e) {
    $db_connected = false;
    $db_error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Pengujian - Perpustakaan UKK PPLG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .portal-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        .portal-card:hover {
            transform: translateY(-5px);
            border-color: rgba(59, 130, 246, 0.5);
            background: rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<body class="py-5">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <!-- Header Brand -->
                <div class="text-center mb-5">
                    <div class="badge bg-primary px-3 py-2 rounded-pill text-uppercase fw-semibold mb-2 shadow-sm">
                        UKK PPLG / RPL Standar Industri
                    </div>
                    <h1 class="display-5 fw-bold text-white mb-2">Aplikasi Manajemen Perpustakaan</h1>
                    <p class="text-secondary fs-5">Native PHP Procedural Modular & Database Relasional MySQL</p>
                    
                    <!-- Status Indikator Database -->
                    <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mt-2 border <?= $db_connected ? 'bg-success bg-opacity-25 border-success text-success-emphasis' : 'bg-danger bg-opacity-25 border-danger text-danger-emphasis' ?>">
                        <span class="p-1 rounded-circle <?= $db_connected ? 'bg-success' : 'bg-danger' ?> d-inline-block"></span>
                        <small class="fw-semibold text-white">
                            <?= $db_connected ? "Database Terhubung: <strong>db_perpustakaan</strong> ({$total_buku} Buku, {$total_anggota} Anggota, {$total_pinjam} Transaksi)" : "Database Terputus: {$db_error}" ?>
                        </small>
                    </div>
                </div>

                <!-- Pilihan Dua Versi Tampilan -->
                <div class="row g-4 mb-5">
                    <!-- Versi 1: Polos (Semantic HTML & Pure CSS) -->
                    <div class="col-md-6">
                        <div class="card h-100 rounded-4 portal-card p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="badge bg-secondary">Versi 1</span>
                                <i class="bi bi-filetype-css fs-2 text-info"></i>
                            </div>
                            <h3 class="fw-bold text-white">Versi Polos (Vanilla CSS)</h3>
                            <p class="text-secondary small mb-4">
                                Dibangun murni menggunakan <strong>HTML5 Semantik</strong> dan 1 berkas <strong>CSS mandiri (assets/style.css)</strong> tanpa CDN/library luar. Sangat ringan, terstruktur rapi, dan mudah dipelajari untuk dasar kompetensi.
                            </p>
                            <div class="mt-auto">
                                <a href="versi-polos/index.php" class="btn btn-outline-info w-100 py-2 fw-semibold">
                                    Buka Versi Polos <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Versi 2: Bootstrap 5 CDN -->
                    <div class="col-md-6">
                        <div class="card h-100 rounded-4 portal-card p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="badge bg-primary">Versi 2</span>
                                <i class="bi bi-bootstrap-fill fs-2 text-primary"></i>
                            </div>
                            <h3 class="fw-bold text-white">Versi Bootstrap 5.3</h3>
                            <p class="text-secondary small mb-4">
                                Logika PHP, query database, dan nama berkas <strong>100% IDENTIK</strong> dengan versi polos, namun tampilan menggunakan framework <strong>Bootstrap 5.3 CDN & Bootstrap Icons</strong> dengan antarmuka modern siap saji.
                            </p>
                            <div class="mt-auto">
                                <a href="versi-bootstrap/index.php" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
                                    Buka Versi Bootstrap <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Kredensial Login & Panduan -->
                <div class="card portal-card rounded-4 p-4 border-0">
                    <div class="row align-items-center">
                        <div class="col-md-7 mb-3 mb-md-0">
                            <h5 class="fw-bold text-white mb-1"><i class="bi bi-shield-lock text-warning me-2"></i>Akun Administrator Default</h5>
                            <p class="text-secondary small mb-0">Gunakan akun ini saat mengakses form login pada kedua versi:</p>
                            <div class="d-flex gap-3 mt-2 text-white font-monospace small">
                                <div>Username: <strong class="text-warning">admin</strong></div>
                                <div>Password: <strong class="text-warning">admin123</strong></div>
                            </div>
                        </div>
                        <div class="col-md-5 text-md-end">
                            <a href="https://github.com/SabilMurti/BelajarUKK" target="_blank" class="btn btn-sm btn-outline-light me-2">
                                <i class="bi bi-github me-1"></i> GitHub Repo
                            </a>
                            <a href="test_queries.php" target="_blank" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-check-circle me-1"></i> Uji Query Database
                            </a>
                        </div>
                    </div>
                </div>

                <div class="text-center text-secondary small mt-4">
                    &copy; <?= date('Y') ?> Proyek Referensi UKK PPLG &bull; Dibuat dengan Standar Industri Bersih
                </div>

            </div>
        </div>
    </div>

</body>
</html>
