<?php
$pageTitle = 'Edit Lokasi';
require_once __DIR__ . '/../../includes/auth_check.php';
$pdo = getConnection();
$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM lokasi WHERE id = ?"); $stmt->execute([$id]);
$data = $stmt->fetch(); if (!$data) { header('Location: index.php'); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrfToken();
    $nama = trim($_POST['nama_lokasi']); $ket = trim($_POST['keterangan']);
    $pdo->prepare("UPDATE lokasi SET nama_lokasi=?, keterangan=? WHERE id=?")->execute([$nama, $ket, $id]);
    logActivity($pdo, $_SESSION['user_id'], 'Edit Lokasi', "Mengedit lokasi: $nama");
    setFlash('success', 'Lokasi berhasil diperbarui!'); header('Location: index.php'); exit;
}
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header"><div><h2><i class="fas fa-edit"></i> Edit Lokasi</h2></div></div>
<div class="card animate-fadeInUp"><div class="card-body">
    <form method="POST">
            <?= generateCsrfToken() ?>
        <div class="form-group"><label>Nama Lokasi *</label><input type="text" class="form-control" name="nama_lokasi" value="<?= htmlspecialchars($data['nama_lokasi']) ?>" required></div>
        <div class="form-group"><label>Keterangan</label><textarea class="form-control" name="keterangan"><?= htmlspecialchars($data['keterangan'] ?? '') ?></textarea></div>
        <div class="btn-group"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn btn-secondary">Batal</a></div>
    </form>
</div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>


