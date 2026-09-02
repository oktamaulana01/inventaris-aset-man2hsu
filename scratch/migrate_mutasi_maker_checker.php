<?php
require_once 'c:/laragon/www/inventaris-aset-man2hsu/config/database.php';
$pdo = getConnection();

// 1. Add columns to mutasi_aset if not exists
try {
    $pdo->exec("ALTER TABLE mutasi_aset ADD COLUMN status ENUM('pending', 'completed', 'cancelled') NOT NULL DEFAULT 'completed' AFTER keterangan");
    echo "Added column status to mutasi_aset\n";
} catch (Exception $e) {
    echo "status in mutasi_aset: " . $e->getMessage() . "\n";
}

try {
    $pdo->exec("ALTER TABLE mutasi_aset ADD COLUMN no_bast VARCHAR(100) NULL AFTER status");
    echo "Added column no_bast to mutasi_aset\n";
} catch (Exception $e) {
    echo "no_bast in mutasi_aset: " . $e->getMessage() . "\n";
}

try {
    $pdo->exec("ALTER TABLE mutasi_aset ADD COLUMN tgl_terima DATETIME NULL AFTER no_bast");
    echo "Added column tgl_terima to mutasi_aset\n";
} catch (Exception $e) {
    echo "tgl_terima in mutasi_aset: " . $e->getMessage() . "\n";
}

try {
    $pdo->exec("ALTER TABLE mutasi_aset ADD COLUMN file_bast_scan VARCHAR(255) NULL AFTER tgl_terima");
    echo "Added column file_bast_scan to mutasi_aset\n";
} catch (Exception $e) {
    echo "file_bast_scan in mutasi_aset: " . $e->getMessage() . "\n";
}

try {
    $pdo->exec("ALTER TABLE mutasi_aset ADD COLUMN catatan_terima TEXT NULL AFTER file_bast_scan");
    echo "Added column catatan_terima to mutasi_aset\n";
} catch (Exception $e) {
    echo "catatan_terima in mutasi_aset: " . $e->getMessage() . "\n";
}

try {
    $pdo->exec("ALTER TABLE mutasi_aset ADD COLUMN id_user_terima INT NULL AFTER catatan_terima");
    echo "Added column id_user_terima to mutasi_aset\n";
} catch (Exception $e) {
    echo "id_user_terima in mutasi_aset: " . $e->getMessage() . "\n";
}

// 2. Add status_mutasi to aset table if not exists
try {
    $pdo->exec("ALTER TABLE aset ADD COLUMN status_mutasi ENUM('none', 'in_transit') NOT NULL DEFAULT 'none' AFTER status_penghapusan");
    echo "Added column status_mutasi to aset\n";
} catch (Exception $e) {
    echo "status_mutasi in aset: " . $e->getMessage() . "\n";
}

// 3. Create upload directory for scan BAST mutasi
$bastDir = 'c:/laragon/www/inventaris-aset-man2hsu/assets/uploads/bast_mutasi';
if (!is_dir($bastDir)) {
    mkdir($bastDir, 0777, true);
    echo "Created directory $bastDir\n";
}

echo "Database migration for Mutasi Maker-Checker completed successfully!\n";
