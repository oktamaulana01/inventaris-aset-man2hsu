<?php
$pageTitle = 'Konfirmasi Penerimaan Mutasi Aset';
require_once __DIR__ . '/../../includes/auth_check.php';
requireStaff();
$pdo = getConnection();

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("
    SELECT m.*, 
           a.kode_aset, a.nama_aset, a.kondisi as kondisi_aset, a.id_lokasi as id_lokasi_sekarang,
           la.nama_lokasi as lokasi_asal, 
           lt.nama_lokasi as lokasi_tujuan, 
           u.nama as nama_petugas_pengaju
    FROM mutasi_aset m
    JOIN aset a ON m.id_aset = a.id
    LEFT JOIN lokasi la ON m.id_lokasi_asal = la.id
    LEFT JOIN lokasi lt ON m.id_lokasi_tujuan = lt.id
    LEFT JOIN users u ON m.id_user = u.id
    WHERE m.id = ?
");
$stmt->execute([$id]);
$mutasi = $stmt->fetch();

if (!$mutasi) {
    setFlash('danger', 'Data mutasi aset tidak ditemukan!');
    header('Location: ' . BASE_URL . '/mutasi');
    exit;
}

// Handle Batalkan Mutasi
if (isset($_POST['action']) && $_POST['action'] === 'batal') {
    validateCsrfToken();
    $pdo->beginTransaction();
    try {
        $stmtBatal = $pdo->prepare("UPDATE mutasi_aset SET status = 'cancelled' WHERE id = ?");
        $stmtBatal->execute([$id]);

        $stmtAset = $pdo->prepare("UPDATE aset SET status_mutasi = 'none' WHERE id = ?");
        $stmtAset->execute([$mutasi['id_aset']]);

        logActivity($pdo, $_SESSION['user_id'], 'Mutasi Aset', "Membatalkan pengajuan mutasi aset {$mutasi['nama_aset']} ({$mutasi['kode_aset']})");
        $pdo->commit();

        setFlash('info', 'Pengajuan mutasi aset telah dibatalkan. Aset tetap di ruangan asal.');
        header('Location: ' . BASE_URL . '/mutasi');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        setFlash('danger', 'Gagal membatalkan mutasi: ' . $e->getMessage());
    }
}

// Handle Konfirmasi Penerimaan (Upload BAST & Pindahkan Lokasi Aset)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['action']) || $_POST['action'] === 'terima')) {
    validateCsrfToken();
    
    $noBast = trim($_POST['no_bast'] ?? '');
    $catatanTerima = trim($_POST['catatan_terima'] ?? '');
    $tglTerima = $_POST['tgl_terima'] ?? date('Y-m-d H:i:s');

    if (!isset($_FILES['file_bast_scan']) || $_FILES['file_bast_scan']['error'] !== UPLOAD_ERR_OK) {
        setFlash('danger', 'Anda wajib melampirkan berkas scan/foto Berita Acara Serah Terima (BAST) yang telah ditandatangani!');
    } else {
        $file = $_FILES['file_bast_scan'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['pdf', 'jpg', 'jpeg', 'png'];

        if (!in_array($ext, $allowed)) {
            setFlash('danger', 'Format berkas tidak didukung. Harap unggah berkas PDF, JPG, JPEG, atau PNG.');
        } else {
            $uploadDir = __DIR__ . '/../../assets/uploads/bast_mutasi/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filename = 'BAST_MUTASI_' . $mutasi['kode_aset'] . '_' . time() . '.' . $ext;
            $destPath = $uploadDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $destPath)) {
                $pdo->beginTransaction();
                try {
                    // 1. Update mutasi_aset record to 'completed'
                    $stmtUpdateMutasi = $pdo->prepare("
                        UPDATE mutasi_aset 
                        SET status = 'completed',
                            no_bast = ?,
                            tgl_terima = ?,
                            file_bast_scan = ?,
                            catatan_terima = ?,
                            id_user_terima = ?
                        WHERE id = ?
                    ");
                    $stmtUpdateMutasi->execute([$noBast ?: ('BA.MUTASI/' . sprintf('%03d', $mutasi['id']) . '/MAN.2.HSU/' . date('Y')), $tglTerima, $filename, $catatanTerima, $_SESSION['user_id'], $id]);

                    // 2. RESMI PINDAHKAN LOKASI ASET DI DATABASE & NORMALKAN STATUS
                    $stmtUpdateAset = $pdo->prepare("
                        UPDATE aset 
                        SET id_lokasi = ?, 
                            status_mutasi = 'none' 
                        WHERE id = ?
                    ");
                    $stmtUpdateAset->execute([$mutasi['id_lokasi_tujuan'], $mutasi['id_aset']]);

                    logActivity($pdo, $_SESSION['user_id'], 'Mutasi Aset', "Penerimaan mutasi aset {$mutasi['nama_aset']} ({$mutasi['kode_aset']}). Lokasi aset resmi berpindah dari {$mutasi['lokasi_asal']} ke {$mutasi['lokasi_tujuan']}.");

                    // Telegram Notification
                    require_once __DIR__ . '/../../config/mailer.php';
                    $msg = "✅ <b>Mutasi Aset Selesai Diterima</b>\n\n" .
                           "Aset: <b>" . htmlspecialchars($mutasi['nama_aset']) . " (" . htmlspecialchars($mutasi['kode_aset']) . ")</b>\n" .
                           "Lokasi Baru: <b>" . htmlspecialchars($mutasi['lokasi_tujuan']) . "</b>\n" .
                           "Status: <b>Selesai Diterima & Lokasi Diperbarui</b>\n" .
                           "Petugas Penerima: " . htmlspecialchars($_SESSION['user_nama']);
                    sendTelegramNotification($pdo, $msg);

                    $pdo->commit();
                    setFlash('success', "Penerimaan mutasi berhasil dikonfirmasi! Lokasi aset {$mutasi['nama_aset']} resmi berpindah ke ruangan {$mutasi['lokasi_tujuan']}.");
                    header('Location: ' . BASE_URL . '/mutasi');
                    exit;
                } catch (Exception $e) {
                    $pdo->rollBack();
                    setFlash('danger', 'Gagal memproses penerimaan mutasi: ' . $e->getMessage());
                }
            } else {
                setFlash('danger', 'Gagal mengunggah berkas scan BAST.');
            }
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header">
    <div>
        <h2><i class="fas fa-boxes-packing text-primary"></i> Konfirmasi Penerimaan Mutasi Aset</h2>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>/dashboard">Dashboard</a>
            <span class="separator">/</span>
            <a href="<?= BASE_URL ?>/mutasi">Mutasi Aset</a>
            <span class="separator">/</span>
            <span>Konfirmasi Penerimaan</span>
        </div>
    </div>
</div>

<!-- Step Tracker Indicator -->
<div class="card mb-4" style="background:var(--bg-secondary); border:1px solid var(--border-color);">
    <div class="card-body" style="padding:15px 20px;">
        <div style="display:flex; justify-content:space-around; align-items:center; flex-wrap:wrap; gap:10px;">
            <div style="display:flex; align-items:center; gap:8px; color:var(--success); font-weight:600;">
                <i class="fas fa-check-circle" style="font-size:18px;"></i>
                <span>1. Pengajuan Mutasi (Maker)</span>
            </div>
            <i class="fas fa-chevron-right text-muted"></i>
            <div style="display:flex; align-items:center; gap:8px; color:var(--accent-primary); font-weight:bold;">
                <i class="fas fa-truck-ramp-box" style="font-size:18px;"></i>
                <span>2. Sedang Dimutasi (In Transit)</span>
            </div>
            <i class="fas fa-chevron-right text-muted"></i>
            <div style="display:flex; align-items:center; gap:8px; color:var(--warning); font-weight:bold;">
                <i class="fas fa-file-signature" style="font-size:18px;"></i>
                <span>3. Cetak BAST & Tanda Tangan</span>
            </div>
            <i class="fas fa-chevron-right text-muted"></i>
            <div style="display:flex; align-items:center; gap:8px; color:var(--success); font-weight:bold;">
                <i class="fas fa-square-check" style="font-size:18px;"></i>
                <span>4. Terima & Pindah Lokasi (Checker)</span>
            </div>
        </div>
    </div>
</div>

<div class="grid-2">
    <!-- Card Kiri: Rincian Mutasi & Cetak BAST -->
    <div class="card animate-fadeInUp">
        <div class="card-header">
            <h3><i class="fas fa-file-contract" style="color:var(--accent-primary);margin-right:8px;"></i> Cetak Berita Acara Serah Terima (BAST)</h3>
        </div>
        <div class="card-body">
            <p style="font-size:13.5px; color:var(--text-secondary); line-height:1.5;">
                Gunakan dokumen Berita Acara ini sebagai bukti serah terima fisik barang saat dipindahkan dari <strong><?= htmlspecialchars($mutasi['lokasi_asal'] ?? 'Ruang Asal') ?></strong> ke <strong><?= htmlspecialchars($mutasi['lokasi_tujuan'] ?? 'Ruang Tujuan') ?></strong>.
            </p>

            <div style="margin: 16px 0; display:flex; flex-direction:column; gap:8px;">
                <a href="<?= BASE_URL ?>/berita-acara/mutasi?id=<?= $mutasi['id'] ?>" target="_blank" class="btn btn-primary" style="justify-content:center;">
                    <i class="fas fa-print"></i> Buka / Cetak Lembar BAST Resmi
                </a>
                <a href="<?= BASE_URL ?>/berita-acara/mutasi?id=<?= $mutasi['id'] ?>&download=pdf" class="btn btn-info" style="justify-content:center;">
                    <i class="fas fa-file-pdf"></i> Unduh Berkas PDF BAST
                </a>
            </div>

            <div style="background:var(--bg-secondary); padding:16px; border-radius:8px; font-size:13px; margin-top:15px; border:1px solid var(--border-color);">
                <strong>Detail Mutasi Saat Ini:</strong>
                <ul style="margin:8px 0 0 16px; padding:0; line-height:1.7;">
                    <li><strong>Kode Aset:</strong> <span class="badge badge-primary"><?= htmlspecialchars($mutasi['kode_aset']) ?></span></li>
                    <li><strong>Nama Barang:</strong> <strong><?= htmlspecialchars($mutasi['nama_aset']) ?></strong></li>
                    <li><strong>Ruangan Asal:</strong> <?= htmlspecialchars($mutasi['lokasi_asal'] ?? '-') ?></li>
                    <li><strong>Ruangan Tujuan:</strong> <strong style="color:var(--accent-primary);"><?= htmlspecialchars($mutasi['lokasi_tujuan'] ?? '-') ?></strong></li>
                    <li><strong>Tgl Pengajuan:</strong> <?= date('d/m/Y', strtotime($mutasi['tanggal_mutasi'])) ?></li>
                    <li><strong>Keterangan:</strong> <em>"<?= htmlspecialchars($mutasi['keterangan'] ?? '-') ?>"</em></li>
                    <li><strong>Petugas Pengirim:</strong> <?= htmlspecialchars($mutasi['nama_petugas_pengaju'] ?? 'Petugas') ?></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Card Kanan: Form Konfirmasi Penerimaan & Upload Scan BAST -->
    <div class="card animate-fadeInUp">
        <div class="card-header" style="background:#ecfdf5; border-bottom:1px solid #a7f3d0;">
            <h3 style="color:#047857; margin:0;"><i class="fas fa-clipboard-check"></i> Konfirmasi Penerimaan Barang</h3>
        </div>
        <div class="card-body">
            <?php if ($mutasi['status'] === 'completed'): ?>
                <div style="background:#dcfce7; color:#166534; padding:16px; border-radius:8px; border:1px solid #bbf7d0; margin-bottom:15px;">
                    <h4 style="margin:0 0 6px 0;"><i class="fas fa-check-circle"></i> Mutasi Selesai Diterima</h4>
                    <p style="margin:0; font-size:13px;">
                        Aset ini telah resmi diterima dan lokasi penempatannya telah diperbarui ke ruangan <strong><?= htmlspecialchars($mutasi['lokasi_tujuan']) ?></strong> pada tanggal <?= date('d/m/Y H:i', strtotime($mutasi['tgl_terima'])) ?>.
                    </p>
                    <?php if ($mutasi['file_bast_scan']): ?>
                        <div style="margin-top:12px;">
                            <a href="<?= BASE_URL ?>/assets/uploads/bast_mutasi/<?= htmlspecialchars($mutasi['file_bast_scan']) ?>" target="_blank" class="btn btn-sm btn-success">
                                <i class="fas fa-file-shield"></i> Lihat Berkas Scan BAST Asli
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p style="font-size:13.5px; color:var(--text-secondary); line-height:1.5;">
                    Setelah barang fisik diperiksa di ruangan tujuan dan lembar BAST telah ditandatangani oleh penanggung jawab ruangan, silakan unggah berkas scan BAST di bawah ini untuk menyelesaikan mutasi.
                </p>

                <form method="POST" enctype="multipart/form-data">
                    <?= generateCsrfToken() ?>
                    <input type="hidden" name="action" value="terima">

                    <div class="form-group">
                        <label><strong>Nomor BAST (Opsional / Otomatis)</strong></label>
                        <input type="text" class="form-control" name="no_bast" placeholder="Contoh: BAST.MUTASI/<?= sprintf('%03d', $mutasi['id']) ?>/MAN.2.HSU/<?= date('Y') ?>" value="<?= htmlspecialchars($mutasi['no_bast'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label><strong>Tanggal & Waktu Penerimaan Barang *</strong></label>
                        <input type="datetime-local" class="form-control" name="tgl_terima" value="<?= date('Y-m-d\TH:i') ?>" required>
                    </div>

                    <div class="form-group">
                        <label><strong>Upload Berkas Scan BAST Bertanda Tangan *</strong></label>
                        <input type="file" class="form-control" name="file_bast_scan" accept=".pdf,image/*" required>
                        <small class="text-muted" style="display:block; margin-top:4px;">Format didukung: PDF, JPG, PNG. Lampirkan lembar BAST yang sudah ditandatangani.</small>
                    </div>

                    <div class="form-group">
                        <label><strong>Catatan Kondisi Barang Saat Diterima</strong></label>
                        <textarea class="form-control" name="catatan_terima" placeholder="Contoh: Barang telah diterima lengkap di ruangan baru, kondisi fisik baik dan berfungsi normal." rows="3"></textarea>
                    </div>

                    <div style="background:#eff6ff; padding:12px; border-radius:6px; border:1px solid #bfdbfe; font-size:12.5px; color:#1e40af; margin: 16px 0;">
                        <i class="fas fa-location-dot"></i> <strong>Pembaruan Otomatis:</strong> Menekan tombol konfirmasi akan memperbarui lokasi aset secara resmi di seluruh data master & kartu inventaris ruangan (KIR).
                    </div>

                    <div class="btn-group" style="display:flex; flex-direction:column; gap:8px;">
                        <button type="submit" class="btn btn-success" style="justify-content:center;" onclick="return confirm('Konfirmasi penerimaan barang dan perbarui lokasi aset ini?')">
                            <i class="fas fa-check-double"></i> Konfirmasi Terima Barang & Pindahkan Lokasi Aset
                        </button>
                    </div>
                </form>

                <hr style="margin:20px 0; border:0; border-top:1px solid var(--border-color);">

                <form method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan proses mutasi ini? Status aset akan kembali normal di ruangan asal.')">
                    <?= generateCsrfToken() ?>
                    <input type="hidden" name="action" value="batal">
                    <button type="submit" class="btn btn-secondary btn-sm" style="width:100%; justify-content:center;">
                        <i class="fas fa-times-circle"></i> Batalkan Pengajuan Mutasi (Kembali ke Lokasi Asal)
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
