<?php
// Modul pergantian kata sandi pengguna
require_once 'config/database.php';
require_once 'config/auth.php';

$page_title = "Ubah Password";
$sukses = '';
$error  = '';

// Proses ubah password saat form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password_lama = trim($_POST['password_lama'] ?? '');
    $password_baru = trim($_POST['password_baru'] ?? '');

    if (empty($password_lama) || empty($password_baru)) {
        $error = "Password lama dan Password baru wajib diisi!";
    } elseif (strlen($password_baru) > 10) {
        $error = "Password baru maksimal 10 karakter!";
    } else {
        // Ambil data akun yang sedang aktif dari database
        $stmt = $pdo->prepare("SELECT * FROM tblpengguna WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $currentUser = $stmt->fetch();

        if ($currentUser) {
            // Validasi apakah password lama sesuai
            $lamaValid = false;
            if (password_verify($password_lama, $currentUser['password']) || $password_lama === $currentUser['password']) {
                $lamaValid = true;
            }

            if (!$lamaValid) {
                $error = "Password lama yang Anda masukkan tidak sesuai!";
            } else {
                // Simpan password baru terenkripsi
                $hashBaru = password_hash($password_baru, PASSWORD_DEFAULT);
                $stmtUpdate = $pdo->prepare("UPDATE tblpengguna SET password = :pwd WHERE id = :id");
                $stmtUpdate->execute([
                    ':pwd' => $hashBaru,
                    ':id'  => $_SESSION['user_id']
                ]);
                $sukses = "Password berhasil diperbarui!";
            }
        } else {
            $error = "Sesi pengguna tidak valid!";
        }
    }
}

require_once 'includes/header.php';
?>

<?php if (!empty($sukses)): ?>
    <div class="alert-toast alert-success"><?= escape($sukses) ?></div>
<?php elseif (!empty($error)): ?>
    <div class="alert-toast alert-danger"><?= escape($error) ?></div>
<?php endif; ?>

<!-- Form ubah kata sandi -->
<div class="form-card-container">
    <form action="ubah_password.php" method="POST">
        <table class="form-table">
            <tr>
                <td style="width: 150px;">Password Lama</td>
                <td style="width: 20px;">:</td>
                <td>
                    <input type="password" name="password_lama" class="form-input" required autofocus>
                </td>
            </tr>
            <tr>
                <td>Password Baru</td>
                <td>:</td>
                <td>
                    <div style="display: flex; align-items: center;">
                        <input type="password" name="password_baru" maxlength="10" class="form-input" required>
                        <span class="form-hint">*Maksimal 10 karakter</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td style="padding-top: 8px;">
                    <button type="submit" class="btn-submit">Ubah</button>
                </td>
            </tr>
        </table>
    </form>
</div>

<?php
require_once 'includes/footer.php';
?>
