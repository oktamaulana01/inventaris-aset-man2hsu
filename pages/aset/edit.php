<?php
$pageTitle = 'Edit Aset';
require_once __DIR__ . '/../../includes/auth_check.php';
$pdo = getConnection();

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM aset WHERE id = ? AND deleted_at IS NULL");
$stmt->execute([$id]);
$aset = $stmt->fetch();
if (!$aset) { header('Location: index.php'); exit; }

$kategoriList = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori")->fetchAll();
$lokasiList = $pdo->query("SELECT * FROM lokasi ORDER BY nama_lokasi")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrfToken();
    $nama = trim($_POST['nama_aset']);
    $idKategori = $_POST['id_kategori'] ?: null;
    $idLokasi = $_POST['id_lokasi'] ?: null;
    $jumlah = intval($_POST['jumlah']);
    $kondisi = $_POST['kondisi'];
    $tahun = $_POST['tahun_perolehan'] ?: null;
    $nilai = floatval(str_replace(['.', ','], ['', '.'], $_POST['nilai_perolehan']));
    $sumber = trim($_POST['sumber_dana']);
    $keterangan = trim($_POST['keterangan']);
    
    $gambar = $aset['gambar'];
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            // Delete old image
            if ($gambar && file_exists(__DIR__ . '/../../assets/uploads/' . $gambar)) {
                unlink(__DIR__ . '/../../assets/uploads/' . $gambar);
            }
            $gambar = 'aset_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . '/../../assets/uploads/' . $gambar);
        }
    }
    
    // Check if lokasi changed for mutasi
    if ($aset['id_lokasi'] != $idLokasi && $idLokasi) {
        $stmtMutasi = $pdo->prepare("INSERT INTO mutasi_aset (id_aset, id_lokasi_asal, id_lokasi_tujuan, tanggal_mutasi, keterangan, id_user) VALUES (?, ?, ?, CURDATE(), ?, ?)");
        $stmtMutasi->execute([$id, $aset['id_lokasi'], $idLokasi, 'Perpindahan via edit aset', $_SESSION['user_id']]);
    }
    
    $stmt = $pdo->prepare("UPDATE aset SET nama_aset=?, id_kategori=?, id_lokasi=?, jumlah=?, kondisi=?, tahun_perolehan=?, nilai_perolehan=?, sumber_dana=?, gambar=?, keterangan=? WHERE id=?");
    $stmt->execute([$nama, $idKategori, $idLokasi, $jumlah, $kondisi, $tahun, $nilai, $sumber, $gambar, $keterangan, $id]);
    
    logActivity($pdo, $_SESSION['user_id'], 'Edit Aset', "Mengedit aset: $nama ({$aset['kode_aset']})");
    setFlash('success', 'Aset berhasil diperbarui!');
    header('Location: ' . BASE_URL . '/pages/aset/index.php');
    exit;
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-edit"></i> Edit Aset</h2>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>/pages/dashboard.php">Dashboard</a>
            <span class="separator">/</span>
            <a href="<?= BASE_URL ?>/pages/aset/index.php">Data Aset</a>
            <span class="separator">/</span>
            <span>Edit</span>
        </div>
    </div>
</div>

<div class="card animate-fadeInUp">
    <div class="card-header">
        <h3>Edit Aset: <?= htmlspecialchars($aset['kode_aset']) ?></h3>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <?= generateCsrfToken() ?>
            <div class="grid-2">
                <div class="form-group">
                    <label>Kode Aset</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($aset['kode_aset']) ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Nama Aset *</label>
                    <input type="text" class="form-control" name="nama_aset" value="<?= htmlspecialchars($aset['nama_aset']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select class="form-control" name="id_kategori">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategoriList as $k): ?>
                            <option value="<?= $k['id'] ?>" <?= $k['id'] == $aset['id_kategori'] ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Lokasi / Ruangan</label>
                    <select class="form-control" name="id_lokasi">
                        <option value="">-- Pilih Lokasi --</option>
                        <?php foreach ($lokasiList as $l): ?>
                            <option value="<?= $l['id'] ?>" <?= $l['id'] == $aset['id_lokasi'] ? 'selected' : '' ?>><?= htmlspecialchars($l['nama_lokasi']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jumlah *</label>
                    <input type="number" class="form-control" name="jumlah" value="<?= $aset['jumlah'] ?>" min="1" required>
                </div>
                <div class="form-group">
                    <label>Kondisi *</label>
                    <select class="form-control" name="kondisi" required>
                        <option value="Baik" <?= $aset['kondisi'] === 'Baik' ? 'selected' : '' ?>>Baik</option>
                        <option value="Rusak Ringan" <?= $aset['kondisi'] === 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
                        <option value="Rusak Berat" <?= $aset['kondisi'] === 'Rusak Berat' ? 'selected' : '' ?>>Rusak Berat</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Tahun Perolehan</label>
                    <input type="number" class="form-control" name="tahun_perolehan" value="<?= $aset['tahun_perolehan'] ?>" min="1990" max="<?= date('Y') ?>">
                </div>
                <div class="form-group">
                    <label>Nilai Perolehan (Rp)</label>
                    <input type="text" class="form-control" name="nilai_perolehan" value="<?= $aset['nilai_perolehan'] ?>">
                </div>
                <div class="form-group">
                    <label>Sumber Dana</label>
                    <input type="text" class="form-control" name="sumber_dana" value="<?= htmlspecialchars($aset['sumber_dana'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Gambar Aset</label>
                    <input type="file" class="form-control" name="gambar" accept="image/*" onchange="previewImage(this, 'imgPreview')">
                    <?php if ($aset['gambar']): ?>
                        <img src="<?= BASE_URL ?>/assets/uploads/<?= $aset['gambar'] ?>" class="img-preview mt-2" id="imgPreview" alt="Gambar aset">
                    <?php else: ?>
                        <img id="imgPreview" class="img-preview mt-2" style="display:none;" alt="Preview">
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <textarea class="form-control" name="keterangan"><?= htmlspecialchars($aset['keterangan'] ?? '') ?></textarea>
            </div>
            <div class="btn-group mt-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                <a href="<?= BASE_URL ?>/pages/aset/index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
