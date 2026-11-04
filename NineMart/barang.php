<?php
// Pengelolaan master data barang
require_once 'config/database.php';
require_once 'config/auth.php';

$page_title = "Data Barang";
$sukses = '';
$error  = '';

// Ambil daftar kelompok untuk pilihan dropdown form
$stmtKelompok = $pdo->query("SELECT kode_kelompok, nama_kelompok FROM tblkelompok ORDER BY kode_kelompok ASC");
$listKelompok = $stmtKelompok->fetchAll();

// Proses tambah data barang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_barang'])) {
    $kode_barang   = trim($_POST['kode_barang'] ?? '');
    $nama_barang   = trim($_POST['nama_barang'] ?? '');
    $kode_kelompok = trim($_POST['kode_kelompok'] ?? '');
    $harga_beli    = (int)($_POST['harga_beli'] ?? 0);
    $harga_jual    = (int)($_POST['harga_jual'] ?? 0);
    $stock         = (int)($_POST['stock'] ?? 0);

    if (empty($kode_barang) || empty($nama_barang) || empty($kode_kelompok)) {
        $error = "Semua kolom data barang wajib diisi!";
    } elseif ($harga_beli < 0 || $harga_jual < 0 || $stock < 0) {
        $error = "Harga dan stok tidak boleh bernilai negatif!";
    } else {
        try {
            // Periksa apakah kode barang sudah terdaftar
            $stmtCek = $pdo->prepare("SELECT id FROM tblbarang WHERE kode_barang = :kd LIMIT 1");
            $stmtCek->execute([':kd' => $kode_barang]);
            if ($stmtCek->fetch()) {
                $error = "Kode barang '{$kode_barang}' sudah ada!";
            } else {
                $stmtInsert = $pdo->prepare("
                    INSERT INTO tblbarang (kode_barang, nama_barang, kode_kelompok, harga_beli, harga_jual, stock)
                    VALUES (:kd, :nm, :klp, :hb, :hj, :stk)
                ");
                $stmtInsert->execute([
                    ':kd'  => $kode_barang,
                    ':nm'  => $nama_barang,
                    ':klp' => $kode_kelompok,
                    ':hb'  => $harga_beli,
                    ':hj'  => $harga_jual,
                    ':stk' => $stock
                ]);
                $sukses = "Barang '{$nama_barang}' berhasil ditambahkan!";
            }
        } catch (PDOException $e) {
            $error = "Kesalahan database: " . $e->getMessage();
        }
    }
}

// Proses pembaruan data barang
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_barang'])) {
    $id            = (int)($_POST['id'] ?? 0);
    $kode_barang   = trim($_POST['kode_barang'] ?? '');
    $nama_barang   = trim($_POST['nama_barang'] ?? '');
    $kode_kelompok = trim($_POST['kode_kelompok'] ?? '');
    $harga_beli    = (int)($_POST['harga_beli'] ?? 0);
    $harga_jual    = (int)($_POST['harga_jual'] ?? 0);
    $stock         = (int)($_POST['stock'] ?? 0);

    if (empty($id) || empty($kode_barang) || empty($nama_barang) || empty($kode_kelompok)) {
        $error = "Data edit barang tidak lengkap!";
    } elseif ($harga_beli < 0 || $harga_jual < 0 || $stock < 0) {
        $error = "Harga dan stok tidak boleh bernilai negatif!";
    } else {
        try {
            $stmtUpdate = $pdo->prepare("
                UPDATE tblbarang 
                SET kode_barang = :kd, nama_barang = :nm, kode_kelompok = :klp, harga_beli = :hb, harga_jual = :hj, stock = :stk
                WHERE id = :id
            ");
            $stmtUpdate->execute([
                ':kd'  => $kode_barang,
                ':nm'  => $nama_barang,
                ':klp' => $kode_kelompok,
                ':hb'  => $harga_beli,
                ':hj'  => $harga_jual,
                ':stk' => $stock,
                ':id'  => $id
            ]);
            $sukses = "Data barang berhasil diperbarui!";
        } catch (PDOException $e) {
            $error = "Gagal memperbarui barang: " . $e->getMessage();
        }
    }
}

