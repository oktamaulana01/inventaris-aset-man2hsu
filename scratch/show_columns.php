<?php
require_once 'c:/laragon/www/inventaris-aset-man2hsu/config/database.php';
$pdo = getConnection();

foreach (['aset', 'peminjaman', 'mutasi_aset', 'users'] as $table) {
    echo "=== COLUMNS OF $table ===\n";
    $cols = $pdo->query("SHOW COLUMNS FROM $table")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        echo "{$c['Field']} ({$c['Type']})\n";
    }
}
