<?php
// Proteksi halaman: periksa sesi login pengguna

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika belum login, alihkan ke halaman login
if (!isset($_SESSION['user'])) {
    header("Location: login.php?pesan=belum_login");
    exit;
}
