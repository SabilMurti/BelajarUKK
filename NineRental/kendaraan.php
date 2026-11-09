<?php
// Koneksi database
require_once __DIR__ . '/config/database.php';

$action = $_GET['action'] ?? 'list';
$msg = $_GET['msg'] ?? '';
$err = $_GET['err'] ?? '';

// Proses Hapus Data
if ($action === 'hapus' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM kendaraantbl WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: kendaraan.php?msg=" . urlencode("Data kendaraan berhasil dihapus"));
    exit;
}

// Proses Simpan Data (Tambah & Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $no_polisi = trim($_POST['no_polisi'] ?? '');
    $nama_kendaraan = trim($_POST['nama_kendaraan'] ?? '');
    $tipe = trim($_POST['tipe'] ?? 'AT');
    $warna = trim($_POST['warna'] ?? '');
    $no_rangka = trim($_POST['no_rangka'] ?? '');
    $no_mesin = trim($_POST['no_mesin'] ?? '');
    $bahan_bakar = trim($_POST['bahan_bakar'] ?? 'Bensin');
    $thn_kendaraan = (int)($_POST['thn_kendaraan'] ?? date('Y'));
    $status = trim($_POST['status'] ?? 'Ready');

    // Penanganan upload foto
    $foto_name = $_POST['foto_lama'] ?? '';
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto_name = 'kendaraan_' . time() . '.' . $ext;
        $dest = __DIR__ . '/uploads/kendaraan/' . $foto_name;
        move_uploaded_file($_FILES['foto']['tmp_name'], $dest);
    }

    if ($no_polisi === '' || $nama_kendaraan === '') {
        $err = "No Polisi dan Nama Kendaraan wajib diisi!";
    } else {
        if ($id > 0) {
            // Update data
            $stmt = $pdo->prepare("UPDATE kendaraantbl SET no_polisi=?, nama_kendaraan=?, tipe=?, warna=?, no_rangka=?, no_mesin=?, bahan_bakar=?, thn_kendaraan=?, foto=?, status=? WHERE id=?");
            $stmt->execute([$no_polisi, $nama_kendaraan, $tipe, $warna, $no_rangka, $no_mesin, $bahan_bakar, $thn_kendaraan, $foto_name, $status, $id]);
            header("Location: kendaraan.php?msg=" . urlencode("Data kendaraan berhasil diperbarui"));
            exit;
        } else {
            // Tambah data baru
            $stmt = $pdo->prepare("INSERT INTO kendaraantbl (no_polisi, nama_kendaraan, tipe, warna, no_rangka, no_mesin, bahan_bakar, thn_kendaraan, foto, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$no_polisi, $nama_kendaraan, $tipe, $warna, $no_rangka, $no_mesin, $bahan_bakar, $thn_kendaraan, $foto_name, $status]);
            header("Location: kendaraan.php?msg=" . urlencode("Data kendaraan berhasil disimpan"));
            exit;
        }
    }
}

// Ambil data untuk form edit
$row_edit = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM kendaraantbl WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $row_edit = $stmt->fetch();
}

