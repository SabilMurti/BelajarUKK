<?php
// Koneksi database
require_once __DIR__ . '/config/database.php';

$action = $_GET['action'] ?? 'list';
$msg = $_GET['msg'] ?? '';
$err = $_GET['err'] ?? '';

// Proses Hapus Data
if ($action === 'hapus' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM pelanggantbl WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: pelanggan.php?msg=" . urlencode("Data pelanggan berhasil dihapus"));
    exit;
}

// Proses Simpan Data (Tambah & Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $no_ktp = trim($_POST['no_ktp'] ?? '');
    $nama_pelanggan = trim($_POST['nama_pelanggan'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');

    // Penanganan upload foto
    $foto_name = $_POST['foto_lama'] ?? '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto_name = 'pelanggan_' . time() . '.' . $ext;
        $dest = __DIR__ . '/uploads/pelanggan/' . $foto_name;
        move_uploaded_file($_FILES['foto']['tmp_name'], $dest);
    }

    if ($no_ktp === '' || $nama_pelanggan === '') {
        $err = "No KTP dan Nama Pelanggan wajib diisi!";
    } else {
        if ($id > 0) {
            // Update data
            $stmt = $pdo->prepare("UPDATE pelanggantbl SET no_ktp=?, nama_pelanggan=?, alamat=?, telepon=?, foto=? WHERE id=?");
            $stmt->execute([$no_ktp, $nama_pelanggan, $alamat, $telepon, $foto_name, $id]);
            header("Location: pelanggan.php?msg=" . urlencode("Data pelanggan berhasil diperbarui"));
            exit;
        } else {
            // Tambah data baru
            $stmt = $pdo->prepare("INSERT INTO pelanggantbl (no_ktp, nama_pelanggan, alamat, telepon, foto) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$no_ktp, $nama_pelanggan, $alamat, $telepon, $foto_name]);
            header("Location: pelanggan.php?msg=" . urlencode("Data pelanggan berhasil disimpan"));
            exit;
        }
    }
}

// Ambil data untuk form edit
$row_edit = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM pelanggantbl WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $row_edit = $stmt->fetch();
}

// Ambil semua daftar pelanggan untuk tabel
$stmt = $pdo->query("SELECT * FROM pelanggantbl ORDER BY id ASC");
$pelanggan_list = $stmt->fetchAll();
$total_record = count($pelanggan_list);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Pelanggan - Nine Rental</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="app-container">
        <!-- Header Banner CRUD -->
        <div class="header-crud">
            <h1>NINE RENTAL</h1>
            <h2>RENTAL MOBIL SEGALA MERK</h2>
            <p>JL.PETERONGANSARI NO 2 SEMARANG</p>
        </div>

        <?php if ($action === 'list'): ?>
            <!-- Subheader Bar Tabel -->
            <div class="subheader-crud">
                DATA PELANGGAN
            </div>

            <div class="crud-content">
                <!-- Navigasi Aksi: Kembali ke Dashboard & Tambah Data -->
                <div class="action-nav-bar">
                    <a href="index.php" class="btn-nav-icon back" title="Kembali ke Dashboard">&le;</a>
                    <a href="pelanggan.php?action=tambah" class="btn-nav-icon add" title="Tambah Data Pelanggan">&plusmn;</a>
                </div>

                <?php if ($msg): ?>
                    <div class="alert-box alert-success"><?= escape($msg) ?></div>
                <?php endif; ?>

                <!-- Tabel Data Pelanggan -->
                <div class="table-responsive">
                    <table class="table-crud">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No KTP</th>
                                <th>Nama Pelanggan</th>
                                <th>Alamat</th>
                                <th>Telepon</th>
                                <th>Foto</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pelanggan_list)): ?>
                                <tr>
                                    <td colspan="7" class="center">Belum ada data pelanggan.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($pelanggan_list as $p): ?>
                                    <tr>
                                        <td class="center"><?= $no++ ?></td>
                                        <td><?= escape($p['no_ktp']) ?></td>
                                        <td><?= escape($p['nama_pelanggan']) ?></td>
                                        <td><?= escape($p['alamat']) ?></td>
                                        <td><?= escape($p['telepon']) ?></td>
                                        <td class="center">
                                            <?php if ($p['foto']): ?>
                                                <img src="uploads/pelanggan/<?= escape($p['foto']) ?>" alt="Foto" style="max-height: 24px; vertical-align: middle;">
                                            <?php endif; ?>
                                        </td>
                                        <td class="center">
                                            <a href="pelanggan.php?action=edit&id=<?= $p['id'] ?>" class="action-link edit" title="Edit">&#9998;</a>
                                            <a href="pelanggan.php?action=hapus&id=<?= $p['id'] ?>" class="action-link delete" title="Hapus" onclick="return confirm('Yakin ingin menghapus data pelanggan ini?');">&#10060;</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Kotak Rekaman Jumlah Data -->
                <div class="record-box">
                    Record : <?= $total_record ?>
                </div>
            </div>

        <?php else: ?>
            <!-- Form Input Tambah / Edit Pelanggan -->
            <div class="crud-content">
                <div class="action-nav-bar">
                    <a href="pelanggan.php" class="btn-nav-icon back" title="Kembali ke Data Pelanggan">&le;</a>
                </div>

                <?php if ($err): ?>
                    <div class="alert-box alert-danger"><?= escape($err) ?></div>
                <?php endif; ?>

                <form method="POST" action="pelanggan.php" enctype="multipart/form-data" class="form-crud">
                    <input type="hidden" name="id" value="<?= $row_edit ? $row_edit['id'] : 0 ?>">
                    <input type="hidden" name="foto_lama" value="<?= $row_edit ? escape($row_edit['foto']) : '' ?>">

                    <!-- Field No KTP -->
                    <div class="form-row">
                        <div class="form-label">No KTP</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <input type="text" name="no_ktp" class="input-text input-sm" value="<?= $row_edit ? escape($row_edit['no_ktp']) : '' ?>" required>
                        </div>
                    </div>

                    <!-- Field Nama Pelanggan -->
                    <div class="form-row">
                        <div class="form-label">Nama Pelanggan</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <input type="text" name="nama_pelanggan" class="input-text input-lg" value="<?= $row_edit ? escape($row_edit['nama_pelanggan']) : '' ?>" required>
                        </div>
                    </div>

                    <!-- Field Alamat -->
                    <div class="form-row">
                        <div class="form-label">Alamat</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <textarea name="alamat" class="input-textarea" required><?= $row_edit ? escape($row_edit['alamat']) : '' ?></textarea>
                        </div>
                    </div>

                    <!-- Field Telepon -->
                    <div class="form-row">
                        <div class="form-label">Telepon</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <input type="text" name="telepon" class="input-text input-sm" value="<?= $row_edit ? escape($row_edit['telepon']) : '' ?>" required>
                        </div>
                    </div>

                    <!-- Field Foto -->
                    <div class="form-row">
                        <div class="form-label">Foto</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <input type="file" name="foto" accept="image/*">
                        </div>
                    </div>

                    <hr class="form-divider">

                    <button type="submit" class="btn-submit">Simpan</button>
                </form>
            </div>
        <?php endif; ?>

        <!-- Footer Putih CRUD -->
        <div class="footer-crud">
            COPYRIGHT @ SMK NEGERI 9 SEMARANG
        </div>
    </div>

</body>
</html>
