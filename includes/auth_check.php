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
        header('Location: /inventaris-aset-man2hsu/pages/dashboard.php');
        exit;
    }
}
?>
