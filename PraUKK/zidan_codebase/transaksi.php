<?php
require_once 'config/database.php';
$page_title = "Data Transaksi";

$keyword = trim($_GET['keyword'] ?? '');
$tanggal = trim($_GET['tanggal'] ?? '');

$sql = "SELECT * FROM transaksi WHERE 1=1";
$params = [];

if (!empty($keyword)) {
    $sql .= " AND (kode_transaksi LIKE :kw OR kode_brg LIKE :kw OR nama_brg LIKE :kw)";
    $params[':kw'] = "%{$keyword}%";
}

if (!empty($tanggal)) {
    $sql .= " AND tanggal = :tgl";
    $params[':tgl'] = $tanggal;
}

$sql .= " ORDER BY tanggal DESC, kode_transaksi DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$transaksi_list = $stmt->fetchAll();

// Hitung total nilai transaksi pada daftar yang tampil
$grand_total = 0;
foreach ($transaksi_list as $t) {
    $grand_total += (int)$t['total_bayar'];
}

include 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Manajemen Transaksi Penjualan</h1>
        <p class="page-subtitle">Pencatatan riwayat pembelian, jumlah barang, kalkulasi total bayar, dan tanggal transaksi</p>
    </div>
    <a href="transaksi_tambah.php" class="btn btn-primary">+ Buat Transaksi Baru</a>
</div>

<div class="card">
    <div class="card-header" style="flex-wrap: wrap; gap: 12px;">
        <form method="GET" action="transaksi.php" style="display: flex; gap: 8px; flex-wrap: wrap; flex: 1;">
            <input type="text" name="keyword" class="form-control" style="max-width: 260px;" 
                   placeholder="Cari kode trx / barang..." value="<?= htmlspecialchars($keyword); ?>">
            <input type="date" name="tanggal" class="form-control" style="max-width: 170px;" 
                   value="<?= htmlspecialchars($tanggal); ?>">
            <button type="submit" class="btn btn-outline">Filter</button>
            <?php if (!empty($keyword) || !empty($tanggal)): ?>
                <a href="transaksi.php" class="btn btn-outline">Reset</a>
            <?php endif; ?>
        </form>
        <div style="font-size: 13px; color: var(--text-muted); text-align: right;">
            Data: <strong><?= count($transaksi_list); ?></strong> | Total: <strong style="color: var(--primary);">Rp <?= number_format($grand_total, 0, ',', '.'); ?></strong>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Kode Trx</th>
                    <th>Tanggal</th>
                    <th>Kode Brg</th>
                    <th>Nama Barang</th>
                    <th>Harga Satuan</th>
                    <th style="text-align: center;">Qty</th>
                    <th>Total Bayar</th>
                    <th style="text-align: center; width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transaksi_list)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 24px; color: var(--text-muted);">
                            Belum ada data transaksi yang sesuai filter.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($transaksi_list as $t): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><span class="badge badge-primary"><?= htmlspecialchars($t['kode_transaksi']); ?></span></td>
                            <td><?= date('d/m/Y', strtotime($t['tanggal'])); ?></td>
                            <td><code><?= htmlspecialchars($t['kode_brg']); ?></code></td>
                            <td><strong><?= htmlspecialchars($t['nama_brg']); ?></strong></td>
                            <td>Rp <?= number_format($t['harga'], 0, ',', '.'); ?></td>
                            <td style="text-align: center;">
                                <span class="badge badge-warning"><?= htmlspecialchars($t['jumlah']); ?> pcs</span>
                            </td>
                            <td><strong style="color: var(--dark);">Rp <?= number_format($t['total_bayar'], 0, ',', '.'); ?></strong></td>
                            <td style="text-align: center;">
                                <a href="transaksi_edit.php?kode=<?= urlencode($t['kode_transaksi']); ?>" class="btn btn-outline btn-sm">Edit</a>
                                <a href="transaksi_hapus.php?kode=<?= urlencode($t['kode_transaksi']); ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Apakah Anda yakin ingin membatalkan transaksi <?= htmlspecialchars($t['kode_transaksi']); ?>? Stok barang akan dikembalikan.');">
                                   Batal
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

