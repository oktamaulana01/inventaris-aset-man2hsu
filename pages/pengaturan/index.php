<?php
$pageTitle = 'Pengaturan Email';
require_once __DIR__ . '/../../includes/auth_check.php';
requireStaff();
$pdo = getConnection();
require_once __DIR__ . '/../../config/mailer.php';

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrfToken();
    $action = $_POST['action'] ?? '';
    
    if ($action === 'save_smtp') {
        $fields = ['smtp_host', 'smtp_port', 'smtp_username', 'smtp_password', 'smtp_sender_name', 'smtp_sender_email', 'notif_h_minus_1', 'notif_h_0', 'notif_h_plus_1', 'telegram_bot_token', 'telegram_chat_id', 'telegram_notif_aktif'];
        $stmt = $pdo->prepare("UPDATE pengaturan SET nilai = ? WHERE kunci = ?");
        foreach ($fields as $f) {
            $val = $_POST[$f] ?? '';
            // Checkbox: jika tidak dikirim, berarti 0
            if (in_array($f, ['notif_h_minus_1', 'notif_h_0', 'notif_h_plus_1', 'telegram_notif_aktif'])) {
                $val = isset($_POST[$f]) ? '1' : '0';
            }
            $stmt->execute([$val, $f]);
        }
        logActivity($pdo, $_SESSION['user_id'], 'Update Pengaturan', 'Memperbarui konfigurasi SMTP email & Telegram');
        setFlash('success', 'Pengaturan berhasil disimpan!');
        header('Location: ' . BASE_URL . '/pengaturan-email'); exit;
    }
    
    if ($action === 'test_email') {
        $testEmail = trim($_POST['test_email']);
        if (filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            $result = sendTestEmail($pdo, $testEmail);
            setFlash($result['success'] ? 'success' : 'danger', $result['message']);
        } else {
            setFlash('danger', 'Alamat email tidak valid!');
        }
        header('Location: ' . BASE_URL . '/pengaturan-email'); exit;
    }

    if ($action === 'test_telegram') {
        $token = trim($_POST['test_token'] ?? '');
        $chatId = trim($_POST['test_chat_id'] ?? '');
        if ($token && $chatId) {
            $result = sendTestTelegramMessage($pdo, $token, $chatId, "⚡ <b>Koneksi Berhasil!</b>\nIni adalah pesan uji coba dari Sistem Inventarisasi Aset MAN 2 HSU.");
            setFlash($result['success'] ? 'success' : 'danger', $result['success'] ? 'Notifikasi Telegram uji coba berhasil dikirim!' : 'Gagal mengirim notifikasi: ' . $result['message']);
        } else {
            setFlash('danger', 'Token Bot dan Chat ID wajib diisi!');
        }
        header('Location: ' . BASE_URL . '/pengaturan-email'); exit;
    }
}

// Ambil pengaturan saat ini
$settings = getSmtpSettings($pdo);

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<style>
.settings-card { max-width: 800px; }
.guide-box {
    background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px;
    padding: 16px 20px; margin-bottom: 20px; font-size: 0.85rem; color: #166534;
}
.guide-box h4 { margin: 0 0 8px; font-size: 0.9rem; }
.guide-box ol { margin: 0; padding-left: 20px; }
.guide-box li { margin-bottom: 4px; }
.notif-toggle { display: flex; align-items: center; gap: 10px; padding: 10px 14px; background: #f8faf9; border-radius: 8px; border: 1px solid #e8ece9; margin-bottom: 8px; }
.notif-toggle label { margin: 0; font-size: 0.88rem; cursor: pointer; flex: 1; }
.notif-toggle input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--accent-primary); }
.test-section { background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 16px 20px; }
</style>

<div class="page-header">
    <div>
        <h2><i class="fas fa-gear"></i> Pengaturan Email</h2>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>/dashboard">Dashboard</a>
            <span class="separator">/</span>
            <span>Pengaturan Email</span>
        </div>
    </div>
    <a href="<?= BASE_URL ?>/log-notifikasi" class="btn btn-info"><i class="fas fa-list"></i> Log Notifikasi</a>
</div>

