<?php
// test_queries.php
// Skrip Pengujian Fungsional, Integritas Relasi, dan Debugging Real-time
// Sistem Penjualan Online - Pra UKK XII RPL

require_once 'config/database.php';

$is_cli = (php_sapi_name() === 'cli');

function log_test($nama_uji, $status, $pesan = "") {
    global $is_cli;
    $simbol = $status ? "[PASS]" : "[FAIL]";
    if ($is_cli) {
        echo "{$simbol} {$nama_uji} : {$pesan}\n";
    } else {
        $color = $status ? "#16a34a" : "#dc2626";
        $bg    = $status ? "#f0fdf4" : "#fef2f2";
        echo "<div style='background:{$bg}; border-left:4px solid {$color}; padding:8px 12px; margin-bottom:8px; font-family:monospace;'>
                <strong style='color:{$color};'>{$simbol} {$nama_uji}</strong> - {$pesan}
              </div>";
    }
}

if (!$is_cli) {
    echo "<!DOCTYPE html><html><head><title>Hasil Pengujian Sistem</title></head><body style='font-family:sans-serif; padding:20px; max-width:800px; margin:0 auto;'>";
    echo "<h2>Laporan Pengujian Otomatis Sistem Penjualan Online</h2>";
    echo "<p>Peserta: Zidan Fathul | Database: sipenjualan_zidan</p><hr style='margin-bottom:20px;'>";
}

$semua_lolos = true;

// 1. Uji Koneksi Database
try {
    $ver = $pdo->query("SELECT VERSION()")->fetchColumn();
    log_test("Koneksi MySQL", true, "Terhubung ke MariaDB/MySQL versi " . $ver);
} catch (Exception $e) {
    log_test("Koneksi MySQL", false, $e->getMessage());
    $semua_lolos = false;
}

// 2. Uji Integritas Tabel Barang
try {
    $jml_barang = $pdo->query("SELECT COUNT(*) FROM barang")->fetchColumn();
    log_test("Integritas Tabel 'barang'", true, "Tabel aktif, memuat {$jml_barang} data produk");
} catch (Exception $e) {
    log_test("Integritas Tabel 'barang'", false, $e->getMessage());
    $semua_lolos = false;
}

// 3. Uji Integritas Tabel Transaksi
try {
    $jml_trx = $pdo->query("SELECT COUNT(*) FROM transaksi")->fetchColumn();
    log_test("Integritas Tabel 'transaksi'", true, "Tabel aktif, memuat {$jml_trx} riwayat transaksi");
} catch (Exception $e) {
    log_test("Integritas Tabel 'transaksi'", false, $e->getMessage());
    $semua_lolos = false;
}

// 4. Uji Penambahan Barang Baru
$test_kd = 'TEST01';
try {
    $pdo->prepare("DELETE FROM barang WHERE kode_brg = :kd")->execute([':kd' => $test_kd]);
    $stmtIns = $pdo->prepare("INSERT INTO barang (kode_brg, nama_brg, merk, harga, jumlah) VALUES (:kd, 'Item Uji Otomatis', 'BrandTest', 50000, 20)");
    $stmtIns->execute([':kd' => $test_kd]);
    log_test("CRUD Barang (Create)", true, "Berhasil menambahkan barang uji {$test_kd} dengan stok 20");
} catch (Exception $e) {
    log_test("CRUD Barang (Create)", false, $e->getMessage());
    $semua_lolos = false;
}

// 5. Uji Transaksi & Pemotongan Stok Real-time
$test_trx = 'TRXTEST1';
try {
    $pdo->beginTransaction();
    $beli = 5;
    
    // Potong stok
    $stmtUp = $pdo->prepare("UPDATE barang SET jumlah = jumlah - :qty WHERE kode_brg = :kd");
    $stmtUp->execute([':qty' => $beli, ':kd' => $test_kd]);
    
    // Simpan transaksi
    $stmtTrx = $pdo->prepare("INSERT INTO transaksi (kode_transaksi, kode_brg, nama_brg, harga, jumlah, total_bayar, tanggal) 
                             VALUES (:trx, :kd, 'Item Uji Otomatis', 50000, :qty, 250000, '2026-11-10')");
    $stmtTrx->execute([':trx' => $test_trx, ':kd' => $test_kd, ':qty' => $beli]);
    $pdo->commit();

    // Verifikasi sisa stok
    $sisa_stok = $pdo->query("SELECT jumlah FROM barang WHERE kode_brg = '{$test_kd}'")->fetchColumn();
    if ((int)$sisa_stok === 15) {
        log_test("CRUD Transaksi & Pemotongan Stok", true, "Stok awal 20 dibeli 5, sisa stok tepat 15 unit");
    } else {
        log_test("CRUD Transaksi & Pemotongan Stok", false, "Kalkulasi stok salah! Sisa stok: " . $sisa_stok);
        $semua_lolos = false;
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    log_test("CRUD Transaksi & Pemotongan Stok", false, $e->getMessage());
    $semua_lolos = false;
}

// 6. Uji Pembatalan Transaksi & Pengembalian Stok
try {
    $pdo->beginTransaction();
    $stmtKembali = $pdo->prepare("UPDATE barang SET jumlah = jumlah + 5 WHERE kode_brg = :kd");
    $stmtKembali->execute([':kd' => $test_kd]);
    $stmtDel = $pdo->prepare("DELETE FROM transaksi WHERE kode_transaksi = :trx");
    $stmtDel->execute([':trx' => $test_trx]);
    $pdo->commit();

    $stok_kembali = $pdo->query("SELECT jumlah FROM barang WHERE kode_brg = '{$test_kd}'")->fetchColumn();
    if ((int)$stok_kembali === 20) {
        log_test("Pembatalan Transaksi & Pengembalian Stok", true, "Transaksi dibatalkan, stok berhasil kembali ke 20 unit");
    } else {
        log_test("Pembatalan Transaksi & Pengembalian Stok", false, "Stok gagal dikembalikan! Sisa: " . $stok_kembali);
        $semua_lolos = false;
    }
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    log_test("Pembatalan Transaksi & Pengembalian Stok", false, $e->getMessage());
    $semua_lolos = false;
}

// Bersihkan data uji
$pdo->prepare("DELETE FROM barang WHERE kode_brg = :kd")->execute([':kd' => $test_kd]);

// Kesimpulan
if ($is_cli) {
    echo "\nStatus Akhir Pengujian: " . ($semua_lolos ? "100% LOLOS PENGUJIAN" : "ADA KESALAHAN") . "\n";
} else {
    echo "<div style='margin-top:20px; font-weight:bold; font-size:16px; color:" . ($semua_lolos ? "#16a34a" : "#dc2626") . "'>
            Hasil Akhir: " . ($semua_lolos ? "100% Seluruh Skenario Pengujian Lolos Uji!" : "Terdapat Modul yang Gagal Uji") . "
          </div></body></html>";
}

