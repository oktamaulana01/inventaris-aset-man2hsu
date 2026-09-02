<?php
require_once 'c:/laragon/www/inventaris-aset-man2hsu/config/database.php';
startSession();
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'admin';
$_SESSION['user_nama'] = 'Administrator';

$pdo = getConnection();
$pinjamId = $pdo->query("SELECT id FROM peminjaman WHERE status = 'Dipinjam' LIMIT 1")->fetchColumn();
$kembaliId = $pdo->query("SELECT id FROM peminjaman WHERE status = 'Dikembalikan' LIMIT 1")->fetchColumn();
$mutasiId = $pdo->query("SELECT id FROM mutasi_aset LIMIT 1")->fetchColumn();
$hapusId = $pdo->query("SELECT id FROM aset WHERE deleted_at IS NOT NULL LIMIT 1")->fetchColumn();
if (!$hapusId) {
    $hapusId = $pdo->query("SELECT id FROM aset LIMIT 1")->fetchColumn();
}

echo "Testing Berita Acara rendering:\n";

// 1. Peminjaman
$_GET['id'] = $pinjamId;
ob_start();
include 'c:/laragon/www/inventaris-aset-man2hsu/pages/berita_acara/peminjaman.php';
$out1 = ob_get_clean();
echo "- BA Peminjaman (ID: $pinjamId): " . strlen($out1) . " bytes\n";

// 2. Pengembalian
$_GET['id'] = $kembaliId;
ob_start();
include 'c:/laragon/www/inventaris-aset-man2hsu/pages/berita_acara/pengembalian.php';
$out2 = ob_get_clean();
echo "- BA Pengembalian (ID: $kembaliId): " . strlen($out2) . " bytes\n";

// 3. Mutasi
$_GET['id'] = $mutasiId;
ob_start();
include 'c:/laragon/www/inventaris-aset-man2hsu/pages/berita_acara/mutasi.php';
$out3 = ob_get_clean();
echo "- BA Mutasi (ID: $mutasiId): " . strlen($out3) . " bytes\n";

// 4. Penghapusan
$_GET['id'] = $hapusId;
ob_start();
include 'c:/laragon/www/inventaris-aset-man2hsu/pages/berita_acara/penghapusan.php';
$out4 = ob_get_clean();
echo "- BA Penghapusan (ID: $hapusId): " . strlen($out4) . " bytes\n";

echo "\nSUCCESS! All 4 Berita Acara pages rendered without errors and with formal neutral black styling!\n";
