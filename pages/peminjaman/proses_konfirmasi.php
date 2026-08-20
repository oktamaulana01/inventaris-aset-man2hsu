<?php
require_once __DIR__ . '/../../includes/auth_check.php';
requireStaff();
validateCsrfToken();
$pdo = getConnection();

$action = $_GET['action'] ?? '';
$id = intval($_POST['id'] ?? 0);

$stmt = $pdo->prepare("SELECT p.*, a.nama_aset FROM peminjaman p JOIN aset a ON p.id_aset = a.id WHERE p.id = ?");
$stmt->execute([$id]); 
$data = $stmt->fetch();

if ($data && $data['status'] === 'Menunggu Konfirmasi') {
    require_once __DIR__ . '/../../config/mailer.php';
    if ($action === 'approve') {
        $pdo->prepare("UPDATE peminjaman SET status = 'Dipinjam' WHERE id = ?")->execute([$id]);
        logActivity($pdo, $_SESSION['user_id'], 'Konfirmasi', "Menyetujui peminjaman aset: {$data['nama_aset']} oleh {$data['nama_peminjam']}");
        
        $msg = "📢 <b>Persetujuan Peminjaman Aset</b>\n\n" .
               "Status: <b>DISETUJUI</b>\n" .
               "Peminjam: <b>" . htmlspecialchars($data['nama_peminjam']) . "</b>\n" .
               "Aset: <b>" . htmlspecialchars($data['nama_aset']) . "</b>\n" .
               "Petugas: " . htmlspecialchars($_SESSION['user_nama']);
        sendTelegramNotification($pdo, $msg);
        
        setFlash('success', 'Permintaan peminjaman berhasil disetujui!');
    } elseif ($action === 'reject') {
        $pdo->prepare("UPDATE peminjaman SET status = 'Ditolak' WHERE id = ?")->execute([$id]);
        logActivity($pdo, $_SESSION['user_id'], 'Konfirmasi', "Menolak peminjaman aset: {$data['nama_aset']} oleh {$data['nama_peminjam']}");
        
        $msg = "📢 <b>Penolakan Peminjaman Aset</b>\n\n" .
               "Status: <b>DITOLAK</b>\n" .
               "Peminjam: <b>" . htmlspecialchars($data['nama_peminjam']) . "</b>\n" .
               "Aset: <b>" . htmlspecialchars($data['nama_aset']) . "</b>\n" .
               "Petugas: " . htmlspecialchars($_SESSION['user_nama']);
        sendTelegramNotification($pdo, $msg);
        
        setFlash('warning', 'Permintaan peminjaman telah ditolak.');
    } else {
        setFlash('danger', 'Aksi tidak valid!');
    }
} else {
    setFlash('danger', 'Data peminjaman tidak valid atau sudah diproses!');
}
header('Location: ' . BASE_URL . '/peminjaman'); 
exit;
