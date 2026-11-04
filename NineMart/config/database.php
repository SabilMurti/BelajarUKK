<?php
/**
 * ==============================================================================
 * MODUL KONEKSI DATABASE (PHP PDO) - NINEMART
 * ==============================================================================
 * Proyek  : Aplikasi Penjualan Online NineMart - SMK Negeri 9 Semarang
 * Standar : Tryout UKK RPL / PPLG Klaster Pemrograman Web
 * ==============================================================================
 */

$db_host = '127.0.0.1';
$db_port = '3306';
$db_name = 'db_ninemart';
$db_user = 'root';
$db_pass = ''; // Default XAMPP

try {
    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, $db_user, $db_pass, $options);

} catch (PDOException $e) {
    die("<div style='font-family: Arial, sans-serif; padding: 20px; background: #ffebee; border: 1px solid #ef5350; border-radius: 6px; max-width: 600px; margin: 40px auto; color: #c62828;'>"
        . "<h3 style='margin-top: 0;'>Koneksi Database Gagal!</h3>"
        . "<p>Pesan Kesalahan: <strong>" . htmlspecialchars($e->getMessage()) . "</strong></p>"
        . "<hr style='border: none; border-top: 1px solid #ef9a9a;'>"
        . "<p style='margin-bottom: 0; font-size: 13px;'>Pastikan MySQL/MariaDB sudah aktif dan database <code>db_ninemart</code> sudah diimpor dari <code>database/db_ninemart.sql</code>.</p>"
        . "</div>");
}

/**
 * Sanitasi string output untuk proteksi Cross-Site Scripting (XSS).
 */
function escape($data) {
    return htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8');
}
