<?php
// =============================================
// Database Configuration - Koneksi MySQL via PDO
// Laragon default: host=localhost, user=root, pass=''
// =============================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'db_inventaris_man2hsu');
define('DB_USER', 'root');
define('DB_PASS', '');

function getConnection() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        die("Koneksi database gagal: " . $e->getMessage());
    }
}

// Helper: Start session jika belum
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// Helper: Log aktivitas
function logActivity($pdo, $userId, $aktivitas, $keterangan = '') {
    $stmt = $pdo->prepare("INSERT INTO riwayat_aktivitas (id_user, aktivitas, keterangan) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $aktivitas, $keterangan]);
}

// Helper: Generate kode aset otomatis
function generateKodeAset($pdo) {
    $tahun = date('Y');
    $stmt = $pdo->query("SELECT MAX(id) as max_id FROM aset");
    $result = $stmt->fetch();
    $nextId = ($result['max_id'] ?? 0) + 1;
    return sprintf("AST-%s-%03d", $tahun, $nextId);
}

// Helper: Format Rupiah
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

// Helper: Alert flash message
function setFlash($type, $message) {
    startSession();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    startSession();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
?>
