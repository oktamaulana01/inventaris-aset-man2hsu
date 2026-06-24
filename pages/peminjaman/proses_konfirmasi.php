<?php
require_once __DIR__ . '/../../includes/auth_check.php';
validateCsrfToken();
$pdo = getConnection();

$action = $_GET['action'] ?? '';
$id = intval($_POST['id'] ?? 0);

$stmt = $pdo->prepare("SELECT p.*, a.nama_aset FROM peminjaman p JOIN aset a ON p.id_aset = a.id WHERE p.id = ?");
$stmt->execute([$id]); 
$data = $stmt->fetch();

if ($data && $data['status'] === 'Menunggu Konfirmasi') {
    if ($action === 'approve') {
        $pdo->prepare("UPDATE peminjaman SET status = 'Dipinjam' WHERE id = ?")->execute([$id]);
        logActivity($pdo, $_SESSION['user_id'], 'Konfirmasi', "Menyetujui peminjaman aset: {$data['nama_aset']} oleh {$data['nama_peminjam']}");
        setFlash('success', 'Permintaan peminjaman berhasil disetujui!');
    } elseif ($action === 'reject') {
        $pdo->prepare("UPDATE peminjaman SET status = 'Ditolak' WHERE id = ?")->execute([$id]);
        logActivity($pdo, $_SESSION['user_id'], 'Konfirmasi', "Menolak peminjaman aset: {$data['nama_aset']} oleh {$data['nama_peminjam']}");
        setFlash('warning', 'Permintaan peminjaman telah ditolak.');
    } else {
        setFlash('danger', 'Aksi tidak valid!');
    }
} else {
    setFlash('danger', 'Data peminjaman tidak valid atau sudah diproses!');
}
header('Location: index.php'); 
exit;
