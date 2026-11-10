<?php
require_once 'config/database.php';
$page_title = "Tambah Barang";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_brg = strtoupper(trim($_POST['kode_brg'] ?? ''));
    $nama_brg = trim($_POST['nama_brg'] ?? '');
    $merk     = trim($_POST['merk'] ?? '');
    $harga    = trim($_POST['harga'] ?? '0');
    $jumlah   = trim($_POST['jumlah'] ?? '0');

    // Validasi input
    if (empty($kode_brg) || empty($nama_brg) || empty($merk) || $harga === '' || $jumlah === '') {
        $_SESSION['error'] = "Seluruh kolom formulir wajib diisi!";
    } elseif (strlen($kode_brg) > 6) {
        $_SESSION['error'] = "Kode barang maksimal 6 karakter sesuai spesifikasi soal!";
    } elseif (!is_numeric($harga) || $harga < 0 || !is_numeric($jumlah) || $jumlah < 0) {
        $_SESSION['error'] = "Harga dan stok harus berupa angka non-negatif!";
    } else {
        // Cek apakah kode barang sudah dipakai
        $stmtCek = $pdo->prepare("SELECT COUNT(*) FROM barang WHERE kode_brg = :kd");
        $stmtCek->execute([':kd' => $kode_brg]);
        if ($stmtCek->fetchColumn() > 0) {
            $_SESSION['error'] = "Kode barang '{$kode_brg}' sudah terdaftar, gunakan kode lain!";
        } else {
            try {
                $stmtInsert = $pdo->prepare("INSERT INTO barang (kode_brg, nama_brg, merk, harga, jumlah) VALUES (:kd, :nm, :mk, :hrg, :jml)");
                $stmtInsert->execute([
                    ':kd'  => $kode_brg,
                    ':nm'  => $nama_brg,
                    ':mk'  => $merk,
                    ':hrg' => (int)$harga,
                    ':jml' => (int)$jumlah,
                ]);

                $_SESSION['sukses'] = "Barang baru '{$nama_brg}' ({$kode_brg}) berhasil ditambahkan!";
                header('Location: barang.php');
                exit;
            } catch (PDOException $e) {
                $_SESSION['error'] = "Gagal menyimpan data: " . $e->getMessage();
            }
        }
    }
}

// Rekomendasi kode otomatis sederhana: BRG001, BRG002, dst
$stmtNext = $pdo->query("SELECT kode_brg FROM barang ORDER BY kode_brg DESC LIMIT 1");
$lastCode = $stmtNext->fetchColumn();
$nextCode = 'BRG001';
if ($lastCode && preg_match('/^BRG(\d+)$/', $lastCode, $m)) {
    $nextCode = sprintf('BRG%03d', (int)$m[1] + 1);
}

include 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Tambah Barang Baru</h1>
        <p class="page-subtitle">Masukkan rincian master produk sesuai struktur tabel database</p>
    </div>
    <a href="barang.php" class="btn btn-outline">&larr; Kembali ke Daftar Barang</a>
</div>

<div class="card" style="max-width: 680px; margin: 0 auto;">
    <form method="POST" action="barang_tambah.php">
        <div class="form-grid">
            <div class="form-group">
                <label for="kode_brg">Kode Barang <span style="color: var(--danger);">*</span></label>
                <input type="text" name="kode_brg" id="kode_brg" class="form-control" maxlength="6" 
                       value="<?= htmlspecialchars($_POST['kode_brg'] ?? $nextCode); ?>" required>
                <div class="form-hint">Maksimal 6 karakter (contoh: BRG001).</div>
            </div>

            <div class="form-group">
                <label for="merk">Merek / Brand <span style="color: var(--danger);">*</span></label>
                <input type="text" name="merk" id="merk" class="form-control" maxlength="30" 
                       value="<?= htmlspecialchars($_POST['merk'] ?? ''); ?>" placeholder="Contoh: Logitech, Razer" required>
            </div>
        </div>

        <div class="form-group">
            <label for="nama_brg">Nama Barang <span style="color: var(--danger);">*</span></label>
            <input type="text" name="nama_brg" id="nama_brg" class="form-control" maxlength="100" 
                   value="<?= htmlspecialchars($_POST['nama_brg'] ?? ''); ?>" placeholder="Contoh: Keyboard Mechanical RGB" required>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="harga">Harga Satuan (Rp) <span style="color: var(--danger);">*</span></label>
                <input type="number" name="harga" id="harga" class="form-control" min="0" 
                       value="<?= htmlspecialchars($_POST['harga'] ?? ''); ?>" placeholder="Contoh: 450000" required>
            </div>

            <div class="form-group">
                <label for="jumlah">Jumlah Stok Awal <span style="color: var(--danger);">*</span></label>
                <input type="number" name="jumlah" id="jumlah" class="form-control" min="0" max="9999" 
                       value="<?= htmlspecialchars($_POST['jumlah'] ?? ''); ?>" placeholder="Contoh: 25" required>
                <div class="form-hint">Tipe INT(4), maksimal 9999 unit.</div>
            </div>
        </div>

        <div class="form-actions">
            <a href="barang.php" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Barang</button>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
