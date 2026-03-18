<?php
$pageTitle = 'Tambah Pengguna';
require_once __DIR__ . '/../../includes/auth_check.php';
requireAdmin();
$pdo = getConnection();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']); $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $pdo->prepare("INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, ?)")->execute([$nama, $username, $password, $role]);
    logActivity($pdo, $_SESSION['user_id'], 'Tambah User', "Menambah pengguna: $nama ($role)");
    setFlash('success', 'Pengguna berhasil ditambahkan!'); header('Location: index.php'); exit;
}
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header"><div><h2><i class="fas fa-user-plus"></i> Tambah Pengguna</h2></div></div>
<div class="card animate-fadeInUp"><div class="card-body">
    <form method="POST">
        <div class="grid-2">
            <div class="form-group"><label>Nama Lengkap *</label><input type="text" class="form-control" name="nama" required></div>
            <div class="form-group"><label>Username *</label><input type="text" class="form-control" name="username" required></div>
            <div class="form-group"><label>Password *</label><input type="password" class="form-control" name="password" required minlength="6"></div>
            <div class="form-group"><label>Role *</label>
                <select class="form-control" name="role" required>
                    <option value="petugas">Petugas</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
        </div>
        <div class="btn-group"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn btn-secondary">Batal</a></div>
    </form>
</div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
