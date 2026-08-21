<?php
$pageTitle = 'Profil Saya';
require_once __DIR__ . '/../includes/auth_check.php';
$pdo = getConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$editMode = isset($_GET['edit']);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrfToken();
    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $telegramChatId = trim($_POST['telegram_chat_id'] ?? '');
    $foto = $user['foto']; // keep existing

    // Validate nama
    if (empty($nama)) {
        $errors[] = 'Nama lengkap wajib diisi.';
    }

    // Validate username
    if (empty($username)) {
        $errors[] = 'Username wajib diisi.';
    } elseif ($username !== $user['username']) {
        // Check if username already taken
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $checkStmt->execute([$username, $_SESSION['user_id']]);
        if ($checkStmt->fetch()) {
            $errors[] = 'Username sudah digunakan oleh pengguna lain.';
        }
    }

    // Validate password
    if (!empty($_POST['password'])) {
        if (strlen($_POST['password']) < 6) {
            $errors[] = 'Password minimal 6 karakter.';
        } elseif ($_POST['password'] !== $_POST['password_konfirmasi']) {
            $errors[] = 'Konfirmasi password tidak cocok.';
        }
    }

    // Handle foto upload
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jfif', 'image/pjpeg'];
        $maxSize = 2 * 1024 * 1024; // 2MB

        if (!in_array($_FILES['foto']['type'], $allowedTypes)) {
            $errors[] = 'Format foto harus JPG, JPEG, PNG, GIF, WEBP, atau JFIF.';
        } elseif ($_FILES['foto']['size'] > $maxSize) {
            $errors[] = 'Ukuran foto maksimal 2MB.';
        } else {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $fotoName = 'user_' . $_SESSION['user_id'] . '_' . time() . '.' . $ext;
            $uploadDir = __DIR__ . '/../assets/uploads/';
            
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $fotoName)) {
                // Delete old foto if exists
                if ($user['foto'] && file_exists($uploadDir . $user['foto'])) {
                    unlink($uploadDir . $user['foto']);
                }
                $foto = $fotoName;
            } else {
                $errors[] = 'Gagal mengupload foto.';
            }
        }
    }

    // Handle hapus foto
    if (isset($_POST['hapus_foto']) && $_POST['hapus_foto'] === '1') {
        $uploadDir = __DIR__ . '/../assets/uploads/';
        if ($user['foto'] && file_exists($uploadDir . $user['foto'])) {
            unlink($uploadDir . $user['foto']);
        }
        $foto = null;
    }

    if (empty($errors)) {
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET nama=?, username=?, password=?, foto=?, telegram_chat_id=? WHERE id=?")
                ->execute([$nama, $username, $password, $foto, $telegramChatId ?: null, $_SESSION['user_id']]);
        } else {
            $pdo->prepare("UPDATE users SET nama=?, username=?, foto=?, telegram_chat_id=? WHERE id=?")
                ->execute([$nama, $username, $foto, $telegramChatId ?: null, $_SESSION['user_id']]);
        }
        $_SESSION['user_nama'] = $nama;
        $_SESSION['user_telegram_chat_id'] = $telegramChatId;
        logActivity($pdo, $_SESSION['user_id'], 'Edit Profil', $nama . ' memperbarui profil');
        setFlash('success', 'Profil berhasil diperbarui!');
        header('Location: ' . BASE_URL . '/profil'); exit;
    } else {
        $editMode = true;
    }
}

