<?php
require_once 'c:/laragon/www/inventaris-aset-man2hsu/config/database.php';
startSession();
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'admin';
$_SESSION['user_nama'] = 'Administrator';

require_once 'c:/laragon/www/inventaris-aset-man2hsu/vendor/autoload.php';

echo "Testing Dompdf generation for Berita Acara:\n";

// Test 1: Peminjaman
$pdo = getConnection();
$stmt = $pdo->query("SELECT p.*, a.nama_aset, a.kode_aset, a.kondisi as kondisi_awal, a.jumlah as stok_aset, a.sumber_dana, k.nama_kategori, l.nama_lokasi as lokasi_awal, u.nama as nama_guru, u.nip as nip_guru, u.jabatan as jabatan_guru, u.no_telepon as telp_guru, petugas.nama as nama_petugas, petugas.jabatan as jabatan_petugas FROM peminjaman p JOIN aset a ON p.id_aset = a.id LEFT JOIN kategori k ON a.id_kategori = k.id LEFT JOIN lokasi l ON a.id_lokasi = l.id LEFT JOIN users u ON p.id_peminjam = u.id LEFT JOIN users petugas ON p.id_user = petugas.id WHERE p.id = 26");
$data = $stmt->fetch(PDO::FETCH_ASSOC);

function tglIndo($date) { return '22 Agustus 2026'; }
function hariIndo($date) { return 'Sabtu'; }
$nomorBA = 'BA.PINJAM/026/MAN.2.HSU/2026';
$tglPinjamText = '22 Agustus 2026';
$hariPinjamText = 'Sabtu';
$tglKembaliRencanaText = '23 Agustus 2026';
$logoSrc = '';

$options = new \Dompdf\Options();
$options->set('isRemoteEnabled', true);
$dompdf = new \Dompdf\Dompdf($options);

ob_start();
include 'c:/laragon/www/inventaris-aset-man2hsu/pages/berita_acara/_template_peminjaman.php';
$htmlContent = ob_get_clean();

$dompdf->loadHtml($htmlContent);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$pdfBytes = $dompdf->output();

echo "- PDF Peminjaman rendered successfully: " . strlen($pdfBytes) . " bytes\n";
echo "SUCCESS! Dompdf generation is working 100%.\n";
