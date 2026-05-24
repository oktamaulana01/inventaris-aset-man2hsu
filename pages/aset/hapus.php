<?php
require_once __DIR__ . '/../../includes/auth_check.php';
validateCsrfToken();
$pdo = getConnection();

$id = intval($_POST['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM aset WHERE id = ? AND deleted_at IS NULL");
$stmt->execute([$id]);
$aset = $stmt->fetch();

if ($aset) {
    // Soft delete
    $pdo->prepare("UPDATE aset SET deleted_at = NOW() WHERE id = ?")->execute([$id]);
    logActivity($pdo, $_SESSION['user_id'], 'Hapus Aset', "Menghapus aset: {$aset['nama_aset']} ({$aset['kode_aset']})");
    setFlash('success', 'Aset berhasil dihapus!');
} else {
    setFlash('danger', 'Aset tidak ditemukan!');
}

header('Location: ' . BASE_URL . '/pages/aset/index.php');
exit;
?>
