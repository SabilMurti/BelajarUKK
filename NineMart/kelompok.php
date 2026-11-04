<?php
// Pengelolaan master data kelompok barang
require_once 'config/database.php';
require_once 'config/auth.php';

$page_title = "Data Kelompok";
$sukses = '';
$error  = '';

// Proses tambah kelompok baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_kelompok'])) {
    $kode = trim($_POST['kode_kelompok'] ?? '');
    $nama = trim($_POST['nama_kelompok'] ?? '');

    if (empty($kode) || empty($nama)) {
        $error = "Semua kolom data kelompok wajib diisi!";
    } else {
        try {
            // Periksa apakah kode kelompok sudah ada
            $stmtCek = $pdo->prepare("SELECT id FROM tblkelompok WHERE kode_kelompok = :kd LIMIT 1");
            $stmtCek->execute([':kd' => $kode]);
            if ($stmtCek->fetch()) {
                $error = "Kode kelompok '{$kode}' sudah ada dalam database!";
            } else {
                $stmtInsert = $pdo->prepare("INSERT INTO tblkelompok (kode_kelompok, nama_kelompok) VALUES (:kd, :nm)");
                $stmtInsert->execute([':kd' => $kode, ':nm' => $nama]);
                $sukses = "Data kelompok '{$nama}' berhasil ditambahkan!";
            }
        } catch (PDOException $e) {
            $error = "Kesalahan database: " . $e->getMessage();
        }
    }
}

// Proses pembaruan data kelompok
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_kelompok'])) {
    $id   = (int)($_POST['id'] ?? 0);
    $kode = trim($_POST['kode_kelompok'] ?? '');
    $nama = trim($_POST['nama_kelompok'] ?? '');

    if (empty($id) || empty($kode) || empty($nama)) {
        $error = "Data edit kelompok tidak lengkap!";
    } else {
        try {
            $stmtUpdate = $pdo->prepare("UPDATE tblkelompok SET kode_kelompok = :kd, nama_kelompok = :nm WHERE id = :id");
            $stmtUpdate->execute([':kd' => $kode, ':nm' => $nama, ':id' => $id]);
            $sukses = "Data kelompok berhasil diperbarui!";
        } catch (PDOException $e) {
            $error = "Gagal memperbarui kelompok: " . $e->getMessage();
        }
    }
}

// Proses hapus data kelompok
if (isset($_GET['aksi']) && $_GET['aksi'] === 'hapus') {
    $idHapus = (int)($_GET['id'] ?? 0);
    try {
        // Ambil data kelompok yang akan dihapus
        $stmtCek = $pdo->prepare("SELECT kode_kelompok FROM tblkelompok WHERE id = :id");
        $stmtCek->execute([':id' => $idHapus]);
        $klp = $stmtCek->fetch();

        if ($klp) {
            // Cek apakah kelompok masih digunakan pada tabel barang
            $stmtBarang = $pdo->prepare("SELECT COUNT(*) FROM tblbarang WHERE kode_kelompok = :kd");
            $stmtBarang->execute([':kd' => $klp['kode_kelompok']]);
            $usedCount = $stmtBarang->fetchColumn();

            if ($usedCount > 0) {
                $error = "Kelompok '{$klp['kode_kelompok']}' tidak dapat dihapus karena masih digunakan oleh {$usedCount} barang!";
            } else {
                $stmtDel = $pdo->prepare("DELETE FROM tblkelompok WHERE id = :id");
                $stmtDel->execute([':id' => $idHapus]);
                $sukses = "Data kelompok berhasil dihapus!";
            }
        }
    } catch (PDOException $e) {
        $error = "Gagal menghapus data: " . $e->getMessage();
    }
}

// Ambil daftar kelompok dari database
$stmt = $pdo->query("SELECT * FROM tblkelompok ORDER BY id ASC");
$listKelompok = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<?php if (!empty($sukses)): ?>
    <div class="alert-toast alert-success"><?= escape($sukses) ?></div>
