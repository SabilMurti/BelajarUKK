<?php
require_once 'config/database.php';
$page_title = "Edit Transaksi";

$kode_transaksi = trim($_GET['kode'] ?? '');
if (empty($kode_transaksi)) {
    $_SESSION['error'] = "Kode transaksi tidak ditentukan!";
    header('Location: transaksi.php');
    exit;
}

// Ambil data transaksi yang akan diedit
$stmt = $pdo->prepare("SELECT * FROM transaksi WHERE kode_transaksi = :trx LIMIT 1");
$stmt->execute([':trx' => $kode_transaksi]);
$transaksi = $stmt->fetch();

if (!$transaksi) {
    $_SESSION['error'] = "Transaksi dengan kode '{$kode_transaksi}' tidak ditemukan!";
    header('Location: transaksi.php');
    exit;
}

// Ambil info stok barang saat ini
$stmtB = $pdo->prepare("SELECT jumlah FROM barang WHERE kode_brg = :kd");
$stmtB->execute([':kd' => $transaksi['kode_brg']]);
$stok_sekarang = (int)$stmtB->fetchColumn();

// Maksimal kuantitas yang bisa diambil = stok_sekarang + jumlah_lama_transaksi
$max_bisa_beli = $stok_sekarang + (int)$transaksi['jumlah'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jumlah_baru = (int)($_POST['jumlah'] ?? 0);
    $tanggal     = trim($_POST['tanggal'] ?? '');

    if ($jumlah_baru <= 0 || empty($tanggal)) {
        $_SESSION['error'] = "Jumlah beli harus lebih dari 0 dan tanggal wajib diisi!";
    } elseif ($jumlah_baru > $max_bisa_beli) {
        $_SESSION['error'] = "Jumlah beli ({$jumlah_baru}) melebihi stok yang tersedia (maksimal {$max_bisa_beli} unit)!";
    } else {
        try {
            $pdo->beginTransaction();

            $selisih = $jumlah_baru - (int)$transaksi['jumlah'];
            $harga_satuan = (int)$transaksi['harga'];
            $total_bayar_baru = $harga_satuan * $jumlah_baru;

            // 1. Sesuaikan stok barang (jika selisih positif, kurangi stok; jika negatif, kembalikan stok)
            if ($selisih !== 0) {
                $stmtUpdateStok = $pdo->prepare("UPDATE barang SET jumlah = jumlah - :selisih WHERE kode_brg = :kd");
                $stmtUpdateStok->execute([
                    ':selisih' => $selisih,
                    ':kd'      => $transaksi['kode_brg'],
                ]);
            }

            // 2. Perbarui data transaksi
            $stmtUpdateTrx = $pdo->prepare("UPDATE transaksi SET jumlah = :jml, total_bayar = :tot, tanggal = :tgl WHERE kode_transaksi = :trx");
            $stmtUpdateTrx->execute([
                ':jml' => $jumlah_baru,
                ':tot' => $total_bayar_baru,
                ':tgl' => $tanggal,
                ':trx' => $kode_transaksi,
            ]);

            $pdo->commit();
            $_SESSION['sukses'] = "Data transaksi '{$kode_transaksi}' berhasil diperbarui! Stok barang telah disesuaikan.";
            header('Location: transaksi.php');
            exit;

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $_SESSION['error'] = "Gagal memperbarui transaksi: " . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Edit Data Transaksi</h1>
        <p class="page-subtitle">Sesuaikan jumlah pembelian atau tanggal untuk transaksi <strong><?= htmlspecialchars($kode_transaksi); ?></strong></p>
    </div>
    <a href="transaksi.php" class="btn btn-outline">&larr; Kembali ke Daftar Transaksi</a>
</div>

<div class="card" style="max-width: 680px; margin: 0 auto;">
    <form method="POST" action="transaksi_edit.php?kode=<?= urlencode($kode_transaksi); ?>">
        <div class="form-grid">
            <div class="form-group">
                <label for="kode_transaksi">Kode Transaksi (Kunci Utama)</label>
                <input type="text" id="kode_transaksi" class="form-control" value="<?= htmlspecialchars($transaksi['kode_transaksi']); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="tanggal">Tanggal Transaksi <span style="color: var(--danger);">*</span></label>
                <input type="date" name="tanggal" id="tanggal" class="form-control" 
                       value="<?= htmlspecialchars($_POST['tanggal'] ?? $transaksi['tanggal']); ?>" required>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="kode_brg">Kode Barang</label>
                <input type="text" id="kode_brg" class="form-control" value="<?= htmlspecialchars($transaksi['kode_brg']); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="nama_brg">Nama Barang</label>
                <input type="text" id="nama_brg" class="form-control" value="<?= htmlspecialchars($transaksi['nama_brg']); ?>" readonly>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="harga">Harga Satuan (Rp)</label>
                <input type="text" id="harga" class="form-control" value="Rp <?= number_format($transaksi['harga'], 0, ',', '.'); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah Beli (Qty) <span style="color: var(--danger);">*</span></label>
                <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" max="<?= $max_bisa_beli; ?>" 
                       value="<?= htmlspecialchars($_POST['jumlah'] ?? $transaksi['jumlah']); ?>" required oninput="hitungTotalEdit()">
                <div class="form-hint">Maksimal dapat diubah hingga <?= $max_bisa_beli; ?> unit.</div>
            </div>
        </div>

        <div class="form-group">
            <label for="total_tampil">Total Bayar Terkalkulasi (Rp)</label>
            <input type="text" id="total_tampil" class="form-control" readonly style="font-weight: 700; color: var(--primary);" 
                   value="Rp <?= number_format($transaksi['total_bayar'], 0, ',', '.'); ?>">
        </div>

        <div class="form-actions">
            <a href="transaksi.php" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>

<script>
const hargaSatuan = <?= (int)$transaksi['harga']; ?>;

function hitungTotalEdit() {
    const qty = parseInt(document.getElementById('jumlah').value) || 0;
    const total = hargaSatuan * qty;
    document.getElementById('total_tampil').value = 'Rp ' + Number(total).toLocaleString('id-ID');
}
</script>

<?php include 'includes/footer.php'; ?>
