<?php
$pageTitle = 'Data Aset';
require_once __DIR__ . '/../../includes/auth_check.php';
$pdo = getConnection();

// Pagination
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 15;
$offset = ($page - 1) * $perPage;

// Search & Filter
$search = $_GET['search'] ?? '';
$filterKondisi = $_GET['kondisi'] ?? '';
$filterKategori = $_GET['kategori'] ?? '';

$where = "WHERE a.deleted_at IS NULL";
$params = [];

if ($search) {
    $where .= " AND (a.kode_aset LIKE ? OR a.nama_aset LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($filterKondisi) {
    $where .= " AND a.kondisi = ?";
    $params[] = $filterKondisi;
}
if ($filterKategori) {
    $where .= " AND a.id_kategori = ?";
    $params[] = $filterKategori;
}

// Count total
$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM aset a $where");
$stmtCount->execute($params);
$total = $stmtCount->fetchColumn();
$totalPages = ceil($total / $perPage);

// Get aset
$stmt = $pdo->prepare("SELECT a.*, k.nama_kategori, l.nama_lokasi 
    FROM aset a 
    LEFT JOIN kategori k ON a.id_kategori = k.id 
    LEFT JOIN lokasi l ON a.id_lokasi = l.id 
    $where 
    ORDER BY a.created_at DESC 
    LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$asetList = $stmt->fetchAll();

// Get kategori for filter
$kategoriList = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori")->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-boxes-stacked"></i> Data Aset</h2>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>/pages/dashboard.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Data Aset</span>
        </div>
    </div>
    <div class="btn-group">
        <a href="<?= BASE_URL ?>/pages/aset/tambah.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tambah Aset Baru
        </a>
        <a href="<?= BASE_URL ?>/pages/aset/tambah_rusak.php" class="btn btn-warning" style="color: #fff;">
            <i class="fas fa-plus-circle"></i> Tambah Aset Rusak
        </a>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="search-bar">
            <input type="text" class="search-input" name="search" placeholder="Cari kode atau nama aset..." value="<?= htmlspecialchars($search) ?>">
            <select class="form-control" name="kondisi" style="max-width:180px;">
                <option value="">Semua Kondisi</option>
                <option value="Baik" <?= $filterKondisi === 'Baik' ? 'selected' : '' ?>>Baik</option>
                <option value="Rusak Ringan" <?= $filterKondisi === 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
                <option value="Rusak Berat" <?= $filterKondisi === 'Rusak Berat' ? 'selected' : '' ?>>Rusak Berat</option>
            </select>
            <select class="form-control" name="kategori" style="max-width:200px;">
                <option value="">Semua Kategori</option>
                <?php foreach ($kategoriList as $kat): ?>
                    <option value="<?= $kat['id'] ?>" <?= $filterKategori == $kat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Cari</button>
            <a href="<?= BASE_URL ?>/aset" class="btn btn-secondary btn-sm"><i class="fas fa-rotate"></i> Reset</a>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card animate-fadeInUp">
    <div class="card-header">
        <h3>Daftar Aset (<?= $total ?> data)</h3>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Aset</th>
                        <th>Kategori</th>
                        <th>Lokasi</th>
                        <th>Jumlah</th>
                        <th>Kondisi</th>
                        <th>QR</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($asetList)): ?>
                        <tr><td colspan="9" class="text-center text-muted">Tidak ada data aset.</td></tr>
                    <?php else: ?>
                        <?php foreach ($asetList as $i => $a): ?>
                        <tr>
                            <td><?= $offset + $i + 1 ?></td>
                            <td><span class="badge badge-primary"><?= htmlspecialchars($a['kode_aset']) ?></span></td>
                            <td>
                                <a href="<?= BASE_URL ?>/aset/detail?id=<?= $a['id'] ?>" style="font-weight:500;">
                                    <?= htmlspecialchars($a['nama_aset']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($a['nama_kategori'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($a['nama_lokasi'] ?? '-') ?></td>
                            <td><?= $a['jumlah'] ?></td>
                            <td>
                                <span class="badge badge-<?= $a['kondisi'] === 'Baik' ? 'success' : ($a['kondisi'] === 'Rusak Ringan' ? 'warning' : 'danger') ?>">
                                    <?= $a['kondisi'] ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($a['qr_code_path']): ?>
                                    <a href="<?= BASE_URL ?>/aset/generate-qr?id=<?= $a['id'] ?>" class="btn btn-sm btn-info" title="Lihat QR">
                                        <i class="fas fa-qrcode"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="<?= BASE_URL ?>/aset/generate-qr?id=<?= $a['id'] ?>" class="btn btn-sm btn-secondary" title="Generate QR">
                                        <i class="fas fa-qrcode"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= BASE_URL ?>/aset/detail?id=<?= $a['id'] ?>" class="btn btn-sm btn-info" title="Detail"><i class="fas fa-eye"></i></a>
                                    <a href="<?= BASE_URL ?>/aset/edit?id=<?= $a['id'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    <a href="<?= BASE_URL ?>/aset/hapus?id=<?= $a['id'] ?>" class="btn btn-sm btn-danger" title="Hapus Aset"><i class="fas fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&kondisi=<?= urlencode($filterKondisi) ?>&kategori=<?= urlencode($filterKategori) ?>">&laquo;</a>
            <?php endif; ?>
            <?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
                <?php if ($p == $page): ?>
                    <span class="active"><?= $p ?></span>
                <?php else: ?>
                    <a href="?page=<?= $p ?>&search=<?= urlencode($search) ?>&kondisi=<?= urlencode($filterKondisi) ?>&kategori=<?= urlencode($filterKategori) ?>"><?= $p ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&kondisi=<?= urlencode($filterKondisi) ?>&kategori=<?= urlencode($filterKategori) ?>">&raquo;</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
