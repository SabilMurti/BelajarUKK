<?php
/**
 * ==============================================================================
 * MIDDLEWARE OTENTIKASI SESSION (AUTH GUARD)
 * ==============================================================================
 * Proyek : Aplikasi Manajemen Perpustakaan Berbasis Web (UKK PPLG)
 * Fungsi : Melindungi halaman administrator dari akses pengguna tanpa login.
 * 
 * Cara Kerja:
 * 1. Memeriksa status session PHP.
 * 2. Memastikan session 'user_id' sudah tersimpan dari proses login yang valid.
 * 3. Jika belum terotentikasi, otomatis melakukan redirect ke halaman login.php.
 * ==============================================================================
 */

// Mulai session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah session user_id sudah ada
if (!isset($_SESSION['user_id'])) {
    // Alihkan ke halaman login dengan query parameter informasi
    header("Location: login.php?pesan=belum_login");
    exit;
}
