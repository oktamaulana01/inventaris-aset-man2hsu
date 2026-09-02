<?php
$pageTitle = 'Ajukan Penghapusan Aset';
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

if ($aset['status_penghapusan'] === 'pending') {
    setFlash('warning', 'Aset ini sudah dalam tahap pengajuan penghapusan. Silakan cetak Berita Acara atau lanjutkan ke Finalisasi.');
    header('Location: ' . BASE_URL . '/aset/finalisasi-hapus?id=' . $id);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrfToken();
    $alasan = trim($_POST['alasan_hapus']);
    
    // Validasi file upload
    if (!isset($_FILES['bukti_hapus']) || $_FILES['bukti_hapus']['error'] !== UPLOAD_ERR_OK) {
        setFlash('danger', 'Anda wajib melampirkan foto bukti kondisi fisik aset!');
    } elseif (empty($alasan)) {
        setFlash('danger', 'Alasan penghapusan wajib diisi secara detail!');
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
                // Set status_penghapusan = 'pending', kondisi = 'Rusak Berat' (tapi BELUM soft delete deleted_at)
                $upStmt = $pdo->prepare("
                    UPDATE aset 
                    SET status_penghapusan = 'pending',
                        kondisi = 'Rusak Berat',
                        bukti_hapus = ?, 
                        alasan_hapus = ?,
                        tgl_pengajuan_hapus = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");
                $upStmt->execute([$filename, $alasan, $id]);
                
                logActivity($pdo, $_SESSION['user_id'], 'Penghapusan', "Pengajuan penghapusan aset: {$aset['nama_aset']} (Kode: {$aset['kode_aset']})");
                setFlash('success', 'Pengajuan penghapusan berhasil dicatat! Silakan cetak dokumen Berita Acara untuk ditandatangani.');
                header('Location: ' . BASE_URL . '/aset/finalisasi-hapus?id=' . $id);
                exit;
            } else {
                setFlash('danger', 'Gagal mengunggah foto bukti fisik.');
            }
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header">
    <div>
        <h2><i class="fas fa-file-signature text-warning"></i> Tahap 1: Ajukan Penghapusan Aset</h2>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>/dashboard">Dashboard</a>
            <span class="separator">/</span>
            <a href="<?= BASE_URL ?>/aset">Data Aset</a>
            <span class="separator">/</span>
            <span>Ajukan Penghapusan</span>
        </div>
    </div>
</div>

<div class="card animate-fadeInUp mb-4" style="border-left: 4px solid var(--warning);">
    <div class="card-body" style="display:flex; align-items:flex-start; gap:16px;">
        <div style="font-size: 28px; color: var(--warning);"><i class="fas fa-info-circle"></i></div>
        <div>
            <h4 style="margin:0 0 6px 0;">Alur Standar Penghapusan Barang Milik Negara (BMN)</h4>
            <p style="margin:0; font-size:13.5px; color:var(--text-secondary); line-height:1.5;">
                Pada tahap ini, Anda mencatat alasan teknis dan foto kondisi fisik barang. Setelah form ini disimpan, sistem akan mengunci aset dari peminjaman dan membuatkan <strong>Draft Berita Acara (BA) Penghapusan Resmi</strong> untuk dicetak dan ditandatangani oleh Tim Pengurus Barang, Kepala Tata Usaha, dan Kepala MAN 2 HSU.
            </p>
        </div>
    </div>
</div>

<div class="card animate-fadeInUp">
    <div class="card-header">
        <h3><i class="fas fa-box-archive" style="color:var(--accent-primary);margin-right:8px;"></i> Informasi Aset Yang Akan Dihapuskan</h3>
    </div>
    <div class="card-body">
        <div style="background:var(--bg-secondary); padding:16px; border-radius:8px; margin-bottom:24px; border:1px solid var(--border-color);">
            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:12px; font-size:14px;">
                <div><strong>Kode Aset:</strong> <span class="badge badge-primary"><?= htmlspecialchars($aset['kode_aset']) ?></span></div>
                <div><strong>Nama Aset:</strong> <?= htmlspecialchars($aset['nama_aset']) ?></div>
                <div><strong>Kondisi Terakhir:</strong> <?= htmlspecialchars($aset['kondisi']) ?></div>
                <div><strong>Tahun Perolehan:</strong> <?= $aset['tahun_perolehan'] ?: '-' ?></div>
                <div><strong>Nilai Perolehan:</strong> Rp <?= number_format($aset['nilai_perolehan'], 0, ',', '.') ?></div>
                <div><strong>Sumber Dana:</strong> <?= htmlspecialchars($aset['sumber_dana'] ?: '-') ?></div>
            </div>
        </div>
        
        <form method="POST" enctype="multipart/form-data">
            <?= generateCsrfToken() ?>
            <div class="form-group">
                <label><strong>1. Foto Bukti Fisik Kerusakan / Kondisi Barang *</strong></label>
                <input type="file" class="form-control" name="bukti_hapus" accept="image/*" required onchange="previewImage(this, 'imgPreview')">
                <img id="imgPreview" class="img-preview mt-2" style="display:none; max-width: 320px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 4px;" alt="Preview Bukti">
                <small class="text-muted" style="display:block; margin-top:4px;">Format didukung: JPG, PNG, WEBP, JFIF. Foto akan otomatis dilampirkan pada Berita Acara PDF.</small>
            </div>
            
            <div class="form-group">
                <label><strong>2. Alasan / Dasar Teknis Penghapusan *</strong></label>
                <textarea class="form-control" name="alasan_hapus" required placeholder="Jelaskan secara spesifik alasan penghapusan, misalnya: Rusak berat akibat korsleting komponen internal, biaya perbaikan melebihi nilai ekonomis aset, atau usang dimakan usia." rows="4"></textarea>
            </div>
            
            <div class="btn-group mt-4">
                <button type="submit" class="btn btn-warning"><i class="fas fa-file-arrow-up"></i> Simpan & Buat Draft Berita Acara</button>
                <a href="<?= BASE_URL ?>/aset" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
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
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
