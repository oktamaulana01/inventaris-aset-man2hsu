<?php
// Guard: Cek apakah user sudah login
require_once __DIR__ . '/../config/database.php';
startSession();

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

// Session timeout: auto-logout setelah 30 menit tidak aktif
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > SESSION_TIMEOUT) {
    session_unset();
    session_destroy();
    session_start();
    setFlash('warning', 'Sesi Anda telah berakhir karena tidak aktif. Silakan login kembali.');
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}
$_SESSION['last_activity'] = time();

// Session regeneration: setiap 15 menit untuk mencegah session fixation
if (!isset($_SESSION['last_regeneration'])) {
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 900) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Cek role admin untuk halaman admin-only
function requireAdmin() {
    if ($_SESSION['user_role'] !== 'admin') {
        $redirect = $_SESSION['user_role'] === 'guru' ? BASE_URL . '/pages/guru/dashboard.php' : BASE_URL . '/pages/dashboard.php';
        header('Location: ' . $redirect);
        exit;
    }
}

// Cek role guru untuk halaman guru-only
function requireGuru() {
    if ($_SESSION['user_role'] !== 'guru') {
        header('Location: ' . BASE_URL . '/pages/dashboard.php');
        exit;
    }
}

// Cek role staff (admin/petugas) — block guru
function requireStaff() {
    if ($_SESSION['user_role'] === 'guru') {
        header('Location: ' . BASE_URL . '/pages/guru/dashboard.php');
        exit;
    }
}
?>
