<?php
// Redirect to login page
require_once __DIR__ . '/config/database.php';
header('Location: ' . BASE_URL . '/login.php');
exit;
?>
