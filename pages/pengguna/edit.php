<?php
$pageTitle = 'Edit Pengguna';
require_once __DIR__ . '/../../includes/auth_check.php';
requireAdmin();
$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?"); $stmt->execute([$id]);
$data = $stmt->fetch(); if (!$data) { header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrfToken();
    $nama = trim($_POST['nama']); $username = trim($_POST['username']); $role = $_POST['role'];
    $email = trim($_POST['email'] ?? '');
    $nip = trim($_POST['nip'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $noTelepon = trim($_POST['no_telepon'] ?? '');
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET nama=?, username=?, email=?, password=?, role=?, nip=?, jabatan=?, no_telepon=? WHERE id=?")->execute([$nama, $username, $email ?: null, $password, $role, $nip ?: null, $jabatan ?: null, $noTelepon ?: null, $id]);
    } else {
        $pdo->prepare("UPDATE users SET nama=?, username=?, email=?, role=?, nip=?, jabatan=?, no_telepon=? WHERE id=?")->execute([$nama, $username, $email ?: null, $role, $nip ?: null, $jabatan ?: null, $noTelepon ?: null, $id]);
    }
    logActivity($pdo, $_SESSION['user_id'], 'Edit User', "Mengedit pengguna: $nama");
    setFlash('success', 'Pengguna berhasil diperbarui!'); header('Location: index.php'); exit;
}
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header"><div><h2><i class="fas fa-user-edit"></i> Edit Pengguna</h2></div></div>
<div class="card animate-fadeInUp"><div class="card-body">
    <form method="POST">
            <?= generateCsrfToken() ?>
        <div class="grid-2">
            <div class="form-group"><label>Nama Lengkap *</label><input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required></div>
            <div class="form-group"><label>Username *</label><input type="text" class="form-control" name="username" value="<?= htmlspecialchars($data['username']) ?>" required></div>
            <div class="form-group"><label>Alamat Email</label><input type="email" class="form-control" name="email" value="<?= htmlspecialchars($data['email'] ?? '') ?>" placeholder="contoh@email.com"></div>
            <div class="form-group"><label>Password Baru <small>(kosongkan jika tidak diubah)</small></label><input type="password" class="form-control" name="password" minlength="6"></div>
            <div class="form-group"><label>Role *</label>
                <select class="form-control" name="role" required>
                    <option value="petugas" <?= $data['role'] === 'petugas' ? 'selected' : '' ?>>Petugas</option>
                    <option value="admin" <?= $data['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="guru" <?= $data['role'] === 'guru' ? 'selected' : '' ?>>Guru / Karyawan</option>
                </select>
            </div>
        </div>
        <div id="guruFields">
            <hr style="border-color:var(--border-glass); margin:16px 0;">
            <p style="font-size:0.85rem; font-weight:600; color:var(--accent-primary); margin-bottom:12px;"><i class="fas fa-id-card"></i> Data Tambahan</p>
            <div class="grid-2">
                <div class="form-group"><label>NIP</label><input type="text" class="form-control" name="nip" value="<?= htmlspecialchars($data['nip'] ?? '') ?>" placeholder="Nomor Induk Pegawai"></div>
                <div class="form-group"><label>Jabatan</label><input type="text" class="form-control" name="jabatan" value="<?= htmlspecialchars($data['jabatan'] ?? '') ?>" placeholder="Contoh: Guru Matematika"></div>
                <div class="form-group"><label>No. Telepon</label><input type="text" class="form-control" name="no_telepon" value="<?= htmlspecialchars($data['no_telepon'] ?? '') ?>" placeholder="08xxxxxxxxxx"></div>
            </div>
        </div>
        <div class="btn-group" style="margin-top:16px;"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn btn-secondary">Batal</a></div>
    </form>
</div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
