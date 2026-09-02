<?php
$lines = file('C:/laragon/www/inventaris-aset-man2hsu/scratch/skripsi_text.txt');

echo "=== DAFTAR BAB DAN SUB-BAB ===\n";
foreach ($lines as $i => $l) {
    $t = trim($l);
    if (preg_match('/^(BAB\s+[IVX]+|[1-5]\.[0-9]+(\.[0-9]+)?\s+[A-Z])/i', $t) && strlen($t) < 80) {
        echo ($i + 1) . ": " . $t . "\n";
    }
}
