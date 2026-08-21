<?php
$pageTitle = 'Konfirmasi Penghapusan Aset';
require_once __DIR__ . '/../../includes/auth_check.php';
requireStaff();
$pdo = getConnection();

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM aset WHERE id = ? AND deleted_at IS NULL");
$stmt->execute([$id]);
$aset = $stmt->fetch();

if (!$aset) {
    setFlash('danger', 'Data aset tidak ditemukan atau sudah dihapus!');
    header('Location: ' . BASE_URL . '/aset');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrfToken();
    $alasan = trim($_POST['alasan_hapus']);
    
    // Validasi file upload
    if (!isset($_FILES['bukti_hapus']) || $_FILES['bukti_hapus']['error'] !== UPLOAD_ERR_OK) {
        setFlash('danger', 'Anda wajib melampirkan foto bukti aset tidak layak!');
    } elseif (empty($alasan)) {
        setFlash('danger', 'Alasan penghapusan wajib diisi!');
    } else {
        $file = $_FILES['bukti_hapus'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'jfif', 'gif', 'bmp', 'svg'];
        
        if (!in_array($ext, $allowed)) {
            setFlash('danger', 'Format foto tidak didukung. Gunakan JPG, JPEG, PNG, WEBP, atau JFIF.');
        } else {
            $uploadDir = __DIR__ . '/../../assets/uploads/bukti_hapus/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filename = 'bukti_' . $aset['kode_aset'] . '_' . time() . '.' . $ext;
            $destPath = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $destPath)) {
                $delStmt = $pdo->prepare("UPDATE aset SET deleted_at = CURRENT_TIMESTAMP, bukti_hapus = ?, alasan_hapus = ? WHERE id = ?");
                $delStmt->execute([$filename, $alasan, $id]);
                
                logActivity($pdo, $_SESSION['user_id'], 'Penghapusan', "Penghapusan aset: {$aset['nama_aset']} (Kode: {$aset['kode_aset']})");
                setFlash('success', 'Aset berhasil dihapus beserta bukti fotonya!');
                header('Location: ' . BASE_URL . '/aset');
                exit;
            } else {
                setFlash('danger', 'Gagal mengunggah foto bukti.');
            }
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header">
    <div>
        <h2><i class="fas fa-trash-alt text-danger"></i> Konfirmasi Penghapusan Aset</h2>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>/dashboard">Dashboard</a>
            <span class="separator">/</span>
            <a href="<?= BASE_URL ?>/aset">Data Aset</a>
            <span class="separator">/</span>
            <span>Konfirmasi Hapus</span>
        </div>
    </div>
</div>

<div class="card animate-fadeInUp">
    <div class="card-header" style="background-color: #fee2e2;">
        <h3 class="text-danger" style="margin:0;"><i class="fas fa-exclamation-triangle"></i> Peringatan: Aset akan dihapus</h3>
    </div>
    <div class="card-body">
        <p>Anda akan menghapus aset berikut dari sistem:</p>
        <div style="background:var(--bg-secondary); padding:15px; border-radius:8px; margin-bottom:20px;">
            <strong>Nama Aset:</strong> <?= htmlspecialchars($aset['nama_aset']) ?><br>
            <strong>Kode Aset:</strong> <?= htmlspecialchars($aset['kode_aset']) ?><br>
            <strong>Kondisi Saat Ini:</strong> <?= htmlspecialchars($aset['kondisi']) ?><br>
        </div>
        
        <form method="POST" enctype="multipart/form-data">
            <?= generateCsrfToken() ?>
            <div class="form-group">
                <label>Foto Bukti (Aset Rusak / Hilang / Tidak Layak) *</label>
                <input type="file" class="form-control" name="bukti_hapus" accept="image/*" required onchange="previewImage(this, 'imgPreview')">
                <img id="imgPreview" class="img-preview mt-2" style="display:none; max-width: 300px; border-radius: 8px; border: 1px solid #ddd;" alt="Preview Bukti">
                <small class="text-muted" style="display:block; margin-top:4px;">Maksimal 2MB. Format: JPG, PNG, WEBP.</small>
            </div>
            
            <div class="form-group">
                <label>Alasan Penghapusan *</label>
                <textarea class="form-control" name="alasan_hapus" required placeholder="Contoh: Barang terbakar, hilang dicuri, atau rusak berat tidak dapat diperbaiki." rows="4"></textarea>
            </div>
            
            <div class="btn-group mt-4">
                <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Konfirmasi Hapus Aset</button>
                <a href="<?= BASE_URL ?>/aset" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input, previewId) {
    var preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
        preview.src = '';
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>


