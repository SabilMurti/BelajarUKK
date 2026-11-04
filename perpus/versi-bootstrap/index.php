<?php
/**
 * ==============================================================================
 * DASHBOARD UTAMA (VERSI BOOTSTRAP 5)
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

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">Dashboard Perpustakaan</h2>
        <p class="text-muted mb-0">Selamat datang kembali, <strong><?= escape($_SESSION['nama_lengkap']) ?></strong>! Berikut adalah ringkasan operasional saat ini.</p>
    </div>
    <div>
        <a href="peminjaman.php" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-circle me-1"></i> Catat Peminjaman
        </a>
    </div>
</div>

<!-- 4 Indikator Statistik Utama -->
<div class="row g-3 mb-4">
    <!-- Stat 1: Total Judul Buku -->
    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 stat-card" style="border-left-color: #0d6efd !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold small mb-1">Koleksi Buku</h6>
                    <h3 class="fw-bold mb-0 text-dark"><?= number_format($statBuku['total_judul']) ?></h3>
                    <small class="text-muted"><?= number_format($statBuku['total_stok']) ?> Total Eksemplar</small>
                </div>
                <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                    <i class="bi bi-journal-album fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 2: Total Anggota -->
    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 stat-card" style="border-left-color: #198754 !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold small mb-1">Total Anggota</h6>
                    <h3 class="fw-bold mb-0 text-dark"><?= number_format($statAnggota['total_anggota']) ?></h3>
                    <small class="text-muted">Pengunjung Terdaftar</small>
                </div>
                <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                    <i class="bi bi-people-fill fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 3: Total Transaksi -->
    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 stat-card" style="border-left-color: #0dcaf0 !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold small mb-1">Total Transaksi</h6>
                    <h3 class="fw-bold mb-0 text-dark"><?= number_format($statTransaksi['total_transaksi']) ?></h3>
                    <small class="text-muted">Riwayat Sirkulasi</small>
                </div>
                <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                    <i class="bi bi-arrow-repeat fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Stat 4: Buku Sedang Dipinjam -->
    <div class="col-sm-6 col-xl-3">
        <div class="card shadow-sm border-0 stat-card" style="border-left-color: #ffc107 !important;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold small mb-1">Sedang Dipinjam</h6>
                    <h3 class="fw-bold mb-0 text-dark"><?= number_format($statDipinjam['total_dipinjam']) ?></h3>
                    <small class="text-muted">Buku di Luar Perpustakaan</small>
                </div>
                <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle">
                    <i class="bi bi-clock-history fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pintasan Cepat & Peminjaman Aktif Terdekat -->
<div class="row g-4">
    <!-- Kolom Kiri: Pintasan Cepat -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 pt-3">
                <h5 class="fw-bold mb-0 text-dark">Aksi Cepat</h5>
            </div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="buku.php" class="btn btn-outline-primary text-start d-flex align-items-center gap-2 py-2">
                    <i class="bi bi-journal-plus fs-5"></i>
                    <div>
                        <div class="fw-semibold">Kelola Katalog Buku</div>
                        <small class="text-muted d-block">Tambah dan perbarui stok koleksi</small>
                    </div>
                </a>

                <a href="anggota.php" class="btn btn-outline-success text-start d-flex align-items-center gap-2 py-2">
                    <i class="bi bi-person-plus fs-5"></i>
                    <div>
                        <div class="fw-semibold">Kelola Anggota</div>
                        <small class="text-muted d-block">Daftarkan pembaca baru</small>
                    </div>
                </a>

                <a href="peminjaman.php" class="btn btn-outline-info text-start d-flex align-items-center gap-2 py-2">
                    <i class="bi bi-arrow-up-right-circle fs-5"></i>
                    <div>
                        <div class="fw-semibold">Catat Peminjaman</div>
                        <small class="text-muted d-block">Proses transaksi pinjam buku</small>
                    </div>
                </a>

                <a href="pengembalian.php" class="btn btn-outline-warning text-start d-flex align-items-center gap-2 py-2">
                    <i class="bi bi-arrow-down-left-circle fs-5"></i>
                    <div>
                        <div class="fw-semibold">Proses Pengembalian</div>
                        <small class="text-muted d-block">Cek keterlambatan & kembalikan stok</small>
                    </div>
                </a>

                <a href="laporan.php" class="btn btn-outline-secondary text-start d-flex align-items-center gap-2 py-2">
                    <i class="bi bi-printer fs-5"></i>
                    <div>
                        <div class="fw-semibold">Rekapitulasi Laporan</div>
                        <small class="text-muted d-block">Filter data dan cetak laporan resmi</small>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Peminjaman Aktif Terdekat -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">Peminjaman Aktif Terdekat</h5>
                <a href="pengembalian.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Peminjam</th>
                                <th>Judul Buku</th>
                                <th>Batas Kembali</th>
                                <th>Batas Waktu</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($peminjamanAktif)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Saat ini tidak ada transaksi buku yang sedang dipinjam.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($peminjamanAktif as $item): ?>
                                    <?php 
                                        $infoKeterlambatan = hitung_keterlambatan($item['tanggal_rencana_kembali']);
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold"><?= escape($item['nama_anggota']) ?></div>
                                            <small class="text-muted"><?= escape($item['nomor_anggota']) ?></small>
                                        </td>
                                        <td>
                                            <div><?= escape($item['judul_buku']) ?></div>
                                            <small class="text-muted">Jumlah: <?= (int)$item['jumlah_pinjam'] ?> buku</small>
                                        </td>
                                        <td><?= format_tanggal($item['tanggal_rencana_kembali']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $infoKeterlambatan['badge_class'] ?>">
                                                <?= $infoKeterlambatan['status_teks'] ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="pengembalian.php" class="btn btn-sm btn-success">
                                                <i class="bi bi-box-arrow-in-left"></i> Kembalikan
                                            </a>
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
</div>

<?php include 'includes/footer.php'; ?>
