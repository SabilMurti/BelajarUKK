<?php
// Proses logout dan penghapusan sesi pengguna
session_start();
$_SESSION = [];

// Hapus cookie sesi jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan sesi dan kembali ke halaman login
session_destroy();
header("Location: login.php?pesan=logout");
exit;
