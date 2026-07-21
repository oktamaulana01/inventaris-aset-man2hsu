<?php
$pageTitle = 'Detail Aset';
require_once __DIR__ . '/../../includes/auth_check.php';
$pdo = getConnection();

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT a.*, k.nama_kategori, l.nama_lokasi FROM aset a 
    LEFT JOIN kategori k ON a.id_kategori = k.id 
    LEFT JOIN lokasi l ON a.id_lokasi = l.id 
    WHERE a.id = ? AND a.deleted_at IS NULL");
$stmt->execute([$id]);
$aset = $stmt->fetch();
if (!$aset) { header('Location: ' . BASE_URL . '/aset'); exit; }

// Get peminjaman history
$stmtPinjam = $pdo->prepare("SELECT * FROM peminjaman WHERE id_aset = ? ORDER BY created_at DESC LIMIT 10");
$stmtPinjam->execute([$id]);
$pinjamList = $stmtPinjam->fetchAll();

// Get mutasi history
$stmtMutasi = $pdo->prepare("SELECT m.*, la.nama_lokasi as lokasi_asal, lt.nama_lokasi as lokasi_tujuan, u.nama 
    FROM mutasi_aset m 
    LEFT JOIN lokasi la ON m.id_lokasi_asal = la.id 
    LEFT JOIN lokasi lt ON m.id_lokasi_tujuan = lt.id 
    LEFT JOIN users u ON m.id_user = u.id 
    WHERE m.id_aset = ? ORDER BY m.created_at DESC");
$stmtMutasi->execute([$id]);
$mutasiList = $stmtMutasi->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-eye"></i> Detail Aset</h2>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>/dashboard">Dashboard</a>
            <span class="separator">/</span>
            <a href="<?= BASE_URL ?>/aset">Data Aset</a>
            <span class="separator">/</span>
            <span>Detail</span>
        </div>
    </div>
    <div class="btn-group">
        <a href="<?= BASE_URL ?>/aset/generate-qr?id=<?= $aset['id'] ?>" class="btn btn-info"><i class="fas fa-qrcode"></i> QR Code</a>
        <a href="<?= BASE_URL ?>/mutasi/tambah?id_aset=<?= $aset['id'] ?>" class="btn btn-primary"><i class="fas fa-arrows-split-up-and-left"></i> Mutasi</a>
        <a href="<?= BASE_URL ?>/aset/edit?id=<?= $aset['id'] ?>" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
        <a href="<?= BASE_URL ?>/aset" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</div>

<div class="grid-2">
    <!-- Info Aset -->
    <div class="card animate-fadeInUp">
        <div class="card-header">
            <h3><i class="fas fa-info-circle"></i> Informasi Aset</h3>
        </div>
        <div class="card-body">
            <div class="detail-grid">
                <div class="detail-label">Kode Aset</div>
                <div class="detail-value"><span class="badge badge-primary"><?= htmlspecialchars($aset['kode_aset']) ?></span></div>
                
                <div class="detail-label">Nama Aset</div>
                <div class="detail-value" style="font-weight:600;"><?= htmlspecialchars($aset['nama_aset']) ?></div>
                
                <div class="detail-label">Kategori</div>
                <div class="detail-value"><?= htmlspecialchars($aset['nama_kategori'] ?? '-') ?></div>
                
                <div class="detail-label">Lokasi</div>
                <div class="detail-value"><?= htmlspecialchars($aset['nama_lokasi'] ?? '-') ?></div>
                
                <div class="detail-label">Jumlah</div>
                <div class="detail-value"><?= $aset['jumlah'] ?></div>
                
                <div class="detail-label">Kondisi</div>
                <div class="detail-value">
                    <span class="badge badge-<?= $aset['kondisi'] === 'Baik' ? 'success' : ($aset['kondisi'] === 'Rusak Ringan' ? 'warning' : 'danger') ?>">
                        <?= $aset['kondisi'] ?>
                    </span>
                </div>
                
                <div class="detail-label">Tahun Perolehan</div>
                <div class="detail-value"><?= $aset['tahun_perolehan'] ?? '-' ?></div>
                
                <div class="detail-label">Nilai Perolehan</div>
                <div class="detail-value"><?= $aset['nilai_perolehan'] ? formatRupiah($aset['nilai_perolehan']) : '-' ?></div>
                
                <div class="detail-label">Sumber Dana</div>
                <div class="detail-value"><?= htmlspecialchars($aset['sumber_dana'] ?? '-') ?></div>
                
                <div class="detail-label">Keterangan</div>
                <div class="detail-value"><?= htmlspecialchars($aset['keterangan'] ?? '-') ?></div>
                
                <div class="detail-label">Ditambahkan</div>
                <div class="detail-value"><?= date('d/m/Y H:i', strtotime($aset['created_at'])) ?></div>
            </div>
        </div>
    </div>

    <!-- Gambar & QR Code -->
    <div>
        <?php if ($aset['gambar']): ?>
        <div class="card animate-fadeInUp mb-4">
            <div class="card-header"><h3><i class="fas fa-image"></i> Gambar Aset</h3></div>
            <div class="card-body text-center">
                <img src="<?= BASE_URL ?>/assets/uploads/<?= $aset['gambar'] ?>" alt="Gambar Aset" style="max-width:100%; border-radius:var(--radius-md);">
            </div>
        </div>
        <?php endif; ?>

        <?php if ($aset['qr_code_path']): ?>
        <div class="card animate-fadeInUp">
            <div class="card-header"><h3><i class="fas fa-qrcode"></i> QR Code</h3></div>
            <div class="card-body qr-container">
                <img src="<?= BASE_URL ?>/qrcodes/<?= $aset['qr_code_path'] ?>" alt="QR Code">
                <div class="qr-info">
                    <p><strong><?= htmlspecialchars($aset['kode_aset']) ?></strong></p>
                    <p><?= htmlspecialchars($aset['nama_aset']) ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Riwayat Peminjaman -->
<?php if (!empty($pinjamList)): ?>
<div class="card mt-4 animate-fadeInUp">
    <div class="card-header"><h3><i class="fas fa-handshake"></i> Riwayat Peminjaman</h3></div>
    <div class="card-body">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Peminjam</th><th>Tgl Pinjam</th><th>Batas Kembali</th><th>Tgl Kembali</th><th>Status</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($pinjamList as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nama_peminjam']) ?></td>
                        <td><?= date('d/m/Y', strtotime($p['tanggal_pinjam'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($p['tanggal_kembali_rencana'])) ?></td>
                        <td><?= $p['tanggal_kembali_aktual'] ? date('d/m/Y', strtotime($p['tanggal_kembali_aktual'])) : '-' ?></td>
                        <td><span class="badge badge-<?= $p['status'] === 'Dipinjam' ? 'warning' : 'success' ?>"><?= $p['status'] ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Riwayat Mutasi -->
<?php if (!empty($mutasiList)): ?>
<div class="card mt-4 animate-fadeInUp">
    <div class="card-header"><h3><i class="fas fa-right-left"></i> Riwayat Mutasi</h3></div>
    <div class="card-body">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr><th>Tanggal</th><th>Dari Lokasi</th><th>Ke Lokasi</th><th>Oleh</th><th>Keterangan</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($mutasiList as $m): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($m['tanggal_mutasi'])) ?></td>
                        <td><?= htmlspecialchars($m['lokasi_asal'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($m['lokasi_tujuan'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($m['nama'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($m['keterangan'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
