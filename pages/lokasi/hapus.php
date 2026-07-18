<?php
require_once __DIR__ . '/../../includes/auth_check.php';
validateCsrfToken();
$pdo = getConnection();
$id = intval($_POST['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM lokasi WHERE id = ?"); $stmt->execute([$id]);
$data = $stmt->fetch();
if ($data) {
    $pdo->prepare("DELETE FROM lokasi WHERE id = ?")->execute([$id]);
    logActivity($pdo, $_SESSION['user_id'], 'Hapus Lokasi', "Menghapus lokasi: {$data['nama_lokasi']}");
    setFlash('success', 'Lokasi berhasil dihapus!');
} else { setFlash('danger', 'Lokasi tidak ditemukan!'); }
header('Location: ' . BASE_URL . '/lokasi'); exit;
?>
