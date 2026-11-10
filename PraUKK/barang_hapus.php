<?php
require_once 'config/database.php';

$kode_brg = trim($_GET['kode'] ?? '');
if (empty($kode_brg)) {
    $_SESSION['error'] = "Kode barang tidak valid!";
    header('Location: barang.php');
    exit;
}

try {
    // 1. Periksa apakah barang sudah memiliki riwayat transaksi (integritas relasi)
    $stmtCek = $pdo->prepare("SELECT COUNT(*) FROM transaksi WHERE kode_brg = :kd");
    $stmtCek->execute([':kd' => $kode_brg]);
    $totalPakai = $stmtCek->fetchColumn();

    if ($totalPakai > 0) {
        $_SESSION['error'] = "Barang '{$kode_brg}' tidak dapat dihapus karena masih digunakan pada {$totalPakai} data transaksi! Hapus transaksi terkait terlebih dahulu.";
        header('Location: barang.php');
        exit;
    }

    // 2. Jika aman, lakukan penghapusan
    $stmtHapus = $pdo->prepare("DELETE FROM barang WHERE kode_brg = :kd");
    $stmtHapus->execute([':kd' => $kode_brg]);

    if ($stmtHapus->rowCount() > 0) {
        $_SESSION['sukses'] = "Barang dengan kode '{$kode_brg}' berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Data barang tidak ditemukan atau sudah terhapus.";
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Gagal menghapus barang: " . $e->getMessage();
}

header('Location: barang.php');
exit;
