<?php
$pageTitle = 'Data Lokasi';
require_once __DIR__ . '/../../includes/auth_check.php';
requireStaff();
$pdo = getConnection();
$lokasiList = $pdo->query("SELECT l.*, (SELECT COUNT(*) FROM aset WHERE id_lokasi = l.id AND deleted_at IS NULL) as total_aset FROM lokasi l ORDER BY l.nama_lokasi")->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header">
    <div><h2><i class="fas fa-location-dot"></i> Data Lokasi / Ruangan</h2>
        <div class="breadcrumb"><a href="<?= BASE_URL ?>/dashboard">Dashboard</a><span class="separator">/</span><span>Lokasi</span></div>
    </div>
    <a href="<?= BASE_URL ?>/lokasi/tambah" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Lokasi</a>
</div>
<div class="card animate-fadeInUp"><div class="card-header"><h3>Daftar Lokasi</h3></div><div class="card-body"><div class="table-wrapper">
    <table><thead><tr><th>No</th><th>Nama Lokasi</th><th>Keterangan</th><th>Total Aset</th><th>Aksi</th></tr></thead>
    <tbody>
        <?php if (empty($lokasiList)): ?>
            <tr><td colspan="5" class="text-center text-muted">Belum ada data lokasi</td></tr>
        <?php else: foreach ($lokasiList as $i => $l): ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td style="font-weight:500;"><?= htmlspecialchars($l['nama_lokasi']) ?></td>
            <td><?= htmlspecialchars($l['keterangan'] ?? '-') ?></td>
            <td><span class="badge badge-info"><?= $l['total_aset'] ?></span></td>
            <td><div class="btn-group">
                <a href="<?= BASE_URL ?>/lokasi/edit?id=<?= $l['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                <a href="javascript:void(0)" onclick="confirmDelete('Hapus lokasi <?= htmlspecialchars($l['nama_lokasi'], ENT_QUOTES) ?>?', '<?= BASE_URL ?>/pages/lokasi/hapus.php', '<?= $l['id'] ?>')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
            </div></td>
        </tr>
        <?php endforeach; endif; ?>
    </tbody></table>
</div></div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
