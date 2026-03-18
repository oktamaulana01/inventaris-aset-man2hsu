<?php
$pageTitle = 'Profil Saya';
require_once __DIR__ . '/../includes/auth_check.php';
$pdo = getConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET nama=?, password=? WHERE id=?")->execute([$nama, $password, $_SESSION['user_id']]);
    } else {
        $pdo->prepare("UPDATE users SET nama=? WHERE id=?")->execute([$nama, $_SESSION['user_id']]);
    }
    $_SESSION['user_nama'] = $nama;
    setFlash('success', 'Profil berhasil diperbarui!');
    header('Location: profil.php'); exit;
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header"><div><h2><i class="fas fa-user"></i> Profil Saya</h2></div></div>
<div class="card animate-fadeInUp" style="max-width:600px;"><div class="card-body">
    <div class="text-center mb-4">
        <div style="width:80px;height:80px;background:var(--accent-gradient);border-radius:20px;display:inline-flex;align-items:center;justify-content:center;font-size:2rem;color:white;font-weight:700;">
            <?= strtoupper(substr($user['nama'], 0, 1)) ?>
        </div>
        <h3 class="mt-3" style="font-size:1.2rem;"><?= htmlspecialchars($user['nama']) ?></h3>
        <span class="badge badge-<?= $user['role'] === 'admin' ? 'primary' : 'info' ?>"><?= ucfirst($user['role']) ?></span>
    </div>
    <form method="POST">
        <div class="form-group"><label>Nama Lengkap</label><input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($user['nama']) ?>" required></div>
        <div class="form-group"><label>Username</label><input type="text" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" disabled></div>
        <div class="form-group"><label>Password Baru <small>(kosongkan jika tidak diubah)</small></label><input type="password" class="form-control" name="password" minlength="6"></div>
        <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-save"></i> Simpan Perubahan</button>
    </form>
</div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
