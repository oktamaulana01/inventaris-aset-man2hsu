<?php
$pageTitle = 'Peminjaman Aset';
require_once __DIR__ . '/../../includes/auth_check.php';
$pdo = getConnection();

$filterStatus = $_GET['status'] ?? '';
$where = "";
$params = [];
if ($filterStatus) { $where = "WHERE p.status = ?"; $params[] = $filterStatus; }

$peminjamanList = $pdo->prepare("SELECT p.*, a.nama_aset, a.kode_aset, u.nama as user_nama, pu.nama as peminjam_nama, l.nama_lokasi 
    FROM peminjaman p 
    JOIN aset a ON p.id_aset = a.id 
    LEFT JOIN users u ON p.id_user = u.id 
    LEFT JOIN users pu ON p.id_peminjam = pu.id
    LEFT JOIN lokasi l ON p.id_lokasi = l.id
    $where
    ORDER BY p.created_at DESC");
$peminjamanList->execute($params);
$peminjamanList = $peminjamanList->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="page-header">
    <div><h2><i class="fas fa-handshake"></i> Peminjaman Aset</h2>
        <div class="breadcrumb"><a href="<?= BASE_URL ?>/pages/dashboard.php">Dashboard</a><span class="separator">/</span><span>Peminjaman</span></div>
    </div>
    <a href="<?= BASE_URL ?>/peminjaman/tambah" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Peminjaman</a>
</div>

<!-- Filter -->
<div class="card mb-4"><div class="card-body">
    <form method="GET" class="search-bar">
        <select class="form-control" name="status" style="max-width:200px;">
            <option value="">Semua Status</option>
            <option value="Menunggu Konfirmasi" <?= $filterStatus === 'Menunggu Konfirmasi' ? 'selected' : '' ?>>Menunggu Konfirmasi</option>
            <option value="Dipinjam" <?= $filterStatus === 'Dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
            <option value="Dikembalikan" <?= $filterStatus === 'Dikembalikan' ? 'selected' : '' ?>>Dikembalikan</option>
            <option value="Ditolak" <?= $filterStatus === 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
        <a href="<?= BASE_URL ?>/peminjaman" class="btn btn-secondary btn-sm">Reset</a>
    </form>
</div></div>

<div class="card animate-fadeInUp"><div class="card-header"><h3>Daftar Peminjaman</h3></div><div class="card-body">
    <div class="table-wrapper">
        <table><thead><tr><th>No</th><th>Kode Aset</th><th>Nama Aset</th><th>Peminjam</th><th>Tgl Pinjam</th><th>Batas Kembali</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php if (empty($peminjamanList)): ?>
                <tr><td colspan="8" class="text-center text-muted">Belum ada data peminjaman</td></tr>
            <?php else: foreach ($peminjamanList as $i => $p): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><span class="badge badge-primary"><?= htmlspecialchars($p['kode_aset']) ?></span></td>
                <td><?= htmlspecialchars($p['nama_aset']) ?></td>
                <td>
                    <?php if ($p['peminjam_nama']): ?>
                        <?= htmlspecialchars($p['peminjam_nama']) ?>
                        <span class="badge badge-success" style="font-size:0.68rem;margin-left:4px;">Guru</span>
                    <?php else: ?>
                        <?= htmlspecialchars($p['nama_peminjam']) ?>
                    <?php endif; ?>
                    <div style="font-size:0.8rem; color:var(--text-secondary); margin-top:4px;">
                        <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($p['nama_lokasi'] ?? 'Tidak ditentukan') ?>
                    </div>
                </td>
                <td><?= date('d/m/Y', strtotime($p['tanggal_pinjam'])) ?></td>
                <td><?= date('d/m/Y', strtotime($p['tanggal_kembali_rencana'])) ?></td>
                <td>
                    <?php
                    $badgeClass = 'success';
                    if ($p['status'] === 'Dipinjam') $badgeClass = 'warning';
                    elseif ($p['status'] === 'Menunggu Konfirmasi') $badgeClass = 'info';
                    elseif ($p['status'] === 'Ditolak') $badgeClass = 'danger';
                    ?>
                    <span class="badge badge-<?= $badgeClass ?>"><?= $p['status'] ?></span>
                </td>
                <td><div class="btn-group">
                    <?php if ($p['status'] === 'Menunggu Konfirmasi'): ?>
                        <a href="javascript:void(0)" onclick="confirmAction('Setujui permintaan peminjaman ini?', '<?= BASE_URL ?>/peminjaman/proses-konfirmasi?action=approve', '<?= $p['id'] ?>')" class="btn btn-sm btn-success" title="Setujui"><i class="fas fa-check"></i></a>
                        <a href="javascript:void(0)" onclick="confirmAction('Tolak permintaan peminjaman ini?', '<?= BASE_URL ?>/peminjaman/proses-konfirmasi?action=reject', '<?= $p['id'] ?>')" class="btn btn-sm btn-danger" title="Tolak"><i class="fas fa-times"></i></a>
                    <?php endif; ?>
                    <?php if ($p['status'] === 'Dipinjam'): ?>
                        <a href="javascript:void(0)" onclick="confirmAction('Kirim email reminder ke peminjam?', '<?= BASE_URL ?>/peminjaman/kirim-notif', '<?= $p['id'] ?>')" class="btn btn-sm btn-info" title="Kirim Reminder Email"><i class="fas fa-envelope"></i></a>
                        <a href="javascript:void(0)" onclick="confirmAction('Kembalikan aset ini?', '<?= BASE_URL ?>/peminjaman/kembali', '<?= $p['id'] ?>')" class="btn btn-sm btn-success" title="Kembalikan"><i class="fas fa-rotate-left"></i></a>
                    <?php endif; ?>
                    <a href="javascript:void(0)" onclick="confirmDelete('Hapus data peminjaman ini?', '<?= BASE_URL ?>/peminjaman/hapus', '<?= $p['id'] ?>')" class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></a>
                </div></td>
            </tr>
            <?php endforeach; endif; ?>
        </tbody></table>
    </div>
</div></div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
