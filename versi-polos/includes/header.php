<?php
/**
 * ==============================================================================
 * TEMPLATE HEADER & NAVIGASI (VERSI POLOS / SEMANTIC HTML5)
 * ==============================================================================
 * Menggunakan HTML5 semantik dan CSS murni dari assets/style.css tanpa CDN.
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
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <!-- Header & Navigasi Utama -->
    <header class="navbar no-print">
        <div class="nav-container">
            <a href="index.php" class="nav-brand">
                📚 <span>PerpusUKK</span>
                <span class="badge-logo">PPLG</span>
            </a>

            <nav>
                <ul class="nav-menu">
                    <li>
                        <a href="index.php" class="nav-link <?= ($current_page === 'index.php') ? 'active' : '' ?>">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="buku.php" class="nav-link <?= ($current_page === 'buku.php') ? 'active' : '' ?>">
                            Data Buku
                        </a>
                    </li>
                    <li>
                        <a href="anggota.php" class="nav-link <?= ($current_page === 'anggota.php') ? 'active' : '' ?>">
                            Data Anggota
                        </a>
                    </li>
                    <li>
                        <a href="peminjaman.php" class="nav-link <?= ($current_page === 'peminjaman.php') ? 'active' : '' ?>">
                            Peminjaman
                        </a>
                    </li>
                    <li>
                        <a href="pengembalian.php" class="nav-link <?= ($current_page === 'pengembalian.php') ? 'active' : '' ?>">
                            Pengembalian
                        </a>
                    </li>
                    <li>
                        <a href="laporan.php" class="nav-link <?= ($current_page === 'laporan.php') ? 'active' : '' ?>">
                            Laporan
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="nav-user">
                <span class="user-tag">👤 Petugas: <strong class="user-name"><?= escape($_SESSION['nama_lengkap'] ?? 'Admin') ?></strong></span>
                <a href="logout.php" class="btn-logout" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')">Keluar</a>
            </div>
        </div>
    </header>

    <!-- Kontainer Isi Halaman -->
    <main class="main-content">
