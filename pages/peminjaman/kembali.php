<?php
$pageTitle = 'Form Pengembalian Aset';
require_once __DIR__ . '/../../includes/auth_check.php';
requireStaff();
$pdo = getConnection();

$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

// Ambil data peminjaman
$stmt = $pdo->prepare("
    SELECT p.*, a.nama_aset, a.kode_aset, a.jumlah as total_stok
    FROM peminjaman p 
    JOIN aset a ON p.id_aset = a.id 
    WHERE p.id = ?
");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    setFlash('danger', 'Data peminjaman tidak ditemukan!');
    header('Location: ' . BASE_URL . '/peminjaman');
    exit;
}

if ($data['status'] !== 'Dipinjam') {
    setFlash('warning', 'Aset ini sudah dikembalikan atau pengajuan belum disetujui!');
    header('Location: ' . BASE_URL . '/peminjaman');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrfToken();
    
    $tglKembali = $_POST['tanggal_kembali_aktual'];
    $kondisi = $_POST['kondisi_saat_dikembalikan'];
    $catatan = trim($_POST['catatan_pengembalian']);
    
    // Update peminjaman status
    $stmtUpdate = $pdo->prepare("
        UPDATE peminjaman 
        SET status = 'Dikembalikan', 
            tanggal_kembali_aktual = ?, 
            kondisi_saat_dikembalikan = ?, 
            catatan_pengembalian = ? 
        WHERE id = ?
    ");
    $stmtUpdate->execute([$tglKembali, $kondisi, $catatan ?: null, $id]);
    
    logActivity($pdo, $_SESSION['user_id'], 'Pengembalian', "Pengembalian aset: {$data['nama_aset']} oleh {$data['nama_peminjam']} (Kondisi: $kondisi)");
    
    // Telegram Notification
    require_once __DIR__ . '/../../config/mailer.php';
    $msg = "✅ <b>Pengembalian Aset Berhasil</b>\n\n" .
           "Peminjam: <b>" . htmlspecialchars($data['nama_peminjam']) . "</b>\n" .
           "Aset: <b>" . htmlspecialchars($data['nama_aset']) . "</b>\n" .
           "Tanggal Pengembalian: " . date('d/m/Y', strtotime($tglKembali)) . "\n" .
           "Kondisi saat kembali: <b>" . htmlspecialchars($kondisi) . "</b>\n" .
           "Catatan: " . htmlspecialchars($catatan ?: '-') . "\n\n" .
           "Petugas: " . htmlspecialchars($_SESSION['user_nama']);
    sendTelegramNotification($pdo, $msg);

    setFlash('success', 'Aset berhasil dikembalikan!');
    header('Location: ' . BASE_URL . '/peminjaman');
    exit;
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-rotate-left"></i> Pengembalian Aset</h2>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>/dashboard">Dashboard</a>
            <span class="separator">/</span>
            <a href="<?= BASE_URL ?>/peminjaman">Peminjaman</a>
            <span class="separator">/</span>
            <span>Pengembalian</span>
        </div>
    </div>
</div>

<div class="card animate-fadeInUp">
    <div class="card-header">
        <h3><i class="fas fa-clipboard-check" style="color:var(--accent-primary);margin-right:8px;"></i> Form Pengembalian Aset</h3>
    </div>
    <div class="card-body">
        <!-- Info Peminjaman -->
        <div class="card mb-4" style="background:rgba(30,114,86,0.04); border-color:rgba(30,114,86,0.15);">
            <div class="card-body" style="padding:16px 20px;">
                <p style="font-size:0.85rem; font-weight:600; color:var(--accent-primary); margin-bottom:8px;">
                    <i class="fas fa-info-circle"></i> Detail Peminjaman
                </p>
                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:16px; font-size:0.88rem; color:var(--text-secondary);">
                    <span><strong>Nama Peminjam:</strong> <?= htmlspecialchars($data['nama_peminjam']) ?></span>
                    <span><strong>Nama Aset:</strong> [<?= htmlspecialchars($data['kode_aset']) ?>] <?= htmlspecialchars($data['nama_aset']) ?></span>
                    <span><strong>Tanggal Pinjam:</strong> <?= date('d/m/Y', strtotime($data['tanggal_pinjam'])) ?></span>
                    <span><strong>Rencana Kembali:</strong> <?= date('d/m/Y', strtotime($data['tanggal_kembali_rencana'])) ?></span>
                </div>
            </div>
        </div>

        <form method="POST">
            <?= generateCsrfToken() ?>
            <input type="hidden" name="id" value="<?= $id ?>">
            
            <div class="grid-2">
                <div class="form-group">
                    <label><i class="fas fa-calendar-check" style="margin-right:4px;"></i> Tanggal Pengembalian *</label>
                    <input type="date" class="form-control" name="tanggal_kembali_aktual" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-shield-heart" style="margin-right:4px;"></i> Kondisi Aset Saat Dikembalikan *</label>
                    <select class="form-control" name="kondisi_saat_dikembalikan" required>
                        <option value="Baik">Baik</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group mt-3">
                <label><i class="fas fa-note-sticky" style="margin-right:4px;"></i> Catatan Pengembalian / Keterangan</label>
                <textarea class="form-control" name="catatan_pengembalian" placeholder="Tulis catatan kondisi fisik barang jika ada yang kurang lengkap atau lecet..." rows="3"></textarea>
            </div>
            
            <div class="btn-group mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Proses Pengembalian</button>
                <a href="<?= BASE_URL ?>/peminjaman" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
