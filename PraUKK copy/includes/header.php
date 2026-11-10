<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' - Sistem Penjualan Online' : 'Sistem Penjualan Online'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="navbar">
    <div class="nav-container">
        <a href="index.php" class="brand">
            <span>SiPenjualan</span>
            <span class="brand-badge">PRA UKK</span>
        </a>
        <ul class="nav-links">
            <li><a href="index.php" class="<?= $current_page === 'index.php' ? 'active' : ''; ?>">Dashboard</a></li>
            <li><a href="barang.php" class="<?= in_array($current_page, ['barang.php', 'barang_tambah.php', 'barang_edit.php']) ? 'active' : ''; ?>">Data Barang</a></li>
            <li><a href="transaksi.php" class="<?= in_array($current_page, ['transaksi.php', 'transaksi_tambah.php', 'transaksi_edit.php']) ? 'active' : ''; ?>">Data Transaksi</a></li>
        </ul>
        <div class="nav-user">
            Peserta: <strong>Zidan Fathul</strong> (XII RPL)
        </div>
    </div>
</header>

<main class="container">
    <?php if (!empty($_SESSION['sukses'])): ?>
        <div class="alert alert-success">
            <strong>Berhasil!</strong> <?= htmlspecialchars($_SESSION['sukses']); unset($_SESSION['sukses']); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <strong>Perhatian:</strong> <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

