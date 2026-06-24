<?php
require 'config/database.php';
$pdo = getConnection();
try {
    $stmt = $pdo->prepare("DELETE FROM aset WHERE deleted_at IS NOT NULL AND alasan_hapus IS NULL");
    $stmt->execute();
    echo "Deleted " . $stmt->rowCount() . " old assets.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
