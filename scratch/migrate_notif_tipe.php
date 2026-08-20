<?php
require_once "c:/laragon/www/inventaris-aset-man2hsu/config/database.php";
$pdo = getConnection();
try {
    $pdo->exec("ALTER TABLE email_notifications MODIFY COLUMN tipe ENUM('reminder','due','overdue','approve','reject','return','mutasi','submit') NOT NULL");
    echo "Migration successful! Table 'email_notifications' column 'tipe' modified.\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
