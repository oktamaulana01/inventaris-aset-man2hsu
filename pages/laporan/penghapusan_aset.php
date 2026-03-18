<?php
$pageTitle = 'Laporan Penghapusan Aset';
require_once __DIR__ . '/../../includes/auth_check.php';
$pdo = getConnection();

$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';
$where = "WHERE a.deleted_at IS NOT NULL";
$params = [];
if ($startDate) { $where .= " AND DATE(a.deleted_at) >= ?"; $params[] = $startDate; }
if ($endDate) { $where .= " AND DATE(a.deleted_at) <= ?"; $params[] = $endDate; }

$stmt = $pdo->prepare("SELECT a.*, k.nama_kategori, l.nama_lokasi FROM aset a 
    LEFT JOIN kategori k ON a.id_kategori = k.id LEFT JOIN lokasi l ON a.id_lokasi = l.id 
    $where ORDER BY a.deleted_at DESC");
$stmt->execute($params);
$data = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<?php require_once __DIR__ . '/../../includes/kop_surat.php'; ?>
<div class="page-header">
    <div><h2><i class="fas fa-trash-can"></i> Laporan Penghapusan Aset</h2></div>
    <div class="btn-group no-print">
        <a href="export_pdf.php?type=penghapusan&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>" class="btn btn-danger"><i class="fas fa-file-pdf"></i> Cetak PDF</a>
        <a href="export_pdf.php?type=penghapusan&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>&view=1" target="_blank" class="btn btn-info"><i class="fas fa-eye"></i> Lihat PDF</a>
        <a href="export_excel.php?type=penghapusan&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>" class="btn btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
    </div>
</div>
<div class="card mb-4 no-print"><div class="card-body">
    <form method="GET" class="search-bar">
        <input type="date" class="form-control" name="start_date" value="<?= $startDate ?>" style="max-width:170px;">
        <input type="date" class="form-control" name="end_date" value="<?= $endDate ?>" style="max-width:170px;">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
        <a href="penghapusan_aset.php" class="btn btn-secondary btn-sm">Reset</a>
    </form>
</div></div>
<div class="card animate-fadeInUp"><div class="card-header"><h3>Aset Dihapus (<?= count($data) ?> item)</h3></div><div class="card-body"><div class="table-wrapper">
    <table><thead><tr><th>No</th><th>Tgl Hapus</th><th>Kode</th><th>Nama Aset</th><th>Kategori</th><th>Lokasi</th><th>Kondisi</th><th>Nilai</th></tr></thead>
    <tbody>
        <?php if (empty($data)): ?>
            <tr><td colspan="8" class="text-center text-muted">Belum ada aset yang dihapus</td></tr>
        <?php else: $total = 0; foreach ($data as $i => $a): $total += $a['nilai_perolehan'] * $a['jumlah']; ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td><?= date('d/m/Y', strtotime($a['deleted_at'])) ?></td>
            <td><?= htmlspecialchars($a['kode_aset']) ?></td><td><?= htmlspecialchars($a['nama_aset']) ?></td>
            <td><?= htmlspecialchars($a['nama_kategori'] ?? '-') ?></td><td><?= htmlspecialchars($a['nama_lokasi'] ?? '-') ?></td>
            <td><span class="badge badge-<?= $a['kondisi'] === 'Baik' ? 'success' : ($a['kondisi'] === 'Rusak Ringan' ? 'warning' : 'danger') ?>"><?= $a['kondisi'] ?></span></td>
            <td style="text-align:right;"><?= formatRupiah($a['nilai_perolehan'] * $a['jumlah']) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr style="font-weight:700;"><td colspan="7" class="text-right">TOTAL NILAI TERHAPUS</td><td style="text-align:right;"><?= formatRupiah($total) ?></td></tr>
        <?php endif; ?>
    </tbody></table>
</div></div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
