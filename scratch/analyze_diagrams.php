<?php
$lines = file('C:/laragon/www/inventaris-aset-man2hsu/scratch/skripsi_text.txt');

echo "=== DAFTAR GAMBAR DIAGRAM DI BAB 3 ===\n";
foreach ($lines as $i => $l) {
    if (preg_match('/(Gambar\s+3\.[0-9]+.*Diagram|3\.3\.[0-9].*Diagram|3\.4\.[0-9].*Diagram)/i', $l)) {
        echo ($i + 1) . ": " . trim($l) . "\n";
    }
}
