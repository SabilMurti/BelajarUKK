<?php
require_once 'versi-polos/config/database.php';
echo "Connected successfully to PDO!\n";

echo "--- Test 1: Index Stats ---\n";
$statBuku = $pdo->query("SELECT COUNT(*) AS total_judul, COALESCE(SUM(stok), 0) AS total_stok FROM buku")->fetch();
echo "Buku: Judul={$statBuku['total_judul']}, Stok={$statBuku['total_stok']}\n";

$statAnggota = $pdo->query("SELECT COUNT(*) AS total_anggota FROM anggota")->fetch();
echo "Anggota: Total={$statAnggota['total_anggota']}\n";

$statTransaksi = $pdo->query("SELECT COUNT(*) AS total_transaksi FROM peminjaman")->fetch();
echo "Transaksi: Total={$statTransaksi['total_transaksi']}\n";

$statDipinjam = $pdo->query("SELECT COALESCE(SUM(jumlah_pinjam), 0) AS total_dipinjam FROM peminjaman WHERE status = 'Dipinjam'")->fetch();
echo "Dipinjam: Total={$statDipinjam['total_dipinjam']}\n";

echo "--- Test 2: Buku Search ---\n";
$stmtBuku = $pdo->prepare("SELECT * FROM buku WHERE judul_buku LIKE :kw1 OR pengarang LIKE :kw2 OR kode_buku LIKE :kw3 ORDER BY id_buku DESC");
$stmtBuku->execute([':kw1' => '%PHP%', ':kw2' => '%PHP%', ':kw3' => '%PHP%']);
$hasilBuku = $stmtBuku->fetchAll();
echo "Buku search count: " . count($hasilBuku) . "\n";

echo "--- Test 3: Anggota Search ---\n";
$stmtAnggota = $pdo->prepare("SELECT * FROM anggota WHERE nama_lengkap LIKE :kw1 OR nomor_anggota LIKE :kw2 ORDER BY id_anggota DESC");
$stmtAnggota->execute([':kw1' => '%Ahmad%', ':kw2' => '%Ahmad%']);
$hasilAnggota = $stmtAnggota->fetchAll();
echo "Anggota search count: " . count($hasilAnggota) . "\n";

echo "--- Test 4: Peminjaman Transaction Simulation ---\n";
$pdo->beginTransaction();
$id_buku = 1;
$jumlah_pinjam = 1;
$id_anggota = 1;
$tanggal_pinjam = date('Y-m-d');
$tanggal_rencana_kembali = date('Y-m-d', strtotime('+7 days'));

$stmtCek = $pdo->prepare("SELECT stok, judul_buku FROM buku WHERE id_buku = :id FOR UPDATE");
$stmtCek->execute([':id' => $id_buku]);
$buku = $stmtCek->fetch();
echo "Stok sebelum pinjam: " . $buku['stok'] . "\n";

$stmtPinjam = $pdo->prepare("INSERT INTO peminjaman (id_anggota, id_buku, tanggal_pinjam, tanggal_rencana_kembali, jumlah_pinjam, status) VALUES (:id_anggota, :id_buku, :tgl_pinjam, :tgl_kembali, :jumlah, 'Dipinjam')");
$stmtPinjam->execute([
    ':id_anggota' => $id_anggota,
    ':id_buku' => $id_buku,
    ':tgl_pinjam' => $tanggal_pinjam,
    ':tgl_kembali' => $tanggal_rencana_kembali,
    ':jumlah' => $jumlah_pinjam
]);
$newPinjamId = $pdo->lastInsertId();

$stmtKurang = $pdo->prepare("UPDATE buku SET stok = stok - :jumlah WHERE id_buku = :id");
$stmtKurang->execute([':jumlah' => $jumlah_pinjam, ':id' => $id_buku]);
$pdo->commit();
echo "Peminjaman berhasil, id={$newPinjamId}\n";

$stmtCek2 = $pdo->prepare("SELECT stok FROM buku WHERE id_buku = :id");
$stmtCek2->execute([':id' => $id_buku]);
echo "Stok sesudah pinjam: " . $stmtCek2->fetchColumn() . "\n";

echo "--- Test 5: Pengembalian Transaction Simulation ---\n";
$pdo->beginTransaction();
$stmtKembali = $pdo->prepare("UPDATE peminjaman SET status = 'Dikembalikan', tanggal_kembali_aktual = :tgl WHERE id_peminjaman = :id");
$stmtKembali->execute([':tgl' => date('Y-m-d'), ':id' => $newPinjamId]);

$stmtTambah = $pdo->prepare("UPDATE buku SET stok = stok + :jumlah WHERE id_buku = :id");
$stmtTambah->execute([':jumlah' => $jumlah_pinjam, ':id' => $id_buku]);
$pdo->commit();

$stmtCek3 = $pdo->prepare("SELECT stok FROM buku WHERE id_buku = :id");
$stmtCek3->execute([':id' => $id_buku]);
echo "Stok sesudah dikembalikan: " . $stmtCek3->fetchColumn() . "\n";

echo "--- Test 6: Laporan Queries ---\n";
$laporan1 = $pdo->query("SELECT * FROM buku ORDER BY id_buku ASC")->fetchAll();
echo "Laporan Buku count: " . count($laporan1) . "\n";

$laporan2 = $pdo->query("SELECT * FROM anggota ORDER BY id_anggota ASC")->fetchAll();
echo "Laporan Anggota count: " . count($laporan2) . "\n";

$laporan3 = $pdo->query("
    SELECT p.*, a.nama_lengkap AS nama_anggota, a.nomor_anggota, b.judul_buku, b.kode_buku
    FROM peminjaman p
    JOIN anggota a ON p.id_anggota = a.id_anggota
    JOIN buku b ON p.id_buku = b.id_buku
    ORDER BY p.id_peminjaman DESC
")->fetchAll();
echo "Laporan Peminjaman count: " . count($laporan3) . "\n";

echo "All tests passed cleanly!\n";
