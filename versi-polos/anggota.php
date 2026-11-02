<?php
/**
 * ==============================================================================
 * MODUL MANAJEMEN DATA ANGGOTA (VERSI POLOS / VANILLA CSS)
 * ==============================================================================
 * Fitur:
 * 1. CRUD (Create, Read, Update, Delete) Data Anggota Perpustakaan.
 * 2. Pencarian anggota berdasarkan nama lengkap atau nomor anggota.
 * 3. Proteksi integritas data referensial (Foreign Key) saat penghapusan.
 * ==============================================================================
 */

require_once 'config/database.php';
require_once 'config/auth.php';

$page_title = "Manajemen Data Anggota";
$sukses = '';
$error  = '';

// 1. Tangani Pesan Sukses dari Redirect
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] === 'sukses_tambah') $sukses = "Data anggota baru berhasil didaftarkan!";
    if ($_GET['pesan'] === 'sukses_edit')   $sukses = "Perubahan data anggota berhasil disimpan!";
    if ($_GET['pesan'] === 'sukses_hapus')  $sukses = "Data anggota berhasil dihapus dari sistem!";
}

// 2. Tangani Aksi Hapus Anggota (GET ?hapus=ID)
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];

    try {
        // Eksekusi penghapusan menggunakan Prepared Statement
        $stmtHapus = $pdo->prepare("DELETE FROM anggota WHERE id_anggota = :id");
        $stmtHapus->execute([':id' => $id_hapus]);

        header("Location: anggota.php?pesan=sukses_hapus");
        exit;
    } catch (PDOException $e) {
        // Tangani kesalahan Foreign Key constraint (SQLSTATE 23000)
        if ($e->getCode() == '23000') {
            $error = "Anggota tidak dapat dihapus karena masih tercatat memiliki transaksi peminjaman buku!";
        } else {
            $error = "Terjadi kesalahan sistem saat menghapus data: " . $e->getMessage();
        }
    }
}

// Variabel default form
$is_edit             = false;
$edit_id             = 0;
$nomor_anggota       = '';
$nama_lengkap        = '';
$jenis_kelamin       = 'Laki-laki';
$alamat              = '';
$no_telepon          = '';
$tanggal_pendaftaran = date('Y-m-d');

// 3. Tangani Mode Edit (GET ?edit=ID)
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmtCari = $pdo->prepare("SELECT * FROM anggota WHERE id_anggota = :id LIMIT 1");
    $stmtCari->execute([':id' => $edit_id]);
    $anggotaEdit = $stmtCari->fetch();

    if ($anggotaEdit) {
        $is_edit             = true;
        $nomor_anggota       = $anggotaEdit['nomor_anggota'];
        $nama_lengkap        = $anggotaEdit['nama_lengkap'];
        $jenis_kelamin       = $anggotaEdit['jenis_kelamin'];
        $alamat              = $anggotaEdit['alamat'];
        $no_telepon          = $anggotaEdit['no_telepon'];
        $tanggal_pendaftaran = $anggotaEdit['tanggal_pendaftaran'];
    } else {
        $error = "Data anggota yang akan diedit tidak ditemukan!";
    }
}

