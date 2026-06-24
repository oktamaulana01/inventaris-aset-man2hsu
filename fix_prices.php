<?php
require 'config/database.php';
$pdo = getConnection();

$updates = [
    'Projector Epson' => 5890000,
    'Lemari Arsip Kantor GRAY-2' => 1290000,
    'Bola Basket' => 250000,
    'Bola Futsal' => 300000
];

foreach ($updates as $nama => $harga_baru) {
    $stmt = $pdo->prepare("UPDATE aset SET nilai_perolehan = ? WHERE nama_aset = ?");
    $stmt->execute([$harga_baru, $nama]);
}
echo "Harga berhasil diperbaiki!\n";
