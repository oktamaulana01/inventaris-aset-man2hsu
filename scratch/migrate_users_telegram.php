<?php
require_once "c:/laragon/www/inventaris-aset-man2hsu/config/database.php";
$pdo = getConnection();
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN telegram_chat_id VARCHAR(50) DEFAULT NULL AFTER no_telepon");
    echo "Migration successful! Column 'telegram_chat_id' added to table 'users'.\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
