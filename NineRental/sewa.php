<?php
// Koneksi database
require_once __DIR__ . '/config/database.php';

$action = $_GET['action'] ?? 'list';
$msg = $_GET['msg'] ?? '';
$err = $_GET['err'] ?? '';

// Proses Hapus Data
if ($action === 'hapus' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Ambil no_polisi sebelum dihapus untuk pemulihan status kendaraan jika perlu
    $stmt = $pdo->prepare("SELECT no_polisi FROM sewatbl WHERE id = ?");
    $stmt->execute([$id]);
    $trx = $stmt->fetch();
    if ($trx) {
        $stmt_update = $pdo->prepare("UPDATE kendaraantbl SET status = 'Ready' WHERE no_polisi = ?");
        $stmt_update->execute([$trx['no_polisi']]);
    }

    $stmt = $pdo->prepare("DELETE FROM sewatbl WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: sewa.php?msg=" . urlencode("Transaksi sewa berhasil dihapus"));
    exit;
}

// Proses Ubah Status Kembali
if ($action === 'kembali' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT no_polisi FROM sewatbl WHERE id = ?");
    $stmt->execute([$id]);
    $trx = $stmt->fetch();
    if ($trx) {
        $stmt_up_sewa = $pdo->prepare("UPDATE sewatbl SET status = 'Kembali' WHERE id = ?");
        $stmt_up_sewa->execute([$id]);

        $stmt_up_ken = $pdo->prepare("UPDATE kendaraantbl SET status = 'Ready' WHERE no_polisi = ?");
        $stmt_up_ken->execute([$trx['no_polisi']]);
    }
    header("Location: sewa.php?msg=" . urlencode("Status sewa berhasil diubah menjadi Kembali"));
    exit;
}

// Proses Simpan Transaksi (Tambah & Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $tanggal = trim($_POST['tanggal'] ?? date('Y-m-d'));
    $no_sewa = trim($_POST['no_sewa'] ?? '');
    $no_ktp = trim($_POST['no_ktp'] ?? '');
    $no_polisi = trim($_POST['no_polisi'] ?? '');
    $tgl_sewa = trim($_POST['tgl_sewa'] ?? date('Y-m-d'));
    $tgl_kembali = trim($_POST['tgl_kembali'] ?? date('Y-m-d'));
    $biaya = (int)($_POST['biaya'] ?? 0);
    $catatan = trim($_POST['catatan'] ?? '-');
    $status = trim($_POST['status'] ?? 'Sewa');

    if ($no_sewa === '' || $no_ktp === '' || $no_polisi === '') {
        $err = "No Sewa, No KTP, dan No Polisi wajib diisi!";
    } else {
        if ($id > 0) {
            // Update transaksi
            $stmt = $pdo->prepare("UPDATE sewatbl SET tanggal=?, no_sewa=?, no_ktp=?, no_polisi=?, tgl_sewa=?, tgl_kembali=?, biaya=?, catatan=?, status=? WHERE id=?");
            $stmt->execute([$tanggal, $no_sewa, $no_ktp, $no_polisi, $tgl_sewa, $tgl_kembali, $biaya, $catatan, $status, $id]);

            // Update status kendaraan terkait
            $status_ken = ($status === 'Kembali') ? 'Ready' : 'Sewa';
            $stmt_ken = $pdo->prepare("UPDATE kendaraantbl SET status=? WHERE no_polisi=?");
            $stmt_ken->execute([$status_ken, $no_polisi]);

            header("Location: sewa.php?msg=" . urlencode("Data transaksi sewa berhasil diperbarui"));
            exit;
        } else {
            // Tambah transaksi baru
            $stmt = $pdo->prepare("INSERT INTO sewatbl (tanggal, no_sewa, no_ktp, no_polisi, tgl_sewa, tgl_kembali, biaya, catatan, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tanggal, $no_sewa, $no_ktp, $no_polisi, $tgl_sewa, $tgl_kembali, $biaya, $catatan, $status]);

            // Set kendaraan menjadi Sewa
            if ($status === 'Sewa') {
                $stmt_ken = $pdo->prepare("UPDATE kendaraantbl SET status='Sewa' WHERE no_polisi=?");
                $stmt_ken->execute([$no_polisi]);
            }

            header("Location: sewa.php?msg=" . urlencode("Transaksi sewa berhasil disimpan"));
            exit;
        }
    }
}

// Ambil data untuk form edit
$row_edit = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM sewatbl WHERE id = ?");
    $stmt->execute([(int)$_GET['id']]);
    $row_edit = $stmt->fetch();
}

