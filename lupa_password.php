<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/mailer.php';
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

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrfToken();
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Email tidak boleh kosong.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        // Cek apakah email terdaftar
        $stmt = $pdo->prepare("SELECT id, nama FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        // Standar keamanan: Selalu tampilkan pesan sukses baik email ada maupun tidak
        $success = 'Jika email tersebut terdaftar, tautan untuk mereset password telah dikirim.';
        
        if ($user) {
            // Generate token
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            
            // Simpan token ke database
            $updateStmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expires_at = ? WHERE id = ?");
            if ($updateStmt->execute([$token, $expiresAt, $user['id']])) {
                // Buat link reset dengan URL absolut
                $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
                $domainName = $_SERVER['HTTP_HOST'];
                $resetLink = $protocol . $domainName . BASE_URL . '/reset_password.php?token=' . $token;
                
                // Kirim email
                sendResetPasswordEmail($pdo, $email, $user['nama'], $resetLink);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Inventarisasi Aset MAN 2 HSU</title>
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
                    <h2 class="fw-bold text-dark">Lupa Password?</h2>
                    <p class="text-muted">Masukkan alamat email Anda yang terdaftar. Kami akan mengirimkan tautan untuk mengatur ulang password Anda.</p>
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
                <?php endif; ?>

                <form action="" method="POST">
            <?= generateCsrfToken() ?>
                    <div class="form-floating mb-4">
                        <input type="email" name="email" class="form-control" id="floatingInput" placeholder="name@example.com" required autofocus value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        <label for="floatingInput"><i class="fas fa-envelope me-2 text-muted"></i>Alamat Email</label>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-login text-white btn-lg">
                            KIRIM LINK RESET <i class="fas fa-paper-plane ms-2 small"></i>
                        </button>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="login.php" class="text-decoration-none text-secondary small">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Halaman Login
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


