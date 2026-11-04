<?php
/**
 * ==============================================================================
 * MODUL DATA PEMASOK - NINEMART
 * ==============================================================================
 * CRUD data master pemasok (PMS-1 s/d PMS-5).
 * ==============================================================================
 */

require_once 'config/database.php';
require_once 'config/auth.php';

$page_title = "Data Pemasok";
$sukses = '';
$error  = '';

// 1. Tambah Pemasok (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_pemasok'])) {
    $kode   = trim($_POST['kode_pemasok'] ?? '');
    $nama   = trim($_POST['nama_pemasok'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if (empty($kode) || empty($nama) || empty($alamat)) {
        $error = "Semua kolom pemasok wajib diisi!";
    } else {
        try {
            $stmtCek = $pdo->prepare("SELECT id FROM tblpemasok WHERE kode_pemasok = :kd LIMIT 1");
            $stmtCek->execute([':kd' => $kode]);
            if ($stmtCek->fetch()) {
                $error = "Kode pemasok '{$kode}' sudah digunakan!";
            } else {
                $stmtInsert = $pdo->prepare("INSERT INTO tblpemasok (kode_pemasok, nama_pemasok, alamat) VALUES (:kd, :nm, :al)");
                $stmtInsert->execute([':kd' => $kode, ':nm' => $nama, ':al' => $alamat]);
                $sukses = "Pemasok '{$nama}' berhasil ditambahkan!";
            }
        } catch (PDOException $e) {
            $error = "Kesalahan database: " . $e->getMessage();
        }
    }
}

// 2. Edit Pemasok (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_pemasok'])) {
    $id     = (int)($_POST['id'] ?? 0);
    $kode   = trim($_POST['kode_pemasok'] ?? '');
    $nama   = trim($_POST['nama_pemasok'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');

    if (empty($id) || empty($kode) || empty($nama) || empty($alamat)) {
        $error = "Data edit pemasok tidak lengkap!";
    } else {
        try {
            $stmtUpdate = $pdo->prepare("UPDATE tblpemasok SET kode_pemasok = :kd, nama_pemasok = :nm, alamat = :al WHERE id = :id");
            $stmtUpdate->execute([':kd' => $kode, ':nm' => $nama, ':al' => $alamat, ':id' => $id]);
            $sukses = "Data pemasok berhasil diperbarui!";
        } catch (PDOException $e) {
            $error = "Gagal memperbarui data pemasok: " . $e->getMessage();
        }
    }
}

// 3. Hapus Pemasok (GET)
if (isset($_GET['aksi']) && $_GET['aksi'] === 'hapus') {
    $idHapus = (int)($_GET['id'] ?? 0);
    try {
        $stmtDel = $pdo->prepare("DELETE FROM tblpemasok WHERE id = :id");
        $stmtDel->execute([':id' => $idHapus]);
        $sukses = "Data pemasok berhasil dihapus!";
    } catch (PDOException $e) {
        $error = "Gagal menghapus pemasok: " . $e->getMessage();
    }
}

// 4. Ambil Daftar Pemasok
$stmt = $pdo->query("SELECT * FROM tblpemasok ORDER BY id ASC");
$listPemasok = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<?php if (!empty($sukses)): ?>
    <div class="alert-toast alert-success"><?= escape($sukses) ?></div>
<?php elseif (!empty($error)): ?>
    <div class="alert-toast alert-danger"><?= escape($error) ?></div>
<?php endif; ?>

<!-- Tabel Data Pemasok Sesuai Screenshot Lembar Uji -->
<div class="table-card">
    <div class="table-card-header">
        <h4 class="table-card-title">DATA PEMASOK</h4>
        <button type="button" class="btn-icon-plus" onclick="openModal('modalTambahPemasok')" title="Tambah Pemasok">
            <img src="assets/img/icon-plus.png" alt="Tambah">
        </button>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">No</th>
                <th style="width: 150px;">Kode Pemasok</th>
                <th style="width: 250px;">Nama Pemasok</th>
                <th>Alamat</th>
                <th class="col-aksi">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listPemasok)): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 15px; color: #666666;">
                        Belum ada data pemasok.
                    </td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($listPemasok as $p): ?>
                <tr>
                    <td class="col-no"><?= $no++ ?></td>
                    <td><?= escape($p['kode_pemasok']) ?></td>
                    <td><?= escape($p['nama_pemasok']) ?></td>
                    <td><?= escape($p['alamat']) ?></td>
                    <td class="col-aksi">
                        <button type="button" class="action-btn" 
                                onclick="editPemasok(<?= $p['id'] ?>, '<?= escape($p['kode_pemasok']) ?>', '<?= escape($p['nama_pemasok']) ?>', '<?= escape($p['alamat']) ?>')" 
                                title="Edit Pemasok">
                            <img src="assets/img/icon-edit.png" alt="Edit">
                        </button>
                        <a href="pemasok.php?aksi=hapus&id=<?= $p['id'] ?>" 
                           class="action-btn" 
                           onclick="return confirm('Hapus pemasok <?= escape($p['nama_pemasok']) ?>?');"
                           title="Hapus Pemasok">
                            <img src="assets/img/icon-delete.png" alt="Hapus">
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah Pemasok -->
<div class="modal-overlay" id="modalTambahPemasok">
    <div class="modal-box">
        <div class="modal-header">
            <h4 class="modal-title">Tambah Pemasok Baru</h4>
            <button type="button" class="modal-close" onclick="closeModal('modalTambahPemasok')">&times;</button>
        </div>
        <form action="pemasok.php" method="POST">
            <input type="hidden" name="tambah_pemasok" value="1">
            <div class="modal-body">
                <div class="form-group">
                    <label for="kode_pemasok">Kode Pemasok (Contoh: PMS-6):</label>
                    <input type="text" id="kode_pemasok" name="kode_pemasok" maxlength="15" required autofocus>
                </div>
                <div class="form-group">
                    <label for="nama_pemasok">Nama Pemasok:</label>
                    <input type="text" id="nama_pemasok" name="nama_pemasok" maxlength="35" required>
                </div>
                <div class="form-group">
                    <label for="alamat">Alamat:</label>
                    <textarea id="alamat" name="alamat" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modalTambahPemasok')">Batal</button>
                <button type="submit" class="btn-submit">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Pemasok -->
