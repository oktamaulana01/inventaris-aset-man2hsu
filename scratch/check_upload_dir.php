<?php
$uploadDir = 'c:/laragon/www/inventaris-aset-man2hsu/assets/uploads/';
echo "Upload dir exists: " . (is_dir($uploadDir) ? 'YES' : 'NO') . "\n";
echo "Upload dir writable: " . (is_writable($uploadDir) ? 'YES' : 'NO') . "\n";
