<?php
/**
 * ==============================================================================
 * TEMPLATE HEADER & NAVIGASI (VERSI BOOTSTRAP 5)
 * ==============================================================================
 * Menggunakan framework CSS Bootstrap 5.3 CDN dan Bootstrap Icons.
 */

// Pastikan session aktif untuk membaca data login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_page = basename($_SERVER['PHP_SELF']);
$app_title = isset($page_title) ? $page_title . " - Perpustakaan UKK" : "Perpustakaan UKK PPLG";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($app_title) ?></title>
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-content {
            flex: 1;
        }
        .stat-card {
            border-left: 4px solid var(--bs-primary);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
        @media print {
            .d-print-none {
                display: none !important;
            }
            body {
                background-color: #ffffff !important;
            }
        }
    </style>
</head>
<body>

    <!-- Header & Navigasi Utama -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm d-print-none">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="index.php">
                <i class="bi bi-book-half text-primary fs-4"></i>
                <span>PerpusUKK</span>
                <span class="badge bg-primary text-uppercase" style="font-size: 0.7rem;">PPLG</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page === 'index.php') ? 'active fw-semibold' : '' ?>" href="index.php">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page === 'buku.php') ? 'active fw-semibold' : '' ?>" href="buku.php">
                            <i class="bi bi-journal-bookmark me-1"></i> Data Buku
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page === 'anggota.php') ? 'active fw-semibold' : '' ?>" href="anggota.php">
                            <i class="bi bi-people me-1"></i> Data Anggota
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page === 'peminjaman.php') ? 'active fw-semibold' : '' ?>" href="peminjaman.php">
                            <i class="bi bi-arrow-up-right-circle me-1"></i> Peminjaman
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page === 'pengembalian.php') ? 'active fw-semibold' : '' ?>" href="pengembalian.php">
                            <i class="bi bi-arrow-down-left-circle me-1"></i> Pengembalian
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($current_page === 'laporan.php') ? 'active fw-semibold' : '' ?>" href="laporan.php">
                            <i class="bi bi-printer me-1"></i> Laporan
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <span class="text-light small">
                        <i class="bi bi-person-circle me-1 text-info"></i>
                        Petugas: <strong><?= escape($_SESSION['nama_lengkap'] ?? 'Admin') ?></strong>
                    </span>
                    <a href="logout.php" class="btn btn-outline-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')">
                        <i class="bi bi-box-arrow-right me-1"></i> Keluar
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Kontainer Utama Halaman -->
    <main class="main-content container py-4">
