<?php
$_GET['id'] = 33;
require_once 'c:/laragon/www/inventaris-aset-man2hsu/config/database.php';
startSession();
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'admin';
$_SESSION['user_nama'] = 'Administrator';

ob_start();
include 'c:/laragon/www/inventaris-aset-man2hsu/pages/berita_acara/peminjaman.php';
$html = ob_get_clean();

echo "Pihak Pertama & Kedua test for ID 33:\n";
preg_match('/<table class="table-pihak">([\s\S]*?)<\/table>/', $html, $m1);
preg_match('/<table class="table-pihak" style="margin-top:6px;">([\s\S]*?)<\/table>/', $html, $m2);

echo "Pihak 1:\n" . strip_tags($m1[0] ?? '') . "\n";
echo "Pihak 2:\n" . strip_tags($m2[0] ?? '') . "\n";
