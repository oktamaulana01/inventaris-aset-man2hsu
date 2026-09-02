<?php
$pageTitle = 'Mutasi Aset';
require_once __DIR__ . '/../../includes/auth_check.php';
requireStaff();
$pdo = getConnection();

// Filter & Search
$q = trim($_GET['q'] ?? '');
$asal = intval($_GET['asal'] ?? 0);
$tujuan = intval($_GET['tujuan'] ?? 0);

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

$where = "WHERE 1=1";
$params = [];

if ($q !== '') {
    $where .= " AND (a.nama_aset LIKE ? OR a.kode_aset LIKE ?)";
    $params[] = "%$q%";
    $params[] = "%$q%";
}
if ($asal > 0) {
    $where .= " AND m.id_lokasi_asal = ?";
    $params[] = $asal;
}
if ($tujuan > 0) {
    $where .= " AND m.id_lokasi_tujuan = ?";
    $params[] = $tujuan;
}

// Total Count
$countStmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM mutasi_aset m
    JOIN aset a ON m.id_aset = a.id
    $where
");
$countStmt->execute($params);
$totalData = $countStmt->fetchColumn();
$totalPages = ceil($totalData / $perPage);

// Fetch Data
$sql = "
    SELECT m.*, a.kode_aset, a.nama_aset, 
           la.nama_lokasi as lokasi_asal, 
           lt.nama_lokasi as lokasi_tujuan, 
           u.nama as nama_petugas
    FROM mutasi_aset m
    JOIN aset a ON m.id_aset = a.id
    LEFT JOIN lokasi la ON m.id_lokasi_asal = la.id
    LEFT JOIN lokasi lt ON m.id_lokasi_tujuan = lt.id
    LEFT JOIN users u ON m.id_user = u.id
    $where
    ORDER BY m.tanggal_mutasi DESC, m.id DESC
    LIMIT $perPage OFFSET $offset
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$mutasiList = $stmt->fetchAll();

// Stats
$statTotal = $pdo->query("SELECT COUNT(*) FROM mutasi_aset")->fetchColumn();
$statMonth = $pdo->query("SELECT COUNT(*) FROM mutasi_aset WHERE MONTH(tanggal_mutasi) = MONTH(CURDATE()) AND YEAR(tanggal_mutasi) = YEAR(CURDATE())")->fetchColumn();
$statAsetCount = $pdo->query("SELECT COUNT(DISTINCT id_aset) FROM mutasi_aset")->fetchColumn();

// Load lokasi for filter
$lokasiList = $pdo->query("SELECT id, nama_lokasi FROM lokasi ORDER BY nama_lokasi ASC")->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-arrows-split-up-and-left"></i> Mutasi Aset</h2>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>/dashboard">Dashboard</a>
            <span class="separator">/</span>
            <span>Mutasi Aset</span>
        </div>
    </div>
    <a href="<?= BASE_URL ?>/mutasi/tambah" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Mutasi
    </a>
</div>

<!-- Stats -->
<div class="stats-grid" style="grid-template-columns: repeat(3, 1fr);">
    <div class="stat-card animate-fadeInUp">
        <div class="stat-icon green"><i class="fas fa-right-left"></i></div>
        <div class="stat-info">
            <h3><?= $statTotal ?></h3>
            <p>Total Transaksi Mutasi</p>
        </div>
    </div>
    <div class="stat-card animate-fadeInUp" style="animation-delay: 0.05s;">
        <div class="stat-icon blue"><i class="fas fa-calendar-check"></i></div>
        <div class="stat-info">
            <h3><?= $statMonth ?></h3>
            <p>Mutasi Bulan Ini</p>
        </div>
    </div>
    <div class="stat-card animate-fadeInUp" style="animation-delay: 0.1s;">
        <div class="stat-icon amber"><i class="fas fa-boxes-stacked"></i></div>
        <div class="stat-info">
            <h3><?= $statAsetCount ?></h3>
            <p>Aset Pernah Dimutasi</p>
        </div>
    </div>
</div>

