<?php
/**
 * ==============================================================================
 * MODUL TRANSAKSI PENGEMBALIAN BUKU (VERSI POLOS / VANILLA CSS)
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

<div class="page-header">
    <div>
        <h1 class="page-title">Transaksi Pengembalian Buku</h1>
        <p class="page-subtitle">Proses penerimaan buku kembali, pengecekan keterlambatan, dan pemulihan stok.</p>
    </div>
    <div>
        <a href="peminjaman.php" class="btn btn-outline">Pinjam Buku Baru</a>
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

<!-- Tabel 1: Daftar Buku yang Sedang Dipinjam & Form Pengembalian -->
<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h2 class="card-title">📖 Daftar Buku yang Sedang Dipinjam (<?= count($daftarDipinjam) ?> Transaksi)</h2>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Peminjam</th>
                        <th>Buku & Jumlah</th>
                        <th>Tgl Pinjam</th>
                        <th>Batas Kembali</th>
                        <th>Status Batas Waktu</th>
                        <th style="min-width: 250px;">Aksi Pengembalian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($daftarDipinjam)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2.5rem; color: #64748b;">
                                🎉 Luar biasa! Seluruh buku yang dipinjam saat ini sudah dikembalikan semua.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($daftarDipinjam as $item): ?>
                            <?php 
                                /**
                                 * Logika Keterlambatan untuk Buku yang Masih Dipinjam:
                                 * Bandingkan tanggal rencana kembali dengan tanggal hari ini.
                                 */
                                $infoKeterlambatan = hitung_keterlambatan($item['tanggal_rencana_kembali']);
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <strong><?= escape($item['nama_anggota']) ?></strong><br>
                                    <small style="color: #64748b;"><?= escape($item['nomor_anggota']) ?></small>
                                </td>
                                <td>
                                    <strong><?= escape($item['judul_buku']) ?></strong><br>
                                    <small style="color: #64748b;"><?= escape($item['kode_buku']) ?> | Jml: <?= (int)$item['jumlah_pinjam'] ?> buku</small>
                                </td>
                                <td><?= format_tanggal($item['tanggal_pinjam']) ?></td>
                                <td><?= format_tanggal($item['tanggal_rencana_kembali']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $infoKeterlambatan['badge_class'] ?>">
                                        <?= $infoKeterlambatan['status_teks'] ?>
                                    </span>
                                </td>
                                <td>
                                    <!-- Formulir Inline untuk Memproses Pengembalian Buku -->
                                    <form action="pengembalian.php" method="POST" style="display: flex; gap: 0.4rem; align-items: center;" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin memproses pengembalian buku \'<?= escape(addslashes($item['judul_buku'])) ?>\'?')">
                                        <input type="hidden" name="proses_kembali" value="1">
                                        <input type="hidden" name="id_peminjaman" value="<?= (int)$item['id_peminjaman'] ?>">
                                        
                                        <input type="date" name="tanggal_kembali_aktual" class="form-control" 
                                               style="width: 140px; padding: 0.3rem 0.5rem; font-size: 0.8rem;" 
                                               value="<?= date('Y-m-d') ?>" required title="Tanggal Pengembalian Aktual">
                                        
                                        <button type="submit" class="btn btn-sm btn-success">
                                            Kembalikan
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

<!-- Tabel 2: Riwayat Buku yang Sudah Dikembalikan -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">✅ 10 Riwayat Pengembalian Terakhir</h2>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
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
                            <td colspan="8" style="text-align: center; padding: 1.5rem; color: #64748b;">
                                Belum ada riwayat pengembalian buku.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $rk = 1; foreach ($riwayatKembali as $history): ?>
                            <?php 
                                /**
                                 * Logika Keterlambatan untuk Buku yang Sudah Dikembalikan:
                                 * Bandingkan tanggal rencana kembali dengan tanggal_kembali_aktual.
                                 */
                                $infoHistory = hitung_keterlambatan($history['tanggal_rencana_kembali'], $history['tanggal_kembali_aktual']);
                            ?>
                            <tr>
                                <td><?= $rk++ ?></td>
                                <td>
                                    <strong><?= escape($history['nama_anggota']) ?></strong><br>
                                    <small style="color: #64748b;"><?= escape($history['nomor_anggota']) ?></small>
                                </td>
                                <td>
                                    <?= escape($history['judul_buku']) ?><br>
                                    <small style="color: #64748b;"><?= (int)$history['jumlah_pinjam'] ?> buku</small>
                                </td>
                                <td><?= format_tanggal($history['tanggal_pinjam']) ?></td>
                                <td><?= format_tanggal($history['tanggal_rencana_kembali']) ?></td>
                                <td><strong><?= format_tanggal($history['tanggal_kembali_aktual']) ?></strong></td>
                                <td>
                                    <span class="badge badge-<?= $infoHistory['badge_class'] ?>">
                                        <?= $infoHistory['status_teks'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-success">Dikembalikan</span>
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
