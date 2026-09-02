<?php
require_once 'c:/laragon/www/inventaris-aset-man2hsu/config/database.php';
$pdo = getConnection();

$assets = $pdo->query("SELECT id, kode_aset, nama_aset, id_kategori, id_lokasi, jumlah, kondisi, tahun_perolehan, nilai_perolehan, sumber_dana FROM aset")->fetchAll(PDO::FETCH_ASSOC);
echo "Existing Assets (" . count($assets) . "):\n";
foreach ($assets as $a) {
    echo "- [{$a['kode_aset']}] {$a['nama_aset']} (Kondisi: {$a['kondisi']}, Jumlah: {$a['jumlah']})\n";
}
