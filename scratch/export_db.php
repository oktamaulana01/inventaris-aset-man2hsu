<?php
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'db_inventaris_man2hsu';
$outputFile = 'c:/laragon/www/inventaris-aset-man2hsu/db_inventaris_man2hsu.sql';

$mysqldumpPath = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe';
if (!file_exists($mysqldumpPath)) {
    // Search for mysqldump in laragon directory
    $dirs = glob('C:\\laragon\\bin\\mysql\\*\\bin\\mysqldump.exe');
    if (!empty($dirs)) {
        $mysqldumpPath = $dirs[0];
    }
}

$cmd = "\"$mysqldumpPath\" -u$dbUser --databases $dbName > \"$outputFile\"";
exec($cmd, $output, $returnCode);

if ($returnCode === 0 && file_exists($outputFile) && filesize($outputFile) > 0) {
    echo "SQL Dump successfully exported to db_inventaris_man2hsu.sql (Size: " . filesize($outputFile) . " bytes)\n";
} else {
    echo "mysqldump command failed or returned code $returnCode.\n";
}
