<?php
require_once __DIR__ . '/../config/database.php';
startSession();

// Proses Logout
if (isset($_GET['logout'])) {
    $pdo = getConnection();
    logActivity($pdo, $_SESSION['user_id'], 'Logout', $_SESSION['user_nama'] . ' melakukan logout');
    session_destroy();
    header('Location: /inventaris-aset-man2hsu/login.php');
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$currentDir = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Inventarisasi Aset' ?> - MAN 2 HSU</title>
    <meta name="description" content="Sistem Informasi Inventarisasi Aset Madrasah Aliyah Negeri 2 Hulu Sungai Utara">
    <link rel="stylesheet" href="/inventaris-aset-man2hsu/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
    <div class="app-layout">
        <div class="sidebar-overlay" onclick="toggleSidebar()"></div>
