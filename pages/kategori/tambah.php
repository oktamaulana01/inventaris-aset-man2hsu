<?php
$pageTitle = 'Tambah Kategori';
require_once __DIR__ . '/../../includes/auth_check.php';
requireStaff();
$pdo = getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrfToken();
    $nama = trim($_POST['nama_kategori']);
    $ket = trim($_POST['keterangan']);
    $pdo->prepare("INSERT INTO kategori (nama_kategori, keterangan) VALUES (?, ?)")->execute([$nama, $ket]);
    logActivity($pdo, $_SESSION['user_id'], 'Tambah Kategori', "Menambah kategori: $nama");
    setFlash('success', 'Kategori berhasil ditambahkan!');
    header('Location: ' . BASE_URL . '/kategori'); exit;
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header"><div><h2><i class="fas fa-plus-circle"></i> Tambah Kategori</h2></div></div>
<div class="card animate-fadeInUp"><div class="card-body">
    <form method="POST">
            <?= generateCsrfToken() ?>
        <div class="form-group"><label>Nama Kategori *</label><input type="text" class="form-control" name="nama_kategori" required></div>
        <div class="form-group"><label>Keterangan</label><textarea class="form-control" name="keterangan"></textarea></div>
        <div class="btn-group"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><a href="<?= BASE_URL ?>/kategori" class="btn btn-secondary">Batal</a></div>
    </form>
</div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
