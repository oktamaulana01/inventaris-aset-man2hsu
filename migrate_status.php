<?php
require 'config/database.php';
$pdo = getConnection();
try {
    $pdo->exec("ALTER TABLE peminjaman MODIFY COLUMN status ENUM('Menunggu Konfirmasi', 'Ditolak', 'Dipinjam', 'Dikembalikan') NOT NULL DEFAULT 'Dipinjam'");
    echo "Migration success";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage();
}
