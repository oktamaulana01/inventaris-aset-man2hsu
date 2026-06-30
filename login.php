<?php
require_once __DIR__ . '/config/database.php';
startSession();

// Proses Login
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrfToken();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Rate Limiting (Anti Brute-Force)
    $max_attempts = 5;
    $lockout_time = 15 * 60; // 15 menit
    
    if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= $max_attempts) {
        $time_since_last_attempt = time() - $_SESSION['last_login_attempt'];
        if ($time_since_last_attempt < $lockout_time) {
            $minutes_left = ceil(($lockout_time - $time_since_last_attempt) / 60);
            $error = "Terlalu banyak percobaan login yang gagal. Silakan coba lagi dalam $minutes_left menit.";
        } else {
            // Reset setelah waktu tunggu selesai
            $_SESSION['login_attempts'] = 0;
        }
    }
    
    if (empty($error)) {
        if (empty($username) || empty($password)) {
            $error = 'Username dan password harus diisi!';
        } else {
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Mencegah pencurian sesi (Session Fixation)
            session_regenerate_id(true);
            
            // Reset pencatat brute-force
            unset($_SESSION['login_attempts']);
            unset($_SESSION['last_login_attempt']);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nama'] = $user['nama'];
            $_SESSION['user_username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_foto'] = $user['foto'];
            $_SESSION['user_nip'] = $user['nip'];
            $_SESSION['user_jabatan'] = $user['jabatan'];
            $_SESSION['user_no_telepon'] = $user['no_telepon'];
            
            // Remember Me
            if (isset($_POST['remember'])) {
                $token = bin2hex(random_bytes(32));
                $stmtToken = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $stmtToken->execute([$token, $user['id']]);
                // Set cookie untuk 30 hari
                setcookie('remember_token', $token, time() + (86400 * 30), '/');
            }
            
            // Log aktivitas login
            logActivity($pdo, $user['id'], 'Login', $user['nama'] . ' berhasil login');
            
            // Redirect berdasarkan role
            if ($user['role'] === 'guru') {
                header('Location: ' . BASE_URL . '/guru/dashboard');
            } else {
                header('Location: ' . BASE_URL . '/dashboard');
            }
            exit;
        } else {
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
            $_SESSION['last_login_attempt'] = time();
            $sisa = $max_attempts - $_SESSION['login_attempts'];
            
            if ($sisa > 0) {
                $error = "Username atau password salah! (Sisa percobaan: $sisa)";
            } else {
                $error = "Terlalu banyak percobaan login yang gagal. Silakan coba lagi dalam 15 menit.";
            }
        }
    }
}
}

// Redirect jika sudah login atau punya remember token yang valid
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] === 'guru') {
        header('Location: ' . BASE_URL . '/guru/dashboard');
    } else {
        header('Location: ' . BASE_URL . '/dashboard');
    }
    exit;
} elseif (isset($_COOKIE['remember_token'])) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE remember_token = ?");
    $stmt->execute([$_COOKIE['remember_token']]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nama'] = $user['nama'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_foto'] = $user['foto'];
        $_SESSION['user_nip'] = $user['nip'];
        $_SESSION['user_jabatan'] = $user['jabatan'];
        $_SESSION['user_no_telepon'] = $user['no_telepon'];
        
        $_SESSION['last_activity'] = time();
        
        if ($user['role'] === 'guru') {
            header('Location: ' . BASE_URL . '/guru/dashboard');
        } else {
            header('Location: ' . BASE_URL . '/dashboard');
        }
        exit;
    } else {
        setcookie('remember_token', '', time() - 3600, '/');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Inventarisasi Aset MAN 2 HSU</title>
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
            height: 100vh;
            display: flex;
            box-shadow: 0 0 50px rgba(0,0,0,0.1);
        }

        /* BAGIAN KIRI (BRANDING) */
        .login-brand {
            background: linear-gradient(135deg, #1e7256 0%, #114a36 100%);
            width: 45%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        /* Pola dekoratif background */
        .login-brand::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .login-brand::after {
            content: '';
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .brand-logo {
            width: 140px;
            height: 140px;
            margin-bottom: 20px;
            background: white;
            border-radius: 50%;
            padding: 10px;
            filter: drop-shadow(0 10px 10px rgba(0,0,0,0.2));
            transition: transform 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 50%;
        }

        .brand-logo:hover {
            transform: scale(1.05);
        }

        /* BAGIAN KANAN (FORM) */
        .login-form-side {
            width: 55%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
        }

        .login-form-wrapper {
            width: 100%;
            max-width: 450px;
        }

        .form-floating > .form-control {
            border-radius: 10px;
            border: 1px solid #e9ecef;
            background-color: #fcfcfc;
        }

        .form-floating > .form-control:focus {
            border-color: #1e7256;
            box-shadow: 0 0 0 0.25rem rgba(30, 114, 86, 0.15);
        }

        .btn-login {
            background: #1e7256;
            border: none;
            padding: 14px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        .btn-login:hover {
            background: #145c43;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(30, 114, 86, 0.2);
            color: white;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #aaa;
            z-index: 10;
        }

        /* RESPONSIVE MOBILE */
        @media (max-width: 768px) {
            body { overflow: auto; }
            .login-container { flex-direction: column; height: auto; }
            .login-brand { width: 100%; padding: 40px 20px; min-height: 250px; }
            .login-form-side { width: 100%; padding: 40px 20px; min-height: 60vh; }
            .brand-logo { width: 100px; height: 100px; }
        }
    </style>
</head>
<body>

    <div class="container-fluid p-0">
        <div class="login-container">
            
            <div class="login-brand text-center">
                <div class="brand-logo">
                    <img src="<?= BASE_URL ?>/assets/uploads/logo_man2.jpg" alt="Logo MAN 2 HSU"> 
                </div>
                <h3 class="fw-bold mb-1">INVENTARISASI ASET</h3>
                <p class="text-white-50 small mb-0">Madrasah Aliyah Negeri 2<br>Hulu Sungai Utara</p>
            </div>

            <div class="login-form-side">
                <div class="login-form-wrapper">
                    <div class="mb-4">
                        <h2 class="fw-bold text-dark">Selamat Datang!</h2>
                        <p class="text-muted">Silakan masukkan akun Anda untuk melanjutkan.</p>
                    </div>

                    <?php if ($error): ?>
                        <div class='alert alert-danger py-2 small'>
                            <i class='fas fa-exclamation-circle me-1'></i> <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST">
                        <?= generateCsrfToken() ?>
                        
                        <div class="form-floating mb-3">
                            <input type="text" name="username" class="form-control" id="floatingInput" placeholder="Username" required autofocus value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                            <label for="floatingInput"><i class="fas fa-user me-2 text-muted"></i>Username</label>
                        </div>

                        <div class="form-floating mb-4 position-relative">
                            <input type="password" name="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                            <label for="floatingPassword"><i class="fas fa-lock me-2 text-muted"></i>Password</label>
                            <i class="fa-solid fa-eye password-toggle" id="toggleEye"></i>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4 text-start">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                                <label class="form-check-label text-muted" for="rememberMe">
                                    Ingat Saya
                                </label>
                            </div>
                            <a href="lupa_password.php" class="text-decoration-none small text-primary fw-medium">Lupa Password?</a>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-login text-white btn-lg">
                                MASUK SEKARANG <i class="fas fa-arrow-right ms-2 small"></i>
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

    <script>
        const toggleEye = document.querySelector('#toggleEye');
        const password = document.querySelector('#floatingPassword');

        toggleEye.addEventListener('click', function () {
            // Ubah tipe input
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Ubah ikon mata
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>

</body>
</html>
