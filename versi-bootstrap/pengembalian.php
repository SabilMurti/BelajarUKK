<?php
/**
 * ==============================================================================
 * MODUL TRANSAKSI PENGEMBALIAN BUKU (VERSI BOOTSTRAP 5)
 * ==============================================================================
 * Konsep Penting UKK:
 * 1. Database Transaction (Atomicity):
 *    - Update status peminjaman menjadi 'Dikembalikan'.
 *    - Catat tanggal pengembalian aktual.
 *    - Tambahkan kembali stok buku yang sebelumnya dipinjam.
 * 2. Perhitungan Keterlambatan:
 *    - Menghitung selisih antara batas tanggal rencana kembali dengan tanggal pengembalian.
 *    - Menampilkan informasi: "Terlambat X Hari" atau "Tepat Waktu".
 * ==============================================================================
 */

require_once 'config/database.php';
require_once 'config/auth.php';

$page_title = "Transaksi Pengembalian Buku";
$sukses = '';
$error  = '';

// 1. Tangani Pesan dari Redirect
if (isset($_GET['pesan']) && $_GET['pesan'] === 'sukses_kembali') {
    $sukses = "Buku berhasil dikembalikan ke perpustakaan! Stok buku telah ditambahkan kembali.";
}

// 2. Proses Pengembalian Buku (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proses_kembali'])) {
    $id_peminjaman          = (int)($_POST['id_peminjaman'] ?? 0);
    $tanggal_kembali_aktual = trim($_POST['tanggal_kembali_aktual'] ?? date('Y-m-d'));

    if (empty($id_peminjaman) || empty($tanggal_kembali_aktual)) {
        $error = "Data pengembalian tidak valid atau tanggal belum diisi!";
    } else {
        /**
         * ==============================================================================
         * DATABASE TRANSACTION PADA PENGEMBALIAN
         * ==============================================================================
         * Jika proses penambahan stok buku gagal, maka status peminjaman TIDAK BOLEH
         * berubah menjadi 'Dikembalikan'. Transaksi ini memastikan keduanya berhasil
         * bersama-sama secara atomik.
         * ==============================================================================
         */
        $pdo->beginTransaction();

        try {
            // A. Ambil dan kunci data peminjaman yang masih aktif
            $stmtCari = $pdo->prepare("
                SELECT * FROM peminjaman 
                WHERE id_peminjaman = :id AND status = 'Dipinjam' 
                FOR UPDATE
            ");
            $stmtCari->execute([':id' => $id_peminjaman]);
            $pinjam = $stmtCari->fetch();

            if (!$pinjam) {
                throw new Exception("Transaksi peminjaman tidak ditemukan atau buku sudah dikembalikan sebelumnya!");
            }

            // Validasi tanggal kembali tidak boleh sebelum tanggal pinjam
            if ($tanggal_kembali_aktual < $pinjam['tanggal_pinjam']) {
                throw new Exception("Tanggal kembali aktual tidak boleh lebih awal dari tanggal pinjam (" . format_tanggal($pinjam['tanggal_pinjam']) . ")!");
            }

            // B. Perbarui status peminjaman menjadi 'Dikembalikan'
            $stmtUpdatePinjam = $pdo->prepare("
                UPDATE peminjaman 
                SET status = 'Dikembalikan', tanggal_kembali_aktual = :tgl_kembali 
                WHERE id_peminjaman = :id
            ");
            $stmtUpdatePinjam->execute([
                ':tgl_kembali' => $tanggal_kembali_aktual,
                ':id'          => $id_peminjaman
            ]);

            // C. Tambahkan kembali stok buku yang dipinjam
            $stmtTambahStok = $pdo->prepare("
                UPDATE buku 
                SET stok = stok + :jumlah 
                WHERE id_buku = :id_buku
            ");
            $stmtTambahStok->execute([
                ':jumlah'  => $pinjam['jumlah_pinjam'],
                ':id_buku' => $pinjam['id_buku']
            ]);

            // D. Komit Transaksi
            $pdo->commit();

            header("Location: pengembalian.php?pesan=sukses_kembali");
            exit;

        } catch (Exception $e) {
            // E. Batalkan semua operasi jika terjadi kegagalan
            $pdo->rollBack();
            $error = $e->getMessage();
        }
    }
}

// 3. Ambil Daftar Buku yang Masih Dipinjam (Status = 'Dipinjam')
$queryDipinjam = "
    SELECT p.*, a.nama_lengkap AS nama_anggota, a.nomor_anggota, b.judul_buku, b.kode_buku
    FROM peminjaman p
    JOIN anggota a ON p.id_anggota = a.id_anggota
    JOIN buku b ON p.id_buku = b.id_buku
    WHERE p.status = 'Dipinjam'
    ORDER BY p.tanggal_rencana_kembali ASC
";
$stmtDipinjam = $pdo->query($queryDipinjam);
$daftarDipinjam = $stmtDipinjam->fetchAll();

// 4. Ambil 10 Riwayat Pengembalian Terakhir (Status = 'Dikembalikan')
$queryRiwayatKembali = "
    SELECT p.*, a.nama_lengkap AS nama_anggota, a.nomor_anggota, b.judul_buku, b.kode_buku
    FROM peminjaman p
    JOIN anggota a ON p.id_anggota = a.id_anggota
    JOIN buku b ON p.id_buku = b.id_buku
    WHERE p.status = 'Dikembalikan'
    ORDER BY p.tanggal_kembali_aktual DESC, p.id_peminjaman DESC
    LIMIT 10
";
$stmtRiwayatKembali = $pdo->query($queryRiwayatKembali);
$riwayatKembali = $stmtRiwayatKembali->fetchAll();

include 'includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h2 class="fw-bold text-dark mb-1">Transaksi Pengembalian Buku</h2>
        <p class="text-muted mb-0">Proses penerimaan buku kembali, pengecekan keterlambatan, dan pemulihan stok.</p>
    </div>
    <div>
        <a href="peminjaman.php" class="btn btn-outline-primary shadow-sm">
            <i class="bi bi-arrow-up-right-circle me-1"></i> Pinjam Buku Baru
        </a>
    </div>
</div>

<?php if (!empty($sukses)): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-check-circle-fill flex-shrink-0"></i>
        <div><?= escape($sukses) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
        <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
        <div><?= escape($error) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- Tabel 1: Daftar Buku yang Sedang Dipinjam -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-0 pt-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-clock-history text-warning me-1"></i>
            Daftar Buku yang Sedang Dipinjam <span class="badge bg-warning text-dark rounded-pill"><?= count($daftarDipinjam) ?></span>
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Peminjam</th>
                        <th>Buku & Jumlah</th>
                        <th>Tgl Pinjam</th>
                        <th>Batas Kembali</th>
                        <th>Status Batas Waktu</th>
                        <th style="min-width: 260px;">Aksi Pengembalian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftarDipinjam)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-check2-circle text-success fs-1 d-block mb-2"></i>
                                Luar biasa! Seluruh buku yang dipinjam saat ini sudah dikembalikan semua.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($daftarDipinjam as $item): ?>
                            <?php 
                                $infoKeterlambatan = hitung_keterlambatan($item['tanggal_rencana_kembali']);
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <div class="fw-bold"><?= escape($item['nama_anggota']) ?></div>
                                    <small class="text-muted"><?= escape($item['nomor_anggota']) ?></small>
                                </td>
                                <td>
                                    <div><?= escape($item['judul_buku']) ?></div>
                                    <small class="text-muted font-monospace"><?= escape($item['kode_buku']) ?> | Jml: <?= (int)$item['jumlah_pinjam'] ?> buku</small>
                                </td>
                                <td><?= format_tanggal($item['tanggal_pinjam']) ?></td>
                                <td><?= format_tanggal($item['tanggal_rencana_kembali']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $infoKeterlambatan['badge_class'] ?>">
                                        <?= $infoKeterlambatan['status_teks'] ?>
                                    </span>
                                </td>
                                <td>
                                    <form action="pengembalian.php" method="POST" class="d-flex gap-2" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin memproses pengembalian buku \'<?= escape(addslashes($item['judul_buku'])) ?>\'?')">
                                        <input type="hidden" name="proses_kembali" value="1">
                                        <input type="hidden" name="id_peminjaman" value="<?= (int)$item['id_peminjaman'] ?>">
                                        
                                        <input type="date" name="tanggal_kembali_aktual" class="form-control form-control-sm" 
                                               style="width: 140px;" value="<?= date('Y-m-d') ?>" required title="Tanggal Pengembalian">
                                        
                                        <button type="submit" class="btn btn-sm btn-success text-nowrap">
                                            <i class="bi bi-box-arrow-in-left me-1"></i> Kembalikan
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Tabel 2: Riwayat Pengembalian Terakhir -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-0 pt-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-check2-all text-success me-1"></i>
            10 Riwayat Pengembalian Terakhir
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Peminjam</th>
                        <th>Judul Buku</th>
                        <th>Tgl Pinjam</th>
                        <th>Batas Kembali</th>
                        <th>Tgl Kembali Aktual</th>
                        <th>Ketepatan Waktu</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($riwayatKembali)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                Belum ada riwayat pengembalian buku.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $rk = 1; foreach ($riwayatKembali as $history): ?>
                            <?php 
                                $infoHistory = hitung_keterlambatan($history['tanggal_rencana_kembali'], $history['tanggal_kembali_aktual']);
                            ?>
                            <tr>
                                <td><?= $rk++ ?></td>
                                <td>
                                    <div class="fw-bold"><?= escape($history['nama_anggota']) ?></div>
                                    <small class="text-muted"><?= escape($history['nomor_anggota']) ?></small>
                                </td>
                                <td>
                                    <div><?= escape($history['judul_buku']) ?></div>
                                    <small class="text-muted"><?= (int)$history['jumlah_pinjam'] ?> buku</small>
                                </td>
                                <td><?= format_tanggal($history['tanggal_pinjam']) ?></td>
                                <td><?= format_tanggal($history['tanggal_rencana_kembali']) ?></td>
                                <td><strong><?= format_tanggal($history['tanggal_kembali_aktual']) ?></strong></td>
                                <td>
                                    <span class="badge bg-<?= $infoHistory['badge_class'] ?>">
                                        <?= $infoHistory['status_teks'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Dikembalikan</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
