<?php
require 'config/database.php';
$pdo = getConnection();
try {
    $pdo->exec("ALTER TABLE aset ADD COLUMN bukti_hapus VARCHAR(255) NULL, ADD COLUMN alasan_hapus TEXT NULL");
    echo "Migration success";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage();
}
