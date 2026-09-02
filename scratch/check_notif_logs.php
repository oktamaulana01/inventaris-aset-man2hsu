<?php
require_once 'c:/laragon/www/inventaris-aset-man2hsu/config/database.php';
$pdo = getConnection();

$cols = $pdo->query("SHOW COLUMNS FROM email_notifications")->fetchAll(PDO::FETCH_ASSOC);
echo "Columns of email_notifications:\n";
foreach ($cols as $c) {
    echo "- {$c['Field']} ({$c['Type']})\n";
}

$rows = $pdo->query("SELECT * FROM email_notifications ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
echo "\nLast 5 rows of email_notifications:\n";
print_r($rows);
