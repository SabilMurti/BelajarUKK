<?php
/**
 * ==============================================================================
 * MODUL REKAPITULASI & LAPORAN PERPUSTAKAAN (VERSI POLOS / VANILLA CSS)
 * ==============================================================================
 * Fitur:
 * 1. Pilihan 3 jenis laporan:
 *    - Laporan Data Koleksi Buku
 *    - Laporan Data Anggota (Filter Tanggal Pendaftaran)
 *    - Laporan Transaksi Peminjaman & Pengembalian (Filter Rentang Tanggal Pinjam)
 * 2. Filter periode tanggal (tanggal_mulai s/d tanggal_selesai).
 * 3. Tombol cetak via window.print() dengan style @media print yang rapi
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

<!-- Header Halaman (Disembunyikan saat dicetak) -->
<div class="page-header no-print">
    <div>
        <h1 class="page-title">Rekapitulasi & Laporan Perpustakaan</h1>
        <p class="page-subtitle">Pilih jenis laporan, saring berdasarkan rentang tanggal, dan cetak dokumen resmi.</p>
    </div>
    <div class="page-actions">
        <button type="button" class="btn btn-primary" onclick="window.print()">
            🖨️ Cetak Laporan (Print)
        </button>
    </div>
</div>

<!-- Card Filter & Tab Laporan (Disembunyikan saat dicetak) -->
<div class="card no-print" style="margin-bottom: 1.5rem;">
    <div class="card-header">
        <h2 class="card-title">Filter Laporan</h2>
    </div>
    <div class="card-body">
        <form action="laporan.php" method="GET" class="filter-form">
            <div class="grid grid-cols-4" style="align-items: flex-end; gap: 1rem;">
                <!-- Pilihan Jenis Laporan -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="jenis" class="form-label">Kategori Laporan</label>
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

                <!-- Filter Tanggal Mulai -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" class="form-control" 
                           value="<?= escape($tanggal_mulai) ?>" 
                           <?= ($jenis_laporan === 'buku') ? 'disabled style="background:#f1f5f9;"' : '' ?>>
                </div>

                <!-- Filter Tanggal Selesai -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                    <input type="date" id="tanggal_selesai" name="tanggal_selesai" class="form-control" 
                           value="<?= escape($tanggal_selesai) ?>"
                           <?= ($jenis_laporan === 'buku') ? 'disabled style="background:#f1f5f9;"' : '' ?>>
                </div>

                <!-- Tombol Filter -->
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Filter</button>
                    <?php if (!empty($tanggal_mulai) || !empty($tanggal_selesai)): ?>
                        <a href="laporan.php?jenis=<?= escape($jenis_laporan) ?>" class="btn btn-outline">Reset</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================================================
     KOP SURAT RESMI (TAMPIL HANYA SAAT CETAK / PRINT MEDIA)
     ============================================================================== -->
<div class="print-header">
    <h2 style="margin: 0; font-size: 16pt; text-transform: uppercase;">PERPUSTAKAAN SMK PPLG JAYA</h2>
    <p style="margin: 2px 0; font-size: 10pt;">Jl. Pendidikan Teknologi Informasi No. 404, Jakarta | Telp: (021) 7654321</p>
    <hr style="border: 0; border-top: 2px solid #000; margin: 8px 0 16px 0;">
    <h3 style="margin: 0; font-size: 13pt; text-decoration: underline;"><?= escape($judulDokumen) ?></h3>
    <p style="margin: 4px 0 12px 0; font-size: 9pt;">
        <?php if (!empty($tanggal_mulai) && !empty($tanggal_selesai)): ?>
            Periode: <?= format_tanggal($tanggal_mulai) ?> s/d <?= format_tanggal($tanggal_selesai) ?>
        <?php else: ?>
            Periode: Seluruh Data Sampai Saat Ini
        <?php endif; ?>
    </p>
</div>

<!-- Tampilan Laporan -->
<div class="card">
    <div class="card-header no-print">
        <h2 class="card-title"><?= escape($judulDokumen) ?> (Total: <?= count($dataLaporan) ?>)</h2>
    </div>
    <div class="card-body" style="padding: 0;">
        <div class="table-responsive">

            <!-- 1. TABEL LAPORAN DATA BUKU -->
            <?php if ($jenis_laporan === 'buku'): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Buku</th>
                            <th>Judul Buku</th>
                            <th>Pengarang</th>
                            <th>Penerbit</th>
                            <th>Tahun</th>
                            <th>Kategori</th>
                            <th>Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dataLaporan)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center; padding: 2rem;">Tidak ada data buku yang tersedia.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($dataLaporan as $b): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><code><?= escape($b['kode_buku']) ?></code></td>
                                    <td><strong><?= escape($b['judul_buku']) ?></strong></td>
                                    <td><?= escape($b['pengarang']) ?></td>
                                    <td><?= escape($b['penerbit']) ?></td>
                                    <td><?= escape($b['tahun_terbit']) ?></td>
                                    <td><?= escape($b['kategori']) ?></td>
                                    <td style="text-align: center;"><strong><?= (int)$b['stok'] ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

            <!-- 2. TABEL LAPORAN DATA ANGGOTA -->
            <?php elseif ($jenis_laporan === 'anggota'): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
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
                                <td colspan="7" style="text-align: center; padding: 2rem;">Tidak ada data anggota dalam rentang tanggal tersebut.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($dataLaporan as $a): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><code><?= escape($a['nomor_anggota']) ?></code></td>
                                    <td><strong><?= escape($a['nama_lengkap']) ?></strong></td>
                                    <td><?= escape($a['jenis_kelamin']) ?></td>
                                    <td><?= escape($a['no_telepon']) ?></td>
                                    <td><?= format_tanggal($a['tanggal_pendaftaran']) ?></td>
                                    <td><?= escape($a['alamat']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>

            <!-- 3. TABEL LAPORAN PEMINJAMAN & PENGEMBALIAN -->
            <?php else: ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Peminjam</th>
                            <th>Buku</th>
                            <th>Tgl Pinjam</th>
                            <th>Rencana Kembali</th>
                            <th>Tgl Kembali Aktual</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                            <th>Keterangan Keterlambatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($dataLaporan)): ?>
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 2rem;">Tidak ada transaksi peminjaman pada periode ini.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($dataLaporan as $p): ?>
                                <?php 
                                    // Hitung keterlambatan berdasarkan status transaksi
                                    $info = hitung_keterlambatan($p['tanggal_rencana_kembali'], $p['tanggal_kembali_aktual']);
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <strong><?= escape($p['nama_anggota']) ?></strong><br>
                                        <small><?= escape($p['nomor_anggota']) ?></small>
                                    </td>
                                    <td>
                                        <?= escape($p['judul_buku']) ?><br>
                                        <small><?= escape($p['kode_buku']) ?></small>
                                    </td>
                                    <td><?= format_tanggal($p['tanggal_pinjam']) ?></td>
                                    <td><?= format_tanggal($p['tanggal_rencana_kembali']) ?></td>
                                    <td><?= format_tanggal($p['tanggal_kembali_aktual']) ?></td>
                                    <td style="text-align: center;"><?= (int)$p['jumlah_pinjam'] ?></td>
                                    <td>
                                        <span class="badge badge-<?= ($p['status'] === 'Dipinjam') ? 'warning' : 'success' ?>">
                                            <?= escape($p['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $info['badge_class'] ?>">
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
     LEMBAR TANDA TANGAN RESMI (DITAMPILKAN SAAT CETAK / PRINT MEDIA)
     ============================================================================== -->
<div class="print-signature">
    <div class="sign-box">
        <p>Mengetahui,</p>
        <p><strong>Kepala Perpustakaan</strong></p>
        <div style="height: 60px;"></div>
        <p style="text-decoration: underline; font-weight: bold;">Drs. H. Hendra Wijaya, M.Pd.</p>
        <p style="font-size: 9pt;">NIP. 19780512 200212 1 002</p>
    </div>

    <div class="sign-box">
        <p>Dicetak pada: <?= date('d/m/Y') ?></p>
        <p><strong>Petugas Perpustakaan</strong></p>
        <div style="height: 60px;"></div>
        <p style="text-decoration: underline; font-weight: bold;"><?= escape($_SESSION['nama_lengkap']) ?></p>
        <p style="font-size: 9pt;">Petugas Administrator</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
