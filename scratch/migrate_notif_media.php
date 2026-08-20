<?php
require_once "c:/laragon/www/inventaris-aset-man2hsu/config/database.php";
$pdo = getConnection();
try {
    $pdo->exec("ALTER TABLE email_notifications ADD COLUMN media ENUM('email', 'telegram') NOT NULL DEFAULT 'email' AFTER tipe");
    echo "Migration successful! Column 'media' added to table 'email_notifications'.\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
