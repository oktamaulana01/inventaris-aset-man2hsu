<?php
$pageTitle = 'Finalisasi Penghapusan Aset';
require_once __DIR__ . '/../../includes/auth_check.php';
requireStaff();
$pdo = getConnection();

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("
    SELECT a.*, k.nama_kategori, l.nama_lokasi 
    FROM aset a 
    LEFT JOIN kategori k ON a.id_kategori = k.id 
    LEFT JOIN lokasi l ON a.id_lokasi = l.id 
    WHERE a.id = ?
");
$stmt->execute([$id]);
$aset = $stmt->fetch();

if (!$aset) {
    setFlash('danger', 'Data aset tidak ditemukan!');
    header('Location: ' . BASE_URL . '/aset');
    exit;
}

// Handle Batalkan Pengajuan
if (isset($_POST['action']) && $_POST['action'] === 'batal') {
    validateCsrfToken();
    $batalStmt = $pdo->prepare("
        UPDATE aset 
        SET status_penghapusan = 'none',
            tgl_pengajuan_hapus = NULL
        WHERE id = ? AND deleted_at IS NULL
    ");
    $batalStmt->execute([$id]);
    
    logActivity($pdo, $_SESSION['user_id'], 'Penghapusan', "Membatalkan pengajuan penghapusan aset: {$aset['nama_aset']}");
    setFlash('info', 'Pengajuan penghapusan aset telah dibatalkan. Aset kembali aktif.');
    header('Location: ' . BASE_URL . '/aset');
    exit;
}

// Handle Finalisasi Penghapusan (Upload Scan BA & Soft Delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['action']) || $_POST['action'] === 'finalisasi')) {
    validateCsrfToken();
    
    // File scan BA bertanda tangan (bisa PDF atau Gambar)
    if (!isset($_FILES['file_ba_scan']) || $_FILES['file_ba_scan']['error'] !== UPLOAD_ERR_OK) {
        setFlash('danger', 'Anda wajib melampirkan berkas scan Berita Acara yang telah ditandatangani dan distempel!');
    } else {
        $file = $_FILES['file_ba_scan'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
        
        if (!in_array($ext, $allowed)) {
            setFlash('danger', 'Format berkas scan tidak didukung. Harap unggah berkas bertipe PDF, JPG, JPEG, atau PNG.');
        } else {
            $uploadDir = __DIR__ . '/../../assets/uploads/ba_penghapusan/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filename = 'BA_HAPUS_SIGNED_' . $aset['kode_aset'] . '_' . time() . '.' . $ext;
            $destPath = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $destPath)) {
                // Soft Delete: status_penghapusan = 'approved', deleted_at = CURRENT_TIMESTAMP
                $finalStmt = $pdo->prepare("
                    UPDATE aset 
                    SET status_penghapusan = 'approved',
                        file_ba_scan = ?,
                        deleted_at = CURRENT_TIMESTAMP
                    WHERE id = ?
                ");
                $finalStmt->execute([$filename, $id]);
                
                logActivity($pdo, $_SESSION['user_id'], 'Penghapusan', "Finalisasi penghapusan aset (Soft Delete): {$aset['nama_aset']} (Kode: {$aset['kode_aset']}) dengan Berita Acara bertanda tangan.");
                setFlash('success', 'Penghapusan aset berhasil difinalisasi! Aset telah dihapus dari inventaris aktif dan tersimpan di Laporan Penghapusan BMN.');
                header('Location: ' . BASE_URL . '/laporan/penghapusan');
                exit;
            } else {
                setFlash('danger', 'Gagal mengunggah berkas scan Berita Acara.');
            }
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header">
    <div>
        <h2><i class="fas fa-stamp text-danger"></i> Tahap 2: Finalisasi Penghapusan Aset</h2>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>/dashboard">Dashboard</a>
            <span class="separator">/</span>
            <a href="<?= BASE_URL ?>/aset">Data Aset</a>
            <span class="separator">/</span>
            <span>Finalisasi Penghapusan</span>
        </div>
    </div>
</div>

<!-- Step Tracker Indicator -->
<div class="card mb-4" style="background:var(--bg-secondary); border:1px solid var(--border-color);">
    <div class="card-body" style="padding:15px 20px;">
        <div style="display:flex; justify-content:space-around; align-items:center; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; align-items:center; gap:8px; color:var(--success); font-weight:600;">
                <i class="fas fa-check-circle" style="font-size:18px;"></i>
                <span>1. Alasan & Bukti Foto Dicatat</span>
            </div>
            <i class="fas fa-chevron-right text-muted"></i>
            <div style="display:flex; align-items:center; gap:8px; color:var(--accent-primary); font-weight:bold;">
                <i class="fas fa-print" style="font-size:18px;"></i>
                <span>2. Cetak & Tanda Tangan BA</span>
            </div>
            <i class="fas fa-chevron-right text-muted"></i>
            <div style="display:flex; align-items:center; gap:8px; color:var(--danger); font-weight:bold;">
                <i class="fas fa-file-upload" style="font-size:18px;"></i>
                <span>3. Upload Scan BA & Soft Delete</span>
            </div>
        </div>
    </div>
</div>

<div class="grid-2">
    <!-- Card Kiri: Rincian Aset & Cetak Dokumen -->
    <div class="card animate-fadeInUp">
        <div class="card-header">
            <h3><i class="fas fa-file-contract" style="color:var(--accent-primary);margin-right:8px;"></i> Langkah 1: Cetak Berita Acara</h3>
        </div>
        <div class="card-body">
            <p style="font-size:13.5px; color:var(--text-secondary); line-height:1.5;">
                Klik tombol di bawah ini untuk membuka dan mencetak lembar Berita Acara Penghapusan resmi untuk ditandatangani oleh <strong>Pengurus Barang, Kepala Tata Usaha,</strong> dan <strong>Kepala Madrasah</strong>.
            </p>
            
            <div style="margin: 16px 0; display:flex; flex-direction:column; gap:8px;">
                <a href="<?= BASE_URL ?>/berita-acara/penghapusan?id=<?= $aset['id'] ?>" target="_blank" class="btn btn-primary" style="justify-content:center;">
                    <i class="fas fa-print"></i> Cetak / Buka Berita Acara Resmi
                </a>
                <a href="<?= BASE_URL ?>/berita-acara/penghapusan?id=<?= $aset['id'] ?>&download=pdf" class="btn btn-info" style="justify-content:center;">
                    <i class="fas fa-file-pdf"></i> Unduh Berkas PDF
                </a>
            </div>

            <div style="background:var(--bg-secondary); padding:14px; border-radius:6px; font-size:13px; margin-top:15px; border:1px solid var(--border-color);">
                <strong>Ringkasan Pengajuan:</strong>
                <ul style="margin:6px 0 0 16px; padding:0; line-height:1.6;">
                    <li><strong>Kode Aset:</strong> <?= htmlspecialchars($aset['kode_aset']) ?></li>
                    <li><strong>Nama Barang:</strong> <?= htmlspecialchars($aset['nama_aset']) ?></li>
                    <li><strong>Tgl Diajukan:</strong> <?= $aset['tgl_pengajuan_hapus'] ? date('d/m/Y H:i', strtotime($aset['tgl_pengajuan_hapus'])) : date('d/m/Y') ?></li>
                    <li><strong>Alasan:</strong> <em>"<?= htmlspecialchars($aset['alasan_hapus'] ?? '-') ?>"</em></li>
                </ul>
                <?php if ($aset['bukti_hapus']): ?>
                    <div style="margin-top:10px;">
                        <a href="<?= BASE_URL ?>/assets/uploads/bukti_hapus/<?= htmlspecialchars($aset['bukti_hapus']) ?>" target="_blank" class="btn btn-sm btn-secondary">
                            <i class="fas fa-image"></i> Lihat Foto Bukti Fisik
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Card Kanan: Form Upload Scan BA & Finalisasi -->
    <div class="card animate-fadeInUp">
        <div class="card-header" style="background:#fee2e2;">
            <h3 style="color:#b91c1c; margin:0;"><i class="fas fa-stamp"></i> Langkah 2: Upload BA & Finalisasi</h3>
        </div>
        <div class="card-body">
            <p style="font-size:13.5px; color:var(--text-secondary); line-height:1.5;">
                Setelah dokumen Berita Acara fisik selesai ditandatangani dan dicap stempel dinas, silakan scan atau foto berkas tersebut dan unggah di bawah ini untuk meresmikan penghapusan.
            </p>

            <form method="POST" enctype="multipart/form-data">
                <?= generateCsrfToken() ?>
                <input type="hidden" name="action" value="finalisasi">

                <div class="form-group">
                    <label><strong>Upload Berkas Scan Berita Acara Bertanda Tangan *</strong></label>
                    <input type="file" class="form-control" name="file_ba_scan" accept=".pdf,image/*" required>
                    <small class="text-muted" style="display:block; margin-top:4px;">Format: PDF, JPG, PNG (Maks 5MB). Dokumen ini akan menjadi arsip audit Barang Milik Negara.</small>
                </div>

                <div style="background:#fffbeb; padding:12px; border-radius:6px; border:1px solid #fde68a; font-size:12.5px; color:#92400e; margin: 16px 0;">
                    <i class="fas fa-shield-halved"></i> <strong>Sistem Soft Delete:</strong> Data aset akan dikeluarkan dari daftar inventaris aktif madrasah tetapi riwayat nilai, foto, dan dokumen Berita Acaranya tetap tersimpan utuh di Laporan Penghapusan BMN.
                </div>

                <div class="btn-group" style="display:flex; flex-direction:column; gap:8px;">
                    <button type="submit" class="btn btn-danger" style="justify-content:center;" onclick="return confirm('Apakah Anda yakin ingin menyelesaikan proses penghapusan aset ini secara permanen?')">
                        <i class="fas fa-check-double"></i> Konfirmasi & Selesaikan Penghapusan
                    </button>
                </div>
            </form>

            <hr style="margin:20px 0; border:0; border-top:1px solid var(--border-color);">

            <form method="POST" onsubmit="return confirm('Batalkan pengajuan penghapusan aset ini?')">
                <?= generateCsrfToken() ?>
                <input type="hidden" name="action" value="batal">
                <button type="submit" class="btn btn-secondary btn-sm" style="width:100%; justify-content:center;">
                    <i class="fas fa-rotate-left"></i> Batalkan Pengajuan (Kembalikan ke Inventaris Aktif)
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
