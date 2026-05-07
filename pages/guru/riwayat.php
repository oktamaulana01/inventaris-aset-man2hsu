<?php
$pageTitle = 'Riwayat Peminjaman';
require_once __DIR__ . '/../../includes/auth_check.php';
requireGuru();
$pdo = getConnection();

$userId = $_SESSION['user_id'];
$filterStatus = $_GET['status'] ?? '';

$where = "WHERE p.id_peminjam = ?";
$params = [$userId];

if ($filterStatus) {
    $where .= " AND p.status = ?";
    $params[] = $filterStatus;
}

$stmt = $pdo->prepare("SELECT p.*, a.nama_aset, a.kode_aset FROM peminjaman p 
    JOIN aset a ON p.id_aset = a.id 
    $where
    ORDER BY p.created_at DESC");
$stmt->execute($params);
$peminjamanList = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-clock-rotate-left"></i> Riwayat Peminjaman Saya</h2>
        <div class="breadcrumb">
            <a href="/inventaris-aset-man2hsu/pages/guru/dashboard.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Riwayat Peminjaman</span>
        </div>
    </div>
    <a href="pinjam.php" class="btn btn-primary"><i class="fas fa-plus"></i> Ajukan Peminjaman</a>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="search-bar">
            <select class="form-control" name="status" style="max-width:200px;">
                <option value="">Semua Status</option>
                <option value="Dipinjam" <?= $filterStatus === 'Dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
                <option value="Dikembalikan" <?= $filterStatus === 'Dikembalikan' ? 'selected' : '' ?>>Dikembalikan</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
            <a href="riwayat.php" class="btn btn-secondary btn-sm">Reset</a>
        </form>
    </div>
</div>

<div class="card animate-fadeInUp">
    <div class="card-header">
        <h3>Daftar Peminjaman</h3>
        <span class="badge badge-info"><?= count($peminjamanList) ?> data</span>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Aset</th>
                        <th>Nama Aset</th>
                        <th>Tgl Pinjam</th>
                        <th>Batas Kembali</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($peminjamanList)): ?>
                        <tr><td colspan="8" class="text-center text-muted">Belum ada data peminjaman</td></tr>
                    <?php else: ?>
                        <?php foreach ($peminjamanList as $i => $p): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><span class="badge badge-primary"><?= htmlspecialchars($p['kode_aset']) ?></span></td>
                            <td style="font-weight:500;"><?= htmlspecialchars($p['nama_aset']) ?></td>
                            <td><?= date('d/m/Y', strtotime($p['tanggal_pinjam'])) ?></td>
                            <td>
                                <?php
                                $batas = strtotime($p['tanggal_kembali_rencana']);
                                $isOverdue = $p['status'] === 'Dipinjam' && time() > $batas;
                                ?>
                                <span style="<?= $isOverdue ? 'color:var(--danger);font-weight:600;' : '' ?>">
                                    <?= date('d/m/Y', $batas) ?>
                                    <?php if ($isOverdue): ?>
                                        <i class="fas fa-exclamation-triangle" title="Terlambat!"></i>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td><?= $p['tanggal_kembali_aktual'] ? date('d/m/Y', strtotime($p['tanggal_kembali_aktual'])) : '-' ?></td>
                            <td>
                                <span class="badge badge-<?= $p['status'] === 'Dipinjam' ? 'warning' : 'success' ?>">
                                    <?= $p['status'] ?>
                                </span>
                            </td>
                            <td style="font-size:0.82rem; color:var(--text-muted);"><?= htmlspecialchars($p['keterangan'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
