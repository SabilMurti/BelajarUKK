<?php
/**
 * ==============================================================================
 * HALAMAN LOGIN ADMINISTRATOR (VERSI BOOTSTRAP 5)
 * ==============================================================================
 * Fitur Keamanan:
 * 1. Prepared Statement PDO: Menghalangi SQL Injection pada kolom login.
 * 2. password_verify(): Memvalidasi password bcrypt satu arah (one-way hashing).
 * 3. session_regenerate_id(): Mencegah serangan Session Fixation.
 * ==============================================================================
 */

require_once 'config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika pengguna sudah login, langsung alihkan ke Dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$pesan = '';

// Tangani pesan notifikasi dari URL query parameter
if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] === 'belum_login') {
        $error = "Silakan login terlebih dahulu untuk mengakses sistem!";
    } elseif ($_GET['pesan'] === 'logout') {
        $pesan = "Anda telah berhasil keluar dari sistem.";
    }
}

// Proses form login saat tombol submit ditekan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validasi input tidak boleh kosong
    if (empty($username) || empty($password)) {
        $error = "Username dan Password wajib diisi!";
    } else {
        // Cari pengguna berdasarkan username menggunakan PDO Prepared Statement
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        // Verifikasi keberadaan user dan kecocokan password hash
        if ($user && password_verify($password, $user['password'])) {
            // Regenerasi ID Session untuk proteksi Session Fixation
            session_regenerate_id(true);

            // Simpan data otentikasi ke dalam session
            $_SESSION['user_id']      = $user['id_user'];
            $_SESSION['username']     = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];

            // Alihkan ke Dashboard
            header("Location: index.php");
            exit;
        } else {
            $error = "Username atau Password yang Anda masukkan tidak sesuai!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Petugas - Perpustakaan UKK</title>
    <!-- Bootstrap 5.3.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center min-vh-100">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-4 p-md-5">
                        
                        <div class="text-center mb-4">
                            <div class="bg-primary text-white rounded-circle d-inline-flex p-3 mb-2 shadow-sm">
                                <i class="bi bi-book-half fs-2"></i>
                            </div>
                            <h3 class="fw-bold text-dark mb-1">PerpusUKK</h3>
                            <p class="text-muted small">Masuk ke Sistem Manajemen Perpustakaan</p>
                        </div>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 small" role="alert">
                                <i class="bi bi-exclamation-triangle-fill flex-shrink-0"></i>
                                <div><?= escape($error) ?></div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($pesan)): ?>
                            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 small" role="alert">
                                <i class="bi bi-check-circle-fill flex-shrink-0"></i>
                                <div><?= escape($pesan) ?></div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="login.php" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label small fw-semibold">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="bi bi-person"></i>
                                    </span>
                                    <input type="text" id="username" name="username" class="form-control border-start-0 ps-0" 
                                           placeholder="Masukkan username" required autofocus 
                                           value="<?= isset($username) ? escape($username) : '' ?>">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label small fw-semibold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0 text-muted">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" id="password" name="password" class="form-control border-start-0 ps-0" 
                                           placeholder="Masukkan password" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold shadow-sm">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk ke Sistem
                            </button>
                        </form>

                        <div class="mt-4 p-3 bg-light rounded-3 text-center border">
                            <small class="text-muted d-block fw-semibold mb-1">Akun Default Pengujian UKK:</small>
                            <small class="text-secondary font-monospace">Username: <strong>admin</strong> | Password: <strong>admin123</strong></small>
                        </div>

                    </div>
                </div>

                <div class="text-center text-muted small mt-3">
                    &copy; <?= date('Y') ?> Perpustakaan UKK PPLG
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
