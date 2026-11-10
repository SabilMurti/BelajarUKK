<?php
require_once 'config/database.php';
$page_title = "Tambah Transaksi";

// Ambil daftar barang yang stoknya masih tersedia (> 0)
$barang_opt = $pdo->query("SELECT kode_brg, nama_brg, merk, harga, jumlah FROM barang ORDER BY nama_brg ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode_transaksi = strtoupper(trim($_POST['kode_transaksi'] ?? ''));
    $kode_brg       = trim($_POST['kode_brg'] ?? '');
    $jumlah         = (int)($_POST['jumlah'] ?? 0);
    $tanggal        = trim($_POST['tanggal'] ?? date('Y-m-d'));

    if (empty($kode_transaksi) || empty($kode_brg) || empty($tanggal) || $jumlah <= 0) {
        $_SESSION['error'] = "Semua kolom transaksi wajib diisi dan jumlah beli harus lebih dari 0!";
    } elseif (strlen($kode_transaksi) > 10) {
        $_SESSION['error'] = "Kode transaksi maksimal 10 karakter!";
    } else {
        // Cek duplikasi kode transaksi
        $stmtCek = $pdo->prepare("SELECT COUNT(*) FROM transaksi WHERE kode_transaksi = :trx");
        $stmtCek->execute([':trx' => $kode_transaksi]);
        if ($stmtCek->fetchColumn() > 0) {
            $_SESSION['error'] = "Kode transaksi '{$kode_transaksi}' sudah pernah digunakan!";
        } else {
            // Ambil data barang terkini dan kunci baris (SELECT FOR UPDATE) dalam transaksi
            try {
                $pdo->beginTransaction();

                $stmtB = $pdo->prepare("SELECT nama_brg, harga, jumlah FROM barang WHERE kode_brg = :kd FOR UPDATE");
                $stmtB->execute([':kd' => $kode_brg]);
                $brg = $stmtB->fetch();

                if (!$brg) {
                    throw new Exception("Barang yang dipilih tidak valid atau sudah tidak ada!");
                }

                if ($brg['jumlah'] < $jumlah) {
                    throw new Exception("Stok tidak mencukupi! Sisa stok '{$brg['nama_brg']}' hanya {$brg['jumlah']} unit, namun Anda meminta {$jumlah} unit.");
                }

                $harga_satuan = (int)$brg['harga'];
                $total_bayar  = $harga_satuan * $jumlah;
                $nama_brg     = $brg['nama_brg'];

                // 1. Kurangi stok barang
                $stmtKurang = $pdo->prepare("UPDATE barang SET jumlah = jumlah - :qty WHERE kode_brg = :kd");
                $stmtKurang->execute([':qty' => $jumlah, ':kd' => $kode_brg]);

                // 2. Simpan transaksi
                $stmtIns = $pdo->prepare("INSERT INTO transaksi (kode_transaksi, kode_brg, nama_brg, harga, jumlah, total_bayar, tanggal) 
                                          VALUES (:trx, :kd, :nm, :hrg, :jml, :tot, :tgl)");
                $stmtIns->execute([
                    ':trx' => $kode_transaksi,
                    ':kd'  => $kode_brg,
                    ':nm'  => $nama_brg,
                    ':hrg' => $harga_satuan,
                    ':jml' => $jumlah,
                    ':tot' => $total_bayar,
                    ':tgl' => $tanggal,
                ]);

                $pdo->commit();
                $_SESSION['sukses'] = "Transaksi '{$kode_transaksi}' berhasil dicatat! Stok barang otomatis terpotong.";
                header('Location: transaksi.php');
                exit;

            } catch (Exception $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $_SESSION['error'] = $e->getMessage();
            }
        }
    }
}

// Rekomendasi kode otomatis sederhana: TRX0001, TRX0002, dst
$stmtNext = $pdo->query("SELECT kode_transaksi FROM transaksi ORDER BY kode_transaksi DESC LIMIT 1");
$lastTrx = $stmtNext->fetchColumn();
$nextTrx = 'TRX0001';
if ($lastTrx && preg_match('/^TRX(\d+)$/', $lastTrx, $m)) {
    $nextTrx = sprintf('TRX%04d', (int)$m[1] + 1);
}

include 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Tambah Transaksi Penjualan</h1>
        <p class="page-subtitle">Pilih produk barang yang dibeli, tentukan jumlah kuantitas, dan sistem akan mengalkulasi total bayar</p>
    </div>
    <a href="transaksi.php" class="btn btn-outline">&larr; Kembali ke Daftar Transaksi</a>
</div>

<div class="card" style="max-width: 680px; margin: 0 auto;">
    <form method="POST" action="transaksi_tambah.php">
        <div class="form-grid">
            <div class="form-group">
                <label for="kode_transaksi">Kode Transaksi <span style="color: var(--danger);">*</span></label>
                <input type="text" name="kode_transaksi" id="kode_transaksi" class="form-control" maxlength="10" 
                       value="<?= htmlspecialchars($_POST['kode_transaksi'] ?? $nextTrx); ?>" required>
                <div class="form-hint">Maksimal 10 karakter (contoh: TRX0004).</div>
            </div>

            <div class="form-group">
                <label for="tanggal">Tanggal Transaksi <span style="color: var(--danger);">*</span></label>
                <input type="date" name="tanggal" id="tanggal" class="form-control" 
                       value="<?= htmlspecialchars($_POST['tanggal'] ?? date('Y-m-d')); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="kode_brg">Pilih Barang Dagangan <span style="color: var(--danger);">*</span></label>
            <select name="kode_brg" id="kode_brg" class="form-control" required onchange="pilihBarang()">
                <option value="">-- Pilih Barang --</option>
                <?php foreach ($barang_opt as $b): ?>
                    <option value="<?= htmlspecialchars($b['kode_brg']); ?>" 
                            data-nama="<?= htmlspecialchars($b['nama_brg']); ?>"
                            data-harga="<?= $b['harga']; ?>"
                            data-stok="<?= $b['jumlah']; ?>"
                            <?= (isset($_POST['kode_brg']) && $_POST['kode_brg'] === $b['kode_brg']) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($b['kode_brg']); ?> - <?= htmlspecialchars($b['nama_brg']); ?> (Sisa Stok: <?= $b['jumlah']; ?> unit)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="harga_tampil">Harga Satuan (Rp)</label>
                <input type="text" id="harga_tampil" class="form-control" readonly placeholder="Otomatis terisi">
            </div>

            <div class="form-group">
                <label for="stok_tampil">Stok Tersedia Saat Ini</label>
                <input type="text" id="stok_tampil" class="form-control" readonly placeholder="Otomatis terisi">
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label for="jumlah">Jumlah Beli (Qty) <span style="color: var(--danger);">*</span></label>
                <input type="number" name="jumlah" id="jumlah" class="form-control" min="1" 
                       value="<?= htmlspecialchars($_POST['jumlah'] ?? '1'); ?>" required oninput="hitungTotal()">
                <div class="form-hint">Jumlah tidak boleh melebihi stok yang tersedia.</div>
            </div>

            <div class="form-group">
                <label for="total_tampil">Total Bayar (Rp)</label>
                <input type="text" id="total_tampil" class="form-control" readonly style="font-weight: 700; color: var(--primary);" placeholder="Rp 0">
            </div>
        </div>

        <div class="form-actions">
            <a href="transaksi.php" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan & Proses Transaksi</button>
        </div>
    </form>
</div>

<script>
let currentHarga = 0;
let currentStok = 0;

function formatRupiah(angka) {
    return 'Rp ' + Number(angka).toLocaleString('id-ID');
}

function pilihBarang() {
    const sel = document.getElementById('kode_brg');
    const opt = sel.options[sel.selectedIndex];

    if (opt && opt.value) {
        currentHarga = parseInt(opt.getAttribute('data-harga')) || 0;
        currentStok  = parseInt(opt.getAttribute('data-stok')) || 0;

        document.getElementById('harga_tampil').value = formatRupiah(currentHarga);
        document.getElementById('stok_tampil').value  = currentStok + ' unit';
        document.getElementById('jumlah').max = currentStok;
    } else {
        currentHarga = 0;
        currentStok = 0;
        document.getElementById('harga_tampil').value = '';
        document.getElementById('stok_tampil').value = '';
    }
    hitungTotal();
}

function hitungTotal() {
    const qtyInput = document.getElementById('jumlah');
    let qty = parseInt(qtyInput.value) || 0;

    if (qty < 0) {
        qty = 0;
        qtyInput.value = 0;
    }

    const total = currentHarga * qty;
    document.getElementById('total_tampil').value = formatRupiah(total);
}

// Inisialisasi awal saat form termuat
window.addEventListener('DOMContentLoaded', pilihBarang);
</script>

<?php include 'includes/footer.php'; ?>
