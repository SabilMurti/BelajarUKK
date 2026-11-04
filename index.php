<?php
/**
 * ==============================================================================
 * PORTAL UTAMA: BELAJAR UKK PPLG / RPL
 * ==============================================================================
 * Hub utama untuk mengakses seluruh modul latihan dan persiapan Uji Kompetensi
 * Keahlian (UKK) Rekayasa Perangkat Lunak / PPLG.
 * ==============================================================================
 */

$modules = [
    [
        'id'          => 'perpus',
        'title'       => 'Aplikasi Manajemen Perpustakaan',
        'badge'       => 'Modul 1 • Selesai & Teruji',
        'badge_class' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30',
        'description' => 'Sistem sirkulasi peminjaman, pengembalian otomatis, proteksi stok atomik (ACID Transaction), dan laporan cetak resmi. Dilengkapi dual versi (Vanilla CSS & Bootstrap 5.3 CDN).',
        'tech'        => ['PHP Native (PDO)', 'MySQL / MariaDB', 'Bootstrap 5.3', 'Pure CSS'],
        'link'        => 'perpus/index.php',
        'status'      => 'ready',
        'icon'        => 'bi-book-half'
    ],
    [
        'id'          => 'ninemart',
        'title'       => 'NineMart — Kasir & Point of Sale (POS)',
        'badge'       => 'Modul 2 • Tryout UKK 2026',
        'badge_class' => 'bg-amber-500/10 text-amber-400 border border-amber-500/30',
        'description' => 'Bahan Tryout UKK 2026 Klaster Pemrograman Web SMKN 9 Semarang. Meliputi dokumen instrumen soal, rancangan database penjualan, dan aset visual.',
        'tech'        => ['PHP / MySQL', 'PDF Panduan', 'Aset Visual'],
        'link'        => 'NineMart/',
        'status'      => 'draft',
        'icon'        => 'bi-cart-check'
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BelajarUKK — Portal Persiapan UKK PPLG / RPL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-canvas: #090d16;
            --surface-card: rgba(255, 255, 255, 0.04);
            --surface-hover: rgba(255, 255, 255, 0.08);
            --border-subtle: rgba(255, 255, 255, 0.08);
            --border-hover: rgba(59, 130, 246, 0.4);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
        }
        body {
            background-color: var(--bg-canvas);
            color: var(--text-primary);
            min-height: 100vh;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        .hero-glow {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 350px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, rgba(9, 13, 22, 0) 70%);
            pointer-events: none;
            z-index: 0;
        }
        .content-wrap {
            position: relative;
            z-index: 1;
        }
        .card-module {
            background: var(--surface-card);
            border: 1px solid var(--border-subtle);
            backdrop-filter: blur(8px);
            transition: transform 0.2s ease, border-color 0.2s ease, background 0.2s ease;
        }
        .card-module:hover {
            transform: translateY(-4px);
            background: var(--surface-hover);
            border-color: var(--border-hover);
        }
        .tech-pill {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.75rem;
            color: #cbd5e1;
        }
    </style>
</head>
<body class="py-5">
    <div class="hero-glow"></div>
    <div class="container content-wrap py-4">
        
        <!-- Header -->
        <div class="text-center max-w-2xl mx-auto mb-5">
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3 border border-primary-subtle bg-primary bg-opacity-10 text-primary small fw-semibold">
                <i class="bi bi-mortarboard-fill"></i> Kurikulum Merdeka & Standar Industri
            </div>
            <h1 class="display-5 fw-bold text-white tracking-tight mb-3">BelajarUKK</h1>
            <p class="text-secondary fs-6 mb-0 mx-auto" style="max-width: 620px;">
                Repositori terpadu paket latihan dan persiapan <strong>Uji Kompetensi Keahlian (UKK)</strong> jurusan Pengembangan Perangkat Lunak dan GIM (PPLG) serta Rekayasa Perangkat Lunak (RPL).
            </p>
        </div>

        <!-- Modules Grid -->
        <div class="row g-4 justify-content-center mb-5">
            <?php foreach ($modules as $mod): ?>
            <div class="col-lg-6">
                <div class="card card-module rounded-4 h-100 p-4 d-flex flex-column">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary border border-primary-subtle">
                            <i class="bi <?= $mod['icon'] ?> fs-4"></i>
                        </div>
                        <span class="badge rounded-pill <?= $mod['badge_class'] ?> px-3 py-2 fw-normal">
                            <?= $mod['badge'] ?>
                        </span>
                    </div>

                    <h4 class="fw-bold text-white mb-2"><?= $mod['title'] ?></h4>
                    <p class="text-secondary small leading-relaxed mb-4 flex-grow-1">
                        <?= $mod['description'] ?>
                    </p>

                    <!-- Tech Stack Tags -->
                    <div class="d-flex flex-wrap gap-1 mb-4">
                        <?php foreach ($mod['tech'] as $t): ?>
                            <span class="tech-pill px-2.5 py-1 rounded-pill fw-medium"><?= $t ?></span>
                        <?php endforeach; ?>
                    </div>

                    <!-- Action Button -->
                    <div>
                        <a href="<?= $mod['link'] ?>" class="btn btn-primary w-100 py-2.5 rounded-3 fw-semibold d-inline-flex align-items-center justify-content-center gap-2 shadow-sm">
                            Buka Modul <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- System Footer -->
        <div class="border-top border-secondary border-opacity-10 pt-4 mt-5 d-flex flex-column flex-md-row align-items-center justify-content-between text-secondary small">
            <div>
                &copy; <?= date('Y') ?> <strong>BelajarUKK</strong> &bull; Repositori Bahan Ajar SMK PPLG / RPL
            </div>
            <div class="mt-2 mt-md-0">
                <a href="https://github.com/SabilMurti/BelajarUKK" target="_blank" class="text-secondary text-decoration-none hover-white">
                    <i class="bi bi-github me-1"></i> GitHub: SabilMurti/BelajarUKK
                </a>
            </div>
        </div>

    </div>
</body>
</html>