<?php elseif (!empty($error)): ?>
    <div class="alert-toast alert-danger"><?= escape($error) ?></div>
<?php endif; ?>

<!-- Tabel daftar kelompok barang -->
<div class="table-card">
    <div class="table-card-header">
        <h4 class="table-card-title">DATA KELOMPOK</h4>
        <button type="button" class="btn-icon-plus" onclick="openModal('modalTambahKelompok')" title="Tambah Kelompok">
            <img src="assets/img/icon-plus.png" alt="Tambah">
        </button>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">No</th>
                <th style="width: 180px;">Kode Kelompok</th>
                <th>Nama Kelompok</th>
                <th class="col-aksi">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listKelompok)): ?>
                <tr>
                    <td colspan="4" style="text-align: center; padding: 15px; color: #666666;">
                        Belum ada data kelompok.
                    </td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($listKelompok as $k): ?>
                <tr>
                    <td class="col-no"><?= $no++ ?></td>
                    <td><?= escape($k['kode_kelompok']) ?></td>
                    <td><?= escape($k['nama_kelompok']) ?></td>
                    <td class="col-aksi">
                        <button type="button" class="action-btn" 
                                onclick="editKelompok(<?= $k['id'] ?>, '<?= escape($k['kode_kelompok']) ?>', '<?= escape($k['nama_kelompok']) ?>')" 
                                title="Edit Kelompok">
                            <img src="assets/img/icon-edit.png" alt="Edit">
                        </button>
                        <a href="kelompok.php?aksi=hapus&id=<?= $k['id'] ?>" 
                           class="action-btn" 
                           onclick="return confirm('Hapus kelompok <?= escape($k['nama_kelompok']) ?>?');"
                           title="Hapus Kelompok">
                            <img src="assets/img/icon-delete.png" alt="Hapus">
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal form tambah kelompok -->
<div class="modal-overlay" id="modalTambahKelompok">
    <div class="modal-box">
        <div class="modal-header">
            <h4 class="modal-title">Tambah Kelompok Barang</h4>
            <button type="button" class="modal-close" onclick="closeModal('modalTambahKelompok')">&times;</button>
        </div>
        <form action="kelompok.php" method="POST">
            <input type="hidden" name="tambah_kelompok" value="1">
            <div class="modal-body">
                <div class="form-group">
                    <label for="kode_kelompok">Kode Kelompok (Contoh: KLP-4):</label>
                    <input type="text" id="kode_kelompok" name="kode_kelompok" maxlength="15" required autofocus>
                </div>
                <div class="form-group">
                    <label for="nama_kelompok">Nama Kelompok:</label>
                    <input type="text" id="nama_kelompok" name="nama_kelompok" maxlength="35" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modalTambahKelompok')">Batal</button>
                <button type="submit" class="btn-submit">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal form edit kelompok -->
<div class="modal-overlay" id="modalEditKelompok">
    <div class="modal-box">
        <div class="modal-header">
            <h4 class="modal-title">Edit Kelompok Barang</h4>
            <button type="button" class="modal-close" onclick="closeModal('modalEditKelompok')">&times;</button>
        </div>
        <form action="kelompok.php" method="POST">
            <input type="hidden" name="edit_kelompok" value="1">
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_kode">Kode Kelompok:</label>
                    <input type="text" id="edit_kode" name="kode_kelompok" maxlength="15" required>
                </div>
                <div class="form-group">
                    <label for="edit_nama">Nama Kelompok:</label>
                    <input type="text" id="edit_nama" name="nama_kelompok" maxlength="35" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modalEditKelompok')">Batal</button>
                <button type="submit" class="btn-submit">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
// Mengisi form edit saat tombol pensil ditekan
function editKelompok(id, kode, nama) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_kode').value = kode;
    document.getElementById('edit_nama').value = nama;
    openModal('modalEditKelompok');
}
</script>

<?php
require_once 'includes/footer.php';
?>
