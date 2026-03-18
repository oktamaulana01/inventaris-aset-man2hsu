<?php
require_once __DIR__ . '/../../includes/auth_check.php';
$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM lokasi WHERE id = ?"); $stmt->execute([$id]);
$data = $stmt->fetch();
if ($data) {
    $pdo->prepare("DELETE FROM lokasi WHERE id = ?")->execute([$id]);
    logActivity($pdo, $_SESSION['user_id'], 'Hapus Lokasi', "Menghapus lokasi: {$data['nama_lokasi']}");
    setFlash('success', 'Lokasi berhasil dihapus!');
} else { setFlash('danger', 'Lokasi tidak ditemukan!'); }
header('Location: index.php'); exit;
?>
