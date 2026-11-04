<?php
// Template header dan navigasi NineMart
if (!defined('APP_INIT')) {
    define('APP_INIT', true);
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' - ' : '' ?>NineMart SMK Negeri 9 Semarang</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="app-container">

    <!-- Banner header -->
    <div class="header-banner">
        <img src="assets/img/header-banner.png" alt="Header Banner Nine Mart SMK Negeri 9 Semarang">
    </div>

    <!-- Menu navigasi -->
    <nav class="main-navbar">
        <ul class="nav-list">
            <li class="nav-item <?= ($current_page === 'index.php') ? 'active' : '' ?>">
                <a href="index.php" class="nav-link">Home</a>
            </li>

            <li class="nav-item dropdown <?= in_array($current_page, ['pengguna.php', 'ubah_password.php']) ? 'active' : '' ?>">
                <a href="#" class="nav-link">
                    Pengguna <span class="nav-arrow">&#9662;</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="pengguna.php" class="dropdown-item">Data Pengguna</a></li>
                    <li><a href="ubah_password.php" class="dropdown-item">Ubah Password</a></li>
                </ul>
            </li>

            <li class="nav-item dropdown <?= in_array($current_page, ['barang.php', 'pemasok.php', 'kelompok.php']) ? 'active' : '' ?>">
                <a href="#" class="nav-link">
                    Data <span class="nav-arrow">&#9662;</span>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="barang.php" class="dropdown-item">Data Barang</a></li>
                    <li><a href="pemasok.php" class="dropdown-item">Data Pemasok</a></li>
                    <li><a href="kelompok.php" class="dropdown-item">Data Kelompok</a></li>
                </ul>
            </li>

            <li class="nav-item">
                <a href="logout.php" class="nav-link">Logout</a>
            </li>
        </ul>
    </nav>

    <!-- Konten halaman -->
    <div class="content-body">
