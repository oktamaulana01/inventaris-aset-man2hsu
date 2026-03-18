<?php
require_once __DIR__ . '/../../includes/auth_check.php';
requireAdmin();
$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);
if ($id == $_SESSION['user_id']) { setFlash('danger', 'Tidak bisa menghapus akun sendiri!'); header('Location: index.php'); exit; }
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?"); $stmt->execute([$id]); $data = $stmt->fetch();
if ($data) {
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    logActivity($pdo, $_SESSION['user_id'], 'Hapus User', "Menghapus pengguna: {$data['nama']}");
    setFlash('success', 'Pengguna berhasil dihapus!');
} else { setFlash('danger', 'Pengguna tidak ditemukan!'); }
header('Location: index.php'); exit;
?>
