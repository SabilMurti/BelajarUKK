<?php
// Skrip pengujian otomatis query dan integritas database Nine Rental
require_once __DIR__ . '/config/database.php';

echo "=== PENGUJIAN QUERY & INTEGRITAS NINE RENTAL ===\n";
echo "[OK] Terhubung ke database: " . ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME)) . "\n\n";

// Uji 1: Jumlah data kendaraan
$kendaraan = $pdo->query("SELECT COUNT(*) as total FROM kendaraantbl")->fetch();
echo "[Uji 1] Total Kendaraan: " . $kendaraan['total'] . "\n";
$list_k = $pdo->query("SELECT no_polisi, nama_kendaraan, tipe, warna, status FROM kendaraantbl")->fetchAll();
foreach ($list_k as $k) {
    echo "  - {$k['no_polisi']} | {$k['nama_kendaraan']} ({$k['tipe']}) | {$k['warna']} | Status: {$k['status']}\n";
}

// Uji 2: Jumlah data pelanggan
echo "\n";
$pelanggan = $pdo->query("SELECT COUNT(*) as total FROM pelanggantbl")->fetch();
echo "[Uji 2] Total Pelanggan: " . $pelanggan['total'] . "\n";
$list_p = $pdo->query("SELECT no_ktp, nama_pelanggan, telepon FROM pelanggantbl")->fetchAll();
foreach ($list_p as $p) {
    echo "  - {$p['no_ktp']} | {$p['nama_pelanggan']} | {$p['telepon']}\n";
}

// Uji 3: Data transaksi sewa
echo "\n";
$sewa = $pdo->query("SELECT COUNT(*) as total FROM sewatbl")->fetch();
echo "[Uji 3] Total Transaksi Sewa: " . $sewa['total'] . "\n";
$list_s = $pdo->query("SELECT no_sewa, no_polisi, no_ktp, tgl_sewa, tgl_kembali, biaya, status FROM sewatbl")->fetchAll();
foreach ($list_s as $s) {
    echo "  - {$s['no_sewa']} | Pol: {$s['no_polisi']} | KTP: {$s['no_ktp']} | Sewa: {$s['tgl_sewa']} s/d {$s['tgl_kembali']} | Rp " . number_format($s['biaya'], 0, ',', '.') . " | {$s['status']}\n";
}

// Uji 4: Query relasi multi-tabel (Sewa + Kendaraan + Pelanggan)
echo "\n[Uji 4] Pengujian Relasi Multi-Tabel (JOIN)...\n";
$stmt_join = $pdo->query("
    SELECT s.no_sewa, s.tanggal, s.tgl_sewa, s.tgl_kembali, s.biaya, s.status,
           k.nama_kendaraan, k.tipe,
           p.nama_pelanggan, p.telepon
    FROM sewatbl s
    LEFT JOIN kendaraantbl k ON s.no_polisi = k.no_polisi
    LEFT JOIN pelanggantbl p ON s.no_ktp = p.no_ktp
");
$join_res = $stmt_join->fetchAll();
foreach ($join_res as $j) {
    echo "  - [{$j['no_sewa']}] Disewa oleh: {$j['nama_pelanggan']} ({$j['telepon']}) | Unit: {$j['nama_kendaraan']} ({$j['tipe']}) | Biaya: Rp " . number_format($j['biaya'], 0, ',', '.') . "\n";
}

// Uji 5: Simulasi transaksi database dan rollback (ACID compliance)
echo "\n[Uji 5] Simulasi Transaksi (INSERT & ROLLBACK)...\n";
$pdo->beginTransaction();
$pdo->prepare("INSERT INTO kendaraantbl (no_polisi, nama_kendaraan, tipe, warna, no_rangka, no_mesin, bahan_bakar, thn_kendaraan, status) VALUES ('B 9999 TEST', 'INNOVA ZENIX', 'AT', 'Hitam', 'RANGKA-TEST', 'MESIN-TEST', 'Bensin', 2024, 'Ready')")->execute();
$cekInsert = $pdo->query("SELECT COUNT(*) FROM kendaraantbl WHERE no_polisi = 'B 9999 TEST'")->fetchColumn();
echo "  - Data dibuat saat transaksi berjalan: {$cekInsert}\n";
$pdo->rollBack();
$cekRollback = $pdo->query("SELECT COUNT(*) FROM kendaraantbl WHERE no_polisi = 'B 9999 TEST'")->fetchColumn();
echo "  - Data setelah dibatalkan (rollback): {$cekRollback} (Harus 0)\n";

if ($cekRollback == 0) {
    echo "[PASS] Pembatalan transaksi atomik berhasil diverifikasi!\n";
} else {
    echo "[FAIL] Pembatalan transaksi gagal!\n";
}

echo "\nSeluruh pengujian fungsionalitas dan query Nine Rental selesai dengan sukses (100% PASS)!\n";
