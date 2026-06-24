<?php
$pageTitle = 'Data Kategori';
require_once __DIR__ . '/../../includes/auth_check.php';
$pdo = getConnection();

$kategoriList = $pdo->query("SELECT k.*, (SELECT COUNT(*) FROM aset WHERE id_kategori = k.id AND deleted_at IS NULL) as total_aset FROM kategori k ORDER BY k.nama_kategori")->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-tags"></i> Data Kategori</h2>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>/pages/dashboard.php">Dashboard</a><span class="separator">/</span><span>Kategori</span>
        </div>
    </div>
    <a href="tambah.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Kategori</a>
</div>

<div class="card animate-fadeInUp">
    <div class="card-header"><h3>Daftar Kategori</h3></div>
    <div class="card-body">
        <div class="table-wrapper">
            <table>
                <thead><tr><th>No</th><th>Nama Kategori</th><th>Keterangan</th><th>Total Aset</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php if (empty($kategoriList)): ?>
                        <tr><td colspan="5" class="text-center text-muted">Belum ada data kategori</td></tr>
                    <?php else: foreach ($kategoriList as $i => $k): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td style="font-weight:500;"><?= htmlspecialchars($k['nama_kategori']) ?></td>
                        <td><?= htmlspecialchars($k['keterangan'] ?? '-') ?></td>
                        <td><span class="badge badge-info"><?= $k['total_aset'] ?></span></td>
                        <td>
                            <div class="btn-group">
                                <a href="edit.php?id=<?= $k['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <a href="javascript:void(0)" onclick="confirmDelete('Hapus kategori <?= htmlspecialchars($k['nama_kategori']) ?>?', 'hapus.php?id=<?= $k['id'] ?>')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
