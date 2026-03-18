<?php
require_once __DIR__ . '/../../includes/auth_check.php';
$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT p.*, a.nama_aset FROM peminjaman p JOIN aset a ON p.id_aset = a.id WHERE p.id = ?");
$stmt->execute([$id]); $data = $stmt->fetch();
if ($data && $data['status'] === 'Dipinjam') {
    $pdo->prepare("UPDATE peminjaman SET status = 'Dikembalikan', tanggal_kembali_aktual = CURDATE() WHERE id = ?")->execute([$id]);
    logActivity($pdo, $_SESSION['user_id'], 'Pengembalian', "Pengembalian aset: {$data['nama_aset']} oleh {$data['nama_peminjam']}");
    setFlash('success', 'Aset berhasil dikembalikan!');
} else { setFlash('danger', 'Data peminjaman tidak valid!'); }
header('Location: index.php'); exit;
?>
