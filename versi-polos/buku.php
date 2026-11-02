<?php
/**
 * ==============================================================================
 * MODUL MANAJEMEN DATA BUKU (VERSI POLOS / VANILLA CSS)
 * ==============================================================================
 * Fitur:
 * 1. CRUD (Create, Read, Update, Delete) dalam 1 file modular.
 * 2. Pencarian buku berdasarkan judul, pengarang, atau kode buku.
 * 3. Proteksi Foreign Key Constraint (mencegah hapus buku yang masih dipinjam).
 * 4. Validasi stok tidak boleh negatif.
 * ==============================================================================
 */

require_once 'config/database.php';
require_once 'config/auth.php';

$page_title = "Manajemen Data Buku";
$sukses = '';
$error  = '';

// 1. Tangani Pesan Sukses dari Redirect
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] === 'sukses_tambah') $sukses = "Data buku baru berhasil ditambahkan!";
    if ($_GET['pesan'] === 'sukses_edit')   $sukses = "Perubahan data buku berhasil disimpan!";
    if ($_GET['pesan'] === 'sukses_hapus')  $sukses = "Data buku berhasil dihapus dari katalog!";
}

// 2. Tangani Aksi Hapus Buku (GET ?hapus=ID)
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];

    try {
        // Menggunakan Prepared Statement untuk keamanan
        $stmtHapus = $pdo->prepare("DELETE FROM buku WHERE id_buku = :id");
        $stmtHapus->execute([':id' => $id_hapus]);

        header("Location: buku.php?pesan=sukses_hapus");
        exit;
    } catch (PDOException $e) {
        // SQLSTATE 23000 menandakan pelanggaran Foreign Key Constraint (ON DELETE RESTRICT)
        if ($e->getCode() == '23000') {
            $error = "Buku ini tidak dapat dihapus karena masih tercatat dalam transaksi peminjaman!";
        } else {
            $error = "Terjadi kesalahan saat menghapus data: " . $e->getMessage();
        }
    }
}

// Variabel default form
$is_edit      = false;
$edit_id      = 0;
$kode_buku    = '';
$judul_buku   = '';
$pengarang    = '';
$penerbit     = '';
$tahun_terbit = date('Y');
$kategori     = '';
$stok         = 1;

// 3. Tangani Mode Edit (GET ?edit=ID)
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmtCari = $pdo->prepare("SELECT * FROM buku WHERE id_buku = :id LIMIT 1");
    $stmtCari->execute([':id' => $edit_id]);
    $bukuEdit = $stmtCari->fetch();

    if ($bukuEdit) {
        $is_edit      = true;
        $kode_buku    = $bukuEdit['kode_buku'];
        $judul_buku   = $bukuEdit['judul_buku'];
        $pengarang    = $bukuEdit['pengarang'];
        $penerbit     = $bukuEdit['penerbit'];
        $tahun_terbit = $bukuEdit['tahun_terbit'];
        $kategori     = $bukuEdit['kategori'];
        $stok         = $bukuEdit['stok'];
    } else {
        $error = "Data buku yang akan diedit tidak ditemukan!";
    }
}

