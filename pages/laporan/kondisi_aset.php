<?php
$pageTitle = 'Laporan Kondisi Aset';
require_once __DIR__ . '/../../includes/auth_check.php';
$pdo = getConnection();

$filterKondisi = $_GET['kondisi'] ?? '';
$where = "WHERE a.deleted_at IS NULL";
$params = [];
if ($filterKondisi) { $where .= " AND a.kondisi = ?"; $params[] = $filterKondisi; }

$stmt = $pdo->prepare("SELECT a.*, k.nama_kategori, l.nama_lokasi FROM aset a 
    LEFT JOIN kategori k ON a.id_kategori = k.id LEFT JOIN lokasi l ON a.id_lokasi = l.id 
    $where ORDER BY a.kondisi, a.nama_aset");
$stmt->execute($params);
$data = $stmt->fetchAll();

// Summary
$summary = $pdo->query("SELECT kondisi, COUNT(*) as total, SUM(jumlah) as unit FROM aset WHERE deleted_at IS NULL GROUP BY kondisi")->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<?php require_once __DIR__ . '/../../includes/kop_surat.php'; ?>
<div class="page-header">
    <div><h2><i class="fas fa-clipboard-check"></i> Laporan Kondisi Aset</h2></div>
    <div class="btn-group no-print">
        <a href="export_pdf.php?type=kondisi&kondisi=<?= urlencode($filterKondisi) ?>" class="btn btn-danger"><i class="fas fa-file-pdf"></i> Cetak PDF</a>
        <a href="export_pdf.php?type=kondisi&kondisi=<?= urlencode($filterKondisi) ?>&view=1" target="_blank" class="btn btn-info"><i class="fas fa-eye"></i> Lihat PDF</a>
        <a href="export_excel.php?type=kondisi&kondisi=<?= urlencode($filterKondisi) ?>" class="btn btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
    </div>
</div>

<!-- Summary Cards -->
<div class="stats-grid mb-4">
    <?php foreach ($summary as $s): ?>
    <div class="stat-card <?= $s['kondisi'] === 'Baik' ? 'teal' : ($s['kondisi'] === 'Rusak Ringan' ? 'amber' : 'pink') ?>">
        <div class="stat-icon <?= $s['kondisi'] === 'Baik' ? 'green' : ($s['kondisi'] === 'Rusak Ringan' ? 'amber' : 'red') ?>">
            <i class="fas fa-<?= $s['kondisi'] === 'Baik' ? 'check-circle' : ($s['kondisi'] === 'Rusak Ringan' ? 'exclamation-triangle' : 'times-circle') ?>"></i>
        </div>
        <div class="stat-info"><h3><?= $s['total'] ?></h3><p><?= $s['kondisi'] ?> (<?= $s['unit'] ?> unit)</p></div>
    </div>
    <?php endforeach; ?>
</div>

<div class="card mb-4 no-print"><div class="card-body">
    <form method="GET" class="search-bar">
        <select class="form-control" name="kondisi" style="max-width:200px;">
            <option value="">Semua Kondisi</option>
            <option value="Baik" <?= $filterKondisi === 'Baik' ? 'selected' : '' ?>>Baik</option>
            <option value="Rusak Ringan" <?= $filterKondisi === 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
            <option value="Rusak Berat" <?= $filterKondisi === 'Rusak Berat' ? 'selected' : '' ?>>Rusak Berat</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
        <a href="<?= BASE_URL ?>/laporan/kondisi" class="btn btn-secondary btn-sm">Reset</a>
    </form>
</div></div>

<div class="card animate-fadeInUp"><div class="card-header"><h3>Detail Kondisi Aset (<?= count($data) ?> item)</h3></div><div class="card-body"><div class="table-wrapper">
    <table><thead><tr><th>No</th><th>Kode</th><th>Nama Aset</th><th>Kategori</th><th>Lokasi</th><th>Jumlah</th><th>Kondisi</th><th>Tahun</th></tr></thead>
    <tbody>
        <?php foreach ($data as $i => $a): ?>
        <tr>
            <td><?= $i+1 ?></td><td><?= htmlspecialchars($a['kode_aset']) ?></td><td><?= htmlspecialchars($a['nama_aset']) ?></td>
            <td><?= htmlspecialchars($a['nama_kategori'] ?? '-') ?></td><td><?= htmlspecialchars($a['nama_lokasi'] ?? '-') ?></td>
            <td><?= $a['jumlah'] ?></td>
            <td><?= htmlspecialchars($a['kondisi']) ?></td>
            <td><?= $a['tahun_perolehan'] ?? '-' ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody></table>
</div></div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
