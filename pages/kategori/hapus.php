<?php
require_once __DIR__ . '/../../includes/auth_check.php';
$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM kategori WHERE id = ?"); $stmt->execute([$id]);
$data = $stmt->fetch();
if ($data) {
    $pdo->prepare("DELETE FROM kategori WHERE id = ?")->execute([$id]);
    logActivity($pdo, $_SESSION['user_id'], 'Hapus Kategori', "Menghapus kategori: {$data['nama_kategori']}");
    setFlash('success', 'Kategori berhasil dihapus!');
} else { setFlash('danger', 'Kategori tidak ditemukan!'); }
header('Location: index.php'); exit;
?>
