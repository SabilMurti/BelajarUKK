<?php
require_once 'config/database.php';
$page_title = "Data Barang";

$keyword = trim($_GET['keyword'] ?? '');

if (!empty($keyword)) {
    $stmt = $pdo->prepare("SELECT * FROM barang WHERE kode_brg LIKE :kw OR nama_brg LIKE :kw OR merk LIKE :kw ORDER BY kode_brg ASC");
    $stmt->execute([':kw' => "%{$keyword}%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM barang ORDER BY kode_brg ASC");
}

$barang_list = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Manajemen Data Barang</h1>
        <p class="page-subtitle">Daftar master produk, merek, harga satuan, dan ketersediaan stok fisik</p>
    </div>
    <a href="barang_tambah.php" class="btn btn-primary">+ Tambah Barang Baru</a>
</div>

<div class="card">
    <div class="card-header" style="flex-wrap: wrap; gap: 12px;">
        <form method="GET" action="barang.php" style="display: flex; gap: 8px; flex: 1; max-width: 400px;">
            <input type="text" name="keyword" class="form-control" placeholder="Cari kode, nama, atau merek..." value="<?= htmlspecialchars($keyword); ?>">
            <button type="submit" class="btn btn-outline">Cari</button>
            <?php if (!empty($keyword)): ?>
                <a href="barang.php" class="btn btn-outline">Reset</a>
            <?php endif; ?>
        </form>
        <div style="font-size: 13px; color: var(--text-muted);">
            Total: <strong><?= count($barang_list); ?></strong> barang
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th style="width: 60px;">No</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Merek</th>
                    <th>Harga Satuan</th>
                    <th>Stok Tersedia</th>
                    <th style="text-align: center; width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($barang_list)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 24px; color: var(--text-muted);">
                            Tidak ada data barang yang ditemukan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($barang_list as $b): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><span class="badge badge-primary"><?= htmlspecialchars($b['kode_brg']); ?></span></td>
                            <td><strong><?= htmlspecialchars($b['nama_brg']); ?></strong></td>
                            <td><?= htmlspecialchars($b['merk']); ?></td>
                            <td>Rp <?= number_format($b['harga'], 0, ',', '.'); ?></td>
                            <td>
                                <?php if ($b['jumlah'] <= 10): ?>
                                    <span class="badge badge-danger"><?= $b['jumlah']; ?> unit</span>
                                <?php else: ?>
                                    <span class="badge badge-success"><?= $b['jumlah']; ?> unit</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center;">
                                <a href="barang_edit.php?kode=<?= urlencode($b['kode_brg']); ?>" class="btn btn-outline btn-sm">Edit</a>
                                <a href="barang_hapus.php?kode=<?= urlencode($b['kode_brg']); ?>" 
                                   class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus barang <?= htmlspecialchars($b['nama_brg']); ?> (<?= $b['kode_brg']; ?>)?');">
                                   Hapus
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

