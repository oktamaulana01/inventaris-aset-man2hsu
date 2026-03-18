<?php
$pageTitle = 'Laporan Aset per Lokasi';
require_once __DIR__ . '/../../includes/auth_check.php';
$pdo = getConnection();

$data = $pdo->query("SELECT l.nama_lokasi, COUNT(a.id) as total_aset, SUM(a.jumlah) as total_unit,
    COALESCE(SUM(a.nilai_perolehan * a.jumlah), 0) as total_nilai,
    SUM(CASE WHEN a.kondisi='Baik' THEN 1 ELSE 0 END) as baik,
    SUM(CASE WHEN a.kondisi='Rusak Ringan' THEN 1 ELSE 0 END) as rusak_ringan,
    SUM(CASE WHEN a.kondisi='Rusak Berat' THEN 1 ELSE 0 END) as rusak_berat
    FROM lokasi l LEFT JOIN aset a ON l.id = a.id_lokasi AND a.deleted_at IS NULL
    GROUP BY l.id, l.nama_lokasi ORDER BY l.nama_lokasi")->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<?php require_once __DIR__ . '/../../includes/kop_surat.php'; ?>
<div class="page-header">
    <div><h2><i class="fas fa-map-location-dot"></i> Laporan Aset per Lokasi</h2></div>
    <div class="btn-group no-print">
        <a href="export_pdf.php?type=per_lokasi" class="btn btn-danger"><i class="fas fa-file-pdf"></i> Cetak PDF</a>
        <a href="export_pdf.php?type=per_lokasi&view=1" target="_blank" class="btn btn-info"><i class="fas fa-eye"></i> Lihat PDF</a>
        <a href="export_excel.php?type=per_lokasi" class="btn btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
    </div>
</div>
<div class="card animate-fadeInUp"><div class="card-header"><h3>Rekapitulasi Aset per Lokasi</h3></div><div class="card-body"><div class="table-wrapper">
    <table><thead><tr><th>No</th><th>Lokasi</th><th>Total Aset</th><th>Total Unit</th><th>Baik</th><th>Rusak Ringan</th><th>Rusak Berat</th><th>Total Nilai</th></tr></thead>
    <tbody>
        <?php $grandTotal = 0; foreach ($data as $i => $d): $grandTotal += $d['total_nilai']; ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td style="font-weight:500;"><?= htmlspecialchars($d['nama_lokasi']) ?></td>
            <td><?= $d['total_aset'] ?></td>
            <td><?= $d['total_unit'] ?? 0 ?></td>
            <td><span class="badge badge-success"><?= $d['baik'] ?? 0 ?></span></td>
            <td><span class="badge badge-warning"><?= $d['rusak_ringan'] ?? 0 ?></span></td>
            <td><span class="badge badge-danger"><?= $d['rusak_berat'] ?? 0 ?></span></td>
            <td style="text-align:right;"><?= formatRupiah($d['total_nilai']) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr style="font-weight:700;"><td colspan="7" class="text-right">GRAND TOTAL</td><td style="text-align:right;"><?= formatRupiah($grandTotal) ?></td></tr>
    </tbody></table>
</div></div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