// Ambil data master untuk autocomplete / dropdown di form transaksi
$list_pelanggan = $pdo->query("SELECT no_ktp, nama_pelanggan FROM pelanggantbl ORDER BY nama_pelanggan ASC")->fetchAll();
$list_kendaraan = $pdo->query("SELECT no_polisi, nama_kendaraan, status FROM kendaraantbl ORDER BY nama_kendaraan ASC")->fetchAll();

// Mapping JSON untuk autofill nama pelanggan & kendaraan
$map_pelanggan = [];
foreach ($list_pelanggan as $p) {
    $map_pelanggan[$p['no_ktp']] = $p['nama_pelanggan'];
}
$map_kendaraan = [];
foreach ($list_kendaraan as $k) {
    $map_kendaraan[$k['no_polisi']] = $k['nama_kendaraan'];
}

// Generate nomor sewa otomatis format: S,YY-MM,N
$count_sewa = $pdo->query("SELECT COUNT(*) FROM sewatbl")->fetchColumn() + 1;
$auto_no_sewa = 'S,' . date('y-m') . ',' . $count_sewa;

// Ambil semua daftar transaksi sewa untuk tabel
$stmt = $pdo->query("SELECT * FROM sewatbl ORDER BY id ASC");
$sewa_list = $stmt->fetchAll();
$total_record = count($sewa_list);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi Sewa - Nine Rental</title>
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
                DATA TRANSAKSI SEWA
            </div>

            <div class="crud-content">
                <!-- Navigasi Aksi: Kembali ke Dashboard & Tambah Data -->
                <div class="action-nav-bar">
                    <a href="index.php" class="btn-nav-icon back" title="Kembali ke Dashboard">&le;</a>
                    <a href="sewa.php?action=tambah" class="btn-nav-icon add" title="Tambah Transaksi Sewa">&plusmn;</a>
                </div>

                <?php if ($msg): ?>
                    <div class="alert-box alert-success"><?= escape($msg) ?></div>
                <?php endif; ?>

                <!-- Tabel Data Transaksi Sewa -->
                <div class="table-responsive">
                    <table class="table-crud">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Sewa</th>
                                <th>Tgl Sewa</th>
                                <th>Tgl Kembali</th>
                                <th>No Polisi</th>
                                <th>No KTP</th>
                                <th>Biaya</th>
                                <th>Catatan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($sewa_list)): ?>
                                <tr>
                                    <td colspan="10" class="center">Belum ada data transaksi sewa.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($sewa_list as $s): ?>
                                    <tr>
                                        <td class="center"><?= $no++ ?></td>
                                        <td><?= escape($s['no_sewa']) ?></td>
                                        <td class="center"><?= escape($s['tgl_sewa']) ?></td>
                                        <td class="center"><?= escape($s['tgl_kembali']) ?></td>
                                        <td><?= escape($s['no_polisi']) ?></td>
                                        <td><?= escape($s['no_ktp']) ?></td>
                                        <td class="right"><?= number_format((float)$s['biaya'], 0, ',', '') ?></td>
                                        <td><?= escape($s['catatan']) ?></td>
                                        <td class="center"><?= escape($s['status']) ?></td>
                                        <td class="center">
                                            <?php if ($s['status'] === 'Sewa'): ?>
                                                <a href="sewa.php?action=kembali&id=<?= $s['id'] ?>" class="action-link" title="Set Kembali">&#10004;</a>
                                            <?php endif; ?>
                                            <a href="sewa.php?action=edit&id=<?= $s['id'] ?>" class="action-link edit" title="Edit">&#9998;</a>
                                            <a href="sewa.php?action=hapus&id=<?= $s['id'] ?>" class="action-link delete" title="Hapus" onclick="return confirm('Yakin ingin menghapus transaksi sewa ini?');">&#10060;</a>
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
            <!-- Form Input Tambah / Edit Transaksi Sewa -->
            <div class="crud-content">
                <div class="action-nav-bar">
                    <a href="sewa.php" class="btn-nav-icon back" title="Kembali ke Data Transaksi Sewa">&le;</a>
                </div>

                <?php if ($err): ?>
                    <div class="alert-box alert-danger"><?= escape($err) ?></div>
                <?php endif; ?>

                <form method="POST" action="sewa.php" class="form-crud">
                    <input type="hidden" name="id" value="<?= $row_edit ? $row_edit['id'] : 0 ?>">

                    <!-- Field Tanggal & No Sewa -->
                    <div class="form-row">
                        <div class="form-label">Tanggal</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <input type="date" name="tanggal" class="input-text input-sm" value="<?= $row_edit ? escape($row_edit['tanggal']) : date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-label">No Sewa</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <input type="text" name="no_sewa" class="input-text input-sm" value="<?= $row_edit ? escape($row_edit['no_sewa']) : $auto_no_sewa ?>" required>
                        </div>
                    </div>

                    <hr class="form-divider">

                    <!-- Field No KTP & Nama Pelanggan -->
                    <div class="form-row">
                        <div class="form-label">No KTP</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <select name="no_ktp" id="no_ktp" class="input-select input-md" onchange="updatePelanggan()" required>
                                <option value="">-- Pilih No KTP --</option>
                                <?php foreach ($list_pelanggan as $p): ?>
                                    <option value="<?= escape($p['no_ktp']) ?>" <?= ($row_edit && $row_edit['no_ktp'] === $p['no_ktp']) ? 'selected' : '' ?>>
                                        <?= escape($p['no_ktp']) ?> - <?= escape($p['nama_pelanggan']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div style="margin-top: 4px;">
                                <input type="text" id="nama_pelanggan" class="input-text input-md readonly" placeholder="Nama Pelanggan" readonly>
                            </div>
                        </div>
                    </div>

                    <hr class="form-divider">

                    <!-- Field No Polisi & Nama Kendaraan -->
                    <div class="form-row">
                        <div class="form-label">No Polisi</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <select name="no_polisi" id="no_polisi" class="input-select input-md" onchange="updateKendaraan()" required>
                                <option value="">-- Pilih No Polisi --</option>
                                <?php foreach ($list_kendaraan as $k): ?>
                                    <option value="<?= escape($k['no_polisi']) ?>" <?= ($row_edit && $row_edit['no_polisi'] === $k['no_polisi']) ? 'selected' : '' ?>>
                                        <?= escape($k['no_polisi']) ?> (<?= escape($k['nama_kendaraan']) ?> - <?= escape($k['status']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div style="margin-top: 4px;">
                                <input type="text" id="nama_kendaraan" class="input-text input-md readonly" placeholder="Nama Kendaraan" readonly>
                            </div>
                        </div>
                    </div>

                    <hr class="form-divider">

                    <!-- Field Tgl Sewa & Tgl Kembali -->
                    <div class="form-row">
                        <div class="form-label">Tgl Sewa</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <input type="date" name="tgl_sewa" class="input-text input-sm" value="<?= $row_edit ? escape($row_edit['tgl_sewa']) : date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-label">Tgl Kembali</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <input type="date" name="tgl_kembali" class="input-text input-sm" value="<?= $row_edit ? escape($row_edit['tgl_kembali']) : date('Y-m-d', strtotime('+2 days')) ?>" required>
                        </div>
                    </div>

                    <!-- Field Biaya /hari -->
                    <div class="form-row">
                        <div class="form-label">Biaya /hari</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <input type="number" name="biaya" class="input-text input-sm" value="<?= $row_edit ? escape($row_edit['biaya']) : '300000' ?>" required>
                        </div>
                    </div>

                    <!-- Field Catatan -->
                    <div class="form-row">
                        <div class="form-label">Catatan</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <textarea name="catatan" class="input-textarea"><?= $row_edit ? escape($row_edit['catatan']) : '-' ?></textarea>
                        </div>
                    </div>

                    <!-- Field Status -->
                    <div class="form-row">
                        <div class="form-label">Status</div>
                        <div class="form-colon">:</div>
                        <div class="form-control-wrap">
                            <select name="status" class="input-select">
                                <option value="Sewa" <?= ($row_edit && $row_edit['status'] === 'Sewa') ? 'selected' : '' ?>>Sewa</option>
                                <option value="Kembali" <?= ($row_edit && $row_edit['status'] === 'Kembali') ? 'selected' : '' ?>>Kembali</option>
                            </select>
                        </div>
                    </div>

                    <hr class="form-divider">

                    <button type="submit" class="btn-submit">Simpan</button>
                </form>

                <script>
                    var mapPelanggan = <?= json_encode($map_pelanggan) ?>;
                    var mapKendaraan = <?= json_encode($map_kendaraan) ?>;

                    function updatePelanggan() {
                        var val = document.getElementById('no_ktp').value;
                        document.getElementById('nama_pelanggan').value = mapPelanggan[val] || '';
                    }

                    function updateKendaraan() {
                        var val = document.getElementById('no_polisi').value;
                        document.getElementById('nama_kendaraan').value = mapKendaraan[val] || '';
                    }

                    // Inisialisasi saat pertama kali dimuat
                    window.onload = function() {
                        updatePelanggan();
                        updateKendaraan();
                    };
                </script>
            </div>
        <?php endif; ?>

        <!-- Footer Putih CRUD -->
        <div class="footer-crud">
            COPYRIGHT @ SMK NEGERI 9 SEMARANG
        </div>
    </div>

</body>
</html>
