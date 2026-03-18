<?php
$pageTitle = 'Edit Pengguna';
require_once __DIR__ . '/../../includes/auth_check.php';
requireAdmin();
$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?"); $stmt->execute([$id]);
$data = $stmt->fetch(); if (!$data) { header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']); $username = trim($_POST['username']); $role = $_POST['role'];
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET nama=?, username=?, password=?, role=? WHERE id=?")->execute([$nama, $username, $password, $role, $id]);
    } else {
        $pdo->prepare("UPDATE users SET nama=?, username=?, role=? WHERE id=?")->execute([$nama, $username, $role, $id]);
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
        <div class="grid-2">
            <div class="form-group"><label>Nama Lengkap *</label><input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required></div>
            <div class="form-group"><label>Username *</label><input type="text" class="form-control" name="username" value="<?= htmlspecialchars($data['username']) ?>" required></div>
            <div class="form-group"><label>Password Baru <small>(kosongkan jika tidak diubah)</small></label><input type="password" class="form-control" name="password" minlength="6"></div>
            <div class="form-group"><label>Role *</label>
                <select class="form-control" name="role" required>
                    <option value="petugas" <?= $data['role'] === 'petugas' ? 'selected' : '' ?>>Petugas</option>
                    <option value="admin" <?= $data['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
        </div>
        <div class="btn-group"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn btn-secondary">Batal</a></div>
    </form>
</div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