// Proses penghapusan data barang
if (isset($_GET['aksi']) && $_GET['aksi'] === 'hapus') {
    $idHapus = (int)($_GET['id'] ?? 0);
    try {
        $stmtDel = $pdo->prepare("DELETE FROM tblbarang WHERE id = :id");
        $stmtDel->execute([':id' => $idHapus]);
        $sukses = "Data barang berhasil dihapus!";
    } catch (PDOException $e) {
        $error = "Gagal menghapus barang: " . $e->getMessage();
    }
}

// Ambil seluruh data barang dari database
$stmt = $pdo->query("SELECT * FROM tblbarang ORDER BY id ASC");
$listBarang = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<?php if (!empty($sukses)): ?>
    <div class="alert-toast alert-success"><?= escape($sukses) ?></div>
<?php elseif (!empty($error)): ?>
    <div class="alert-toast alert-danger"><?= escape($error) ?></div>
<?php endif; ?>

<!-- Tabel daftar barang -->
<div class="table-card">
    <div class="table-card-header">
        <h4 class="table-card-title">DATA BARANG</h4>
        <button type="button" class="btn-icon-plus" onclick="openModal('modalTambahBarang')" title="Tambah Barang">
            <img src="assets/img/icon-plus.png" alt="Tambah">
        </button>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 45px; text-align: center;">No</th>
                <th style="width: 130px;">Kode Barang</th>
                <th>Nama Barang</th>
                <th style="width: 100px; text-align: right; padding-right: 15px;">Harga Beli</th>
                <th style="width: 100px; text-align: right; padding-right: 15px;">Harga Jual</th>
                <th style="width: 70px; text-align: right; padding-right: 15px;">Stock</th>
                <th class="col-aksi">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listBarang)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 15px; color: #666666;">
                        Belum ada data barang.
                    </td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($listBarang as $b): ?>
                <tr>
                    <td class="col-no"><?= $no++ ?></td>
                    <td><?= escape($b['kode_barang']) ?></td>
                    <td><?= escape($b['nama_barang']) ?></td>
                    <td class="col-number"><?= number_format($b['harga_beli'], 0, ',', '') ?></td>
                    <td class="col-number"><?= number_format($b['harga_jual'], 0, ',', '') ?></td>
                    <td class="col-number"><?= escape($b['stock']) ?></td>
                    <td class="col-aksi">
                        <button type="button" class="action-btn" 
                                onclick="editBarang(<?= $b['id'] ?>, '<?= escape($b['kode_barang']) ?>', '<?= escape($b['nama_barang']) ?>', '<?= escape($b['kode_kelompok']) ?>', <?= $b['harga_beli'] ?>, <?= $b['harga_jual'] ?>, <?= $b['stock'] ?>)" 
                                title="Edit Barang">
                            <img src="assets/img/icon-edit.png" alt="Edit">
                        </button>
                        <a href="barang.php?aksi=hapus&id=<?= $b['id'] ?>" 
                           class="action-btn" 
                           onclick="return confirm('Hapus barang <?= escape($b['nama_barang']) ?>?');"
                           title="Hapus Barang">
                            <img src="assets/img/icon-delete.png" alt="Hapus">
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal form tambah barang -->
<div class="modal-overlay" id="modalTambahBarang">
    <div class="modal-box">
        <div class="modal-header">
            <h4 class="modal-title">Tambah Data Barang</h4>
            <button type="button" class="modal-close" onclick="closeModal('modalTambahBarang')">&times;</button>
        </div>
        <form action="barang.php" method="POST">
            <input type="hidden" name="tambah_barang" value="1">
            <div class="modal-body">
                <div class="form-group">
                    <label for="kode_barang">Kode Barang (Contoh: BRG-4):</label>
                    <input type="text" id="kode_barang" name="kode_barang" maxlength="15" required autofocus>
                </div>
                <div class="form-group">
                    <label for="nama_barang">Nama Barang:</label>
                    <input type="text" id="nama_barang" name="nama_barang" maxlength="35" required>
                </div>
                <div class="form-group">
                    <label for="kode_kelompok">Kelompok Barang:</label>
                    <select id="kode_kelompok" name="kode_kelompok" required>
                        <option value="">-- Pilih Kelompok --</option>
                        <?php foreach ($listKelompok as $klp): ?>
                            <option value="<?= escape($klp['kode_kelompok']) ?>">
                                <?= escape($klp['kode_kelompok']) ?> - <?= escape($klp['nama_kelompok']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="harga_beli">Harga Beli (Rp):</label>
                    <input type="number" id="harga_beli" name="harga_beli" min="0" required>
                </div>
                <div class="form-group">
                    <label for="harga_jual">Harga Jual (Rp):</label>
                    <input type="number" id="harga_jual" name="harga_jual" min="0" required>
                </div>
                <div class="form-group">
                    <label for="stock">Stock:</label>
                    <input type="number" id="stock" name="stock" min="0" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modalTambahBarang')">Batal</button>
                <button type="submit" class="btn-submit">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal form edit barang -->
<div class="modal-overlay" id="modalEditBarang">
    <div class="modal-box">
        <div class="modal-header">
            <h4 class="modal-title">Edit Data Barang</h4>
            <button type="button" class="modal-close" onclick="closeModal('modalEditBarang')">&times;</button>
        </div>
        <form action="barang.php" method="POST">
            <input type="hidden" name="edit_barang" value="1">
            <input type="hidden" name="id" id="edit_barang_id">
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_barang_kode">Kode Barang:</label>
                    <input type="text" id="edit_barang_kode" name="kode_barang" maxlength="15" required>
                </div>
                <div class="form-group">
                    <label for="edit_barang_nama">Nama Barang:</label>
                    <input type="text" id="edit_barang_nama" name="nama_barang" maxlength="35" required>
                </div>
                <div class="form-group">
                    <label for="edit_barang_klp">Kelompok Barang:</label>
                    <select id="edit_barang_klp" name="kode_kelompok" required>
                        <?php foreach ($listKelompok as $klp): ?>
                            <option value="<?= escape($klp['kode_kelompok']) ?>">
                                <?= escape($klp['kode_kelompok']) ?> - <?= escape($klp['nama_kelompok']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_barang_hb">Harga Beli (Rp):</label>
                    <input type="number" id="edit_barang_hb" name="harga_beli" min="0" required>
                </div>
                <div class="form-group">
                    <label for="edit_barang_hj">Harga Jual (Rp):</label>
                    <input type="number" id="edit_barang_hj" name="harga_jual" min="0" required>
                </div>
                <div class="form-group">
                    <label for="edit_barang_stk">Stock:</label>
                    <input type="number" id="edit_barang_stk" name="stock" min="0" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modalEditBarang')">Batal</button>
                <button type="submit" class="btn-submit">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
// Mengisi data barang ke modal edit saat tombol pensil ditekan
function editBarang(id, kode, nama, kelompok, hb, hj, stock) {
    document.getElementById('edit_barang_id').value = id;
    document.getElementById('edit_barang_kode').value = kode;
    document.getElementById('edit_barang_nama').value = nama;
    document.getElementById('edit_barang_klp').value = kelompok;
    document.getElementById('edit_barang_hb').value = hb;
    document.getElementById('edit_barang_hj').value = hj;
    document.getElementById('edit_barang_stk').value = stock;
    openModal('modalEditBarang');
}
</script>

<?php
require_once 'includes/footer.php';
?>
