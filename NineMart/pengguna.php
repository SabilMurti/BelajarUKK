<?php
// Pengelolaan master data pengguna
require_once 'config/database.php';
require_once 'config/auth.php';

$page_title = "Data Pengguna";
$sukses = '';
$error  = '';

// Proses penambahan pengguna baru
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_pengguna'])) {
    $username = trim($_POST['user'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "Username dan Password wajib diisi!";
    } elseif (strlen($username) > 15) {
        $error = "Username maksimal 15 karakter!";
    } else {
        try {
            // Cek apakah username sudah dipakai
            $stmtCek = $pdo->prepare("SELECT id FROM tblpengguna WHERE user = :user LIMIT 1");
            $stmtCek->execute([':user' => $username]);
            if ($stmtCek->fetch()) {
                $error = "Username '{$username}' sudah terdaftar dalam sistem!";
            } else {
                $hashPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmtInsert = $pdo->prepare("INSERT INTO tblpengguna (user, password) VALUES (:user, :password)");
                $stmtInsert->execute([
                    ':user'     => $username,
                    ':password' => $hashPassword
                ]);
                $sukses = "Pengguna '{$username}' berhasil ditambahkan!";
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan database: " . $e->getMessage();
        }
    }
}

// Proses penghapusan data pengguna
if (isset($_GET['aksi']) && $_GET['aksi'] === 'hapus') {
    $idHapus = (int)($_GET['id'] ?? 0);

    // Mencegah akun yang sedang login dihapus sendiri
    if ($idHapus === (int)$_SESSION['user_id']) {
        $error = "Anda tidak dapat menghapus akun yang sedang Anda gunakan!";
    } else {
        try {
            $stmtHapus = $pdo->prepare("DELETE FROM tblpengguna WHERE id = :id");
            $stmtHapus->execute([':id' => $idHapus]);
            $sukses = "Data pengguna berhasil dihapus!";
        } catch (PDOException $e) {
            $error = "Gagal menghapus pengguna: " . $e->getMessage();
        }
    }
}

// Ambil seluruh daftar pengguna dari database
$stmt = $pdo->query("SELECT id, user FROM tblpengguna ORDER BY id ASC");
$listPengguna = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<?php if (!empty($sukses)): ?>
    <div class="alert-toast alert-success"><?= escape($sukses) ?></div>
<?php elseif (!empty($error)): ?>
    <div class="alert-toast alert-danger"><?= escape($error) ?></div>
<?php endif; ?>

<!-- Tabel daftar pengguna -->
<div class="table-card">
    <div class="table-card-header">
        <h4 class="table-card-title">DATA PENGGUNA</h4>
        <button type="button" class="btn-icon-plus" onclick="openModal('modalTambahPengguna')" title="Tambah Pengguna">
            <img src="assets/img/icon-plus.png" alt="Tambah">
        </button>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">No</th>
                <th>User</th>
                <th class="col-aksi">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listPengguna)): ?>
                <tr>
                    <td colspan="3" style="text-align: center; padding: 15px; color: #666666;">
                        Belum ada data pengguna.
                    </td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($listPengguna as $p): ?>
                <tr>
                    <td class="col-no"><?= $no++ ?></td>
                    <td><?= escape($p['user']) ?></td>
                    <td class="col-aksi">
                        <a href="pengguna.php?aksi=hapus&id=<?= $p['id'] ?>" 
                           class="action-btn" 
                           onclick="return confirm('Apakah Anda yakin ingin menghapus user <?= escape($p['user']) ?>?');"
                           title="Hapus User">
                            <img src="assets/img/icon-delete.png" alt="Hapus">
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal tambah pengguna -->
<div class="modal-overlay" id="modalTambahPengguna">
    <div class="modal-box">
        <div class="modal-header">
            <h4 class="modal-title">Tambah Pengguna Baru</h4>
            <button type="button" class="modal-close" onclick="closeModal('modalTambahPengguna')">&times;</button>
        </div>
        <form action="pengguna.php" method="POST">
            <input type="hidden" name="tambah_pengguna" value="1">
            <div class="modal-body">
                <div class="form-group">
                    <label for="user">Username (Maks 15 Karakter):</label>
                    <input type="text" id="user" name="user" maxlength="15" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modalTambahPengguna')">Batal</button>
                <button type="submit" class="btn-submit">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>
