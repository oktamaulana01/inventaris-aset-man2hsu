<?php
$pageTitle = 'Laporan Peminjaman';
require_once __DIR__ . '/../../includes/auth_check.php';
$pdo = getConnection();

$filterStatus = $_GET['status'] ?? '';
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';
$where = "WHERE 1=1";
$params = [];
if ($filterStatus) { $where .= " AND p.status = ?"; $params[] = $filterStatus; }
if ($startDate) { $where .= " AND p.tanggal_pinjam >= ?"; $params[] = $startDate; }
if ($endDate) { $where .= " AND p.tanggal_pinjam <= ?"; $params[] = $endDate; }

$stmt = $pdo->prepare("SELECT p.*, a.nama_aset, a.kode_aset FROM peminjaman p 
    JOIN aset a ON p.id_aset = a.id $where ORDER BY p.tanggal_pinjam DESC");
$stmt->execute($params);
$data = $stmt->fetchAll();

$totalDipinjam = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status='Dipinjam'")->fetchColumn();
$totalDikembalikan = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status='Dikembalikan'")->fetchColumn();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<?php require_once __DIR__ . '/../../includes/kop_surat.php'; ?>
<div class="page-header">
    <div><h2><i class="fas fa-hand-holding"></i> Laporan Peminjaman</h2></div>
    <div class="btn-group no-print">
        <a href="export_pdf.php?type=peminjaman&status=<?= urlencode($filterStatus) ?>&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>" class="btn btn-danger"><i class="fas fa-file-pdf"></i> Cetak PDF</a>
        <a href="export_pdf.php?type=peminjaman&status=<?= urlencode($filterStatus) ?>&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>&view=1" target="_blank" class="btn btn-info"><i class="fas fa-eye"></i> Lihat PDF</a>
        <a href="export_excel.php?type=peminjaman&status=<?= urlencode($filterStatus) ?>&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>" class="btn btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
    </div>
</div>
<div class="stats-grid mb-4">
    <div class="stat-card amber"><div class="stat-icon amber"><i class="fas fa-hand-holding"></i></div><div class="stat-info"><h3><?= $totalDipinjam ?></h3><p>Sedang Dipinjam</p></div></div>
    <div class="stat-card teal"><div class="stat-icon green"><i class="fas fa-check-circle"></i></div><div class="stat-info"><h3><?= $totalDikembalikan ?></h3><p>Sudah Dikembalikan</p></div></div>
</div>

<div class="card mb-4 no-print"><div class="card-body">
    <form method="GET" class="search-bar">
        <select class="form-control" name="status" style="max-width:180px;">
            <option value="">Semua Status</option>
            <option value="Dipinjam" <?= $filterStatus === 'Dipinjam' ? 'selected' : '' ?>>Dipinjam</option>
            <option value="Dikembalikan" <?= $filterStatus === 'Dikembalikan' ? 'selected' : '' ?>>Dikembalikan</option>
        </select>
        <input type="date" class="form-control" name="start_date" value="<?= $startDate ?>" style="max-width:170px;" placeholder="Dari tanggal">
        <input type="date" class="form-control" name="end_date" value="<?= $endDate ?>" style="max-width:170px;" placeholder="Sampai tanggal">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
        <a href="<?= BASE_URL ?>/laporan/peminjaman" class="btn btn-secondary btn-sm">Reset</a>
    </form>
</div></div>

<div class="card animate-fadeInUp"><div class="card-header"><h3>Data Peminjaman (<?= count($data) ?> record)</h3></div><div class="card-body"><div class="table-wrapper">
    <table><thead><tr><th>No</th><th>Kode</th><th>Nama Aset</th><th>Peminjam</th><th>Tgl Pinjam</th><th>Batas</th><th>Tgl Kembali</th><th>Status</th></tr></thead>
    <tbody>
        <?php foreach ($data as $i => $p): ?>
        <tr>
            <td><?= $i+1 ?></td><td><?= htmlspecialchars($p['kode_aset']) ?></td><td><?= htmlspecialchars($p['nama_aset']) ?></td>
            <td><?= htmlspecialchars($p['nama_peminjam']) ?></td>
            <td><?= date('d/m/Y', strtotime($p['tanggal_pinjam'])) ?></td>
            <td><?= date('d/m/Y', strtotime($p['tanggal_kembali_rencana'])) ?></td>
            <td><?= $p['tanggal_kembali_aktual'] ? date('d/m/Y', strtotime($p['tanggal_kembali_aktual'])) : '-' ?></td>
            <td><span class="badge badge-<?= $p['status'] === 'Dipinjam' ? 'warning' : 'success' ?>"><?= $p['status'] ?></span></td>
        </tr>
        <?php endforeach; ?>
    </tbody></table>
</div></div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
