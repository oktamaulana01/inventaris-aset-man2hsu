<?php
/**
 * Cron Job: Cek Jatuh Tempo Peminjaman & Kirim Email
 * 
 * Jalankan script ini setiap hari, misalnya jam 07:00 pagi:
 * 
 * Windows Task Scheduler:
 *   Program: C:\laragon\bin\php\php-8.2.28-Win32-vs16-x64\php.exe
 *   Arguments: C:\laragon\www\inventaris-aset-man2hsu\cron\check_due_dates.php
 *   Schedule: Daily at 07:00
 * 
 * Linux Cron:
 *   0 7 * * * /usr/bin/php /path/to/inventaris-aset-man2hsu/cron/check_due_dates.php
 */

// Boleh dijalankan dari CLI atau browser (admin only)
$isCli = (php_sapi_name() === 'cli');

require_once __DIR__ . '/../config/mailer.php';

if (!$isCli) {
    // Jika diakses via browser, harus staff (admin/petugas)
    require_once __DIR__ . '/../includes/auth_check.php';
    requireStaff();
}

$pdo = getConnection();

echo $isCli ? "" : "<pre>";
echo "=== Cek Jatuh Tempo Peminjaman ===\n";
echo "Tanggal: " . date('d/m/Y H:i:s') . "\n\n";

$totalSent = 0;
try {
    $results = checkAndSendNotifications($pdo);
    
    if (empty($results)) {
        echo "Tidak ada notifikasi yang perlu dikirim hari ini.\n";
    } else {
        echo "Hasil pengiriman notifikasi:\n";
        echo str_repeat('-', 60) . "\n";
        
        $sent = 0;
        $failed = 0;
        
        foreach ($results as $r) {
            $status = $r['success'] ? '✅ SENT' : '❌ FAILED';
            $tipLabel = strtoupper($r['tipe']);
            echo "[{$status}] [{$tipLabel}] {$r['nama_aset']} → {$r['peminjam']}\n";
            if (!$r['success']) {
                echo "  Error: {$r['message']}\n";
            }
            $r['success'] ? $sent++ : $failed++;
        }
        
        echo str_repeat('-', 60) . "\n";
        echo "Total: " . count($results) . " | Sent: {$sent} | Failed: {$failed}\n";
    }
    
    // Log ke riwayat aktivitas
    $totalSent = count(array_filter($results, fn($r) => $r['success']));
    if ($totalSent > 0) {
        logActivity($pdo, 0, 'Notifikasi Email', "Cron: {$totalSent} email notifikasi jatuh tempo terkirim");
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== Selesai ===\n";
echo $isCli ? "" : "</pre>";

if (!$isCli) {
    // Redirect kembali jika dari browser
    if (isset($_GET['redirect'])) {
        setFlash('success', "Cron check selesai. {$totalSent} email terkirim.");
        header('Location: ' . BASE_URL . '/pages/pengaturan/log_email.php');
        exit;
    }
}
?>