<!-- Search & Filter -->
<div class="card mb-4 no-print">
    <div class="card-body">
        <form method="GET" class="search-bar" style="display:flex; gap:12px; flex-wrap:wrap;">
            <input type="text" class="form-control" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Cari nama / kode aset..." style="max-width:230px;">
            
            <select class="form-control" name="asal" style="max-width:190px;">
                <option value="">Semua Lokasi Asal</option>
                <?php foreach ($lokasiList as $l): ?>
                    <option value="<?= $l['id'] ?>" <?= $asal == $l['id'] ? 'selected' : '' ?>><?= htmlspecialchars($l['nama_lokasi']) ?></option>
                <?php endforeach; ?>
            </select>

            <select class="form-control" name="tujuan" style="max-width:190px;">
                <option value="">Semua Lokasi Tujuan</option>
                <?php foreach ($lokasiList as $l): ?>
                    <option value="<?= $l['id'] ?>" <?= $tujuan == $l['id'] ? 'selected' : '' ?>><?= htmlspecialchars($l['nama_lokasi']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
            <a href="<?= BASE_URL ?>/mutasi" class="btn btn-secondary btn-sm">Reset</a>
        </form>
    </div>
</div>

<!-- Table Data -->
<div class="card animate-fadeInUp">
    <div class="card-header">
        <h3><i class="fas fa-list" style="color:var(--accent-primary);margin-right:8px;"></i> Riwayat Mutasi Aset (<?= $totalData ?> Total)</h3>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Kode Aset</th>
                        <th>Nama Aset</th>
                        <th>Lokasi Asal</th>
                        <th>Lokasi Tujuan</th>
                        <th>Status Mutasi</th>
                        <th>Petugas Pengirim</th>
                        <th style="text-align:center;">Aksi & BAST</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($mutasiList)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted" style="padding: 24px;">Belum ada data transaksi mutasi aset</td>
                        </tr>
                    <?php else: foreach ($mutasiList as $i => $m): ?>
                        <tr>
                            <td><?= $offset + $i + 1 ?></td>
                            <td style="white-space:nowrap;"><?= date('d/m/Y', strtotime($m['tanggal_mutasi'])) ?></td>
                            <td>
                                <span class="badge badge-primary" style="font-size:0.75rem;"><?= htmlspecialchars($m['kode_aset']) ?></span>
                            </td>
                            <td style="font-weight:600;"><?= htmlspecialchars($m['nama_aset']) ?></td>
                            <td><?= htmlspecialchars($m['lokasi_asal'] ?? '-') ?></td>
                            <td>
                                <strong style="color:var(--accent-primary);"><?= htmlspecialchars($m['lokasi_tujuan'] ?? '-') ?></strong>
                            </td>
                            <td>
                                <?php if ($m['status'] === 'pending'): ?>
                                    <span class="badge badge-warning" style="font-size:0.75rem;"><i class="fas fa-truck-fast"></i> In Transit</span>
                                <?php elseif ($m['status'] === 'completed'): ?>
                                    <span class="badge badge-success" style="font-size:0.75rem;"><i class="fas fa-check"></i> Selesai</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary" style="font-size:0.75rem;">Dibatalkan</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-size:0.85rem;"><?= htmlspecialchars($m['nama_petugas'] ?? 'Sistem') ?></td>
                            <td style="text-align:center;">
                                <div class="btn-group" style="justify-content:center;">
                                    <?php if ($m['status'] === 'pending'): ?>
                                        <a href="<?= BASE_URL ?>/mutasi/konfirmasi-terima?id=<?= $m['id'] ?>" class="btn btn-sm btn-success" title="Konfirmasi Terima Barang (Upload BAST)">
                                            <i class="fas fa-clipboard-check"></i> Terima
                                        </a>
                                        <a href="<?= BASE_URL ?>/berita-acara/mutasi?id=<?= $m['id'] ?>" target="_blank" class="btn btn-sm btn-primary" title="Cetak Draft BAST">
                                            <i class="fas fa-print"></i> Cetak BAST
                                        </a>
                                    <?php elseif ($m['status'] === 'completed'): ?>
                                        <?php if ($m['file_bast_scan']): ?>
                                            <a href="<?= BASE_URL ?>/assets/uploads/bast_mutasi/<?= htmlspecialchars($m['file_bast_scan']) ?>" target="_blank" class="btn btn-sm btn-success" title="Buka BAST Asli Bertanda Tangan">
                                                <i class="fas fa-file-signature"></i> BAST (Bertanda Tangan)
                                            </a>
                                            <a href="<?= BASE_URL ?>/berita-acara/mutasi?id=<?= $m['id'] ?>" target="_blank" class="btn btn-sm btn-secondary" title="Cetak Ulang Dokumen Digital BAST">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= BASE_URL ?>/berita-acara/mutasi?id=<?= $m['id'] ?>" target="_blank" class="btn btn-sm btn-primary" title="Cetak BAST Resmi">
                                                <i class="fas fa-print"></i> BAST
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size:0.8rem;">Dibatalkan</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="pagination mt-4">
            <?php if ($page > 1): ?><a href="?page=<?= $page-1 ?>&q=<?= urlencode($q) ?>&asal=<?= $asal ?>&tujuan=<?= $tujuan ?>">&laquo;</a><?php endif; ?>
            <?php for ($p = max(1,$page-2); $p <= min($totalPages,$page+2); $p++): ?>
                <?php if ($p == $page): ?><span class="active"><?= $p ?></span>
                <?php else: ?><a href="?page=<?= $p ?>&q=<?= urlencode($q) ?>&asal=<?= $asal ?>&tujuan=<?= $tujuan ?>"><?= $p ?></a><?php endif; ?>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?><a href="?page=<?= $page+1 ?>&q=<?= urlencode($q) ?>&asal=<?= $asal ?>&tujuan=<?= $tujuan ?>">&raquo;</a><?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