<div class="settings-card">
    <!-- Panduan Gmail -->
    <div class="guide-box animate-fadeInUp">
        <h4><i class="fas fa-lightbulb"></i> Panduan Konfigurasi Gmail SMTP</h4>
        <ol>
            <li>Buka <a href="https://myaccount.google.com/security" target="_blank" style="color:#1e7256;font-weight:600;">Google Account Security</a></li>
            <li>Aktifkan <strong>Verifikasi 2 Langkah</strong> (wajib)</li>
            <li>Buka <a href="https://myaccount.google.com/apppasswords" target="_blank" style="color:#1e7256;font-weight:600;">App Passwords</a></li>
            <li>Buat App Password baru (pilih "Other" → ketik "Inventaris Aset")</li>
            <li>Salin password 16 karakter yang dihasilkan ke field "SMTP Password" di bawah</li>
        </ol>
    </div>

    <!-- Form SMTP Settings -->
    <div class="card animate-fadeInUp" style="animation-delay:.05s;">
        <div class="card-header">
            <h3><i class="fas fa-server" style="color:var(--accent-primary);margin-right:8px;"></i> Konfigurasi SMTP</h3>
        </div>
        <div class="card-body">
            <form method="POST">
                <?= generateCsrfToken() ?>
                <input type="hidden" name="action" value="save_smtp">
                
                <div class="grid-2">
                    <div class="form-group">
                        <label><i class="fas fa-server" style="margin-right:4px;color:var(--accent-primary);"></i> SMTP Host</label>
                        <input type="text" class="form-control" name="smtp_host" value="<?= htmlspecialchars($settings['smtp_host'] ?? 'smtp.gmail.com') ?>" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-hashtag" style="margin-right:4px;color:var(--accent-primary);"></i> SMTP Port</label>
                        <input type="number" class="form-control" name="smtp_port" value="<?= htmlspecialchars($settings['smtp_port'] ?? '587') ?>" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope" style="margin-right:4px;color:var(--accent-primary);"></i> SMTP Username (Email Gmail)</label>
                        <input type="email" class="form-control" name="smtp_username" value="<?= htmlspecialchars($settings['smtp_username'] ?? '') ?>" placeholder="contoh@gmail.com" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-key" style="margin-right:4px;color:var(--accent-primary);"></i> SMTP Password (App Password)</label>
                        <input type="password" class="form-control" name="smtp_password" value="<?= htmlspecialchars($settings['smtp_password'] ?? '') ?>" placeholder="xxxx xxxx xxxx xxxx" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-user" style="margin-right:4px;color:var(--accent-primary);"></i> Nama Pengirim</label>
                        <input type="text" class="form-control" name="smtp_sender_name" value="<?= htmlspecialchars($settings['smtp_sender_name'] ?? 'MAN 2 HSU - Inventaris Aset') ?>">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-at" style="margin-right:4px;color:var(--accent-primary);"></i> Email Pengirim</label>
                        <input type="email" class="form-control" name="smtp_sender_email" value="<?= htmlspecialchars($settings['smtp_sender_email'] ?? '') ?>" placeholder="Kosongkan = sama dengan SMTP Username">
                    </div>
                </div>
                
                <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">
                
                <h4 style="font-size:0.95rem;margin-bottom:12px;">
                    <i class="fab fa-telegram" style="color:#0088cc;margin-right:4px;"></i> Konfigurasi Notifikasi Telegram
                </h4>
                
                <div class="grid-2">
                    <div class="form-group">
                        <label><i class="fab fa-telegram-plane" style="margin-right:4px;color:#0088cc;"></i> Token Bot Telegram</label>
                        <input type="text" class="form-control" name="telegram_bot_token" value="<?= htmlspecialchars($settings['telegram_bot_token'] ?? '') ?>" placeholder="1234567890:ABCdefGhIJKlmNoPQRsTUVwxyZ">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-id-card" style="margin-right:4px;color:#0088cc;"></i> Chat ID Penerima (Grup / Pribadi)</label>
                        <input type="text" class="form-control" name="telegram_chat_id" value="<?= htmlspecialchars($settings['telegram_chat_id'] ?? '') ?>" placeholder="-123456789 atau 123456789">
                    </div>
                </div>
                
                <div class="notif-toggle mt-3">
                    <input type="checkbox" name="telegram_notif_aktif" id="notif_tg" value="1" <?= ($settings['telegram_notif_aktif'] ?? '0') === '1' ? 'checked' : '' ?>>
                    <label for="notif_tg"><strong>Aktifkan Telegram</strong> — Kirim notifikasi otomatis secara real-time ke Telegram</label>
                </div>

                <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">
                
                <h4 style="font-size:0.95rem;margin-bottom:12px;">
                    <i class="fas fa-bell" style="color:var(--accent-primary);margin-right:4px;"></i> Jadwal Notifikasi
                </h4>
                
                <div class="notif-toggle">
                    <input type="checkbox" name="notif_h_minus_1" id="notif1" value="1" <?= ($settings['notif_h_minus_1'] ?? '1') === '1' ? 'checked' : '' ?>>
                    <label for="notif1"><strong>H-1</strong> — Kirim pengingat 1 hari sebelum jatuh tempo</label>
                </div>
                <div class="notif-toggle">
                    <input type="checkbox" name="notif_h_0" id="notif2" value="1" <?= ($settings['notif_h_0'] ?? '1') === '1' ? 'checked' : '' ?>>
                    <label for="notif2"><strong>H+0</strong> — Kirim peringatan pada hari jatuh tempo</label>
                </div>
                <div class="notif-toggle">
                    <input type="checkbox" name="notif_h_plus_1" id="notif3" value="1" <?= ($settings['notif_h_plus_1'] ?? '1') === '1' ? 'checked' : '' ?>>
                    <label for="notif3"><strong>H+1</strong> — Kirim peringatan setelah melewati jatuh tempo (overdue)</label>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width:100%;margin-top:16px;">
                    <i class="fas fa-save"></i> Simpan Pengaturan
                </button>
            </form>
        </div>
    </div>

    <!-- Test Email -->
    <div class="card animate-fadeInUp" style="animation-delay:.1s;margin-top:20px;">
        <div class="card-header">
            <h3><i class="fas fa-paper-plane" style="color:var(--warning);margin-right:8px;"></i> Test Kirim Email</h3>
        </div>
        <div class="card-body">
            <div class="test-section">
                <p style="font-size:0.85rem;color:#92400e;margin-top:0;">
                    <i class="fas fa-info-circle"></i> Kirim email percobaan untuk memastikan konfigurasi SMTP sudah benar.
                </p>
                <form method="POST" style="display:flex;gap:10px;flex-wrap:wrap;">
                    <?= generateCsrfToken() ?>
                    <input type="hidden" name="action" value="test_email">
                    <input type="email" class="form-control" name="test_email" placeholder="Masukkan email tujuan test..." required style="flex:1;min-width:250px;">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-paper-plane"></i> Kirim Test
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Test Telegram -->
    <div class="card animate-fadeInUp" style="animation-delay:.12s;margin-top:20px;">
        <div class="card-header">
            <h3><i class="fab fa-telegram" style="color:#0088cc;margin-right:8px;"></i> Test Kirim Telegram</h3>
        </div>
        <div class="card-body">
            <div class="test-section" style="background:#e0f2fe; border-color:#bae6fd;">
                <p style="font-size:0.85rem;color:#0369a1;margin-top:0;">
                    <i class="fas fa-info-circle"></i> Kirim pesan uji coba ke bot Telegram untuk memverifikasi token dan chat ID.
                </p>
                <form method="POST" style="display:flex;flex-direction:column;gap:12px;">
                    <?= generateCsrfToken() ?>
                    <input type="hidden" name="action" value="test_telegram">
                    <div class="grid-2" style="margin-bottom:0;grid-gap:15px;">
                        <div class="form-group" style="margin-bottom:0;">
                            <input type="text" class="form-control" name="test_token" placeholder="Token Bot Telegram..." required value="<?= htmlspecialchars($settings['telegram_bot_token'] ?? '') ?>">
                        </div>
                        <div class="form-group" style="margin-bottom:0;">
                            <input type="text" class="form-control" name="test_chat_id" placeholder="Chat ID Penerima..." required value="<?= htmlspecialchars($settings['telegram_chat_id'] ?? '') ?>">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="background:#0284c7;border-color:#0284c7;align-self:flex-end;">
                        <i class="fas fa-paper-plane"></i> Kirim Test Telegram
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Jalankan Cron Manual -->
    <div class="card animate-fadeInUp" style="animation-delay:.15s;margin-top:20px;">
        <div class="card-header">
            <h3><i class="fas fa-clock" style="color:var(--info);margin-right:8px;"></i> Jalankan Pengecekan Manual</h3>
        </div>
        <div class="card-body">
            <p style="font-size:0.88rem;color:var(--text-secondary);margin-top:0;">
                Klik tombol di bawah untuk menjalankan pengecekan jatuh tempo dan mengirim notifikasi sekarang.
            </p>
            <a href="<?= BASE_URL ?>/cron/check_due_dates.php?redirect=1" class="btn btn-info" style="width:100%;">
                <i class="fas fa-play"></i> Jalankan Pengecekan Sekarang
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
