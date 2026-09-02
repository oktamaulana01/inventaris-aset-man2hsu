<?php
require_once 'c:/laragon/www/inventaris-aset-man2hsu/config/database.php';
$pdo = getConnection();

// Update peminjaman where id_user is guru / same as id_peminjam to staff id 2 (Rudiannor, S.Sos)
$affected = $pdo->exec("
    UPDATE peminjaman 
    SET id_user = 2 
    WHERE id_user IN (SELECT id FROM users WHERE role = 'guru')
       OR (id_peminjam IS NOT NULL AND id_user = id_peminjam)
       OR id_user IS NULL
");
echo "Updated $affected peminjaman records to have id_user = 2 (Rudiannor, S.Sos)\n";
