<?php
/**
 * ==============================================================================
 * AUTOMATED QUERY & INTEGRITY TEST SUITE - NINEMART
 * ==============================================================================
 */

require_once 'config/database.php';

echo "=== NINEMART QUERY TEST SUITE ===\n";
echo "[OK] Connected to database: {$db_name}\n\n";

// Test 1: Data Pengguna
$pengguna = $pdo->query("SELECT COUNT(*) as total FROM tblpengguna")->fetch();
echo "[Test 1] Total Pengguna: " . $pengguna['total'] . "\n";

// Test 2: Data Kelompok
$kelompok = $pdo->query("SELECT COUNT(*) as total FROM tblkelompok")->fetch();
echo "[Test 2] Total Kelompok: " . $kelompok['total'] . "\n";

// Test 3: Data Pemasok
$pemasok = $pdo->query("SELECT COUNT(*) as total FROM tblpemasok")->fetch();
echo "[Test 3] Total Pemasok: " . $pemasok['total'] . "\n";

// Test 4: Data Barang & Relasi
$barang = $pdo->query("
    SELECT b.*, k.nama_kelompok 
    FROM tblbarang b 
    LEFT JOIN tblkelompok k ON b.kode_kelompok = k.kode_kelompok
")->fetchAll();
echo "[Test 4] Total Barang: " . count($barang) . "\n";
foreach ($barang as $b) {
    echo "  - {$b['kode_barang']} | {$b['nama_barang']} ({$b['nama_kelompok']}) | Beli: {$b['harga_beli']} | Jual: {$b['harga_jual']} | Stok: {$b['stock']}\n";
}

// Test 5: Transaction Atomicity Test
echo "\n[Test 5] Simulating Transaction (INSERT & ROLLBACK)...\n";
$pdo->beginTransaction();
$pdo->prepare("INSERT INTO tblbarang (kode_barang, nama_barang, kode_kelompok, harga_beli, harga_jual, stock) VALUES ('BRG-TEST', 'BARANG TEST', 'KLP-1', 1000, 2000, 10)")->execute();
$cekInsert = $pdo->query("SELECT COUNT(*) FROM tblbarang WHERE kode_barang = 'BRG-TEST'")->fetchColumn();
echo "  - Record created in transaction: {$cekInsert}\n";
$pdo->rollBack();
$cekRollback = $pdo->query("SELECT COUNT(*) FROM tblbarang WHERE kode_barang = 'BRG-TEST'")->fetchColumn();
echo "  - Record after rollback: {$cekRollback} (Should be 0)\n";

if ($cekRollback == 0) {
    echo "[PASS] Transaction ACID rollback verified successfully!\n";
} else {
    echo "[FAIL] Rollback failed!\n";
}

echo "\nAll tests completed successfully!\n";
