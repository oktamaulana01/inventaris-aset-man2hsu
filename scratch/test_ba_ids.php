<?php
require_once 'c:/laragon/www/inventaris-aset-man2hsu/config/database.php';
$pdo = getConnection();

// 1. Test Peminjaman BA
$pinjam = $pdo->query("SELECT id FROM peminjaman WHERE status = 'Dipinjam' LIMIT 1")->fetchColumn();
echo "Sample Dipinjam ID: $pinjam\n";

// 2. Test Pengembalian BA
$kembali = $pdo->query("SELECT id FROM peminjaman WHERE status = 'Dikembalikan' LIMIT 1")->fetchColumn();
echo "Sample Dikembalikan ID: $kembali\n";

// 3. Test Mutasi BA
$mutasi = $pdo->query("SELECT id FROM mutasi_aset LIMIT 1")->fetchColumn();
echo "Sample Mutasi ID: $mutasi\n";

// 4. Test Penghapusan BA
$hapus = $pdo->query("SELECT id FROM aset LIMIT 1")->fetchColumn();
echo "Sample Aset ID: $hapus\n";
