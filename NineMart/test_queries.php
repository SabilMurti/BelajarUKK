<?php
// Skrip pengujian otomatis query dan integritas database NineMart
require_once 'config/database.php';

echo "=== PENGUJIAN QUERY NINEMART ===\n";
echo "[OK] Terhubung ke database: {$db_name}\n\n";

// Uji 1: Jumlah data pengguna
$pengguna = $pdo->query("SELECT COUNT(*) as total FROM tblpengguna")->fetch();
echo "[Uji 1] Total Pengguna: " . $pengguna['total'] . "\n";

// Uji 2: Jumlah data kelompok
$kelompok = $pdo->query("SELECT COUNT(*) as total FROM tblkelompok")->fetch();
echo "[Uji 2] Total Kelompok: " . $kelompok['total'] . "\n";

// Uji 3: Jumlah data pemasok
$pemasok = $pdo->query("SELECT COUNT(*) as total FROM tblpemasok")->fetch();
echo "[Uji 3] Total Pemasok: " . $pemasok['total'] . "\n";

// Uji 4: Data barang beserta relasi kelompoknya
$barang = $pdo->query("
    SELECT b.*, k.nama_kelompok 
    FROM tblbarang b 
    LEFT JOIN tblkelompok k ON b.kode_kelompok = k.kode_kelompok
")->fetchAll();
echo "[Uji 4] Total Barang: " . count($barang) . "\n";
foreach ($barang as $b) {
    echo "  - {$b['kode_barang']} | {$b['nama_barang']} ({$b['nama_kelompok']}) | Beli: {$b['harga_beli']} | Jual: {$b['harga_jual']} | Stok: {$b['stock']}\n";
}

// Uji 5: Simulasi transaksi database dan rollback
echo "\n[Uji 5] Simulasi Transaksi (INSERT & ROLLBACK)...\n";
$pdo->beginTransaction();
$pdo->prepare("INSERT INTO tblbarang (kode_barang, nama_barang, kode_kelompok, harga_beli, harga_jual, stock) VALUES ('BRG-TEST', 'BARANG TEST', 'KLP-1', 1000, 2000, 10)")->execute();
$cekInsert = $pdo->query("SELECT COUNT(*) FROM tblbarang WHERE kode_barang = 'BRG-TEST'")->fetchColumn();
echo "  - Data dibuat saat transaksi berjalan: {$cekInsert}\n";
$pdo->rollBack();
$cekRollback = $pdo->query("SELECT COUNT(*) FROM tblbarang WHERE kode_barang = 'BRG-TEST'")->fetchColumn();
echo "  - Data setelah dibatalkan (rollback): {$cekRollback} (Harus 0)\n";

if ($cekRollback == 0) {
    echo "[PASS] Pembatalan transaksi atomik berhasil diverifikasi!\n";
} else {
    echo "[FAIL] Pembatalan transaksi gagal!\n";
}

echo "\nSeluruh pengujian selesai dengan baik!\n";
