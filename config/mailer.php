<?php
// =============================================
// Email Notification Helper
// Menggunakan PHPMailer untuk kirim email via SMTP
// =============================================

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/database.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Ambil semua pengaturan SMTP dari database
 */
function getSmtpSettings($pdo) {
    $stmt = $pdo->query("SELECT kunci, nilai FROM pengaturan");
    $settings = [];
    while ($row = $stmt->fetch()) {
        $settings[$row['kunci']] = $row['nilai'];
    }
    return $settings;
}

/**
 * Inisialisasi PHPMailer dengan konfigurasi SMTP dari database
 */
function getMailer($pdo) {
    $settings = getSmtpSettings($pdo);
    
    if (empty($settings['smtp_username']) || empty($settings['smtp_password'])) {
        throw new Exception('Konfigurasi SMTP belum lengkap. Silakan isi di halaman Pengaturan.');
    }
    
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = $settings['smtp_host'] ?? 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = $settings['smtp_username'];
    $mail->Password   = $settings['smtp_password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = intval($settings['smtp_port'] ?? 587);
    $mail->CharSet    = 'UTF-8';
    
    $senderEmail = $settings['smtp_sender_email'] ?: $settings['smtp_username'];
    $senderName  = $settings['smtp_sender_name'] ?: 'MAN 2 HSU - Inventaris Aset';
    $mail->setFrom($senderEmail, $senderName);
    
    return $mail;
}

/**
 * Generate HTML template email notifikasi
 */
function generateEmailTemplate($data, $tipe) {
    $namaAset = htmlspecialchars($data['nama_aset']);
    $kodeAset = htmlspecialchars($data['kode_aset']);
    $peminjam = htmlspecialchars($data['nama_peminjam']);
    $tglPinjam = date('d/m/Y', strtotime($data['tanggal_pinjam']));
    $tglKembali = date('d/m/Y', strtotime($data['tanggal_kembali_rencana']));
    
    $today = new DateTime();
    $dueDate = new DateTime($data['tanggal_kembali_rencana']);
    $diff = $today->diff($dueDate);
    $daysLate = $today > $dueDate ? $diff->days : 0;

    // Warna dan teks berdasarkan tipe
    switch ($tipe) {
        case 'reminder':
            $headerColor = '#1e7256'; // Warna hijau resmi MAN 2
            $headerIcon = '⏰';
            $headerText = 'Pengingat Pengembalian Aset';
            $messageText = "peminjaman aset berikut <strong>akan jatuh tempo besok</strong>. Mohon persiapkan pengembalian.";
            break;
        case 'due':
            $headerColor = '#ef4444';
            $headerIcon = '⚠️';
            $headerText = 'Jatuh Tempo Pengembalian Hari Ini';
            $messageText = "peminjaman aset berikut <strong>jatuh tempo hari ini</strong>. Mohon segera kembalikan.";
            break;
        case 'overdue':
            $headerColor = '#dc2626';
            $headerIcon = '🚨';
            $headerText = 'Keterlambatan Pengembalian Aset';
            $messageText = "peminjaman aset berikut <strong>sudah melewati batas pengembalian selama {$daysLate} hari</strong>. Mohon segera kembalikan.";
            break;
        default:
            $headerColor = '#1e7256';
            $headerIcon = '📋';
            $headerText = 'Notifikasi Peminjaman Aset';
            $messageText = "berikut informasi peminjaman aset Anda.";
    }
    
    return "
    <!DOCTYPE html>
    <html>
    <head><meta charset='UTF-8'></head>
    <body style='margin:0;padding:0;font-family:Arial,Helvetica,sans-serif;background:#f4f7f5;'>
        <div style='max-width:600px;margin:20px auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);'>
            <!-- Header -->
            <div style='background:{$headerColor};padding:28px 32px;text-align:center;'>
                <div style='font-size:2rem;margin-bottom:8px;'>{$headerIcon}</div>
                <h1 style='color:#fff;font-size:1.3rem;margin:0;font-weight:700;'>{$headerText}</h1>
            </div>
            
            <!-- Body -->
            <div style='padding:32px;'>
                <p style='font-size:0.95rem;color:#333;line-height:1.6;margin-top:0;'>
                    Yth. <strong>{$peminjam}</strong>,
                </p>
                <p style='font-size:0.95rem;color:#333;line-height:1.6;'>
                    Kami informasikan bahwa {$messageText}
                </p>
                
                <!-- Detail Card -->
                <div style='background:#f8faf9;border:1px solid #e0e7e3;border-radius:10px;padding:20px;margin:20px 0;'>
                    <table style='width:100%;border-collapse:collapse;font-size:0.9rem;'>
                        <tr>
                            <td style='padding:8px 12px;color:#666;width:140px;'>Kode Aset</td>
                            <td style='padding:8px 12px;font-weight:600;color:#1e7256;'>{$kodeAset}</td>
                        </tr>
                        <tr>
                            <td style='padding:8px 12px;color:#666;border-top:1px solid #e8ece9;'>Nama Aset</td>
                            <td style='padding:8px 12px;font-weight:600;color:#333;border-top:1px solid #e8ece9;'>{$namaAset}</td>
                        </tr>
                        <tr>
                            <td style='padding:8px 12px;color:#666;border-top:1px solid #e8ece9;'>Tanggal Pinjam</td>
                            <td style='padding:8px 12px;color:#333;border-top:1px solid #e8ece9;'>{$tglPinjam}</td>
                        </tr>
                        <tr>
                            <td style='padding:8px 12px;color:#666;border-top:1px solid #e8ece9;'>Batas Kembali</td>
                            <td style='padding:8px 12px;font-weight:700;color:{$headerColor};border-top:1px solid #e8ece9;'>{$tglKembali}</td>
                        </tr>
                    </table>
                </div>
                
                <p style='font-size:0.9rem;color:#555;line-height:1.6;'>
                    Mohon segera kembalikan aset tersebut ke petugas inventaris madrasah.
                </p>
                
                <hr style='border:none;border-top:1px solid #eee;margin:24px 0;'>
                
                <p style='font-size:0.78rem;color:#999;line-height:1.5;margin-bottom:0;'>
                    Email ini dikirim secara otomatis oleh Sistem Inventarisasi Aset MAN 2 Hulu Sungai Utara. 
                    Jika sudah mengembalikan aset, abaikan email ini.
                </p>
            </div>
            
            <!-- Footer -->
            <div style='background:#f0f5f1;padding:16px 32px;text-align:center;'>
                <p style='margin:0;font-size:0.78rem;color:#888;'>
                    &copy; " . date('Y') . " Sistem Inventarisasi Aset — MAN 2 Hulu Sungai Utara
                </p>
            </div>
        </div>
    </body>
    </html>";
}

/**
 * Kirim email notifikasi untuk satu peminjaman
 */
function sendNotification($pdo, $peminjaman, $tipe, $emailTujuan) {
    try {
        $mail = getMailer($pdo);
        $mail->addAddress($emailTujuan);
        $mail->isHTML(true);
        
        // Subject
        $namaAset = $peminjaman['nama_aset'];
        switch ($tipe) {
            case 'reminder':
                $mail->Subject = "⏰ Pengingat Pengembalian Aset — {$namaAset}";
                break;
            case 'due':
                $mail->Subject = "⚠️ Jatuh Tempo Hari Ini — {$namaAset}";
                break;
            case 'overdue':
                $mail->Subject = "🚨 Keterlambatan Pengembalian — {$namaAset}";
                break;
        }
        
        $mail->Body = generateEmailTemplate($peminjaman, $tipe);
        $mail->AltBody = "Pengingat: Aset {$namaAset} ({$peminjaman['kode_aset']}) harus dikembalikan pada " . date('d/m/Y', strtotime($peminjaman['tanggal_kembali_rencana']));
        
        $mail->send();
        
        // Log success
        $stmt = $pdo->prepare("INSERT INTO email_notifications (id_peminjaman, tipe, email_tujuan, status) VALUES (?, ?, ?, 'sent')");
        $stmt->execute([$peminjaman['id'], $tipe, $emailTujuan]);
        
        return ['success' => true, 'message' => 'Email berhasil dikirim'];
        
    } catch (Exception $e) {
        // Log failure
        $errorMsg = $e->getMessage();
        $stmt = $pdo->prepare("INSERT INTO email_notifications (id_peminjaman, tipe, email_tujuan, status, pesan_error) VALUES (?, ?, ?, 'failed', ?)");
        $stmt->execute([$peminjaman['id'], $tipe, $emailTujuan, $errorMsg]);
        
        return ['success' => false, 'message' => 'Gagal kirim email: ' . $errorMsg];
    }
}

/**
 * Cek dan kirim notifikasi untuk semua peminjaman aktif
 * Dipanggil oleh cron job harian
 */
function checkAndSendNotifications($pdo) {
    $settings = getSmtpSettings($pdo);
    $results = [];
    
    // Ambil semua peminjaman aktif yang peminjamnya punya email
    $stmt = $pdo->query("
        SELECT p.*, a.nama_aset, a.kode_aset, u.email, u.nama as user_nama
        FROM peminjaman p
        JOIN aset a ON p.id_aset = a.id
        LEFT JOIN users u ON p.id_peminjam = u.id
        WHERE p.status = 'Dipinjam'
        AND u.email IS NOT NULL AND u.email != ''
        ORDER BY p.tanggal_kembali_rencana ASC
    ");
    $peminjamanList = $stmt->fetchAll();
    
    $today = new DateTime(date('Y-m-d'));
    
    foreach ($peminjamanList as $p) {
        $dueDate = new DateTime($p['tanggal_kembali_rencana']);
        $diff = $today->diff($dueDate);
        $daysDiff = (int) $diff->format('%r%a'); // negatif = sudah lewat
        
        $tipe = null;
        
        if ($daysDiff === 1 && ($settings['notif_h_minus_1'] ?? '1') === '1') {
            $tipe = 'reminder'; // H-1
        } elseif ($daysDiff === 0 && ($settings['notif_h_0'] ?? '1') === '1') {
            $tipe = 'due'; // H+0
        } elseif ($daysDiff < 0 && ($settings['notif_h_plus_1'] ?? '1') === '1') {
            $tipe = 'overdue'; // H+1 dst
        }
        
        if ($tipe) {
            // Cek apakah sudah pernah dikirim tipe ini untuk peminjaman ini
            $checkStmt = $pdo->prepare("
                SELECT COUNT(*) FROM email_notifications 
                WHERE id_peminjaman = ? AND tipe = ? AND status = 'sent'
            ");
            $checkStmt->execute([$p['id'], $tipe]);
            $alreadySent = $checkStmt->fetchColumn() > 0;
            
            if (!$alreadySent) {
                $result = sendNotification($pdo, $p, $tipe, $p['email']);
                $result['peminjaman_id'] = $p['id'];
                $result['nama_aset'] = $p['nama_aset'];
                $result['peminjam'] = $p['nama_peminjam'];
                $result['tipe'] = $tipe;
                $results[] = $result;
            }
        }
    }
    
    return $results;
}

/**
 * Kirim test email untuk verifikasi konfigurasi SMTP
 */
function sendTestEmail($pdo, $emailTujuan) {
    try {
        $mail = getMailer($pdo);
        $mail->addAddress($emailTujuan);
        $mail->isHTML(true);
        $mail->Subject = '✅ Test Email — Sistem Inventarisasi Aset MAN 2 HSU';
        $mail->Body = "
        <div style='font-family:Arial,sans-serif;max-width:500px;margin:20px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);'>
            <div style='background:linear-gradient(135deg,#1e7256,#28956e);padding:24px;text-align:center;'>
                <h2 style='color:#fff;margin:0;'>✅ Test Email Berhasil!</h2>
            </div>
            <div style='padding:24px;'>
                <p style='color:#333;'>Konfigurasi SMTP sudah benar. Sistem notifikasi email siap digunakan.</p>
                <p style='color:#888;font-size:0.85rem;'>Dikirim pada: " . date('d/m/Y H:i:s') . "</p>
            </div>
        </div>";
        $mail->AltBody = 'Test email berhasil. Konfigurasi SMTP sudah benar.';
        $mail->send();
        
        return ['success' => true, 'message' => 'Test email berhasil dikirim ke ' . $emailTujuan];
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'Gagal: ' . $e->getMessage()];
    }
}

/**
 * Kirim email untuk reset password
 */
function sendResetPasswordEmail($pdo, $emailTujuan, $nama, $resetLink) {
    try {
        $mail = getMailer($pdo);
        $mail->addAddress($emailTujuan);
        $mail->isHTML(true);
        $mail->Subject = '🔑 Reset Password — Sistem Inventarisasi Aset MAN 2 HSU';
        $mail->Body = "
        <div style='font-family:Arial,sans-serif;max-width:500px;margin:20px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);'>
            <div style='background:linear-gradient(135deg,#1e7256,#28956e);padding:24px;text-align:center;'>
                <h2 style='color:#fff;margin:0;'>Reset Password</h2>
            </div>
            <div style='padding:24px;'>
                <p style='color:#333;'>Halo <strong>" . htmlspecialchars($nama) . "</strong>,</p>
                <p style='color:#333;line-height:1.5;'>Kami menerima permintaan untuk mengatur ulang password akun Anda. Klik tombol di bawah ini untuk melanjutkan proses reset password. Link ini hanya berlaku selama 30 menit.</p>
                <div style='text-align:center;margin:30px 0;'>
                    <a href='" . htmlspecialchars($resetLink) . "' style='background:#1e7256;color:#fff;text-decoration:none;padding:12px 24px;border-radius:6px;font-weight:bold;display:inline-block;'>Reset Password Sekarang</a>
                </div>
                <p style='color:#888;font-size:0.85rem;line-height:1.4;'>Jika Anda tidak meminta reset password, abaikan email ini. Akun Anda akan tetap aman.</p>
            </div>
            <div style='background:#f0f5f1;padding:16px;text-align:center;color:#888;font-size:0.8rem;'>
                &copy; " . date('Y') . " MAN 2 Hulu Sungai Utara
            </div>
        </div>";
        $mail->AltBody = "Halo $nama,\n\nKami menerima permintaan untuk mereset password Anda. Silakan klik link berikut untuk melanjutkan: $resetLink \n\nLink ini berlaku selama 30 menit.";
        $mail->send();
        
        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Mengirim pesan notifikasi ke Telegram Bot
 */
function sendTelegramNotification($pdo, $message) {
    try {
        $settings = getSmtpSettings($pdo);
        
        $notifAktif = $settings['telegram_notif_aktif'] ?? '0';
        $token = $settings['telegram_bot_token'] ?? '';
        $chatId = $settings['telegram_chat_id'] ?? '';
        
        if ($notifAktif !== '1' || empty($token) || empty($chatId)) {
            return ['success' => false, 'message' => 'Notifikasi Telegram dinonaktifkan atau konfigurasi tidak lengkap.'];
        }
        
        $url = "https://api.telegram.org/bot" . $token . "/sendMessage";
        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        
        if ($result === false) {
            return ['success' => false, 'message' => 'Gagal mengirim request ke API Telegram: ' . $err];
        }
        
        $resObj = json_decode($result, true);
        if (isset($resObj['ok']) && $resObj['ok'] === true) {
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => $resObj['description'] ?? 'Gagal mengirim pesan ke Telegram.'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

/**
 * Menguji koneksi dan pengiriman pesan ke Telegram Bot
 */
function sendTestTelegramMessage($pdo, $token, $chatId, $message) {
    try {
        $url = "https://api.telegram.org/bot" . $token . "/sendMessage";
        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
        
        if ($result === false) {
            return ['success' => false, 'message' => 'Koneksi gagal: ' . $err];
        }
        
        $resObj = json_decode($result, true);
        if (isset($resObj['ok']) && $resObj['ok'] === true) {
            return ['success' => true];
        } else {
            return ['success' => false, 'message' => $resObj['description'] ?? 'Gagal mengirim pesan.'];
        }
    } catch (Exception $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
?>
