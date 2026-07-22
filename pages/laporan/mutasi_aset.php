<?php
$pageTitle = 'Laporan Mutasi Aset';
require_once __DIR__ . '/../../includes/auth_check.php';
requireStaff();
$pdo = getConnection();

$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';
$where = "WHERE 1=1";
$params = [];
if ($startDate) { $where .= " AND m.tanggal_mutasi >= ?"; $params[] = $startDate; }
if ($endDate) { $where .= " AND m.tanggal_mutasi <= ?"; $params[] = $endDate; }

$stmt = $pdo->prepare("SELECT m.*, a.kode_aset, a.nama_aset, la.nama_lokasi as lokasi_asal, lt.nama_lokasi as lokasi_tujuan, u.nama as oleh
    FROM mutasi_aset m
    JOIN aset a ON m.id_aset = a.id
    LEFT JOIN lokasi la ON m.id_lokasi_asal = la.id
    LEFT JOIN lokasi lt ON m.id_lokasi_tujuan = lt.id
    LEFT JOIN users u ON m.id_user = u.id
    $where ORDER BY m.tanggal_mutasi DESC");
$stmt->execute($params);
$data = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<?php require_once __DIR__ . '/../../includes/kop_surat.php'; ?>
<div class="page-header">
    <div><h2><i class="fas fa-right-left"></i> Laporan Mutasi Aset</h2></div>
    <div class="btn-group no-print">
        <a href="export_pdf.php?type=mutasi&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>" class="btn btn-danger"><i class="fas fa-file-pdf"></i> Cetak PDF</a>
        <a href="export_pdf.php?type=mutasi&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>&view=1" target="_blank" class="btn btn-info"><i class="fas fa-eye"></i> Lihat PDF</a>
        <a href="export_excel.php?type=mutasi&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>" class="btn btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
    </div>
</div>
<div class="card mb-4 no-print"><div class="card-body">
    <form method="GET" class="search-bar">
        <input type="date" class="form-control" name="start_date" value="<?= $startDate ?>" style="max-width:170px;">
        <input type="date" class="form-control" name="end_date" value="<?= $endDate ?>" style="max-width:170px;">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
        <a href="<?= BASE_URL ?>/laporan/mutasi" class="btn btn-secondary btn-sm">Reset</a>
    </form>
</div></div>
<div class="card animate-fadeInUp"><div class="card-header"><h3>Data Mutasi (<?= count($data) ?> record)</h3></div><div class="card-body"><div class="table-wrapper">
    <table><thead><tr><th>No</th><th>Tanggal</th><th>Kode Aset</th><th>Nama Aset</th><th>Dari Lokasi</th><th>Ke Lokasi</th><th>Oleh</th><th>Keterangan</th></tr></thead>
    <tbody>
        <?php if (empty($data)): ?>
            <tr><td colspan="8" class="text-center text-muted">Belum ada data mutasi</td></tr>
        <?php else: foreach ($data as $i => $m): ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td><?= date('d/m/Y', strtotime($m['tanggal_mutasi'])) ?></td>
            <td><?= htmlspecialchars($m['kode_aset']) ?></td><td><?= htmlspecialchars($m['nama_aset']) ?></td>
            <td><?= htmlspecialchars($m['lokasi_asal'] ?? '-') ?></td><td><?= htmlspecialchars($m['lokasi_tujuan'] ?? '-') ?></td>
            <td><?= htmlspecialchars($m['oleh'] ?? '-') ?></td><td><?= htmlspecialchars($m['keterangan'] ?? '-') ?></td>
        </tr>
        <?php endforeach; endif; ?>
    </tbody></table>
</div></div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