// 4. Tangani Submit Form Tambah / Update (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action       = $_POST['action'] ?? 'tambah';
    $kode_buku    = trim($_POST['kode_buku'] ?? '');
    $judul_buku   = trim($_POST['judul_buku'] ?? '');
    $pengarang    = trim($_POST['pengarang'] ?? '');
    $penerbit     = trim($_POST['penerbit'] ?? '');
    $tahun_terbit = (int)($_POST['tahun_terbit'] ?? 0);
    $kategori     = trim($_POST['kategori'] ?? '');
    $stok         = (int)($_POST['stok'] ?? 0);

    // Validasi form dasar
    if (empty($kode_buku) || empty($judul_buku) || empty($pengarang) || empty($penerbit) || empty($kategori)) {
        $error = "Semua field formulir buku wajib diisi!";
    } elseif ($tahun_terbit < 1900 || $tahun_terbit > (int)date('Y') + 1) {
        $error = "Tahun terbit tidak valid (antara 1900 s/d " . ((int)date('Y') + 1) . ")!";
    } elseif ($stok < 0) {
        $error = "Stok buku tidak boleh bernilai negatif!";
    } else {
        if ($action === 'tambah') {
            // Cek keunikan kode_buku
            $stmtCek = $pdo->prepare("SELECT COUNT(*) FROM buku WHERE kode_buku = :kode");
            $stmtCek->execute([':kode' => $kode_buku]);
            if ($stmtCek->fetchColumn() > 0) {
                $error = "Kode buku '{$kode_buku}' sudah digunakan! Silakan gunakan kode lain.";
            } else {
                // Simpan data buku baru dengan Prepared Statement
                $stmtInsert = $pdo->prepare("
                    INSERT INTO buku (kode_buku, judul_buku, pengarang, penerbit, tahun_terbit, kategori, stok)
                    VALUES (:kode, :judul, :pengarang, :penerbit, :tahun, :kategori, :stok)
                ");
                $stmtInsert->execute([
                    ':kode'      => $kode_buku,
                    ':judul'     => $judul_buku,
                    ':pengarang' => $pengarang,
                    ':penerbit'  => $penerbit,
                    ':tahun'     => $tahun_terbit,
                    ':kategori'  => $kategori,
                    ':stok'      => $stok
                ]);

                header("Location: buku.php?pesan=sukses_tambah");
                exit;
            }
        } elseif ($action === 'edit') {
            $id_buku = (int)$_POST['id_buku'];

            // Cek keunikan kode_buku untuk buku selain yang sedang diedit
            $stmtCek = $pdo->prepare("SELECT COUNT(*) FROM buku WHERE kode_buku = :kode AND id_buku != :id");
            $stmtCek->execute([':kode' => $kode_buku, ':id' => $id_buku]);
            if ($stmtCek->fetchColumn() > 0) {
                $error = "Kode buku '{$kode_buku}' sudah digunakan oleh buku lain!";
            } else {
                // Update data buku
                $stmtUpdate = $pdo->prepare("
                    UPDATE buku SET 
                        kode_buku = :kode,
                        judul_buku = :judul,
                        pengarang = :pengarang,
                        penerbit = :penerbit,
                        tahun_terbit = :tahun,
                        kategori = :kategori,
                        stok = :stok
                    WHERE id_buku = :id
                ");
                $stmtUpdate->execute([
                    ':kode'      => $kode_buku,
                    ':judul'     => $judul_buku,
                    ':pengarang' => $pengarang,
                    ':penerbit'  => $penerbit,
                    ':tahun'     => $tahun_terbit,
                    ':kategori'  => $kategori,
                    ':stok'      => $stok,
                    ':id'        => $id_buku
                ]);

                header("Location: buku.php?pesan=sukses_edit");
                exit;
            }
        }
    }
}

// 5. Fitur Pencarian & Pengambilan Data Buku
$keyword = trim($_GET['keyword'] ?? '');
if ($keyword !== '') {
    // Pencarian dengan PDO parameter binding
    $stmtBuku = $pdo->prepare("
        SELECT * FROM buku 
        WHERE judul_buku LIKE :kw1 OR pengarang LIKE :kw2 OR kode_buku LIKE :kw3
        ORDER BY id_buku DESC
    ");
    $param = "%{$keyword}%";
    $stmtBuku->execute([':kw1' => $param, ':kw2' => $param, ':kw3' => $param]);
} else {
    $stmtBuku = $pdo->query("SELECT * FROM buku ORDER BY id_buku DESC");
}
$daftarBuku = $stmtBuku->fetchAll();

include 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Katalog & Data Buku</h1>
        <p class="page-subtitle">Kelola inventaris dan ketersediaan stok buku perpustakaan.</p>
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
    <!-- Kolom Kiri: Formulir Tambah / Edit Buku -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><?= $is_edit ? '✏️ Edit Data Buku' : '➕ Tambah Buku Baru' ?></h2>
            <?php if ($is_edit): ?>
                <a href="buku.php" class="btn btn-sm btn-outline">Batal</a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <form action="buku.php" method="POST">
                <input type="hidden" name="action" value="<?= $is_edit ? 'edit' : 'tambah' ?>">
                <?php if ($is_edit): ?>
                    <input type="hidden" name="id_buku" value="<?= (int)$edit_id ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="kode_buku" class="form-label">Kode Buku (Unik)</label>
                    <input type="text" id="kode_buku" name="kode_buku" class="form-control" 
                           placeholder="Contoh: BK-006" required 
                           value="<?= escape($kode_buku) ?>">
                </div>

                <div class="form-group">
                    <label for="judul_buku" class="form-label">Judul Buku</label>
                    <input type="text" id="judul_buku" name="judul_buku" class="form-control" 
                           placeholder="Masukkan judul buku lengkap" required 
                           value="<?= escape($judul_buku) ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="pengarang" class="form-label">Pengarang</label>
                        <input type="text" id="pengarang" name="pengarang" class="form-control" 
                               placeholder="Nama pengarang" required 
                               value="<?= escape($pengarang) ?>">
                    </div>
                    <div class="form-group">
                        <label for="penerbit" class="form-label">Penerbit</label>
                        <input type="text" id="penerbit" name="penerbit" class="form-control" 
                               placeholder="Nama penerbit" required 
                               value="<?= escape($penerbit) ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
                        <input type="number" id="tahun_terbit" name="tahun_terbit" class="form-control" 
                               min="1900" max="<?= date('Y') + 1 ?>" required 
                               value="<?= (int)$tahun_terbit ?>">
                    </div>
                    <div class="form-group">
                        <label for="stok" class="form-label">Jumlah Stok</label>
                        <input type="number" id="stok" name="stok" class="form-control" 
                               min="0" required 
                               value="<?= (int)$stok ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="kategori" class="form-label">Kategori Buku</label>
                    <input type="text" id="kategori" name="kategori" class="form-control" 
                           placeholder="Contoh: Teknologi, Fiksi, Sejarah" required 
                           value="<?= escape($kategori) ?>">
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem;">
                    <?= $is_edit ? 'Simpan Perubahan' : 'Simpan Buku Baru' ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Kolom Kanan: Tabel Daftar Buku & Pencarian -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Daftar Koleksi Buku (<?= count($daftarBuku) ?>)</h2>
            <!-- Form Pencarian Buku -->
            <form action="buku.php" method="GET" style="display: flex; gap: 0.5rem;">
                <input type="text" name="keyword" class="form-control" style="width: 200px; padding: 0.35rem 0.6rem; font-size: 0.85rem;" 
                       placeholder="Cari judul/pengarang..." value="<?= escape($keyword) ?>">
                <button type="submit" class="btn btn-sm btn-primary">Cari</button>
                <?php if ($keyword !== ''): ?>
                    <a href="buku.php" class="btn btn-sm btn-outline">Reset</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode</th>
                            <th>Judul & Pengarang</th>
                            <th>Penerbit / Thn</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daftarBuku)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem; color: #64748b;">
                                    <?= ($keyword !== '') ? 'Tidak ditemukan buku dengan kata kunci tersebut.' : 'Belum ada data buku.' ?>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($daftarBuku as $buku): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><code><?= escape($buku['kode_buku']) ?></code></td>
                                    <td>
                                        <strong><?= escape($buku['judul_buku']) ?></strong><br>
                                        <small style="color: #64748b;">Penulis: <?= escape($buku['pengarang']) ?></small>
                                    </td>
                                    <td>
                                        <?= escape($buku['penerbit']) ?><br>
                                        <small style="color: #64748b;">(<?= escape($buku['tahun_terbit']) ?>)</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary"><?= escape($buku['kategori']) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($buku['stok'] > 0): ?>
                                            <span class="badge badge-success"><?= (int)$buku['stok'] ?> tersedia</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Habis</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="text-align: center; white-space: nowrap;">
                                        <a href="buku.php?edit=<?= (int)$buku['id_buku'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="buku.php?hapus=<?= (int)$buku['id_buku'] ?>" class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus buku \'<?= escape(addslashes($buku['judul_buku'])) ?>\'?')">
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
