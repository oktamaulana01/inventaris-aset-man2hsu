<?php
$pageTitle = 'Laporan Inventaris Keseluruhan';
require_once __DIR__ . '/../../includes/auth_check.php';
requireStaff();
$pdo = getConnection();

$filterKondisi = $_GET['kondisi'] ?? '';
$filterKategori = $_GET['kategori'] ?? '';
$filterJenis = $_GET['jenis'] ?? '';

$where = "WHERE a.deleted_at IS NULL";
$params = [];
if ($filterKondisi) { $where .= " AND a.kondisi = ?"; $params[] = $filterKondisi; }
if ($filterKategori) { $where .= " AND a.id_kategori = ?"; $params[] = $filterKategori; }
if ($filterJenis) { $where .= " AND a.jenis_barang = ?"; $params[] = $filterJenis; }

$stmt = $pdo->prepare("SELECT a.*, k.nama_kategori, l.nama_lokasi FROM aset a 
    LEFT JOIN kategori k ON a.id_kategori = k.id LEFT JOIN lokasi l ON a.id_lokasi = l.id 
    $where ORDER BY a.kode_aset");
$stmt->execute($params);
$data = $stmt->fetchAll();

$kategoriList = $pdo->query("SELECT * FROM kategori ORDER BY nama_kategori")->fetchAll();
$totalNilai = 0;
foreach ($data as $d) { $totalNilai += $d['nilai_perolehan'] * $d['jumlah']; }

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<?php require_once __DIR__ . '/../../includes/kop_surat.php'; ?>
<div class="page-header">
    <div><h2><i class="fas fa-file-lines"></i> Laporan Inventaris Keseluruhan</h2></div>
    <div class="btn-group no-print">
        <a href="export_pdf.php?type=keseluruhan&kondisi=<?= urlencode($filterKondisi) ?>&kategori=<?= urlencode($filterKategori) ?>&jenis=<?= urlencode($filterJenis) ?>" class="btn btn-danger"><i class="fas fa-file-pdf"></i> Cetak PDF</a>
        <a href="export_pdf.php?type=keseluruhan&kondisi=<?= urlencode($filterKondisi) ?>&kategori=<?= urlencode($filterKategori) ?>&jenis=<?= urlencode($filterJenis) ?>&view=1" target="_blank" class="btn btn-info"><i class="fas fa-eye"></i> Lihat PDF</a>
        <a href="export_excel.php?type=keseluruhan&kondisi=<?= urlencode($filterKondisi) ?>&kategori=<?= urlencode($filterKategori) ?>&jenis=<?= urlencode($filterJenis) ?>" class="btn btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
    </div>
</div>

<div class="card mb-4 no-print"><div class="card-body">
    <form method="GET" class="search-bar">
        <select class="form-control" name="jenis" style="max-width:180px;">
            <option value="">Semua Klasifikasi</option>
            <option value="Aset Tetap" <?= $filterJenis === 'Aset Tetap' ? 'selected' : '' ?>>Aset Tetap</option>
            <option value="Inventaris Barang" <?= $filterJenis === 'Inventaris Barang' ? 'selected' : '' ?>>Inventaris Barang</option>
        </select>
        <select class="form-control" name="kondisi" style="max-width:170px;">
            <option value="">Semua Kondisi</option>
            <option value="Baik" <?= $filterKondisi === 'Baik' ? 'selected' : '' ?>>Baik</option>
            <option value="Rusak Ringan" <?= $filterKondisi === 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
            <option value="Rusak Berat" <?= $filterKondisi === 'Rusak Berat' ? 'selected' : '' ?>>Rusak Berat</option>
        </select>
        <select class="form-control" name="kategori" style="max-width:200px;">
            <option value="">Semua Kategori</option>
            <?php foreach ($kategoriList as $k): ?>
                <option value="<?= $k['id'] ?>" <?= $filterKategori == $k['id'] ? 'selected' : '' ?>><?= htmlspecialchars($k['nama_kategori']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
        <a href="<?= BASE_URL ?>/laporan/keseluruhan" class="btn btn-secondary btn-sm">Reset</a>
    </form>
</div></div>

<div class="card animate-fadeInUp"><div class="card-header"><h3>Data Barang (<?= count($data) ?> item) — Total Nilai: <?= formatRupiah($totalNilai) ?></h3></div>
<div class="card-body"><div class="table-wrapper">
    <table><thead><tr><th>No</th><th>Kode</th><th>Nama Barang</th><th>Klasifikasi</th><th>Kategori</th><th>Lokasi</th><th>Jumlah</th><th>Kondisi</th><th>Tahun</th><th>Nilai</th></tr></thead>
    <tbody>
        <?php foreach ($data as $i => $a): ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td><span class="badge badge-primary"><?= htmlspecialchars($a['kode_aset']) ?></span></td>
            <td><strong><?= htmlspecialchars($a['nama_aset']) ?></strong></td>
            <td>
                <?php if ($a['jenis_barang'] === 'Aset Tetap'): ?>
                    <span class="badge badge-primary" style="font-size:0.75rem;">Aset Tetap</span>
                <?php else: ?>
                    <span class="badge badge-info" style="font-size:0.75rem;">Inventaris</span>
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($a['nama_kategori'] ?? '-') ?></td>
            <td><?= htmlspecialchars($a['nama_lokasi'] ?? '-') ?></td>
            <td><?= $a['jumlah'] ?></td>
            <td>
                <span class="badge badge-<?= $a['kondisi'] === 'Baik' ? 'success' : ($a['kondisi'] === 'Rusak Ringan' ? 'warning' : 'danger') ?>">
                    <?= $a['kondisi'] ?>
                </span>
            </td>
            <td><?= $a['tahun_perolehan'] ?? '-' ?></td>
            <td><?= formatRupiah($a['nilai_perolehan'] * $a['jumlah']) ?></td>
        </tr>
        <?php endforeach; ?>
        <tr style="font-weight:700;"><td colspan="9" style="text-align:right;">TOTAL KESELURUHAN</td><td><?= formatRupiah($totalNilai) ?></td></tr>
    </tbody></table>
</div></div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
