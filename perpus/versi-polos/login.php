<?php
/**
 * ==============================================================================
 * HALAMAN LOGIN ADMINISTRATOR (VERSI POLOS / VANILLA CSS)
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
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <h1>📚 PerpusUKK</h1>
                <p>Masuk ke Sistem Manajemen Perpustakaan</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger">
                    <span>⚠️ <?= escape($error) ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($pesan)): ?>
                <div class="alert alert-success">
                    <span>✅ <?= escape($pesan) ?></span>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" 
                           placeholder="Masukkan username" required autofocus 
                           value="<?= isset($username) ? escape($username) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" 
                           placeholder="Masukkan password" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="margin-top: 1.25rem;">
                    Masuk ke Sistem
                </button>
            </form>

            <div style="margin-top: 1.5rem; text-align: center; font-size: 0.8rem; color: #64748b; background: #f8fafc; padding: 0.75rem; border-radius: 6px; border: 1px dashed #cbd5e1;">
                <strong>Akun Default Pengujian UKK:</strong><br>
                Username: <code>admin</code> | Password: <code>admin123</code>
            </div>
        </div>
    </div>

</body>
</html>
