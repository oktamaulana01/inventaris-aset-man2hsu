<?php
$pageTitle = 'Laporan Penghapusan Aset';
require_once __DIR__ . '/../../includes/auth_check.php';
requireStaff();
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
        <a href="<?= BASE_URL ?>/laporan/penghapusan" class="btn btn-secondary btn-sm">Reset</a>
    </form>
</div></div>
<div class="card animate-fadeInUp"><div class="card-header"><h3>Aset Dihapus (<?= count($data) ?> item)</h3></div><div class="card-body"><div class="table-wrapper">
    <table><thead><tr><th>No</th><th>Tgl Hapus</th><th>Kode</th><th>Nama Aset</th><th>Kategori</th><th>Kondisi</th><th>Alasan</th><th>Bukti Foto</th><th style="text-align:center;">Aksi</th></tr></thead>
    <tbody>
        <?php if (empty($data)): ?>
            <tr><td colspan="9" class="text-center text-muted">Belum ada aset yang dihapus</td></tr>
        <?php else: $total = 0; foreach ($data as $i => $a): $total += $a['nilai_perolehan'] * $a['jumlah']; ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td><?= date('d/m/Y', strtotime($a['deleted_at'])) ?></td>
            <td><?= htmlspecialchars($a['kode_aset']) ?></td><td><?= htmlspecialchars($a['nama_aset']) ?></td>
            <td><?= htmlspecialchars($a['nama_kategori'] ?? '-') ?></td>
            <td><?= htmlspecialchars($a['kondisi']) ?></td>
            <td style="max-width: 200px; white-space: normal;"><?= nl2br(htmlspecialchars($a['alasan_hapus'] ?? '-')) ?></td>
            <td>
                <?php if ($a['bukti_hapus']): ?>
                    <a href="<?= BASE_URL ?>/assets/uploads/bukti_hapus/<?= htmlspecialchars($a['bukti_hapus']) ?>" target="_blank" class="btn btn-sm btn-info" title="Lihat Bukti Foto">
                        <i class="fas fa-image"></i>
                    </a>
                <?php else: ?>
                    <span class="text-muted">-</span>
                <?php endif; ?>
            </td>
            <td style="text-align:center;">
                <a href="<?= BASE_URL ?>/berita-acara/penghapusan?id=<?= $a['id'] ?>" target="_blank" class="btn btn-sm btn-primary" title="Cetak Berita Acara Penghapusan">
                    <i class="fas fa-file-contract"></i> BA Hapus
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody></table>
</div></div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
