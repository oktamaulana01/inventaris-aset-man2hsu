<?php
$pageTitle = 'Riwayat Peminjaman per Aset';
require_once __DIR__ . '/../../includes/auth_check.php';
requireStaff();
$pdo = getConnection();

// Ambil daftar semua aset untuk opsi dropdown (termasuk yang dihapus, jaga-jaga kalau mau lihat riwayat aset lama)
$stmtAset = $pdo->query("SELECT id, kode_aset, nama_aset FROM aset ORDER BY nama_aset ASC");
$listAset = $stmtAset->fetchAll();

$filterAsetId = $_GET['id_aset'] ?? '';
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

$where = "WHERE 1=1";
$params = [];

if ($filterAsetId) {
    $where .= " AND p.id_aset = ?";
    $params[] = $filterAsetId;
}

if ($startDate) {
    $where .= " AND p.tanggal_pinjam >= ?";
    $params[] = $startDate;
}

if ($endDate) {
    $where .= " AND p.tanggal_pinjam <= ?";
    $params[] = $endDate;
}

// Ambil data jika aset sudah dipilih
$data = [];
$asetInfo = null;

if ($filterAsetId) {
    $stmtInfo = $pdo->prepare("SELECT kode_aset, nama_aset FROM aset WHERE id = ?");
    $stmtInfo->execute([$filterAsetId]);
    $asetInfo = $stmtInfo->fetch();

    $stmt = $pdo->prepare("SELECT p.*, a.nama_aset, a.kode_aset FROM peminjaman p 
        JOIN aset a ON p.id_aset = a.id $where ORDER BY p.tanggal_pinjam DESC");
    $stmt->execute($params);
    $data = $stmt->fetchAll();
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<?php require_once __DIR__ . '/../../includes/kop_surat.php'; ?>

<div class="page-header">
    <div><h2><i class="fas fa-history"></i> Riwayat Peminjaman Aset</h2></div>
    <div class="btn-group no-print">
        <?php if ($filterAsetId): ?>
        <a href="export_pdf.php?type=riwayat_aset&id_aset=<?= urlencode($filterAsetId) ?>&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>" class="btn btn-danger"><i class="fas fa-file-pdf"></i> Cetak PDF</a>
        <a href="export_pdf.php?type=riwayat_aset&id_aset=<?= urlencode($filterAsetId) ?>&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>&view=1" target="_blank" class="btn btn-info"><i class="fas fa-eye"></i> Lihat PDF</a>
        <a href="export_excel.php?type=riwayat_aset&id_aset=<?= urlencode($filterAsetId) ?>&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>" class="btn btn-success"><i class="fas fa-file-excel"></i> Export Excel</a>
        <?php endif; ?>
    </div>
</div>

<div class="card mb-4 no-print">
    <div class="card-body">
        <form method="GET" class="search-bar" style="display:flex; flex-wrap:wrap; gap:10px;">
            <select class="form-control select2-init" name="id_aset" style="flex:1; min-width: 250px;" required>
                <option value="">-- Pilih Barang / Aset --</option>
                <?php foreach ($listAset as $a): ?>
                    <option value="<?= $a['id'] ?>" <?= ($filterAsetId == $a['id']) ? 'selected' : '' ?>>
                        [<?= htmlspecialchars($a['kode_aset']) ?>] <?= htmlspecialchars($a['nama_aset']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input type="date" class="form-control" name="start_date" value="<?= $startDate ?>" style="max-width:170px;" placeholder="Dari tanggal">
            <input type="date" class="form-control" name="end_date" value="<?= $endDate ?>" style="max-width:170px;" placeholder="Sampai tanggal">
            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Cari Riwayat</button>
            <a href="<?= BASE_URL ?>/laporan/riwayat" class="btn btn-secondary btn-sm">Reset</a>
        </form>
    </div>
</div>

<?php if ($filterAsetId): ?>
    <div class="card animate-fadeInUp">
        <div class="card-header">
            <h3>Rekam Jejak: <?= htmlspecialchars($asetInfo['nama_aset'] ?? 'Aset Tidak Ditemukan') ?> (<?= count($data) ?> record)</h3>
        </div>
        <div class="card-body">
            <?php if (empty($data)): ?>
                <div style="text-align:center; padding:30px; color:#64748b;">
                    <i class="fas fa-info-circle fa-3x" style="margin-bottom:15px; color:#cbd5e1;"></i>
                    <p>Barang ini belum pernah dipinjam pada periode ini.</p>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Peminjam</th>
                                <th>Tgl Pinjam</th>
                                <th>Tgl Kembali (Rencana)</th>
                                <th>Tgl Kembali (Aktual)</th>
                                <th>Kondisi Pengembalian</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data as $i => $p): ?>
                            <tr>
                                <td><?= $i+1 ?></td>
                                <td style="font-weight:600;"><?= htmlspecialchars($p['nama_peminjam']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($p['tanggal_pinjam'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($p['tanggal_kembali_rencana'])) ?></td>
                                <td>
                                    <?php if ($p['tanggal_kembali_aktual']): ?>
                                        <span style="color:var(--primary-color);font-weight:500;">
                                            <i class="fas fa-check-circle"></i> <?= date('d/m/Y H:i', strtotime($p['tanggal_kembali_aktual'])) ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color:#ef4444;"><i class="fas fa-clock"></i> Belum Kembali</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($p['kondisi_saat_dikembalikan'] ?? '-') ?></td>
                                <td>
                                    <?php
                                    $badge = match($p['status']) {
                                        'Menunggu Konfirmasi' => 'warning',
                                        'Dipinjam' => 'info',
                                        'Dikembalikan' => 'success',
                                        'Ditolak' => 'danger',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <?= htmlspecialchars($p['status']) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div style="text-align:center; padding:50px; color:#94a3b8;">
        <i class="fas fa-arrow-up fa-3x mb-3" style="animation: bounce 2s infinite;"></i>
        <h4>Silakan pilih barang terlebih dahulu di atas</h4>
        <p>Anda bisa mengetik nama atau kode barang pada kotak pencarian.</p>
    </div>
    <style>
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
            40% {transform: translateY(-15px);}
            60% {transform: translateY(-7px);}
        }
    </style>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
