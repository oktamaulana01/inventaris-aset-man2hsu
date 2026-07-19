<?php
$pageTitle = 'Tambah Aset';
require_once __DIR__ . '/../../includes/auth_check.php';
$pdo = getConnection();

$kategoriList = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori")->fetchAll();
$lokasiList = $pdo->query("SELECT * FROM lokasi ORDER BY nama_lokasi")->fetchAll();
$kodeAset = generateKodeAset($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrfToken();
    $kode = trim($_POST['kode_aset']);
    $nama = trim($_POST['nama_aset']);
    $idKategori = $_POST['id_kategori'] ?: null;
    $idLokasi = $_POST['id_lokasi'] ?: null;
    $jumlah = intval($_POST['jumlah']);
    $kondisi = $_POST['kondisi'];
    $tahun = $_POST['tahun_perolehan'] ?: null;
    $nilai = floatval(str_replace(['.', ','], ['', '.'], $_POST['nilai_perolehan']));
    $sumber = trim($_POST['sumber_dana']);
    $keterangan = trim($_POST['keterangan']);
    
    // Upload gambar
    $gambar = null;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (in_array($ext, $allowed)) {
            $gambar = 'aset_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['gambar']['tmp_name'], __DIR__ . '/../../assets/uploads/' . $gambar);
        }
    }
    
    $stmt = $pdo->prepare("INSERT INTO aset (kode_aset, nama_aset, id_kategori, id_lokasi, jumlah, kondisi, tahun_perolehan, nilai_perolehan, sumber_dana, gambar, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$kode, $nama, $idKategori, $idLokasi, $jumlah, $kondisi, $tahun, $nilai, $sumber, $gambar, $keterangan]);
    
    logActivity($pdo, $_SESSION['user_id'], 'Tambah Aset', "Menambah aset: $nama ($kode)");
    setFlash('success', 'Aset berhasil ditambahkan!');
    header('Location: ' . BASE_URL . '/aset');
    exit;
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-plus-circle"></i> Tambah Aset Baru</h2>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>/dashboard">Dashboard</a>
            <span class="separator">/</span>
            <a href="<?= BASE_URL ?>/aset">Data Aset</a>
            <span class="separator">/</span>
            <span>Tambah</span>
        </div>
    </div>
</div>

<div class="card animate-fadeInUp">
    <div class="card-header">
        <h3>Form Tambah Aset</h3>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <?= generateCsrfToken() ?>
            <div class="grid-2">
                <div class="form-group">
                    <label>Kode Aset</label>
                    <input type="text" class="form-control" name="kode_aset" value="<?= htmlspecialchars($kodeAset) ?>" required>
                </div>
                <div class="form-group">
                    <label>Nama Aset *</label>
                    <input type="text" class="form-control" name="nama_aset" placeholder="Masukkan nama aset" required>
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select class="form-control" name="id_kategori">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategoriList as $k): ?>
                            <option value="<?= $k['id'] ?>"><?= htmlspecialchars($k['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Lokasi / Ruangan</label>
                    <select class="form-control" name="id_lokasi">
                        <option value="">-- Pilih Lokasi --</option>
                        <?php foreach ($lokasiList as $l): ?>
                            <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['nama_lokasi']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jumlah *</label>
                    <input type="number" class="form-control" name="jumlah" value="1" min="1" required>
                </div>
                <div class="form-group">
                    <label>Kondisi *</label>
                    <input type="text" class="form-control" name="kondisi" value="Baik" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                    <small class="text-muted">Aset baru otomatis berstatus Baik. Untuk aset rusak, gunakan menu "Tambah Aset Rusak".</small>
                </div>
                <div class="form-group">
                    <label>Tahun Perolehan</label>
                    <input type="number" class="form-control" name="tahun_perolehan" placeholder="2024" min="1990" max="<?= date('Y') ?>">
                </div>
                <div class="form-group">
                    <label>Nilai Perolehan (Rp)</label>
                    <input type="number" class="form-control" name="nilai_perolehan" placeholder="0">
                </div>
                <div class="form-group">
                    <label>Sumber Dana</label>
                    <input type="text" class="form-control" name="sumber_dana" placeholder="contoh: Dana BOS, APBD">
                </div>
                <div class="form-group">
                    <label>Gambar Aset</label>
                    <input type="file" class="form-control" name="gambar" accept="image/*" onchange="previewImage(this, 'imgPreview')">
                    <img id="imgPreview" class="img-preview mt-2" style="display:none;" alt="Preview">
                </div>
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <textarea class="form-control" name="keterangan" placeholder="Keterangan tambahan..."></textarea>
            </div>
            <div class="btn-group mt-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="<?= BASE_URL ?>/aset" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
