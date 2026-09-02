<?php
$pageTitle = 'Tambah Aset';
require_once __DIR__ . '/../../includes/auth_check.php';
requireStaff();
$pdo = getConnection();

$kategoriList = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori")->fetchAll();
$lokasiList = $pdo->query("SELECT * FROM lokasi ORDER BY nama_lokasi")->fetchAll();
$kodeAset = generateKodeAset($pdo);

$errors = [];
$post_kode = $kodeAset;
$post_nama = '';
$post_idKategori = '';
$post_idLokasi = '';
$post_jumlah = 1;
$post_tahun = '';
$post_nilai = '';
$post_sumber = '';
$post_keterangan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrfToken();
    $kode = trim($_POST['kode_aset']);
    $nama = trim($_POST['nama_aset']);
    $idKategori = $_POST['id_kategori'] ?: null;
    $idLokasi = $_POST['id_lokasi'] ?: null;
    $jenisBarang = $_POST['jenis_barang'] ?? 'Aset Tetap';
    $jumlah = intval($_POST['jumlah']);
    $kondisi = $_POST['kondisi'];
    $tahun = $_POST['tahun_perolehan'] ?: null;
    $nilai = floatval(str_replace(['.', ','], ['', '.'], $_POST['nilai_perolehan']));
    $sumber = trim($_POST['sumber_dana']);
    $keterangan = trim($_POST['keterangan']);
    
    // Retain variables
    $post_kode = $kode;
    $post_nama = $nama;
    $post_jenis = $jenisBarang;
    $post_idKategori = $_POST['id_kategori'];
    $post_idLokasi = $_POST['id_lokasi'];
    $post_jumlah = $jumlah;
    $post_tahun = $_POST['tahun_perolehan'];
    $post_nilai = $_POST['nilai_perolehan'];
    $post_sumber = $sumber;
    $post_keterangan = $keterangan;

    // Check if code already exists
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM aset WHERE kode_aset = ?");
    $stmtCheck->execute([$kode]);
    if ($stmtCheck->fetchColumn() > 0) {
        $errors[] = "Kode Aset '{$kode}' sudah terdaftar dalam sistem. Silakan gunakan kode lain atau biarkan kode otomatis.";
    }

    if (empty($errors)) {
        // Upload gambar
        $gambar = null;
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif', 'bmp', 'svg'];
            if (in_array($ext, $allowed)) {
                $gambar = 'aset_' . time() . '_' . rand(100, 999) . '.' . $ext;
                $destination = __DIR__ . '/../../assets/uploads/' . $gambar;
                if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $destination)) {
                    $errors[] = "Gagal memindahkan berkas gambar ke folder upload.";
                }
            } else {
                $errors[] = "Format gambar tidak didukung (.{$ext}). Gunakan format JPG, JPEG, PNG, GIF, WEBP, atau JFIF.";
            }
        } elseif (isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = "Terjadi kesalahan saat mengupload gambar (Error code: " . $_FILES['gambar']['error'] . ").";
        }
        
        if (empty($errors)) {
            $stmt = $pdo->prepare("INSERT INTO aset (kode_aset, nama_aset, jenis_barang, id_kategori, id_lokasi, jumlah, kondisi, tahun_perolehan, nilai_perolehan, sumber_dana, gambar, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$kode, $nama, $jenisBarang, $idKategori, $idLokasi, $jumlah, $kondisi, $tahun, $nilai, $sumber, $gambar, $keterangan]);
            
            logActivity($pdo, $_SESSION['user_id'], 'Tambah Aset', "Menambah $jenisBarang: $nama ($kode)");
            setFlash('success', 'Data barang berhasil ditambahkan!');
            header('Location: ' . BASE_URL . '/aset');
            exit;
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-plus-circle"></i> Tambah Barang Baru</h2>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>/dashboard">Dashboard</a>
            <span class="separator">/</span>
            <a href="<?= BASE_URL ?>/aset">Data Aset & Inventaris</a>
            <span class="separator">/</span>
            <span>Tambah</span>
        </div>
    </div>
</div>

<div class="card animate-fadeInUp">
    <div class="card-header">
        <h3>Form Tambah Barang</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger mb-4">
                <i class="fas fa-exclamation-circle"></i>
                <div style="margin-left: 8px;">
                    <?php foreach ($errors as $err): ?>
                        <div><?= htmlspecialchars($err) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <?= generateCsrfToken() ?>
            <div class="grid-2">
                <div class="form-group">
                    <label>Kode Barang</label>
                    <input type="text" class="form-control" name="kode_aset" value="<?= htmlspecialchars($post_kode) ?>" required>
                </div>
                <div class="form-group">
                    <label>Nama Barang *</label>
                    <input type="text" class="form-control" name="nama_aset" value="<?= htmlspecialchars($post_nama) ?>" placeholder="Masukkan nama barang" required>
                </div>
                <div class="form-group">
                    <label>Klasifikasi Barang *</label>
                    <select class="form-control" name="jenis_barang" required>
                        <option value="Aset Tetap" <?= ($post_jenis ?? 'Aset Tetap') === 'Aset Tetap' ? 'selected' : '' ?>>🏷️ Aset Tetap (Kapitalisasi BMN / Barang Bernilai Tinggi)</option>
                        <option value="Inventaris Barang" <?= ($post_jenis ?? '') === 'Inventaris Barang' ? 'selected' : '' ?>>📦 Inventaris Barang (Peralatan Operasional / Non-Kapitalisasi)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select class="form-control" name="id_kategori">
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach ($kategoriList as $k): ?>
                            <option value="<?= $k['id'] ?>" <?= $k['id'] == $post_idKategori ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Lokasi / Ruangan</label>
                    <select class="form-control" name="id_lokasi">
                        <option value="">-- Pilih Lokasi --</option>
                        <?php foreach ($lokasiList as $l): ?>
                            <option value="<?= $l['id'] ?>" <?= $l['id'] == $post_idLokasi ? 'selected' : '' ?>><?= htmlspecialchars($l['nama_lokasi']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jumlah *</label>
                    <input type="number" class="form-control" name="jumlah" value="<?= htmlspecialchars($post_jumlah) ?>" min="1" required>
                </div>
                <div class="form-group">
                    <label>Kondisi *</label>
                    <input type="text" class="form-control" name="kondisi" value="Baik" readonly style="background-color: #e9ecef; cursor: not-allowed;">
                    <small class="text-muted">Aset baru otomatis berstatus Baik. Untuk aset rusak, gunakan menu "Tambah Aset Rusak".</small>
                </div>
                <div class="form-group">
                    <label>Tahun Perolehan</label>
                    <input type="number" class="form-control" name="tahun_perolehan" value="<?= htmlspecialchars($post_tahun) ?>" placeholder="2024" min="1990" max="<?= date('Y') ?>">
                </div>
                <div class="form-group">
                    <label>Nilai Perolehan (Rp)</label>
                    <input type="number" class="form-control" name="nilai_perolehan" value="<?= htmlspecialchars($post_nilai) ?>" placeholder="0">
                </div>
                <div class="form-group">
                    <label>Sumber Dana</label>
                    <input type="text" class="form-control" name="sumber_dana" value="<?= htmlspecialchars($post_sumber) ?>" placeholder="contoh: Dana BOS, APBD">
                </div>
                <div class="form-group">
                    <label>Gambar Aset</label>
                    <input type="file" class="form-control" name="gambar" accept="image/*" onchange="previewImage(this, 'imgPreview')">
                    <img id="imgPreview" class="img-preview mt-2" style="display:none;" alt="Preview">
                </div>
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <textarea class="form-control" name="keterangan" placeholder="Keterangan tambahan..."><?= htmlspecialchars($post_keterangan) ?></textarea>
            </div>
            <div class="btn-group mt-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="<?= BASE_URL ?>/aset" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
