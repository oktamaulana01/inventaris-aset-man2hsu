<?php
require_once __DIR__ . '/config/database.php';
startSession();
$pdo = getConnection();

// Jika sudah login, arahkan ke dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'guru') {
        header('Location: ' . BASE_URL . '/pages/guru/dashboard.php');
    } else {
        header('Location: ' . BASE_URL . '/pages/dashboard.php');
    }
    exit;
}

$token = $_GET['token'] ?? '';
$error = '';
$success = '';
$tokenValid = false;
$user = null;

if (empty($token)) {
    $error = 'Token reset password tidak valid atau tidak ditemukan.';
} else {
    // Validasi token
    $stmt = $pdo->prepare("SELECT id, nama FROM users WHERE reset_token = ? AND reset_token_expires_at > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        $tokenValid = true;
    } else {
        $error = 'Token reset password tidak valid atau sudah kedaluwarsa. Silakan ajukan ulang permintaan lupa password.';
    }
}

if ($tokenValid && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($password) || empty($confirm_password)) {
        $error = 'Password dan konfirmasi password harus diisi.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirm_password) {
        $error = 'Password dan konfirmasi password tidak cocok.';
    } else {
        // Update password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $updateStmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires_at = NULL WHERE id = ?");
        if ($updateStmt->execute([$hashedPassword, $user['id']])) {
            $success = 'Password berhasil diubah. Silakan login dengan password baru Anda.';
            $tokenValid = false; // Sembunyikan form
        } else {
            $error = 'Gagal mengubah password. Terjadi kesalahan pada sistem.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Inventarisasi Aset MAN 2 HSU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            height: 100vh;
            overflow: hidden;
        }

        .login-container {
            display: flex;
            height: 100vh;
        }

        .login-sidebar {
            flex: 1;
            background: linear-gradient(135deg, var(--bs-success) 0%, #1e7256 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .login-sidebar::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url('assets/images/pattern.png');
            opacity: 0.1;
            z-index: 1;
        }

        .login-sidebar > * { z-index: 2; position: relative; }

        .login-form-side {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: white;
            padding: 2rem;
        }

        .login-form-wrapper {
            width: 100%;
            max-width: 400px;
        }

        .btn-login {
            background: linear-gradient(135deg, #1e7256 0%, var(--bs-success) 100%);
            border: none;
            padding: 12px;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 114, 86, 0.3);
            background: linear-gradient(135deg, #165641 0%, #1e7256 100%);
        }

        .form-floating > .form-control:focus ~ label::after {
            background-color: transparent !important;
        }

        .form-control:focus {
            border-color: #1e7256;
            box-shadow: 0 0 0 0.25rem rgba(30, 114, 86, 0.25);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 5;
        }

        @media (max-width: 768px) {
            .login-sidebar { display: none; }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-sidebar d-none d-md-flex text-center">
            <div class="mb-4">
                <img src="assets/images/logo-man2hsu.png" alt="Logo MAN 2 HSU" style="width: 120px; filter: drop-shadow(0 4px 6px rgba(0,0,0,0.2));" onerror="this.style.display='none'">
            </div>
            <h3 class="fw-bold mb-1">INVENTARISASI ASET</h3>
            <p class="text-white-50 small mb-0">Madrasah Aliyah Negeri 2<br>Hulu Sungai Utara</p>
        </div>

        <div class="login-form-side">
            <div class="login-form-wrapper">
                <div class="mb-4">
                    <h2 class="fw-bold text-dark">Reset Password</h2>
                    <?php if ($tokenValid): ?>
                        <p class="text-muted">Halo <?= htmlspecialchars($user['nama']) ?>, silakan masukkan password baru untuk akun Anda.</p>
                    <?php endif; ?>
                </div>

                <?php if ($error): ?>
                    <div class='alert alert-danger py-2 small'>
                        <i class='fas fa-exclamation-circle me-1'></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class='alert alert-success py-2 small'>
                        <i class='fas fa-check-circle me-1'></i> <?= htmlspecialchars($success) ?>
                    </div>
                    <div class="d-grid mt-4">
                        <a href="login.php" class="btn btn-login text-white btn-lg">
                            <i class="fas fa-sign-in-alt me-2 small"></i> MENUJU HALAMAN LOGIN
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ($tokenValid): ?>
                <form action="" method="POST">
            <?= generateCsrfToken() ?>
                    
                    <div class="form-floating mb-3 position-relative">
                        <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password Baru" required minlength="6">
                        <label for="floatingPassword"><i class="fas fa-lock me-2 text-muted"></i>Password Baru</label>
                        <i class="fa-solid fa-eye password-toggle" id="toggleEye1"></i>
                    </div>

                    <div class="form-floating mb-4 position-relative">
                        <input type="password" name="confirm_password" class="form-control" id="floatingConfirmPassword" placeholder="Konfirmasi Password Baru" required minlength="6">
                        <label for="floatingConfirmPassword"><i class="fas fa-lock me-2 text-muted"></i>Konfirmasi Password</label>
                        <i class="fa-solid fa-eye password-toggle" id="toggleEye2"></i>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-login text-white btn-lg">
                            UBAH PASSWORD SEKARANG <i class="fas fa-check ms-2 small"></i>
                        </button>
                    </div>
                </form>
                <?php endif; ?>
                
                <?php if (!$tokenValid && !$success): ?>
                    <div class="text-center mt-4">
                        <a href="lupa_password.php" class="text-decoration-none text-primary fw-medium small">
                            <i class="fas fa-redo me-1"></i> Ajukan Ulang Lupa Password
                        </a>
                        <br><br>
                        <a href="login.php" class="text-decoration-none text-secondary small">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Halaman Login
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        const toggleEye1 = document.querySelector('#toggleEye1');
        const passwordInput1 = document.querySelector('#floatingPassword');
        
        if (toggleEye1 && passwordInput1) {
            toggleEye1.addEventListener('click', function () {
                const type = passwordInput1.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput1.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }

        const toggleEye2 = document.querySelector('#toggleEye2');
        const passwordInput2 = document.querySelector('#floatingConfirmPassword');
        
        if (toggleEye2 && passwordInput2) {
            toggleEye2.addEventListener('click', function () {
                const type = passwordInput2.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput2.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
    </script>
</body>
</html>


