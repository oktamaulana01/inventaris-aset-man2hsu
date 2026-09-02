<?php
require_once 'c:/laragon/www/inventaris-aset-man2hsu/config/database.php';
$pdo = getConnection();
$stmt = $pdo->prepare("
    SELECT p.*, 
           u.nama as u_nama, u.role as u_role,
           petugas.nama as pet_nama, petugas.role as pet_role
    FROM peminjaman p
    LEFT JOIN users u ON p.id_peminjam = u.id
    LEFT JOIN users petugas ON p.id_user = petugas.id
    WHERE p.id = ?
");
$stmt->execute([33]);
$r = $stmt->fetch(PDO::FETCH_ASSOC);
print_r($r);
