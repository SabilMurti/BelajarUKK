<?php
/**
 * ==============================================================================
 * MODUL KONEKSI DATABASE (PHP PDO)
 * ==============================================================================
 * Proyek  : Aplikasi Manajemen Perpustakaan Berbasis Web (UKK PPLG)
 * Fitur   : Menggunakan PDO (PHP Data Objects) untuk koneksi aman & terstandar.
 * 
 * Keunggulan PDO yang dipelajari dalam UKK:
 * 1. Prepared Statements: Mencegah SQL Injection secara menyeluruh dengan
 *    memisahkan instruksi SQL dari data masukan pengguna.
 * 2. Error Mode Exception: Mengubah kesalahan query SQL menjadi PDOException
 *    sehingga dapat ditangkap menggunakan blok try-catch.
 * 3. Database Transaction: Memungkinkan eksekusi multi-query (seperti
 *    peminjaman dan update stok) secara atomik (semua berhasil atau dibatalkan).
 * ==============================================================================
 */

// Pengaturan kredensial database MySQL (Default XAMPP)
$db_host = '127.0.0.1';
$db_port = '3306';
$db_name = 'db_perpustakaan';
$db_user = 'root';
$db_pass = ''; // Default instalasi XAMPP tanpa password

try {
    // DSN (Data Source Name)
    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";

    // Konfigurasi atribut PDO
    $options = [
        // Mode pelaporan error: Selalu lemparkan Exception saat terjadi query error
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        
        // Mode pengambilan data: Otomatis mengembalikan array asosiatif (nama_kolom => nilai)
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        
        // Nonaktifkan emulasi prepared statement untuk keamanan maksimal (menggunakan native driver)
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // Inisialisasi objek PDO
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);

} catch (PDOException $e) {
    // Tangani kegagalan koneksi agar tidak menampilkan kredensial ke publik
    die("<div class='container my-5'><div class='alert alert-danger shadow-sm'>"
        . "<h4 class='alert-heading'>Koneksi Database Gagal!</h4>"
        . "<p>Pesan Kesalahan: <strong>" . htmlspecialchars($e->getMessage()) . "</strong></p>"
        . "<hr>"
        . "<p class='mb-0'>Pastikan Service MySQL di XAMPP / MariaDB sudah berjalan dan database <code>db_perpustakaan</code> sudah diimpor dari folder <code>database/db_perpustakaan.sql</code>.</p>"
        . "</div></div>");
}

/**
 * ==============================================================================
 * HELPER FUNCTIONS (Fungsi Bantu Pembelajaran)
 * ==============================================================================
 */

/**
 * Sanitasi string output untuk mencegah serangan XSS (Cross-Site Scripting).
 *
 * @param mixed $data Data string masukan
 * @return string Data string yang telah di-escape
 */
function escape($data) {
    return htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8');
}

/**
 * Memformat tanggal dari format database (YYYY-MM-DD) menjadi format Indonesia.
 *
 * @param string|null $tanggal Format YYYY-MM-DD
 * @return string Format '17 Oktober 2026' atau '-' jika null
 */
function format_tanggal($tanggal) {
    if (empty($tanggal) || $tanggal === '0000-00-00') {
        return '-';
    }

    $bulanIndo = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    $timestamp = strtotime($tanggal);
    if (!$timestamp) return $tanggal;

    $hari  = date('d', $timestamp);
    $bulan = $bulanIndo[(int)date('m', $timestamp)];
    $tahun = date('Y', $timestamp);

    return "{$hari} {$bulan} {$tahun}";
}

/**
 * Menghitung selisih keterlambatan pengembalian buku dalam satuan hari.
 *
 * Logika:
 * - Jika status buku masih dipinjam, bandingkan tanggal rencana kembali dengan tanggal hari ini.
 * - Jika status buku sudah dikembalikan, bandingkan tanggal rencana kembali dengan tanggal kembali aktual.
 *
 * @param string $tglRencana Format YYYY-MM-DD
 * @param string|null $tglAktual Format YYYY-MM-DD atau null jika belum kembali
 * @return array ['terlambat' => bool, 'hari' => int, 'status_teks' => string, 'badge_class' => string]
 */
function hitung_keterlambatan($tglRencana, $tglAktual = null) {
    $targetRencana = new DateTime($tglRencana);
    
    // Jika tglAktual tidak diisi, gunakan tanggal hari ini
    $targetSelesai = !empty($tglAktual) ? new DateTime($tglAktual) : new DateTime(date('Y-m-d'));

    // Reset jam/menit/detik agar perbandingan murni pada tingkat tanggal
    $targetRencana->setTime(0, 0, 0);
    $targetSelesai->setTime(0, 0, 0);

    if ($targetSelesai > $targetRencana) {
        $interval = $targetRencana->diff($targetSelesai);
        $hariTerlambat = (int)$interval->format('%a');

        return [
            'terlambat'   => true,
            'hari'        => $hariTerlambat,
            'status_teks' => "Terlambat {$hariTerlambat} Hari",
            'badge_class' => 'danger' // Warna merah untuk peringatan keterlambatan
        ];
    } else {
        return [
            'terlambat'   => false,
            'hari'        => 0,
            'status_teks' => 'Tepat Waktu',
            'badge_class' => 'success' // Warna hijau untuk status aman
        ];
    }
}
