<?php
// =============================================
// Database Configuration - Koneksi MySQL via PDO
// Laragon default: host=localhost, user=root, pass=''
// =============================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'db_inventaris_man2hsu');
define('DB_USER', 'root');
define('DB_PASS', '');
define('BASE_URL', '/inventaris-aset-man2hsu');
define('SESSION_TIMEOUT', 600); // 10 menit

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
        error_log("Koneksi database gagal: " . $e->getMessage());
        die("Terjadi kesalahan pada sistem. Silakan hubungi administrator.");
    }
}

// Helper: Start session jika belum
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Konfigurasi Keamanan Session Cookie
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']), // True jika pakai HTTPS
            'httponly' => true, // Mencegah akses cookie via JavaScript (XSS)
            'samesite' => 'Lax' // Melindungi dari serangan CSRF
        ]);
        session_start();
    }
}

// Helper: Log aktivitas
function logActivity($pdo, $userId, $aktivitas, $keterangan = '') {
    $userId = empty($userId) ? null : $userId; // Convert 0 or empty to null
    $stmt = $pdo->prepare("INSERT INTO riwayat_aktivitas (id_user, aktivitas, keterangan) VALUES (?, ?, ?)");
    $stmt->execute([$userId, $aktivitas, $keterangan]);
}

// Helper: Generate kode aset otomatis
function generateKodeAset($pdo) {
    $tahun = date('Y');
    $prefix = "AST-$tahun-";
    $stmt = $pdo->prepare("SELECT kode_aset FROM aset WHERE kode_aset LIKE ? ORDER BY kode_aset DESC LIMIT 1");
    $stmt->execute([$prefix . '%']);
    $last = $stmt->fetchColumn();
    if ($last) {
        $lastNumber = intval(substr($last, strrpos($last, '-') + 1));
        $nextNumber = $lastNumber + 1;
    } else {
        $nextNumber = 1;
    }
    return sprintf("AST-%s-%03d", $tahun, $nextNumber);
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

// Helper: Generate CSRF Token (returns hidden input HTML)
function generateCsrfToken() {
    $token = getCsrfTokenValue();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

// Helper: Get current CSRF token value
function getCsrfTokenValue() {
    startSession();
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Helper: Validate CSRF Token
function validateCsrfToken() {
    startSession();
    $token = $_POST['csrf_token'] ?? '';
    if (!isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        die('Akses ditolak: Token keamanan tidak valid. Silakan muat ulang halaman.');
    }
}
?>