// 4. Tangani Submit Form Tambah / Update (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action              = $_POST['action'] ?? 'tambah';
    $nomor_anggota       = trim($_POST['nomor_anggota'] ?? '');
    $nama_lengkap        = trim($_POST['nama_lengkap'] ?? '');
    $jenis_kelamin       = $_POST['jenis_kelamin'] ?? 'Laki-laki';
    $alamat              = trim($_POST['alamat'] ?? '');
    $no_telepon          = trim($_POST['no_telepon'] ?? '');
    $tanggal_pendaftaran = trim($_POST['tanggal_pendaftaran'] ?? date('Y-m-d'));

    // Validasi input
    if (empty($nomor_anggota) || empty($nama_lengkap) || empty($alamat) || empty($no_telepon) || empty($tanggal_pendaftaran)) {
        $error = "Semua kolom formulir anggota wajib diisi lengkap!";
    } elseif (!in_array($jenis_kelamin, ['Laki-laki', 'Perempuan'])) {
        $error = "Pilihan jenis kelamin tidak valid!";
    } else {
        if ($action === 'tambah') {
            // Cek keunikan nomor anggota
            $stmtCek = $pdo->prepare("SELECT COUNT(*) FROM anggota WHERE nomor_anggota = :nomor");
            $stmtCek->execute([':nomor' => $nomor_anggota]);
            if ($stmtCek->fetchColumn() > 0) {
                $error = "Nomor anggota '{$nomor_anggota}' sudah terdaftar! Gunakan nomor lain.";
            } else {
                // Simpan data anggota baru
                $stmtInsert = $pdo->prepare("
                    INSERT INTO anggota (nomor_anggota, nama_lengkap, jenis_kelamin, alamat, no_telepon, tanggal_pendaftaran)
                    VALUES (:nomor, :nama, :jk, :alamat, :telepon, :tgl_daftar)
                ");
                $stmtInsert->execute([
                    ':nomor'      => $nomor_anggota,
                    ':nama'       => $nama_lengkap,
                    ':jk'         => $jenis_kelamin,
                    ':alamat'     => $alamat,
                    ':telepon'    => $no_telepon,
                    ':tgl_daftar' => $tanggal_pendaftaran
                ]);

                header("Location: anggota.php?pesan=sukses_tambah");
                exit;
            }
        } elseif ($action === 'edit') {
            $id_anggota = (int)$_POST['id_anggota'];

            // Cek keunikan nomor anggota untuk anggota selain yang sedang diedit
            $stmtCek = $pdo->prepare("SELECT COUNT(*) FROM anggota WHERE nomor_anggota = :nomor AND id_anggota != :id");
            $stmtCek->execute([':nomor' => $nomor_anggota, ':id' => $id_anggota]);
            if ($stmtCek->fetchColumn() > 0) {
                $error = "Nomor anggota '{$nomor_anggota}' sudah digunakan oleh anggota lain!";
            } else {
                // Update data anggota
                $stmtUpdate = $pdo->prepare("
                    UPDATE anggota SET
                        nomor_anggota = :nomor,
                        nama_lengkap = :nama,
                        jenis_kelamin = :jk,
                        alamat = :alamat,
                        no_telepon = :telepon,
                        tanggal_pendaftaran = :tgl_daftar
                    WHERE id_anggota = :id
                ");
                $stmtUpdate->execute([
                    ':nomor'      => $nomor_anggota,
                    ':nama'       => $nama_lengkap,
                    ':jk'         => $jenis_kelamin,
                    ':alamat'     => $alamat,
                    ':telepon'    => $no_telepon,
                    ':tgl_daftar' => $tanggal_pendaftaran,
                    ':id'         => $id_anggota
                ]);

                header("Location: anggota.php?pesan=sukses_edit");
                exit;
            }
        }
    }
}

// 5. Pencarian & Pengambilan Data Anggota
$keyword = trim($_GET['keyword'] ?? '');
if ($keyword !== '') {
    $stmtAnggota = $pdo->prepare("
        SELECT * FROM anggota 
        WHERE nama_lengkap LIKE :kw1 OR nomor_anggota LIKE :kw2
        ORDER BY id_anggota DESC
    ");
    $param = "%{$keyword}%";
    $stmtAnggota->execute([':kw1' => $param, ':kw2' => $param]);
} else {
    $stmtAnggota = $pdo->query("SELECT * FROM anggota ORDER BY id_anggota DESC");
}
$daftarAnggota = $stmtAnggota->fetchAll();

include 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Manajemen Data Anggota</h1>
        <p class="page-subtitle">Kelola pendaftaran anggota dan data identitas peminjam perpustakaan.</p>
    </div>
</div>

<?php if (!empty($sukses)): ?>
    <div class="alert alert-success">
        <span>✅ <?= escape($sukses) ?></span>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger">
        <span>⚠️ <?= escape($error) ?></span>
    </div>
<?php endif; ?>

<div class="grid grid-split">
    <!-- Kolom Kiri: Form Tambah / Edit Anggota -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><?= $is_edit ? '✏️ Edit Data Anggota' : '➕ Pendaftaran Anggota Baru' ?></h2>
            <?php if ($is_edit): ?>
                <a href="anggota.php" class="btn btn-sm btn-outline">Batal</a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <form action="anggota.php" method="POST">
                <input type="hidden" name="action" value="<?= $is_edit ? 'edit' : 'tambah' ?>">
                <?php if ($is_edit): ?>
                    <input type="hidden" name="id_anggota" value="<?= (int)$edit_id ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="nomor_anggota" class="form-label">Nomor Anggota (Unik)</label>
                    <input type="text" id="nomor_anggota" name="nomor_anggota" class="form-control" 
                           placeholder="Contoh: AG-2026-005" required 
                           value="<?= escape($nomor_anggota) ?>">
                </div>

                <div class="form-group">
                    <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" class="form-control" 
                           placeholder="Masukkan nama lengkap siswa/guru" required 
                           value="<?= escape($nama_lengkap) ?>">
                </div>

                <div class="form-group">
                    <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" class="form-select" required>
                        <option value="Laki-laki" <?= ($jenis_kelamin === 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="Perempuan" <?= ($jenis_kelamin === 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="no_telepon" class="form-label">Nomor Telepon / WhatsApp</label>
                    <input type="text" id="no_telepon" name="no_telepon" class="form-control" 
                           placeholder="Contoh: 08123456789" required 
                           value="<?= escape($no_telepon) ?>">
                </div>

                <div class="form-group">
                    <label for="tanggal_pendaftaran" class="form-label">Tanggal Pendaftaran</label>
                    <input type="date" id="tanggal_pendaftaran" name="tanggal_pendaftaran" class="form-control" 
                           required value="<?= escape($tanggal_pendaftaran) ?>">
                </div>

                <div class="form-group">
                    <label for="alamat" class="form-label">Alamat Lengkap</label>
                    <textarea id="alamat" name="alamat" class="form-control" 
                              placeholder="Masukkan alamat tinggal" required><?= escape($alamat) ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem;">
                    <?= $is_edit ? 'Simpan Perubahan Anggota' : 'Daftarkan Anggota Baru' ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Kolom Kanan: Tabel Daftar Anggota & Pencarian -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Daftar Anggota Terdaftar (<?= count($daftarAnggota) ?>)</h2>
            <!-- Form Pencarian Anggota -->
            <form action="anggota.php" method="GET" style="display: flex; gap: 0.5rem;">
                <input type="text" name="keyword" class="form-control" style="width: 200px; padding: 0.35rem 0.6rem; font-size: 0.85rem;" 
                       placeholder="Cari nama / nomor..." value="<?= escape($keyword) ?>">
                <button type="submit" class="btn btn-sm btn-primary">Cari</button>
                <?php if ($keyword !== ''): ?>
                    <a href="anggota.php" class="btn btn-sm btn-outline">Reset</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No. Anggota</th>
                            <th>Nama & Jenis Kelamin</th>
                            <th>Kontak / Telp</th>
                            <th>Alamat</th>
                            <th>Tgl Terdaftar</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daftarAnggota)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem; color: #64748b;">
                                    <?= ($keyword !== '') ? 'Tidak ditemukan anggota dengan kata kunci tersebut.' : 'Belum ada data anggota.' ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($daftarAnggota as $anggota): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><code><?= escape($anggota['nomor_anggota']) ?></code></td>
                                    <td>
                                        <strong><?= escape($anggota['nama_lengkap']) ?></strong><br>
                                        <small style="color: #64748b;"><?= escape($anggota['jenis_kelamin']) ?></small>
                                    </td>
                                    <td><?= escape($anggota['no_telepon']) ?></td>
                                    <td><small><?= escape($anggota['alamat']) ?></small></td>
                                    <td><?= format_tanggal($anggota['tanggal_pendaftaran']) ?></td>
                                    <td style="text-align: center; white-space: nowrap;">
                                        <a href="anggota.php?edit=<?= (int)$anggota['id_anggota'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="anggota.php?hapus=<?= (int)$anggota['id_anggota'] ?>" class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus data anggota \'<?= escape(addslashes($anggota['nama_lengkap'])) ?>\'?')">
                                           Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
