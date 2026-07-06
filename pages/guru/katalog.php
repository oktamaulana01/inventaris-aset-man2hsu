<?php
$pageTitle = 'Katalog Aset';
require_once __DIR__ . '/../../includes/auth_check.php';
requireGuru();
$pdo = getConnection();

// Filter
$filterKategori = $_GET['kategori'] ?? '';
$search = $_GET['search'] ?? '';

$where = "WHERE a.deleted_at IS NULL";
$params = [];

if ($filterKategori) {
    $where .= " AND a.id_kategori = ?";
    $params[] = $filterKategori;
}
if ($search) {
    $where .= " AND (a.nama_aset LIKE ? OR a.kode_aset LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$stmt = $pdo->prepare("SELECT a.*, k.nama_kategori, l.nama_lokasi FROM aset a 
    LEFT JOIN kategori k ON a.id_kategori = k.id 
    LEFT JOIN lokasi l ON a.id_lokasi = l.id 
    $where
    ORDER BY a.nama_aset ASC");
$stmt->execute($params);
$asetList = $stmt->fetchAll();

$kategoriList = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori")->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-boxes-stacked"></i> Katalog Aset</h2>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>/pages/guru/dashboard.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Katalog Aset</span>
        </div>
    </div>
</div>

<!-- Filter & Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="search-bar">
            <input type="text" name="search" class="search-input" placeholder="Cari nama atau kode aset..." value="<?= htmlspecialchars($search) ?>">
            <select class="form-control" name="kategori" style="max-width:200px;">
                <option value="">Semua Kategori</option>
                <?php foreach ($kategoriList as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $filterKategori == $k['id'] ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kategori']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Cari</button>
            <a href="<?= BASE_URL ?>/guru/katalog" class="btn btn-secondary btn-sm">Reset</a>
        </form>
    </div>
</div>

<!-- Aset Grid Cards -->
<div class="katalog-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(320px, 1fr)); gap:20px;">
    <?php if (empty($asetList)): ?>
        <div class="card animate-fadeInUp" style="grid-column:1/-1;">
            <div class="card-body">
                <div class="empty-state">
                    <div class="empty-icon"><i class="fas fa-search"></i></div>
                    <p>Tidak ada aset ditemukan</p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($asetList as $a): ?>
        <div class="card animate-fadeInUp" style="transition:transform 0.2s ease;">
            <div class="card-body" style="padding:20px;">
                <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom:12px;">
                    <span class="badge badge-primary" style="font-size:0.78rem;"><?= htmlspecialchars($a['kode_aset']) ?></span>
                    <span class="badge badge-<?= $a['kondisi'] === 'Baik' ? 'success' : ($a['kondisi'] === 'Rusak Ringan' ? 'warning' : 'danger') ?>">
                        <?= $a['kondisi'] ?>
                    </span>
                </div>
                
                <h4 style="font-size:1.05rem; font-weight:600; margin-bottom:8px; color:var(--text-primary);">
                    <?= htmlspecialchars($a['nama_aset']) ?>
                </h4>
                
                <div style="font-size:0.82rem; color:var(--text-muted); margin-bottom:16px;">
                    <div style="display:flex; gap:16px; flex-wrap:wrap; margin-bottom:6px;">
                        <span><i class="fas fa-tags" style="width:16px;"></i> <?= htmlspecialchars($a['nama_kategori'] ?? '-') ?></span>
                        <span><i class="fas fa-location-dot" style="width:16px;"></i> <?= htmlspecialchars($a['nama_lokasi'] ?? '-') ?></span>
                    </div>
                    <div style="display:flex; gap:16px; flex-wrap:wrap;">
                        <span><i class="fas fa-layer-group" style="width:16px;"></i> Jumlah: <?= $a['jumlah'] ?></span>
                        <?php if ($a['tahun_perolehan']): ?>
                            <span><i class="fas fa-calendar" style="width:16px;"></i> Tahun: <?= $a['tahun_perolehan'] ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($a['keterangan']): ?>
                    <p style="font-size:0.82rem; color:var(--text-secondary); margin-bottom:16px; line-height:1.5;">
                        <?= htmlspecialchars(mb_strimwidth($a['keterangan'], 0, 80, '...')) ?>
                    </p>
                <?php endif; ?>
                
                <?php if ($a['kondisi'] === 'Baik'): ?>
                    <a href="<?= BASE_URL ?>/guru/pinjam?id_aset=<?= $a['id'] ?>" class="btn btn-primary btn-sm" style="width:100%;">
                        <i class="fas fa-hand-holding-hand"></i> Ajukan Peminjaman
                    </a>
                <?php else: ?>
                    <button class="btn btn-secondary btn-sm" style="width:100%; opacity:0.6; cursor:not-allowed;" disabled>
                        <i class="fas fa-ban"></i> Tidak Tersedia
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
