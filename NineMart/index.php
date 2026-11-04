<?php
/**
 * ==============================================================================
 * DASHBOARD UTAMA - NINEMART
 * ==============================================================================
 * Menampilkan pesan sambutan 'Sugeng Rawuh' pada kotak kuning dengan latar
 * belakang hijau terang persis seperti lembar soal UKK.
 * ==============================================================================
 */

require_once 'config/database.php';
require_once 'config/auth.php';

$page_title = "Dashboard";
require_once 'includes/header.php';
?>

<!-- Kotak Ucapan Kuning Terang Sesuai Soal -->
<div class="greeting-box">
    <div class="greeting-title">SUGENG RAWUH ... |!!!!</div>
    <div class="greeting-text">SELAMAT DATANG DI SISTEM APLIKASI PENJUALAN ONLINE</div>
    <div class="greeting-text">APLIKASI INI MERUPAKAN APLIKASI JUAL BELI ONLINE MILIK SMKN 9 SEMARANG</div>
    <div class="greeting-text">TERIMAKASIH SELAMAT BERBELANJA</div>
    <div class="greeting-copyright">.: COPYRIGHT @2024 - SMKN 9 SEMARANG :.</div>
</div>

<?php
require_once 'includes/footer.php';
?>
