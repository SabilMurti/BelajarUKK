<?php
/**
 * ==============================================================================
 * PROSES LOGOUT (PENGHANCURAN SESSION)
 * ==============================================================================
 * Mengosongkan seluruh variabel $_SESSION, menghapus cookie session dari browser,
 * dan memanggil session_destroy() untuk mengakhiri sesi pengguna secara aman.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Kosongkan array superglobal $_SESSION
$_SESSION = [];

// 2. Hapus cookie session jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3. Hancurkan sesi di server
session_destroy();

// 4. Redirect ke halaman login dengan pesan sukses logout
header("Location: login.php?pesan=logout");
exit;