// Format date
$createdAt = isset($user['created_at']) ? date('d F Y', strtotime($user['created_at'])) : '-';
// Indonesian month names
$bulanIndo = ['January'=>'Januari','February'=>'Februari','March'=>'Maret','April'=>'April','May'=>'Mei','June'=>'Juni','July'=>'Juli','August'=>'Agustus','September'=>'September','October'=>'Oktober','November'=>'November','December'=>'Desember'];
foreach ($bulanIndo as $en => $id) {
    $createdAt = str_replace($en, $id, $createdAt);
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<style>
.profile-container { max-width: 700px; }
.profile-header-card {
    background: var(--accent-gradient);
    border-radius: 16px;
    padding: 30px;
    text-align: center;
    color: #fff;
    position: relative;
    margin-bottom: 20px;
}
.profile-avatar {
    width: 110px; height: 110px;
    border-radius: 50%;
    border: 4px solid rgba(255,255,255,0.4);
    display: inline-flex; align-items: center; justify-content: center;
    font-size: 2.8rem; font-weight: 700; color: #fff;
    background: rgba(255,255,255,0.15);
    margin-bottom: 12px;
    overflow: hidden;
    object-fit: cover;
}
.profile-avatar img {
    width: 100%; height: 100%;
    object-fit: cover; border-radius: 50%;
}
.profile-name { font-size: 1.5rem; font-weight: 700; margin-bottom: 4px; }
.profile-role {
    display: inline-block;
    padding: 4px 16px;
    border-radius: 20px;
    font-size: 0.82rem;
    font-weight: 600;
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(4px);
}
.detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.detail-item {
    padding: 14px 16px;
    background: var(--sidebar-bg, #f8faf9);
    border-radius: 10px;
    border: 1px solid #e8ece9;
}
.detail-item .detail-label {
    font-size: 0.75rem;
    color: #888;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 4px;
}
.detail-item .detail-value {
    font-size: 1rem;
    font-weight: 600;
    color: #333;
}
.btn-edit-profil {
    position: absolute;
    top: 15px; right: 15px;
    background: rgba(255,255,255,0.2);
    color: #fff;
    border: 1.5px solid rgba(255,255,255,0.4);
    padding: 6px 16px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 0.85rem;
    transition: all 0.2s;
    text-decoration: none;
}
.btn-edit-profil:hover {
    background: rgba(255,255,255,0.35);
}
.foto-upload-area {
    width: 130px; height: 130px;
    border: 2px dashed #ccc;
    border-radius: 50%;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    cursor: pointer;
    margin: 0 auto 15px;
    overflow: hidden;
    position: relative;
    transition: border-color 0.2s;
    background: #f9f9f9;
}
.foto-upload-area:hover { border-color: var(--primary); }
.foto-upload-area img {
    width: 100%; height: 100%;
    object-fit: cover;
    position: absolute; top: 0; left: 0;
}
.foto-upload-area .overlay-edit {
    position: absolute; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.4);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 1.5rem;
    opacity: 0; transition: opacity 0.2s;
    border-radius: 50%;
}
.foto-upload-area:hover .overlay-edit { opacity: 1; }
.error-list {
    background: #fff5f5; border: 1px solid #fed7d7;
    color: #c53030; border-radius: 10px;
    padding: 12px 16px; margin-bottom: 16px;
    font-size: 0.88rem;
}
.error-list ul { margin: 4px 0 0; padding-left: 18px; }
.profile-stat {
    text-align: center; padding: 12px;
    border-radius: 10px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
}
.profile-stat .stat-num { font-size: 1.3rem; font-weight: 700; color: var(--primary); }
.profile-stat .stat-lbl { font-size: 0.75rem; color: #666; }
@media (max-width: 600px) {
    .detail-grid { grid-template-columns: 1fr; }
}
</style>

<div class="page-header">
    <div><h2><i class="fas fa-user"></i> Profil Saya</h2></div>
</div>

<div class="profile-container">
<?php if (!$editMode): ?>
    <!-- ===== DETAIL VIEW ===== -->
    <div class="profile-header-card animate-fadeInUp">
        <a href="?edit=1" class="btn-edit-profil"><i class="fas fa-pen"></i> Edit Profil</a>
        <div class="profile-avatar">
            <?php if ($user['foto'] && file_exists(__DIR__ . '/../assets/uploads/' . $user['foto'])): ?>
                <img src="<?= BASE_URL ?>/assets/uploads/<?= htmlspecialchars($user['foto']) ?>" alt="Foto Profil">
            <?php else: ?>
                <?= strtoupper(substr($user['nama'], 0, 1)) ?>
            <?php endif; ?>
        </div>
        <div class="profile-name"><?= htmlspecialchars($user['nama']) ?></div>
        <div class="profile-role">
            <i class="fas fa-<?= $user['role'] === 'admin' ? 'crown' : 'user-shield' ?>"></i>
            <?= ucfirst($user['role']) ?>
        </div>
    </div>

    <div class="card animate-fadeInUp" style="animation-delay:.1s">
        <div class="card-body">
            <h4 style="margin-top:0;margin-bottom:16px;font-size:1.05rem;">
                <i class="fas fa-id-card" style="color:var(--primary);margin-right:6px;"></i> Informasi Akun
            </h4>
            <div class="detail-grid">
                <div class="detail-item">
                    <div class="detail-label"><i class="fas fa-user"></i> Nama Lengkap</div>
                    <div class="detail-value"><?= htmlspecialchars($user['nama']) ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"><i class="fas fa-at"></i> Username</div>
                    <div class="detail-value"><?= htmlspecialchars($user['username']) ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"><i class="fas fa-shield-halved"></i> Role</div>
                    <div class="detail-value">
                        <span class="badge badge-<?= $user['role'] === 'admin' ? 'primary' : 'info' ?>">
                            <?= ucfirst($user['role']) ?>
                        </span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"><i class="fas fa-calendar"></i> Bergabung Sejak</div>
                    <div class="detail-value"><?= $createdAt ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"><i class="fab fa-telegram-plane"></i> Chat ID Telegram</div>
                    <div class="detail-value"><?= htmlspecialchars($user['telegram_chat_id'] ?? 'Belum diisi') ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card animate-fadeInUp" style="animation-delay:.15s">
        <div class="card-body">
            <h4 style="margin-top:0;margin-bottom:16px;font-size:1.05rem;">
                <i class="fas fa-lock" style="color:var(--primary);margin-right:6px;"></i> Keamanan
            </h4>
            <div class="detail-item" style="display:flex;align-items:center;justify-content:space-between;">
                <div>
                    <div class="detail-label"><i class="fas fa-key"></i> Password</div>
                    <div class="detail-value">••••••••</div>
                </div>
                <a href="?edit=1" class="btn btn-outline" style="font-size:.85rem;padding:6px 14px;">
                    <i class="fas fa-pen"></i> Ubah
                </a>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- ===== EDIT MODE ===== -->
    <div class="card animate-fadeInUp">
        <div class="card-body">
            <h3 style="margin-top:0;margin-bottom:4px;">
                <i class="fas fa-user-edit" style="color:var(--primary);"></i> Edit Profil
            </h3>
            <p style="color:#888;font-size:.88rem;margin-top:0;margin-bottom:20px;">Perbarui informasi profil dan foto Anda.</p>

            <?php if (!empty($errors)): ?>
            <div class="error-list">
                <strong><i class="fas fa-exclamation-circle"></i> Terjadi kesalahan:</strong>
                <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
            <?= generateCsrfToken() ?>
                <!-- Foto Upload -->
                <div style="text-align:center;margin-bottom:20px;">
                    <label for="foto-input" class="foto-upload-area" id="foto-preview-area">
                        <?php if ($user['foto'] && file_exists(__DIR__ . '/../assets/uploads/' . $user['foto'])): ?>
                            <img src="<?= BASE_URL ?>/assets/uploads/<?= htmlspecialchars($user['foto']) ?>" alt="Foto" id="foto-preview">
                        <?php else: ?>
                            <div id="foto-placeholder">
                                <i class="fas fa-camera" style="font-size:1.8rem;color:#aaa;"></i>
                                <small style="color:#aaa;margin-top:4px;">Upload Foto</small>
                            </div>
                            <img src="" alt="" id="foto-preview" style="display:none;">
                        <?php endif; ?>
                        <div class="overlay-edit"><i class="fas fa-camera"></i></div>
                    </label>
                    <input type="file" name="foto" id="foto-input" accept="image/*" style="display:none;" onchange="previewFoto(this)">
                    <div>
                        <small style="color:#888;">Klik foto untuk mengubah · JPG, PNG, WEBP · Maks 2MB</small>
                    </div>
                    <?php if ($user['foto']): ?>
                    <label style="display:inline-flex;align-items:center;gap:6px;margin-top:8px;cursor:pointer;font-size:.85rem;color:#e53e3e;">
                        <input type="checkbox" name="hapus_foto" value="1" id="hapus-foto-cb"> 
                        <i class="fas fa-trash-alt"></i> Hapus foto profil
                    </label>
                    <?php endif; ?>
                </div>

                <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">

                <!-- Nama -->
                <div class="form-group">
                    <label><i class="fas fa-user" style="color:var(--primary);margin-right:4px;"></i> Nama Lengkap <span style="color:red;">*</span></label>
                    <input type="text" class="form-control" name="nama" 
                           value="<?= htmlspecialchars($_POST['nama'] ?? $user['nama']) ?>" required>
                </div>

                <!-- Username -->
                <div class="form-group">
                    <label><i class="fas fa-at" style="color:var(--primary);margin-right:4px;"></i> Username <span style="color:red;">*</span></label>
                    <input type="text" class="form-control" name="username" 
                           value="<?= htmlspecialchars($_POST['username'] ?? $user['username']) ?>" required>
                    <small style="color:#888;">Username digunakan untuk login ke sistem.</small>
                </div>

                <!-- Telegram Chat ID -->
                <div class="form-group">
                    <label><i class="fab fa-telegram-plane" style="color:#0088cc;margin-right:4px;"></i> Chat ID Telegram Pribadi</label>
                    <input type="text" class="form-control" name="telegram_chat_id" 
                           value="<?= htmlspecialchars($_POST['telegram_chat_id'] ?? $user['telegram_chat_id'] ?? '') ?>" placeholder="Contoh: 6873151654">
                    <small style="color:#888;">Gunakan chat ID Anda agar bot Telegram dapat mengirimkan pesan notifikasi secara langsung ke chat pribadi Anda.</small>
                </div>

                <hr style="border:none;border-top:1px solid #eee;margin:20px 0;">

                <h4 style="margin-top:0;margin-bottom:12px;font-size:.95rem;">
                    <i class="fas fa-lock" style="color:var(--primary);margin-right:4px;"></i> Ubah Password
                    <small style="color:#888;font-weight:normal;"> — kosongkan jika tidak ingin mengubah</small>
                </h4>

                <!-- Password -->
                <div class="form-group">
                    <label>Password Baru</label>
                    <div style="position:relative;">
                        <input type="password" class="form-control" name="password" id="input-password" 
                               minlength="6" placeholder="Minimal 6 karakter"
                               style="padding-right:40px;">
                        <button type="button" onclick="togglePassword('input-password', this)" 
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#888;font-size:1rem;">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Konfirmasi Password -->
                <div class="form-group">
                    <label>Konfirmasi Password Baru</label>
                    <div style="position:relative;">
                        <input type="password" class="form-control" name="password_konfirmasi" id="input-konfirmasi" 
                               minlength="6" placeholder="Ketik ulang password baru"
                               style="padding-right:40px;">
                        <button type="button" onclick="togglePassword('input-konfirmasi', this)"
                                style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#888;font-size:1rem;">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div style="display:flex;gap:10px;margin-top:20px;">
                    <button type="submit" class="btn btn-primary" style="flex:1;">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                    <a href="<?= BASE_URL ?>/profil" class="btn btn-secondary" style="flex:0 0 auto;">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>
</div>

<script>
function previewFoto(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById('foto-preview');
            var placeholder = document.getElementById('foto-placeholder');
            preview.src = e.target.result;
            preview.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
            // Uncheck hapus foto
            var cb = document.getElementById('hapus-foto-cb');
            if (cb) cb.checked = false;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function togglePassword(inputId, btn) {
    var input = document.getElementById(inputId);
    var icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>


