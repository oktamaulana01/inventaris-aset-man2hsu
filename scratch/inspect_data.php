<?php
require_once 'c:/laragon/www/inventaris-aset-man2hsu/config/database.php';
$pdo = getConnection();

echo "=== USERS ===\n";
$users = $pdo->query("SELECT id, nama, username, role, nip, jabatan, no_telepon FROM users")->fetchAll(PDO::FETCH_ASSOC);
print_r($users);

echo "\n=== KATEGORI ===\n";
$kat = $pdo->query("SELECT * FROM kategori")->fetchAll(PDO::FETCH_ASSOC);
print_r($kat);

echo "\n=== LOKASI ===\n";
$lok = $pdo->query("SELECT * FROM lokasi")->fetchAll(PDO::FETCH_ASSOC);
print_r($lok);

echo "\n=== ASET (First 5) ===\n";
$aset = $pdo->query("SELECT id, kode_aset, nama_aset, id_kategori, id_lokasi, jumlah, kondisi, sumber_dana, tahun_perolehan, harga, status FROM aset LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
print_r($aset);
