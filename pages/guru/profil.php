<?php
$pageTitle = 'Profil Saya';
require_once __DIR__ . '/../../includes/auth_check.php';
requireGuru();
$pdo = getConnection();

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Update profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profil') {
        $nama = trim($_POST['nama']);
        $nip = trim($_POST['nip']);
        $jabatan = trim($_POST['jabatan']);
        $noTelepon = trim($_POST['no_telepon']);
        
        $stmt = $pdo->prepare("UPDATE users SET nama=?, nip=?, jabatan=?, no_telepon=? WHERE id=?");
        $stmt->execute([$nama, $nip, $jabatan, $noTelepon, $userId]);
        
        // Update session
        $_SESSION['user_nama'] = $nama;
        $_SESSION['user_nip'] = $nip;
        $_SESSION['user_jabatan'] = $jabatan;
        $_SESSION['user_no_telepon'] = $noTelepon;
        
        logActivity($pdo, $userId, 'Edit Profil', "$nama memperbarui profil");
        setFlash('success', 'Profil berhasil diperbarui!');
        header('Location: profil.php'); exit;
    }
    
    if ($action === 'update_password') {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];
        
        if (!password_verify($currentPassword, $user['password'])) {
            setFlash('danger', 'Password lama salah!');
        } elseif ($newPassword !== $confirmPassword) {
            setFlash('danger', 'Password baru dan konfirmasi tidak sama!');
        } elseif (strlen($newPassword) < 6) {
            setFlash('danger', 'Password minimal 6 karakter!');
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([$hashedPassword, $userId]);
            logActivity($pdo, $userId, 'Ganti Password', $_SESSION['user_nama'] . ' mengganti password');
            setFlash('success', 'Password berhasil diganti!');
        }
        header('Location: profil.php'); exit;
    }
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-user-circle"></i> Profil Saya</h2>
        <div class="breadcrumb">
            <a href="/inventaris-aset-man2hsu/pages/guru/dashboard.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Profil</span>
        </div>
    </div>
</div>

<div class="grid-2">
    <!-- Info Profil -->
    <div class="card animate-fadeInUp">
        <div class="card-header">
            <h3><i class="fas fa-id-card" style="color:var(--accent-primary);margin-right:8px;"></i> Edit Profil</h3>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="action" value="update_profil">
                <div class="form-group">
                    <label>Nama Lengkap *</label>
                    <input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required>
                </div>
                <div class="form-group">
                    <label>NIP</label>
                    <input type="text" class="form-control" name="nip" value="<?= htmlspecialchars($user['nip'] ?? '') ?>" placeholder="Nomor Induk Pegawai">
                </div>
                <div class="form-group">
                    <label>Jabatan</label>
                    <input type="text" class="form-control" name="jabatan" value="<?= htmlspecialchars($user['jabatan'] ?? '') ?>" placeholder="Contoh: Guru Matematika">
                </div>
                <div class="form-group">
                    <label>No. Telepon</label>
                    <input type="text" class="form-control" name="no_telepon" value="<?= htmlspecialchars($user['no_telepon'] ?? '') ?>" placeholder="08xxxxxxxxxx">
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled style="background:#f5f5f5;">
                    <small style="color:var(--text-muted);font-size:0.78rem;">Username tidak dapat diubah</small>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <!-- Ganti Password -->
    <div>
        <div class="card animate-fadeInUp mb-4">
            <div class="card-header">
                <h3><i class="fas fa-lock" style="color:var(--warning);margin-right:8px;"></i> Ganti Password</h3>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="update_password">
                    <div class="form-group">
                        <label>Password Lama *</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label>Password Baru *</label>
                        <input type="password" class="form-control" name="new_password" required minlength="6">
                    </div>
                    <div class="form-group">
                        <label>Konfirmasi Password Baru *</label>
                        <input type="password" class="form-control" name="confirm_password" required minlength="6">
                    </div>
                    <button type="submit" class="btn btn-warning" style="width:100%;">
                        <i class="fas fa-key"></i> Ganti Password
                    </button>
                </form>
            </div>
        </div>

        <!-- Info Card -->
        <div class="card animate-fadeInUp">
            <div class="card-body" style="padding:20px;">
                <div style="display:flex; align-items:center; gap:16px; margin-bottom:16px;">
                    <div class="stat-icon purple" style="width:56px;height:56px;font-size:1.4rem;border-radius:14px;">
                        <?= strtoupper(substr($user['nama'], 0, 1)) ?>
                    </div>
                    <div>
                        <h4 style="font-size:1.05rem; font-weight:600;"><?= htmlspecialchars($user['nama']) ?></h4>
                        <span class="badge badge-info">Guru / Karyawan</span>
                    </div>
                </div>
                <div style="font-size:0.82rem; color:var(--text-muted); line-height:2;">
                    <div><i class="fas fa-user" style="width:20px;"></i> Username: <strong><?= htmlspecialchars($user['username']) ?></strong></div>
                    <div><i class="fas fa-id-card" style="width:20px;"></i> NIP: <strong><?= htmlspecialchars($user['nip'] ?? '-') ?></strong></div>
                    <div><i class="fas fa-briefcase" style="width:20px;"></i> Jabatan: <strong><?= htmlspecialchars($user['jabatan'] ?? '-') ?></strong></div>
                    <div><i class="fas fa-phone" style="width:20px;"></i> Telepon: <strong><?= htmlspecialchars($user['no_telepon'] ?? '-') ?></strong></div>
                    <div><i class="fas fa-calendar" style="width:20px;"></i> Terdaftar: <strong><?= date('d/m/Y', strtotime($user['created_at'])) ?></strong></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
