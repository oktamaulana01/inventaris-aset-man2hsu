<?php
// Guard: Cek apakah user sudah login
require_once __DIR__ . '/../config/database.php';
startSession();

if (!isset($_SESSION['user_id'])) {
    header('Location: /inventaris-aset-man2hsu/login.php');
    exit;
}

// Cek role admin untuk halaman admin-only
function requireAdmin() {
    if ($_SESSION['user_role'] !== 'admin') {
        $redirect = $_SESSION['user_role'] === 'guru' ? '/inventaris-aset-man2hsu/pages/guru/dashboard.php' : '/inventaris-aset-man2hsu/pages/dashboard.php';
        header('Location: ' . $redirect);
        exit;
    }
}

// Cek role guru untuk halaman guru-only
function requireGuru() {
    if ($_SESSION['user_role'] !== 'guru') {
        header('Location: /inventaris-aset-man2hsu/pages/dashboard.php');
        exit;
    }
}

// Cek role staff (admin/petugas) — block guru
function requireStaff() {
    if ($_SESSION['user_role'] === 'guru') {
        header('Location: /inventaris-aset-man2hsu/pages/guru/dashboard.php');
        exit;
    }
}
?>
