<?php
$pageTitle = 'Edit Kategori';
require_once __DIR__ . '/../../includes/auth_check.php';
$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM kategori WHERE id = ?"); $stmt->execute([$id]);
$data = $stmt->fetch(); if (!$data) { header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama_kategori']); $ket = trim($_POST['keterangan']);
    $pdo->prepare("UPDATE kategori SET nama_kategori=?, keterangan=? WHERE id=?")->execute([$nama, $ket, $id]);
    logActivity($pdo, $_SESSION['user_id'], 'Edit Kategori', "Mengedit kategori: $nama");
    setFlash('success', 'Kategori berhasil diperbarui!');
    header('Location: index.php'); exit;
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header"><div><h2><i class="fas fa-edit"></i> Edit Kategori</h2></div></div>
<div class="card animate-fadeInUp"><div class="card-body">
    <form method="POST">
        <div class="form-group"><label>Nama Kategori *</label><input type="text" class="form-control" name="nama_kategori" value="<?= htmlspecialchars($data['nama_kategori']) ?>" required></div>
        <div class="form-group"><label>Keterangan</label><textarea class="form-control" name="keterangan"><?= htmlspecialchars($data['keterangan'] ?? '') ?></textarea></div>
        <div class="btn-group"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn btn-secondary">Batal</a></div>
    </form>
</div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
