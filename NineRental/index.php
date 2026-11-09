<?php
// Hubungkan database dan konfigurasi
require_once __DIR__ . '/config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Nine Rental Mobil - SMK Negeri 9 Semarang</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="app-container">
        <!-- Header Banner Dashboard -->
        <div class="header-dashboard">
            <div class="logo-text">SMK</div>
            <div class="title-text">
                <div>SEKOLAH MENENGAH KEJURUAN</div>
                <div>KOTA SEMARANG</div>
            </div>
        </div>

        <!-- Subheader Orange -->
        <div class="subheader-orange">
            APLIKASI RENTAL MOBIL "NINE RENTAL"
        </div>

        <!-- Dashboard Body Menu Navigasi -->
        <div class="dashboard-body">
            <div class="dashboard-menu-grid">
                
                <!-- Kolom Master Kendaraan -->
                <div class="menu-column">
                    <div class="menu-column-title">Master</div>
                    <a href="kendaraan.php" class="menu-box-btn" title="Master Kendaraan">
                        <span class="menu-letter">A</span>
                    </a>
                    <div class="menu-caption">Kendaraan</div>
                </div>

                <!-- Kolom Transaksi Sewa -->
                <div class="menu-column">
                    <div class="menu-column-title">Transaksi</div>
                    <a href="sewa.php" class="menu-box-btn" title="Transaksi Sewa">
                        <span class="menu-letter">B</span>
                    </a>
                    <div class="menu-caption">Sewa</div>
                </div>

                <!-- Kolom Customer Pelanggan -->
                <div class="menu-column">
                    <div class="menu-column-title">Customer</div>
                    <a href="pelanggan.php" class="menu-box-btn" title="Customer Pelanggan">
                        <span class="menu-letter">C</span>
                    </a>
                    <div class="menu-caption">Pelanggan</div>
                </div>

            </div>
        </div>

        <!-- Footer Biru Dashboard -->
        <div class="footer-dashboard">
            COPYRIGHT @ SMK NEGERI 9 SEMARANG
        </div>
    </div>

</body>
</html>
