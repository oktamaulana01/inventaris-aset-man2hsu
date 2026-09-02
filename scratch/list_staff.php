<?php
require_once 'c:/laragon/www/inventaris-aset-man2hsu/config/database.php';
$pdo = getConnection();
$staff = $pdo->query("SELECT id, nama, nip, jabatan, role FROM users WHERE role IN ('admin', 'petugas')")->fetchAll(PDO::FETCH_ASSOC);
print_r($staff);