// Ambil semua daftar kendaraan untuk tabel
$stmt = $pdo->query("SELECT * FROM kendaraantbl ORDER BY id ASC");
$kendaraan_list = $stmt->fetchAll();
$total_record = count($kendaraan_list);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Kendaraan - Nine Rental</title>
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
                DATA KENDARAAN
            </div>

            <div class="crud-content">
                <!-- Navigasi Aksi: Kembali ke Dashboard & Tambah Data -->
                <div class="action-nav-bar">
                    <a href="index.php" class="btn-nav-icon back" title="Kembali ke Dashboard">&le;</a>
                    <a href="kendaraan.php?action=tambah" class="btn-nav-icon add" title="Tambah Data Kendaraan">&plusmn;</a>
                </div>

                <?php if ($msg): ?>
                    <div class="alert-box alert-success"><?= escape($msg) ?></div>
                <?php endif; ?>

                <!-- Tabel Data Kendaraan -->
                <div class="table-responsive">
                    <table class="table-crud">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Polisi</th>
                                <th>Nama Kendaraan</th>
                                <th>Tipe</th>
                                <th>Warna</th>
                                <th>No Mesin</th>
                                <th>No Rangka</th>
                                <th>Bahan Bakar</th>
                                <th>Tahun</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($kendaraan_list)): ?>
                                <tr>
                                    <td colspan="11" class="center">Belum ada data kendaraan.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($kendaraan_list as $k): ?>
                                    <tr>
                                        <td class="center"><?= $no++ ?></td>
                                        <td><?= escape($k['no_polisi']) ?></td>
                                        <td><?= escape($k['nama_kendaraan']) ?></td>
                                        <td class="center"><?= escape($k['tipe']) ?></td>
                                        <td><?= escape($k['warna']) ?></td>
                                        <td><?= escape($k['no_mesin']) ?></td>
                                        <td><?= escape($k['no_rangka']) ?></td>
                                        <td><?= escape($k['bahan_bakar']) ?></td>
                                        <td class="center"><?= escape($k['thn_kendaraan']) ?></td>
                                        <td class="center"><?= escape($k['status']) ?></td>
                                        <td class="center">
                                            <a href="kendaraan.php?action=edit&id=<?= $k['id'] ?>" class="action-link edit" title="Edit">&#9998;</a>
                                            <a href="kendaraan.php?action=hapus&id=<?= $k['id'] ?>" class="action-link delete" title="Hapus" onclick="return confirm('Yakin ingin menghapus data kendaraan ini?');">&#10060;</a>
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
            <!-- Form Input Tambah / Edit Kendaraan -->
            <div class="crud-content">
                <div class="action-nav-bar">
                    <a href="kendaraan.php" class="btn-nav-icon back" title="Kembali ke Data Kendaraan">&le;</a>
                </div>

                <?php if ($err): ?>
                    <div class="alert-box alert-danger"><?= escape($err) ?></div>
                <?php endif; ?>

                <form method="POST" action="kendaraan.php" enctype="multipart/form-data" class="form-crud">
                    <input type="hidden" name="id" value="<?= $row_edit ? $row_edit['id'] : 0 ?>">
                    <input type="hidden" name="foto_lama" value="<?= $row_edit ? escape($row_edit['foto']) : '' ?>">

                    <!-- Field No Polisi -->
                    <div class="form-row">
                        <div class="form-label">No Polisi</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <input type="text" name="no_polisi" class="input-text input-sm" value="<?= $row_edit ? escape($row_edit['no_polisi']) : '' ?>" required>
                        </div>
                    </div>

                    <!-- Field Nama Kendaraan -->
                    <div class="form-row">
                        <div class="form-label">Nama Kendaraan</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <input type="text" name="nama_kendaraan" class="input-text input-lg" value="<?= $row_edit ? escape($row_edit['nama_kendaraan']) : '' ?>" required>
                        </div>
                    </div>

                    <!-- Field Tipe -->
                    <div class="form-row">
                        <div class="form-label">Tipe</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <select name="tipe" class="input-select">
                                <option value="AT" <?= ($row_edit && $row_edit['tipe'] === 'AT') ? 'selected' : '' ?>>AT</option>
                                <option value="MT" <?= ($row_edit && $row_edit['tipe'] === 'MT') ? 'selected' : '' ?>>MT</option>
                            </select>
                        </div>
                    </div>

                    <!-- Field Warna -->
                    <div class="form-row">
                        <div class="form-label">Warna</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <input type="text" name="warna" class="input-text input-sm" value="<?= $row_edit ? escape($row_edit['warna']) : '' ?>" required>
                        </div>
                    </div>

                    <!-- Field No Rangka -->
                    <div class="form-row">
                        <div class="form-label">No Rangka</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <input type="text" name="no_rangka" class="input-text input-md" value="<?= $row_edit ? escape($row_edit['no_rangka']) : '' ?>" required>
                        </div>
                    </div>

                    <!-- Field No Mesin -->
                    <div class="form-row">
                        <div class="form-label">No Mesin</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <input type="text" name="no_mesin" class="input-text input-md" value="<?= $row_edit ? escape($row_edit['no_mesin']) : '' ?>" required>
                        </div>
                    </div>

                    <!-- Field Bahan Bakar -->
                    <div class="form-row">
                        <div class="form-label">Bahan Bakar</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <input type="text" name="bahan_bakar" class="input-text input-sm" value="<?= $row_edit ? escape($row_edit['bahan_bakar']) : 'Bensin' ?>" required>
                        </div>
                    </div>

                    <!-- Field Tahun Kendaraan -->
                    <div class="form-row">
                        <div class="form-label">Tahun Kendaraan</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <input type="number" name="thn_kendaraan" class="input-text input-sm" value="<?= $row_edit ? escape($row_edit['thn_kendaraan']) : date('Y') ?>" required>
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

                    <!-- Field Status -->
                    <div class="form-row">
                        <div class="form-label">Status</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <select name="status" class="input-select">
                                <option value="Ready" <?= ($row_edit && $row_edit['status'] === 'Ready') ? 'selected' : '' ?>>Ready</option>
                                <option value="Sewa" <?= ($row_edit && $row_edit['status'] === 'Sewa') ? 'selected' : '' ?>>Sewa</option>
                            </select>
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
