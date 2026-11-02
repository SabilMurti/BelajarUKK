<?php
/**
 * ==============================================================================
 * MODUL TRANSAKSI PEMINJAMAN BUKU (VERSI POLOS / VANILLA CSS)
 * ==============================================================================
 * Konsep Penting UKK: DATABASE TRANSACTION (ACID)
 * 1. $pdo->beginTransaction(): Memulai transaksi database secara terisolasi.
 * 2. SELECT ... FOR UPDATE: Mengunci baris data buku agar tidak terjadi race condition.
 * 3. Validasi Stok: Memastikan jumlah pinjam <= stok tersedia.
 * 4. INSERT peminjaman & UPDATE stok buku secara atomik.
 * 5. $pdo->commit(): Menyimpan seluruh perubahan permanen ke database jika sukses.
 * 6. $pdo->rollBack(): Membatalkan seluruh perubahan jika terjadi galat / exception.
 * ==============================================================================
 */

require_once 'config/database.php';
require_once 'config/auth.php';

$page_title = "Transaksi Peminjaman Buku";
$sukses = '';
$error  = '';

// 1. Tangani Pesan Sukses dari URL
if (isset($_GET['pesan']) && $_GET['pesan'] === 'sukses_pinjam') {
    $sukses = "Transaksi peminjaman buku berhasil dicatat! Stok buku otomatis dikurangi.";
}

// 2. Ambil Data Anggota & Buku untuk Dropdown Form
$stmtAnggota = $pdo->query("SELECT id_anggota, nomor_anggota, nama_lengkap FROM anggota ORDER BY nama_lengkap ASC");
$listAnggota = $stmtAnggota->fetchAll();

$stmtBuku = $pdo->query("SELECT id_buku, kode_buku, judul_buku, stok FROM buku ORDER BY judul_buku ASC");
$listBuku = $stmtBuku->fetchAll();

// Default values form
$id_anggota              = '';
$id_buku                 = '';
$tanggal_pinjam          = date('Y-m-d');
$tanggal_rencana_kembali = date('Y-m-d', strtotime('+7 days')); // Default durasi peminjaman 7 hari
$jumlah_pinjam           = 1;

