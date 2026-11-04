<?php
/**
 * ==============================================================================
 * MIDDLEWARE OTENTIKASI SESSION (AUTH GUARD) - NINEMART
 * ==============================================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: login.php?pesan=belum_login");
    exit;
}
