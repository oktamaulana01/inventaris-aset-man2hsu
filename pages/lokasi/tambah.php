<?php
$pageTitle = 'Tambah Lokasi';
require_once __DIR__ . '/../../includes/auth_check.php';
$pdo = getConnection();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrfToken();
    $nama = trim($_POST['nama_lokasi']); $ket = trim($_POST['keterangan']);
    $pdo->prepare("INSERT INTO lokasi (nama_lokasi, keterangan) VALUES (?, ?)")->execute([$nama, $ket]);
    logActivity($pdo, $_SESSION['user_id'], 'Tambah Lokasi', "Menambah lokasi: $nama");
    setFlash('success', 'Lokasi berhasil ditambahkan!'); header('Location: index.php'); exit;
}
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header"><div><h2><i class="fas fa-plus-circle"></i> Tambah Lokasi</h2></div></div>
<div class="card animate-fadeInUp"><div class="card-body">
    <form method="POST">
            <?= generateCsrfToken() ?>
        <div class="form-group"><label>Nama Lokasi *</label><input type="text" class="form-control" name="nama_lokasi" required placeholder="contoh: Lab Komputer"></div>
        <div class="form-group"><label>Keterangan</label><textarea class="form-control" name="keterangan"></textarea></div>
        <div class="btn-group"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn btn-secondary">Batal</a></div>
    </form>
</div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>


