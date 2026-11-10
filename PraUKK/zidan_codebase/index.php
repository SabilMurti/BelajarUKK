<?php
require_once 'config/database.php';
$page_title = "Dashboard";

// 1. Hitung total data untuk statistik ringkasan
$total_jenis_brg = $pdo->query("SELECT COUNT(*) FROM barang")->fetchColumn();
$total_stok_brg  = $pdo->query("SELECT COALESCE(SUM(jumlah), 0) FROM barang")->fetchColumn();
$total_transaksi = $pdo->query("SELECT COUNT(*) FROM transaksi")->fetchColumn();
$total_omzet     = $pdo->query("SELECT COALESCE(SUM(total_bayar), 0) FROM transaksi")->fetchColumn();

// 2. Ambil 5 transaksi terbaru
$stmt_trx = $pdo->query("SELECT * FROM transaksi ORDER BY tanggal DESC, kode_transaksi DESC LIMIT 5");
$transaksi_terbaru = $stmt_trx->fetchAll();

// 3. Ambil data barang stok menipis (<= 20)
$stmt_stok = $pdo->query("SELECT * FROM barang ORDER BY jumlah ASC LIMIT 5");
$barang_stok = $stmt_stok->fetchAll();

include 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard Sistem Penjualan Online</h1>
        <p class="page-subtitle">Selamat datang di aplikasi kasir dan pencatatan transaksi penjualan</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="barang_tambah.php" class="btn btn-outline">+ Tambah Barang</a>
        <a href="transaksi_tambah.php" class="btn btn-primary">+ Buat Transaksi Baru</a>
    </div>
</div>

<!-- Grid Kartu Statistik -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Jenis Produk</div>
        <div class="stat-value"><?= number_format($total_jenis_brg, 0, ',', '.'); ?></div>
        <div class="stat-desc">Item barang terdaftar</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Stok Fisik</div>
        <div class="stat-value"><?= number_format($total_stok_brg, 0, ',', '.'); ?></div>
        <div class="stat-desc">Unit siap dijual</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Transaksi</div>
        <div class="stat-value"><?= number_format($total_transaksi, 0, ',', '.'); ?></div>
        <div class="stat-desc">Transaksi berhasil</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Pendapatan</div>
        <div class="stat-value" style="color: var(--primary);">Rp <?= number_format($total_omzet, 0, ',', '.'); ?></div>
        <div class="stat-desc">Akumulasi penjualan</div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 3fr 2fr; gap: 24px;">
    <!-- Panel 1: Transaksi Terbaru -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h2 class="card-title">Transaksi Penjualan Terbaru</h2>
            <a href="transaksi.php" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Kode Trx</th>
                        <th>Barang</th>
                        <th>Qty</th>
                        <th>Total Bayar</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transaksi_terbaru)): ?>
                        <tr><td colspan="5" style="text-align: center; color: var(--text-muted);">Belum ada riwayat transaksi.</td></tr>
                    <?php else: ?>
                        <?php foreach ($transaksi_terbaru as $trx): ?>
                            <tr>
                                <td><span class="badge badge-primary"><?= htmlspecialchars($trx['kode_transaksi']); ?></span></td>
                                <td>
                                    <strong><?= htmlspecialchars($trx['nama_brg']); ?></strong><br>
                                    <small style="color: var(--text-muted);"><?= htmlspecialchars($trx['kode_brg']); ?></small>
                                </td>
                                <td><?= htmlspecialchars($trx['jumlah']); ?> unit</td>
                                <td><strong>Rp <?= number_format($trx['total_bayar'], 0, ',', '.'); ?></strong></td>
                                <td><?= date('d/m/Y', strtotime($trx['tanggal'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Panel 2: Monitoring Ketersediaan Stok -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h2 class="card-title">Monitoring Stok Barang</h2>
            <a href="barang.php" class="btn btn-outline btn-sm">Kelola</a>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th>Sisa Stok</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($barang_stok)): ?>
                        <tr><td colspan="3" style="text-align: center; color: var(--text-muted);">Belum ada data barang.</td></tr>
                    <?php else: ?>
                        <?php foreach ($barang_stok as $brg): ?>
                            <tr>
                                <td><code><?= htmlspecialchars($brg['kode_brg']); ?></code></td>
                                <td><?= htmlspecialchars($brg['nama_brg']); ?></td>
                                <td>
                                    <?php if ($brg['jumlah'] <= 15): ?>
                                        <span class="badge badge-danger"><?= $brg['jumlah']; ?> unit (Menipis)</span>
                                    <?php else: ?>
                                        <span class="badge badge-success"><?= $brg['jumlah']; ?> unit</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

