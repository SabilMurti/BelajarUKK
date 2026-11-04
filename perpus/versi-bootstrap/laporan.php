<?php
/**
 * ==============================================================================
 * MODUL REKAPITULASI & LAPORAN PERPUSTAKAAN (VERSI BOOTSTRAP 5)
 * ==============================================================================
 * Fitur:
 * 1. Pilihan 3 jenis laporan:
 *    - Laporan Data Koleksi Buku
 *    - Laporan Data Anggota (Filter Tanggal Pendaftaran)
 *    - Laporan Transaksi Peminjaman & Pengembalian (Filter Rentang Tanggal Pinjam)
 * 2. Filter periode tanggal (tanggal_mulai s/d tanggal_selesai).
 * 3. Tombol cetak via window.print() dengan kelas Bootstrap d-print-none
 *    (menyembunyikan navigasi, filter, tombol, dan memunculkan kop resmi).
 * ==============================================================================
 */

require_once 'config/database.php';
require_once 'config/auth.php';

$page_title = "Laporan Perpustakaan";

// 1. Parameter Filter
$jenis_laporan   = $_GET['jenis'] ?? 'peminjaman';
$tanggal_mulai   = $_GET['tanggal_mulai'] ?? '';
$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';

// 2. Eksekusi Query Sesuai Jenis Laporan
$dataLaporan = [];
$judulDokumen = "";