<div class="modal-overlay" id="modalEditPemasok">
    <div class="modal-box">
        <div class="modal-header">
            <h4 class="modal-title">Edit Data Pemasok</h4>
            <button type="button" class="modal-close" onclick="closeModal('modalEditPemasok')">&times;</button>
        </div>
        <form action="pemasok.php" method="POST">
            <input type="hidden" name="edit_pemasok" value="1">
            <input type="hidden" name="id" id="edit_pemasok_id">
            <div class="modal-body">
                <div class="form-group">
                    <label for="edit_pemasok_kode">Kode Pemasok:</label>
                    <input type="text" id="edit_pemasok_kode" name="kode_pemasok" maxlength="15" required>
                </div>
                <div class="form-group">
                    <label for="edit_pemasok_nama">Nama Pemasok:</label>
                    <input type="text" id="edit_pemasok_nama" name="nama_pemasok" maxlength="35" required>
                </div>
                <div class="form-group">
                    <label for="edit_pemasok_alamat">Alamat:</label>
                    <textarea id="edit_pemasok_alamat" name="alamat" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modalEditPemasok')">Batal</button>
                <button type="submit" class="btn-submit">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function editPemasok(id, kode, nama, alamat) {
    document.getElementById('edit_pemasok_id').value = id;
    document.getElementById('edit_pemasok_kode').value = kode;
    document.getElementById('edit_pemasok_nama').value = nama;
    document.getElementById('edit_pemasok_alamat').value = alamat;
    openModal('modalEditPemasok');
}
</script>

<?php
require_once 'includes/footer.php';
?>
