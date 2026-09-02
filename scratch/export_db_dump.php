<?php
require_once 'c:/laragon/www/inventaris-aset-man2hsu/config/database.php';
$pdo = getConnection();

$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
$sqlDump = "-- Database Dump Inventaris MAN 2 HSU\n-- Date: " . date('Y-m-d H:i:s') . "\n\nSET FOREIGN_KEY_CHECKS=0;\n\n";

foreach ($tables as $t) {
    $create = $pdo->query("SHOW CREATE TABLE `$t`")->fetch(PDO::FETCH_ASSOC);
    $sqlDump .= "DROP TABLE IF EXISTS `$t`;\n" . $create['Create Table'] . ";\n\n";
    
    $rows = $pdo->query("SELECT * FROM `$t`")->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($rows)) {
        $cols = array_keys($rows[0]);
        $colsStr = '`' . implode('`, `', $cols) . '`';
        $sqlDump .= "INSERT INTO `$t` ($colsStr) VALUES\n";
        $valStrings = [];
        foreach ($rows as $row) {
            $vals = array_map(function($v) use ($pdo) {
                if ($v === null) return 'NULL';
                return $pdo->quote($v);
            }, array_values($row));
            $valStrings[] = '(' . implode(', ', $vals) . ')';
        }
        $sqlDump .= implode(",\n", $valStrings) . ";\n\n";
    }
}
$sqlDump .= "SET FOREIGN_KEY_CHECKS=1;\n";

file_put_contents('c:/laragon/www/inventaris-aset-man2hsu/db_inventaris_man2hsu.sql', $sqlDump);
echo "Exported db_inventaris_man2hsu.sql successfully! (" . strlen($sqlDump) . " bytes)\n";
