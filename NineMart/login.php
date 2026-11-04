<?php
/**
 * ==============================================================================
 * MODUL LOGIN SISTEM - NINEMART
 * ==============================================================================
 * Menampilkan antarmuka otentikasi dengan background Lawang Sewu dan kartu
 * login persis seperti spesifikasi lembar soal UKK.
 * ==============================================================================
 */

session_start();
require_once 'config/database.php';

// Jika sudah login, langsung alihkan ke Dashboard
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$error = '';
$pesan = '';

if (isset($_GET['pesan'])) {
    if ($_GET['pesan'] === 'logout') {
        $pesan = "Anda telah berhasil logout dari sistem.";
    } elseif ($_GET['pesan'] === 'belum_login') {
        $error = "Silakan login terlebih dahulu untuk mengakses sistem!";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['user'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $error = "Username dan Password wajib diisi!";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM tblpengguna WHERE user = :user LIMIT 1");
        $stmt->execute([':user' => $username]);
        $user = $stmt->fetch();

        // Validasi kata sandi: mendukung password_verify (Bcrypt) dan plain-text fallback standar UKK
        $password_valid = false;
        if ($user) {
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                $password_valid = true;
            }
        }

        if ($password_valid) {
            session_regenerate_id(true);
            $_SESSION['user']    = $user['user'];
            $_SESSION['user_id'] = $user['id'];

            header("Location: index.php");
            exit;
        } else {
            $error = "Username atau Password yang Anda masukkan salah!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem - NineMart SMK Negeri 9 Semarang</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-body">

    <div class="login-card">
        <!-- Top Mini Banner -->
        <div class="login-card-banner">
            <img src="assets/img/header-banner.png" alt="Header Banner Nine Mart">
        </div>

        <!-- Header Card Bar -->
        <div class="login-card-header">
            LOGIN SISTEM
        </div>

        <!-- Form Body -->
        <div class="login-card-body">
            <div class="login-key-wrap">
                <img src="assets/img/key-icon.png" alt="Key Icon">
            </div>

            <div class="login-form-wrap">
                <?php if (!empty($error)): ?>
                    <div style="color: #d32f2f; font-size: 11px; margin-bottom: 8px; font-weight: bold;">
                        <?= escape($error) ?>
                    </div>
                <?php elseif (!empty($pesan)): ?>
                    <div style="color: #2e7d32; font-size: 11px; margin-bottom: 8px; font-weight: bold;">
                        <?= escape($pesan) ?>
                    </div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <table class="login-table">
                        <tr>
                            <td style="width: 75px;">Username</td>
                            <td style="width: 10px;">:</td>
                            <td>
                                <input type="text" name="user" required autofocus value="<?= isset($username) ? escape($username) : '' ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>Password</td>
                            <td>:</td>
                            <td>
                                <input type="password" name="password" required>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="padding-top: 6px;">
                                <button type="submit" class="login-btn">Login</button>
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>

        <!-- Footer Card Bar -->
        <div class="login-card-footer">
            NINE MART TOKO NYA SMKN 9 SEMARANG | 2024
        </div>
    </div>

</body>
</html>
