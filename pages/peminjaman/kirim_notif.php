<?php
/**
 * Kirim notifikasi email manual untuk satu peminjaman
 */
require_once __DIR__ . '/../../includes/auth_check.php';
validateCsrfToken();
$pdo = getConnection();
require_once __DIR__ . '/../../config/mailer.php';

$id = intval($_POST['id'] ?? 0);

// Ambil data peminjaman
$stmt = $pdo->prepare("
    SELECT p.*, a.nama_aset, a.kode_aset, u.email
    FROM peminjaman p
    JOIN aset a ON p.id_aset = a.id
    LEFT JOIN users u ON p.id_peminjam = u.id
    WHERE p.id = ? AND p.status = 'Dipinjam'
");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    setFlash('danger', 'Data peminjaman tidak ditemukan atau sudah dikembalikan!');
    header('Location: index.php'); exit;
}

if (empty($data['email'])) {
    setFlash('danger', 'Peminjam tidak memiliki alamat email! Silakan tambahkan email di data pengguna.');
    header('Location: index.php'); exit;
}

// Tentukan tipe berdasarkan tanggal
$today = new DateTime(date('Y-m-d'));
$dueDate = new DateTime($data['tanggal_kembali_rencana']);
$diff = $today->diff($dueDate);
$daysDiff = (int) $diff->format('%r%a');

if ($daysDiff > 0) {
    $tipe = 'reminder';
} elseif ($daysDiff === 0) {
    $tipe = 'due';
} else {
    $tipe = 'overdue';
}

$result = sendNotification($pdo, $data, $tipe, $data['email']);
logActivity($pdo, $_SESSION['user_id'], 'Kirim Notifikasi', "Kirim email {$tipe} untuk aset: {$data['nama_aset']} ke {$data['email']}");

setFlash($result['success'] ? 'success' : 'danger', $result['message']);
header('Location: index.php'); exit;
?>