// 3. Proses Transaksi Peminjaman (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_anggota              = (int)($_POST['id_anggota'] ?? 0);
    $id_buku                 = (int)($_POST['id_buku'] ?? 0);
    $tanggal_pinjam          = trim($_POST['tanggal_pinjam'] ?? '');
    $tanggal_rencana_kembali = trim($_POST['tanggal_rencana_kembali'] ?? '');
    $jumlah_pinjam           = (int)($_POST['jumlah_pinjam'] ?? 1);

    // Validasi dasar
    if (empty($id_anggota) || empty($id_buku) || empty($tanggal_pinjam) || empty($tanggal_rencana_kembali)) {
        $error = "Semua kolom transaksi peminjaman wajib diisi!";
    } elseif ($jumlah_pinjam <= 0) {
        $error = "Jumlah buku yang dipinjam minimal 1 eksemplar!";
    } elseif ($tanggal_rencana_kembali < $tanggal_pinjam) {
        $error = "Tanggal rencana kembali tidak boleh lebih awal dari tanggal pinjam!";
    } else {
        /**
         * ==============================================================================
         * PENJELASAN KRUSIAL: DATABASE TRANSACTION
         * ==============================================================================
         * Transaksi menjamin prinsip Atomicity: Jika salah satu perintah gagal (misal
         * query peminjaman berhasil tetapi update stok buku gagal atau stok habis),
         * maka SELURUH perubahan yang terjadi di dalam blok ini akan dibatalkan
         * (rollBack), sehingga database tetap konsisten dan stok tidak bocor/negatif.
         * ==============================================================================
         */
        $pdo->beginTransaction();

        try {
            // A. Kunci dan periksa ketersediaan data buku secara real-time
            // Menggunakan FOR UPDATE untuk mencegah race condition dari transaksi paralel
            $stmtCekBuku = $pdo->prepare("SELECT stok, judul_buku FROM buku WHERE id_buku = :id FOR UPDATE");
            $stmtCekBuku->execute([':id' => $id_buku]);
            $buku = $stmtCekBuku->fetch();

            if (!$buku) {
                throw new Exception("Buku yang dipilih tidak ditemukan dalam database!");
            }

            // B. Validasi stok tidak boleh kurang dari jumlah pinjam
            if ($buku['stok'] < $jumlah_pinjam) {
                throw new Exception("Stok untuk buku '{$buku['judul_buku']}' tidak mencukupi! Tersedia: {$buku['stok']}, Diminta: {$jumlah_pinjam}.");
            }

            // C. Masukkan data ke tabel peminjaman
            $stmtInsertPinjam = $pdo->prepare("
                INSERT INTO peminjaman (id_anggota, id_buku, tanggal_pinjam, tanggal_rencana_kembali, jumlah_pinjam, status)
                VALUES (:id_anggota, :id_buku, :tgl_pinjam, :tgl_kembali, :jumlah, 'Dipinjam')
            ");
            $stmtInsertPinjam->execute([
                ':id_anggota'   => $id_anggota,
                ':id_buku'      => $id_buku,
                ':tgl_pinjam'   => $tanggal_pinjam,
                ':tgl_kembali'  => $tanggal_rencana_kembali,
                ':jumlah'       => $jumlah_pinjam
            ]);

            // D. Kurangi stok pada tabel buku secara matematis
            $stmtKurangStok = $pdo->prepare("
                UPDATE buku 
                SET stok = stok - :jumlah 
                WHERE id_buku = :id
            ");
            $stmtKurangStok->execute([
                ':jumlah' => $jumlah_pinjam,
                ':id'     => $id_buku
            ]);

            // E. Komit Transaksi (Semua query berhasil tanpa kendala)
            $pdo->commit();

            header("Location: peminjaman.php?pesan=sukses_pinjam");
            exit;

        } catch (Exception $e) {
            // F. Rollback Transaksi (Batalkan semua perubahan jika terjadi error)
            $pdo->rollBack();
            $error = $e->getMessage();
        }
    }
}

// 4. Ambil Seluruh Daftar Riwayat Peminjaman (JOIN anggota & buku)
$queryRiwayat = "
    SELECT p.*, a.nama_lengkap AS nama_anggota, a.nomor_anggota, b.judul_buku, b.kode_buku
    FROM peminjaman p
    JOIN anggota a ON p.id_anggota = a.id_anggota
    JOIN buku b ON p.id_buku = b.id_buku
    ORDER BY p.id_peminjaman DESC
";
$stmtRiwayat = $pdo->query($queryRiwayat);
$daftarPinjam = $stmtRiwayat->fetchAll();

include 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Transaksi Peminjaman Buku</h1>
        <p class="page-subtitle">Pencatatan sirkulasi peminjaman buku dengan pemotongan stok otomatis.</p>
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
    <!-- Kolom Kiri: Formulir Pencatatan Peminjaman -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">📤 Formulir Peminjaman Baru</h2>
        </div>
        <div class="card-body">
            <form action="peminjaman.php" method="POST">
                <div class="form-group">
                    <label for="id_anggota" class="form-label">Nama Anggota Peminjam</label>
                    <select id="id_anggota" name="id_anggota" class="form-select" required>
                        <option value="">-- Pilih Anggota --</option>
                        <?php foreach ($listAnggota as $a): ?>
                            <option value="<?= (int)$a['id_anggota'] ?>" <?= ((int)$id_anggota === (int)$a['id_anggota']) ? 'selected' : '' ?>>
                                <?= escape($a['nomor_anggota']) ?> - <?= escape($a['nama_lengkap']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_buku" class="form-label">Buku yang Dipinjam</label>
                    <select id="id_buku" name="id_buku" class="form-select" required>
                        <option value="">-- Pilih Judul Buku --</option>
                        <?php foreach ($listBuku as $b): ?>
                            <?php $isStokAda = ($b['stok'] > 0); ?>
                            <option value="<?= (int)$b['id_buku'] ?>" 
                                    <?= ((int)$id_buku === (int)$b['id_buku']) ? 'selected' : '' ?>
                                    <?= !$isStokAda ? 'disabled style="color:#94a3b8;"' : '' ?>>
                                <?= escape($b['kode_buku']) ?> - <?= escape($b['judul_buku']) ?> 
                                (<?= $isStokAda ? 'Sisa Stok: ' . (int)$b['stok'] : 'STOK HABIS' ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
                        <input type="date" id="tanggal_pinjam" name="tanggal_pinjam" class="form-control" 
                               required value="<?= escape($tanggal_pinjam) ?>">
                    </div>

                    <div class="form-group">
                        <label for="tanggal_rencana_kembali" class="form-label">Batas Pengembalian</label>
                        <input type="date" id="tanggal_rencana_kembali" name="tanggal_rencana_kembali" class="form-control" 
                               required value="<?= escape($tanggal_rencana_kembali) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="jumlah_pinjam" class="form-label">Jumlah Eksemplar</label>
                    <input type="number" id="jumlah_pinjam" name="jumlah_pinjam" class="form-control" 
                           min="1" max="5" required value="<?= (int)$jumlah_pinjam ?>">
                    <small style="color: #64748b; font-size: 0.775rem;">Maksimal 5 eksemplar per transaksi.</small>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1rem;">
                    Proses Peminjaman
                </button>
            </form>
        </div>
    </div>

    <!-- Kolom Kanan: Tabel Riwayat Peminjaman Keseluruhan -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Daftar Semua Transaksi Peminjaman (<?= count($daftarPinjam) ?>)</h2>
            <a href="pengembalian.php" class="btn btn-sm btn-outline">Proses Pengembalian</a>
        </div>
        <div class="card-body" style="padding: 0;">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Anggota</th>
                            <th>Judul Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Rencana Kembali</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($daftarPinjam)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem; color: #64748b;">
                                    Belum ada transaksi peminjaman buku.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($daftarPinjam as $row): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <strong><?= escape($row['nama_anggota']) ?></strong><br>
                                        <small style="color: #64748b;"><?= escape($row['nomor_anggota']) ?></small>
                                    </td>
                                    <td>
                                        <?= escape($row['judul_buku']) ?><br>
                                        <small style="color: #64748b;"><?= escape($row['kode_buku']) ?></small>
                                    </td>
                                    <td><?= format_tanggal($row['tanggal_pinjam']) ?></td>
                                    <td><?= format_tanggal($row['tanggal_rencana_kembali']) ?></td>
                                    <td style="text-align: center;"><?= (int)$row['jumlah_pinjam'] ?></td>
                                    <td>
                                        <?php if ($row['status'] === 'Dipinjam'): ?>
                                            <span class="badge badge-warning">Dipinjam</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">Dikembalikan</span>
                                        <?php endif; ?>
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
