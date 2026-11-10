<?php
require_once 'config/database.php';
$page_title = "Edit Barang";

$kode_brg = trim($_GET['kode'] ?? '');
if (empty($kode_brg)) {
    $_SESSION['error'] = "Kode barang tidak ditentukan!";
    header('Location: barang.php');
    exit;
}

// Ambil data barang yang akan diedit
$stmt = $pdo->prepare("SELECT * FROM barang WHERE kode_brg = :kd LIMIT 1");
$stmt->execute([':kd' => $kode_brg]);
$barang = $stmt->fetch();

if (!$barang) {
    $_SESSION['error'] = "Data barang dengan kode '{$kode_brg}' tidak ditemukan!";
    header('Location: barang.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_brg = trim($_POST['nama_brg'] ?? '');
    $merk     = trim($_POST['merk'] ?? '');
    $harga    = trim($_POST['harga'] ?? '');
    $jumlah   = trim($_POST['jumlah'] ?? '');

    if (empty($nama_brg) || empty($merk) || $harga === '' || $jumlah === '') {
        $_SESSION['error'] = "Seluruh kolom formulir wajib diisi!";
    } elseif (!is_numeric($harga) || $harga < 0 || !is_numeric($jumlah) || $jumlah < 0) {
        $_SESSION['error'] = "Harga dan stok harus berupa angka non-negatif!";
    } else {
        try {
            $stmtUpdate = $pdo->prepare("UPDATE barang SET nama_brg = :nm, merk = :mk, harga = :hrg, jumlah = :jml WHERE kode_brg = :kd");
            $stmtUpdate->execute([
                ':nm'  => $nama_brg,
                ':mk'  => $merk,
                ':hrg' => (int)$harga,
                ':jml' => (int)$jumlah,
                ':kd'  => $kode_brg,
            ]);

            $_SESSION['sukses'] = "Data barang '{$nama_brg}' ({$kode_brg}) berhasil diperbarui!";
            header('Location: barang.php');
            exit;
        } catch (PDOException $e) {
            $_SESSION['error'] = "Gagal memperbarui data: " . $e->getMessage();
        }
    }
}

include 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Edit Data Barang</h1>
        <p class="page-subtitle">Perbarui informasi barang untuk kode <strong><?= htmlspecialchars($kode_brg); ?></strong></p>
    </div>
    <a href="barang.php" class="btn btn-outline">&larr; Kembali ke Daftar Barang</a>
</div>

<div class="card" style="max-width: 680px; margin: 0 auto;">
    <form method="POST" action="barang_edit.php?kode=<?= urlencode($kode_brg); ?>">
        <div class="form-grid">
            <div class="form-group">
                <label for="kode_brg">Kode Barang (Kunci Utama)</label>
                <input type="text" id="kode_brg" class="form-control" value="<?= htmlspecialchars($barang['kode_brg']); ?>" readonly>
                <div class="form-hint">Kode barang tidak dapat diubah (Primary Key).</div>
            </div>

            <div class="form-group">
                <label for="merk">Merek / Brand <span style="color: var(--danger);">*</span></label>
                <input type="text" name="merk" id="merk" class="form-control" maxlength="30" 
                       value="<?= htmlspecialchars($_POST['merk'] ?? $barang['merk']); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="nama_brg">Nama Barang <span style="color: var(--danger);">*</span></label>
            <input type="text" name="nama_brg" id="nama_brg" class="form-control" maxlength="100" 
                   value="<?= htmlspecialchars($_POST['nama_brg'] ?? $barang['nama_brg']); ?>" required>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="harga">Harga Satuan (Rp) <span style="color: var(--danger);">*</span></label>
                <input type="number" name="harga" id="harga" class="form-control" min="0" 
                       value="<?= htmlspecialchars($_POST['harga'] ?? $barang['harga']); ?>" required>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah Stok Tersedia <span style="color: var(--danger);">*</span></label>
                <input type="number" name="jumlah" id="jumlah" class="form-control" min="0" max="9999" 
                       value="<?= htmlspecialchars($_POST['jumlah'] ?? $barang['jumlah']); ?>" required>
            </div>
        </div>

        <div class="form-actions">
            <a href="barang.php" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
