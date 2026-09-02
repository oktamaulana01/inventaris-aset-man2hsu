<?php
$lines = file('C:/laragon/www/inventaris-aset-man2hsu/scratch/skripsi_text.txt');

function printRange($lines, $start, $end) {
    for ($i = $start - 1; $i < $end && $i < count($lines); $i++) {
        echo ($i + 1) . ": " . $lines[$i];
    }
}

echo "=== USE CASE DIAGRAM (2213 - 2240) ===\n";
printRange($lines, 2213, 2240);

echo "\n=== CLASS DIAGRAM (2225 - 2250) ===\n";
printRange($lines, 2225, 2250);

echo "\n=== ACTIVITY DIAGRAM PEMINJAMAN (2380 - 2450) ===\n";
printRange($lines, 2380, 2450);

echo "\n=== SEQUENCE DIAGRAM PEMINJAMAN (2600 - 2640) ===\n";
printRange($lines, 2600, 2640);

echo "\n=== ERD (2680 - 2710) ===\n";
printRange($lines, 2680, 2710);
