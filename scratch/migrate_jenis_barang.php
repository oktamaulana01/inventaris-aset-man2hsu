<?php
require_once 'c:/laragon/www/inventaris-aset-man2hsu/config/database.php';
$pdo = getConnection();

// 1. Add jenis_barang column to aset table if not exists
try {
    $pdo->exec("ALTER TABLE aset ADD COLUMN jenis_barang ENUM('Aset Tetap', 'Inventaris Barang') NOT NULL DEFAULT 'Aset Tetap' AFTER id_kategori");
    echo "Added column jenis_barang to aset table\n";
} catch (Exception $e) {
    echo "jenis_barang: " . $e->getMessage() . "\n";
}

// 2. Classify existing dummy/sample items
// Items in Alat Olahraga / ATK / perlengkapan ringan -> 'Inventaris Barang'
// Items in Elektronik / Furniture / Mesin / Kendaraan -> 'Aset Tetap'
$pdo->exec("
    UPDATE aset a
    LEFT JOIN kategori k ON a.id_kategori = k.id
    SET a.jenis_barang = 'Inventaris Barang'
    WHERE k.nama_kategori IN ('Alat Olahraga', 'ATK', 'Perlengkapan', 'Buku / Media Pembelajaran')
       OR a.nama_aset LIKE '%Bola%'
       OR a.nama_aset LIKE '%Raket%'
       OR a.nama_aset LIKE '%Kabel%'
       OR a.nama_aset LIKE '%Papan%'
       OR a.nama_aset LIKE '%Spidol%'
       OR a.nama_aset LIKE '%Mouse%'
");

$pdo->exec("
    UPDATE aset a
    LEFT JOIN kategori k ON a.id_kategori = k.id
    SET a.jenis_barang = 'Aset Tetap'
    WHERE a.jenis_barang IS NULL 
       OR (k.nama_kategori IN ('Elektronik', 'Mebel / Furnitur', 'Kendaraan', 'Mesin & Generator', 'Peralatan Laboratorium')
           AND a.nama_aset NOT LIKE '%Bola%'
           AND a.nama_aset NOT LIKE '%Kabel%')
");

$countAset = $pdo->query("SELECT COUNT(*) FROM aset WHERE jenis_barang = 'Aset Tetap'")->fetchColumn();
$countInv = $pdo->query("SELECT COUNT(*) FROM aset WHERE jenis_barang = 'Inventaris Barang'")->fetchColumn();

echo "Classification summary:\n";
echo "- Aset Tetap: $countAset item\n";
echo "- Inventaris Barang: $countInv item\n";
echo "Database migration completed!\n";
