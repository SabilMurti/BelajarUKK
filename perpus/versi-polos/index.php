<?php
/**
 * ==============================================================================
 * DASHBOARD UTAMA (VERSI POLOS / VANILLA CSS)
 * ==============================================================================
 * Menampilkan ringkasan statistik perpustakaan:
 * 1. Jumlah judul buku
 * 2. Jumlah anggota terdaftar
 * 3. Total transaksi peminjaman
 * 4. Jumlah buku yang sedang dipinjam
 * Disertai ringkasan peminjaman aktif terbaru untuk pemantauan cepat.
 * ==============================================================================
 */

require_once 'config/database.php';
require_once 'config/auth.php';

$page_title = "Dashboard Utama";

// 1. Ambil Statistik dari Database menggunakan PDO Query
// Total Judul Buku
$stmtBuku = $pdo->query("SELECT COUNT(*) AS total_judul, COALESCE(SUM(stok), 0) AS total_stok FROM buku");
$statBuku = $stmtBuku->fetch();

// Total Anggota
$stmtAnggota = $pdo->query("SELECT COUNT(*) AS total_anggota FROM anggota");
$statAnggota = $stmtAnggota->fetch();

// Total Transaksi Peminjaman (Semua Riwayat)
$stmtTransaksi = $pdo->query("SELECT COUNT(*) AS total_transaksi FROM peminjaman");
$statTransaksi = $stmtTransaksi->fetch();

// Total Buku yang Sedang Dipinjam (Status = 'Dipinjam')
$stmtDipinjam = $pdo->query("SELECT COALESCE(SUM(jumlah_pinjam), 0) AS total_dipinjam FROM peminjaman WHERE status = 'Dipinjam'");
$statDipinjam = $stmtDipinjam->fetch();

// 2. Ambil 5 Transaksi Peminjaman Aktif Terbaru untuk Pemantauan Cepat
$queryAktif = "
    SELECT p.*, a.nama_lengkap AS nama_anggota, a.nomor_anggota, b.judul_buku, b.kode_buku
    FROM peminjaman p
    JOIN anggota a ON p.id_anggota = a.id_anggota
    JOIN buku b ON p.id_buku = b.id_buku
    WHERE p.status = 'Dipinjam'
    ORDER BY p.tanggal_rencana_kembali ASC
    LIMIT 5
";
$stmtAktif = $pdo->query($queryAktif);
$peminjamanAktif = $stmtAktif->fetchAll();

include 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Perpustakaan</h1>
        <p class="page-subtitle">Selamat datang kembali, <strong><?= escape($_SESSION['nama_lengkap']) ?></strong>! Berikut adalah ringkasan operasional perpustakaan saat ini.</p>
    </div>
    <div>
        <a href="peminjaman.php" class="btn btn-primary">+ Catat Peminjaman</a>
    </div>
</div>

<!-- Grid 4 Indikator Statistik Utama -->
<div class="grid grid-cols-4" style="margin-bottom: 2rem;">
    <!-- Stat 1: Total Judul Buku -->
    <div class="stat-card">
        <div class="stat-info">
            <h3>Koleksi Buku</h3>
            <div class="stat-value"><?= number_format($statBuku['total_judul']) ?></div>
            <small style="color: #64748b;"><?= number_format($statBuku['total_stok']) ?> Total Eksemplar</small>
        </div>
        <div class="stat-icon">📚</div>
    </div>

    <!-- Stat 2: Total Anggota -->
    <div class="stat-card stat-green">
        <div class="stat-info">
            <h3>Total Anggota</h3>
            <div class="stat-value"><?= number_format($statAnggota['total_anggota']) ?></div>
            <small style="color: #64748b;">Pengunjung Terdaftar</small>
        </div>
        <div class="stat-icon">👥</div>
    </div>

    <!-- Stat 3: Total Transaksi -->
    <div class="stat-card stat-blue">
        <div class="stat-info">
            <h3>Total Transaksi</h3>
            <div class="stat-value"><?= number_format($statTransaksi['total_transaksi']) ?></div>
            <small style="color: #64748b;">Riwayat Sirkulasi</small>
        </div>
        <div class="stat-icon">📋</div>
    </div>

    <!-- Stat 4: Buku Sedang Dipinjam -->
    <div class="stat-card stat-orange">
        <div class="stat-info">
            <h3>Sedang Dipinjam</h3>
            <div class="stat-value"><?= number_format($statDipinjam['total_dipinjam']) ?></div>
            <small style="color: #64748b;">Buku di Luar Perpustakaan</small>
        </div>
        <div class="stat-icon">⏳</div>
    </div>
</div>

<!-- Pintasan Cepat & Peminjaman Aktif -->
<div class="grid grid-split">
    <!-- Kolom Kiri: Akses Cepat Menu -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Aksi Cepat</h2>
        </div>
        <div class="card-body" style="display: flex; flex-direction: column; gap: 0.75rem;">
            <a href="buku.php" class="btn btn-outline" style="justify-content: flex-start;">
                📖 Kelola Data Katalog Buku
            </a>
            <a href="anggota.php" class="btn btn-outline" style="justify-content: flex-start;">
                👤 Kelola Data Anggota Perpustakaan
            </a>
            <a href="peminjaman.php" class="btn btn-outline" style="justify-content: flex-start;">
                📤 Transaksi Peminjaman Baru
            </a>
            <a href="pengembalian.php" class="btn btn-outline" style="justify-content: flex-start;">
                📥 Proses Pengembalian Buku
            </a>
            <a href="laporan.php" class="btn btn-outline" style="justify-content: flex-start;">
                🖨️ Rekapitulasi & Cetak Laporan
            </a>
        </div>
    </div>

    <!-- Kolom Kanan: Peminjaman Aktif yang Perlu Dipantau -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Peminjaman Aktif Terdekat</h2>
            <a href="pengembalian.php" class="btn btn-sm btn-outline">Lihat Semua</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Peminjam</th>
                            <th>Judul Buku</th>
                            <th>Batas Kembali</th>
                            <th>Status Batas Waktu</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($peminjamanAktif)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 2rem; color: #64748b;">
                                    Saat ini tidak ada transaksi buku yang sedang dipinjam.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($peminjamanAktif as $item): ?>
                                <?php 
                                    // Hitung keterlambatan berdasarkan tanggal rencana kembali
                                    $infoKeterlambatan = hitung_keterlambatan($item['tanggal_rencana_kembali']);
                                ?>
                                <tr>
                                    <td>
                                        <strong><?= escape($item['nama_anggota']) ?></strong><br>
                                        <small style="color: #64748b;"><?= escape($item['nomor_anggota']) ?></small>
                                    </td>
                                    <td>
                                        <?= escape($item['judul_buku']) ?><br>
                                        <small style="color: #64748b;">Jumlah: <?= (int)$item['jumlah_pinjam'] ?> buku</small>
                                    </td>
                                    <td><?= format_tanggal($item['tanggal_rencana_kembali']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $infoKeterlambatan['badge_class'] ?>">
                                            <?= $infoKeterlambatan['status_teks'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="pengembalian.php" class="btn btn-sm btn-success">Kembalikan</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
