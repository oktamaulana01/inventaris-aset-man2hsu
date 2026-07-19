<?php
require_once __DIR__ . '/../../includes/auth_check.php';
validateCsrfToken();
$pdo = getConnection();
$id = intval($_POST['id'] ?? 0);
$pdo->prepare("DELETE FROM peminjaman WHERE id = ?")->execute([$id]);
logActivity($pdo, $_SESSION['user_id'], 'Hapus Peminjaman', "Menghapus data peminjaman ID: $id");
setFlash('success', 'Data peminjaman berhasil dihapus!');
header('Location: ' . BASE_URL . '/peminjaman'); exit;
?>
