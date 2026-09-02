<?php
require_once 'c:/laragon/www/inventaris-aset-man2hsu/config/database.php';
$pdo = getConnection();

// 1. Add status_penghapusan, tgl_pengajuan_hapus, file_ba_scan if not exists
try {
    $pdo->exec("ALTER TABLE aset ADD COLUMN status_penghapusan ENUM('none', 'pending', 'approved') NOT NULL DEFAULT 'none' AFTER bukti_hapus");
    echo "Added column status_penghapusan\n";
} catch (Exception $e) {
    echo "status_penghapusan already exists or error: " . $e->getMessage() . "\n";
}

try {
    $pdo->exec("ALTER TABLE aset ADD COLUMN tgl_pengajuan_hapus DATETIME NULL AFTER status_penghapusan");
    echo "Added column tgl_pengajuan_hapus\n";
} catch (Exception $e) {
    echo "tgl_pengajuan_hapus already exists or error: " . $e->getMessage() . "\n";
}

try {
    $pdo->exec("ALTER TABLE aset ADD COLUMN file_ba_scan VARCHAR(255) NULL AFTER tgl_pengajuan_hapus");
    echo "Added column file_ba_scan\n";
} catch (Exception $e) {
    echo "file_ba_scan already exists or error: " . $e->getMessage() . "\n";
}

// 2. Sync existing soft-deleted assets to status_penghapusan = 'approved'
$updated = $pdo->exec("UPDATE aset SET status_penghapusan = 'approved' WHERE deleted_at IS NOT NULL AND status_penghapusan != 'approved'");
echo "Synced $updated soft-deleted assets to status_penghapusan = 'approved'\n";

// 3. Create upload directory for scan BA if not exists
$scanDir = 'c:/laragon/www/inventaris-aset-man2hsu/assets/uploads/ba_penghapusan';
if (!is_dir($scanDir)) {
    mkdir($scanDir, 0777, true);
    echo "Created directory $scanDir\n";
}