if ($jenis_laporan === 'buku') {
    $judulDokumen = "LAPORAN DATA KOLEKSI BUKU PERPUSTAKAAN";
    $stmt = $pdo->query("SELECT * FROM buku ORDER BY id_buku ASC");
    $dataLaporan = $stmt->fetchAll();

} elseif ($jenis_laporan === 'anggota') {
    $judulDokumen = "LAPORAN DATA ANGGOTA PERPUSTAKAAN";
    
    if (!empty($tanggal_mulai) && !empty($tanggal_selesai)) {
        $stmt = $pdo->prepare("
            SELECT * FROM anggota 
            WHERE tanggal_pendaftaran BETWEEN :tgl_awal AND :tgl_akhir
            ORDER BY tanggal_pendaftaran ASC
        ");
        $stmt->execute([':tgl_awal' => $tanggal_mulai, ':tgl_akhir' => $tanggal_selesai]);
    } else {
        $stmt = $pdo->query("SELECT * FROM anggota ORDER BY id_anggota ASC");
    }
    $dataLaporan = $stmt->fetchAll();

} else { // default: peminjaman
    $jenis_laporan = 'peminjaman';
    $judulDokumen = "LAPORAN TRANSAKSI PEMINJAMAN & PENGEMBALIAN BUKU";

    if (!empty($tanggal_mulai) && !empty($tanggal_selesai)) {
        $stmt = $pdo->prepare("
            SELECT p.*, a.nama_lengkap AS nama_anggota, a.nomor_anggota, b.judul_buku, b.kode_buku
            FROM peminjaman p
            JOIN anggota a ON p.id_anggota = a.id_anggota
            JOIN buku b ON p.id_buku = b.id_buku
            WHERE p.tanggal_pinjam BETWEEN :tgl_awal AND :tgl_akhir
            ORDER BY p.tanggal_pinjam ASC
        ");
        $stmt->execute([':tgl_awal' => $tanggal_mulai, ':tgl_akhir' => $tanggal_selesai]);
    } else {
        $stmt = $pdo->query("
            SELECT p.*, a.nama_lengkap AS nama_anggota, a.nomor_anggota, b.judul_buku, b.kode_buku
            FROM peminjaman p
            JOIN anggota a ON p.id_anggota = a.id_anggota
            JOIN buku b ON p.id_buku = b.id_buku
            ORDER BY p.id_peminjaman DESC
        ");
    }
    $dataLaporan = $stmt->fetchAll();
}

include 'includes/header.php';
?>

<!-- Header Halaman (Disembunyikan saat cetak) -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 d-print-none">
    <div>
        <h2 class="fw-bold text-dark mb-1">Rekapitulasi & Laporan Perpustakaan</h2>
        <p class="text-muted mb-0">Pilih jenis laporan, saring berdasarkan rentang tanggal, dan cetak dokumen resmi.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary shadow-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Cetak Dokumen (Print)
        </button>
    </div>
</div>

<!-- Card Filter (Disembunyikan saat cetak) -->
<div class="card shadow-sm border-0 mb-4 d-print-none">
    <div class="card-header bg-white border-0 pt-3">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="bi bi-funnel text-primary me-1"></i> Parameter Filter Laporan
        </h5>
    </div>
    <div class="card-body">
        <form action="laporan.php" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="jenis" class="form-label small fw-semibold">Kategori Laporan</label>
                    <select id="jenis" name="jenis" class="form-select" onchange="this.form.submit()">
                        <option value="peminjaman" <?= ($jenis_laporan === 'peminjaman') ? 'selected' : '' ?>>
                            Sirkulasi Peminjaman & Pengembalian
                        </option>
                        <option value="buku" <?= ($jenis_laporan === 'buku') ? 'selected' : '' ?>>
                            Katalog Data Buku
                        </option>
                        <option value="anggota" <?= ($jenis_laporan === 'anggota') ? 'selected' : '' ?>>
                            Daftar Anggota Perpustakaan
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="tanggal_mulai" class="form-label small fw-semibold">Tanggal Mulai</label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control" 
                           value="<?= escape($tanggal_mulai) ?>" 
                           <?= ($jenis_laporan === 'buku') ? 'disabled' : '' ?>>
                </div>

                <div class="col-md-3">
                    <label for="tanggal_selesai" class="form-label small fw-semibold">Tanggal Selesai</label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="form-control" 
                           value="<?= escape($tanggal_selesai) ?>"
                           <?= ($jenis_laporan === 'buku') ? 'disabled' : '' ?>>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <?php if (!empty($tanggal_mulai) || !empty($tanggal_selesai)): ?>
                        <a href="laporan.php?jenis=<?= escape($jenis_laporan) ?>" class="btn btn-outline-secondary" title="Reset Filter">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================================================
     KOP SURAT RESMI (HANYA MUNCUL DI CETAKAN)
     ============================================================================== -->
<div class="d-none d-print-block text-center border-bottom border-dark pb-2 mb-4">
    <h3 class="fw-bold text-uppercase mb-0">PERPUSTAKAAN SMK PPLG JAYA</h3>
    <p class="small mb-1">Jl. Pendidikan Teknologi Informasi No. 404, Jakarta | Telp: (021) 7654321 | Email: perpus@smkpplg.sch.id</p>
    <div class="border-top border-dark border-2 my-2"></div>
    <h4 class="fw-bold text-decoration-underline mb-1"><?= escape($judulDokumen) ?></h4>
    <p class="small text-muted mb-0">
        <?php if (!empty($tanggal_mulai) && !empty($tanggal_selesai)): ?>
            Periode: <?= format_tanggal($tanggal_mulai) ?> s/d <?= format_tanggal($tanggal_selesai) ?>
        <?php else: ?>
            Periode: Seluruh Data Tercatat Sampai Saat Ini
        <?php endif; ?>
    </p>
</div>

<!-- Card Tabel Laporan -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center d-print-none">
        <h5 class="fw-bold mb-0 text-dark">
            <?= escape($judulDokumen) ?> <span class="badge bg-secondary rounded-pill"><?= count($dataLaporan) ?> Data</span>
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">

            <!-- 1. TABEL LAPORAN DATA BUKU -->
            <?php if ($jenis_laporan === 'buku'): ?>
                <table class="table table-bordered table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Kode Buku</th>
                            <th>Judul Buku</th>
                            <th>Pengarang</th>
                            <th>Penerbit</th>
                            <th class="text-center">Tahun</th>
                            <th>Kategori</th>
                            <th class="text-center">Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dataLaporan)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">Tidak ada data buku yang tersedia.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($dataLaporan as $b): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><span class="font-monospace fw-bold"><?= escape($b['kode_buku']) ?></span></td>
                                    <td><strong><?= escape($b['judul_buku']) ?></strong></td>
                                    <td><?= escape($b['pengarang']) ?></td>
                                    <td><?= escape($b['penerbit']) ?></td>
                                    <td class="text-center"><?= escape($b['tahun_terbit']) ?></td>
                                    <td><?= escape($b['kategori']) ?></td>
                                    <td class="text-center fw-bold"><?= (int)$b['stok'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

            <!-- 2. TABEL LAPORAN DATA ANGGOTA -->
            <?php elseif ($jenis_laporan === 'anggota'): ?>
                <table class="table table-bordered table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>Nomor Anggota</th>
                            <th>Nama Lengkap</th>
                            <th>Jenis Kelamin</th>
                            <th>No. Telepon</th>
                            <th>Tanggal Daftar</th>
                            <th>Alamat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dataLaporan)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Tidak ada data anggota dalam rentang tanggal tersebut.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($dataLaporan as $a): ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td><span class="font-monospace fw-bold"><?= escape($a['nomor_anggota']) ?></span></td>
                                    <td><strong><?= escape($a['nama_lengkap']) ?></strong></td>
                                    <td><?= escape($a['jenis_kelamin']) ?></td>
                                    <td><?= escape($a['no_telepon']) ?></td>
                                    <td><?= format_tanggal($a['tanggal_pendaftaran']) ?></td>
                                    <td><small><?= escape($a['alamat']) ?></small></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

            <!-- 3. TABEL LAPORAN PEMINJAMAN & PENGEMBALIAN -->
            <?php else: ?>
                <table class="table table-bordered table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 40px;">No</th>
                            <th>Peminjam</th>
                            <th>Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Batas Kembali</th>
                            <th>Kembali Aktual</th>
                            <th class="text-center">Jml</th>
                            <th>Status</th>
                            <th>Keterangan Keterlambatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dataLaporan)): ?>
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">Tidak ada transaksi peminjaman pada periode ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($dataLaporan as $p): ?>
                                <?php 
                                    $info = hitung_keterlambatan($p['tanggal_rencana_kembali'], $p['tanggal_kembali_aktual']);
                                ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td>
                                        <div class="fw-bold"><?= escape($p['nama_anggota']) ?></div>
                                        <small class="text-muted"><?= escape($p['nomor_anggota']) ?></small>
                                    </td>
                                    <td>
                                        <div><?= escape($p['judul_buku']) ?></div>
                                        <small class="text-muted font-monospace"><?= escape($p['kode_buku']) ?></small>
                                    </td>
                                    <td><?= format_tanggal($p['tanggal_pinjam']) ?></td>
                                    <td><?= format_tanggal($p['tanggal_rencana_kembali']) ?></td>
                                    <td><?= format_tanggal($p['tanggal_kembali_aktual']) ?></td>
                                    <td class="text-center fw-bold"><?= (int)$p['jumlah_pinjam'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= ($p['status'] === 'Dipinjam') ? 'warning text-dark' : 'success' ?>">
                                            <?= escape($p['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $info['badge_class'] ?>">
                                            <?= $info['status_teks'] ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        </div>
    </div>
</div>

<!-- ==============================================================================
     LEMBAR TANDA TANGAN DOKUMEN CETAK RESMI
     ============================================================================== -->
<div class="d-none d-print-flex justify-content-between mt-5 pt-4">
    <div class="text-center" style="width: 220px;">
        <p class="mb-1">Mengetahui,</p>
        <p class="fw-bold mb-5">Kepala Perpustakaan</p>
        <p class="text-decoration-underline fw-bold mb-0">Drs. H. Hendra Wijaya, M.Pd.</p>
        <small class="text-muted">NIP. 19780512 200212 1 002</small>
    </div>

    <div class="text-center" style="width: 220px;">
        <p class="mb-1">Dicetak pada: <?= date('d/m/Y') ?></p>
        <p class="fw-bold mb-5">Petugas Perpustakaan</p>
        <p class="text-decoration-underline fw-bold mb-0"><?= escape($_SESSION['nama_lengkap']) ?></p>
        <small class="text-muted">Petugas Administrator</small>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
