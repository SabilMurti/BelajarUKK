<?php
require_once 'config/database.php';

$kode_transaksi = trim($_GET['kode'] ?? '');
if (empty($kode_transaksi)) {
    $_SESSION['error'] = "Kode transaksi tidak valid!";
    header('Location: transaksi.php');
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Ambil data transaksi yang akan dihapus
    $stmt = $pdo->prepare("SELECT kode_brg, jumlah FROM transaksi WHERE kode_transaksi = :trx FOR UPDATE");
    $stmt->execute([':trx' => $kode_transaksi]);
    $trx = $stmt->fetch();

    if (!$trx) {
        throw new Exception("Transaksi dengan kode '{$kode_transaksi}' tidak ditemukan!");
    }

    // 2. Kembalikan stok barang yang sudah dibeli
    $stmtKembalikan = $pdo->prepare("UPDATE barang SET jumlah = jumlah + :qty WHERE kode_brg = :kd");
    $stmtKembalikan->execute([
        ':qty' => (int)$trx['jumlah'],
        ':kd'  => $trx['kode_brg'],
    ]);

    // 3. Hapus data transaksi
    $stmtDel = $pdo->prepare("DELETE FROM transaksi WHERE kode_transaksi = :trx");
    $stmtDel->execute([':trx' => $kode_transaksi]);

    $pdo->commit();
    $_SESSION['sukses'] = "Transaksi '{$kode_transaksi}' berhasil dibatalkan dan {$trx['jumlah']} unit stok barang telah dikembalikan!";
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['error'] = "Gagal membatalkan transaksi: " . $e->getMessage();
}

header('Location: transaksi.php');
exit;

