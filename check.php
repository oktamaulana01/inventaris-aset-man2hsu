<?php
require 'config/database.php';
$pdo = getConnection();
$stmt = $pdo->query("SELECT id, kode_aset, nama_aset, deleted_at FROM aset WHERE deleted_at IS NOT NULL AND alasan_hapus IS NULL");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
