<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/auth_check.php';
requireStaff();
$pdo = getConnection();

// Statistik
$totalAset = $pdo->query("SELECT COUNT(*) FROM aset WHERE deleted_at IS NULL")->fetchColumn();
$totalKategori = $pdo->query("SELECT COUNT(*) FROM kategori")->fetchColumn();
$totalLokasi = $pdo->query("SELECT COUNT(*) FROM lokasi")->fetchColumn();
$totalPeminjaman = $pdo->query("SELECT COUNT(*) FROM peminjaman WHERE status = 'Dipinjam'")->fetchColumn();
$totalNilai = $pdo->query("SELECT COALESCE(SUM(nilai_perolehan * jumlah), 0) FROM aset WHERE deleted_at IS NULL")->fetchColumn();

// Kondisi aset untuk chart
$kondisi = $pdo->query("SELECT kondisi, COUNT(*) as total FROM aset WHERE deleted_at IS NULL GROUP BY kondisi")->fetchAll();
$kondisiLabels = array_column($kondisi, 'kondisi');
$kondisiData = array_column($kondisi, 'total');

// Aset paling sering dipinjam (top 5 semua status peminjaman)
$topDipinjam = $pdo->query("SELECT a.kode_aset, a.nama_aset,
        COUNT(p.id) AS total_pinjam,
        SUM(CASE WHEN p.status = 'Dipinjam' THEN 1 ELSE 0 END) AS sedang_dipinjam,
        SUM(CASE WHEN p.status = 'Dikembalikan' THEN 1 ELSE 0 END) AS sudah_kembali
    FROM peminjaman p
    JOIN aset a ON p.id_aset = a.id
    GROUP BY a.id, a.kode_aset, a.nama_aset
    ORDER BY total_pinjam DESC, a.nama_aset ASC
    LIMIT 5")->fetchAll();
$topLabels = array_column($topDipinjam, 'nama_aset');
$topData = array_map('intval', array_column($topDipinjam, 'total_pinjam'));

// Aset terbaru
$asetTerbaru = $pdo->query("SELECT a.*, k.nama_kategori, l.nama_lokasi FROM aset a 
    LEFT JOIN kategori k ON a.id_kategori = k.id 
    LEFT JOIN lokasi l ON a.id_lokasi = l.id 
    WHERE a.deleted_at IS NULL
    ORDER BY a.created_at DESC LIMIT 5")->fetchAll();

// Peminjaman aktif
$pinjamanAktif = $pdo->query("SELECT p.*, a.nama_aset, a.kode_aset FROM peminjaman p 
    JOIN aset a ON p.id_aset = a.id 
    WHERE p.status = 'Dipinjam' 
    ORDER BY p.tanggal_pinjam DESC LIMIT 5")->fetchAll();

// Aktivitas terbaru
$aktivitas = $pdo->query("SELECT r.*, u.nama FROM riwayat_aktivitas r 
    LEFT JOIN users u ON r.id_user = u.id 
    ORDER BY r.created_at DESC LIMIT 8")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card purple">
        <div class="stat-icon purple"><i class="fas fa-boxes-stacked"></i></div>
        <div class="stat-info">
            <h3 class="stat-number" data-target="<?= $totalAset ?>">0</h3>
            <p>Total Aset</p>
        </div>
    </div>
    <div class="stat-card teal">
        <div class="stat-icon teal"><i class="fas fa-tags"></i></div>
        <div class="stat-info">
            <h3 class="stat-number" data-target="<?= $totalKategori ?>">0</h3>
            <p>Kategori</p>
        </div>
    </div>
    <div class="stat-card pink">
        <div class="stat-icon pink"><i class="fas fa-location-dot"></i></div>
        <div class="stat-info">
            <h3 class="stat-number" data-target="<?= $totalLokasi ?>">0</h3>
            <p>Lokasi</p>
        </div>
    </div>
    <div class="stat-card amber">
        <div class="stat-icon amber"><i class="fas fa-handshake"></i></div>
        <div class="stat-info">
            <h3 class="stat-number" data-target="<?= $totalPeminjaman ?>">0</h3>
            <p>Peminjaman Aktif</p>
        </div>
    </div>
</div>

<!-- Total Nilai Aset -->
<div class="card mb-4 animate-fadeInUp">
    <div class="card-body" style="text-align:center; padding: 20px;">
        <p class="text-muted mb-2">Total Nilai Aset Keseluruhan</p>
        <h2 style="font-size: 1.8rem; font-weight:800; background: var(--accent-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <?= formatRupiah($totalNilai) ?>
        </h2>
    </div>
</div>

<div class="grid-2">
    <!-- Chart Kondisi Aset -->
    <div class="card animate-fadeInUp">
        <div class="card-header">
            <h3><i class="fas fa-chart-doughnut"></i> Distribusi Kondisi Aset</h3>
        </div>
        <div class="card-body">
            <div class="chart-container" style="height:260px">
                <canvas id="kondisiChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Aktivitas Terbaru -->
    <div class="card animate-fadeInUp">
        <div class="card-header">
            <h3><i class="fas fa-clock-rotate-left"></i> Aktivitas Terbaru</h3>
        </div>
        <div class="card-body" style="max-height:300px; overflow-y:auto;">
            <?php if (empty($aktivitas)): ?>
                <div class="empty-state"><p>Belum ada aktivitas</p></div>
            <?php else: ?>
                <?php foreach ($aktivitas as $act): ?>
                <div style="display:flex; align-items:flex-start; gap:12px; padding:10px 0; border-bottom:1px solid var(--border-glass);">
                    <div class="stat-icon blue" style="width:36px;height:36px;font-size:0.8rem;flex-shrink:0;">
                        <i class="fas fa-circle-info"></i>
                    </div>
                    <div>
                        <p style="font-size:0.85rem; font-weight:500;"><?= htmlspecialchars($act['aktivitas']) ?></p>
                        <p style="font-size:0.75rem; color:var(--text-muted);">
                            <?= htmlspecialchars($act['nama'] ?? 'System') ?> — <?= date('d/m/Y H:i', strtotime($act['created_at'])) ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Aset Terbaru -->
<div class="card mt-4 animate-fadeInUp">
    <div class="card-header">
        <h3><i class="fas fa-clock"></i> Aset Terbaru Ditambahkan</h3>
        <a href="<?= BASE_URL ?>/pages/aset/index.php" class="btn btn-sm btn-secondary">Lihat Semua</a>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Aset</th>
                        <th>Kategori</th>
                        <th>Lokasi</th>
                        <th>Kondisi</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($asetTerbaru)): ?>
                        <tr><td colspan="6" class="text-center text-muted">Belum ada data aset</td></tr>
                    <?php else: ?>
                        <?php foreach ($asetTerbaru as $a): ?>
                        <tr>
                            <td><span class="badge badge-primary"><?= htmlspecialchars($a['kode_aset']) ?></span></td>
                            <td><?= htmlspecialchars($a['nama_aset']) ?></td>
                            <td><?= htmlspecialchars($a['nama_kategori'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($a['nama_lokasi'] ?? '-') ?></td>
                            <td>
                                <span class="badge badge-<?= $a['kondisi'] === 'Baik' ? 'success' : ($a['kondisi'] === 'Rusak Ringan' ? 'warning' : 'danger') ?>">
                                    <?= $a['kondisi'] ?>
                                </span>
                            </td>
                            <td><?= $a['jumlah'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Aset Paling Sering Dipinjam -->
<div class="card mt-4 animate-fadeInUp">
    <div class="card-header">
        <h3><i class="fas fa-fire"></i> Aset Paling Sering Dipinjam</h3>
        <a href="<?= BASE_URL ?>/pages/laporan/peminjaman.php" class="btn btn-sm btn-secondary">Laporan Peminjaman</a>
    </div>
    <div class="card-body">
        <?php if (empty($topDipinjam)): ?>
            <div class="empty-state"><p>Belum ada data peminjaman</p></div>
        <?php else: ?>
        <div class="grid-2" style="align-items:start;">
            <div class="chart-container" style="height:280px;">
                <canvas id="topPinjamChart"></canvas>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kode</th>
                            <th>Nama Aset</th>
                            <th style="text-align:center;">Total</th>
                            <th style="text-align:center;">Aktif</th>
                            <th style="text-align:center;">Selesai</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topDipinjam as $i => $t): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><span class="badge badge-primary"><?= htmlspecialchars($t['kode_aset']) ?></span></td>
                            <td><?= htmlspecialchars($t['nama_aset']) ?></td>
                            <td style="text-align:center; font-weight:600;"><?= $t['total_pinjam'] ?></td>
                            <td style="text-align:center;"><?= $t['sedang_dipinjam'] ?></td>
                            <td style="text-align:center;"><?= $t['sudah_kembali'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Peminjaman Aktif -->
<?php if (!empty($pinjamanAktif)): ?>
<div class="card mt-4 animate-fadeInUp">
    <div class="card-header">
        <h3><i class="fas fa-handshake"></i> Peminjaman Aktif</h3>
        <a href="<?= BASE_URL ?>/pages/peminjaman/index.php" class="btn btn-sm btn-secondary">Lihat Semua</a>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Kode Aset</th>
                        <th>Nama Aset</th>
                        <th>Peminjam</th>
                        <th>Tgl Pinjam</th>
                        <th>Batas Kembali</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pinjamanAktif as $p): ?>
                    <tr>
                        <td><span class="badge badge-primary"><?= htmlspecialchars($p['kode_aset']) ?></span></td>
                        <td><?= htmlspecialchars($p['nama_aset']) ?></td>
                        <td><?= htmlspecialchars($p['nama_peminjam']) ?></td>
                        <td><?= date('d/m/Y', strtotime($p['tanggal_pinjam'])) ?></td>
                        <td><?= date('d/m/Y', strtotime($p['tanggal_kembali_rencana'])) ?></td>
                        <td><span class="badge badge-warning">Dipinjam</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
// Doughnut Chart - Kondisi Aset
const ctx = document.getElementById('kondisiChart');
if (ctx) {
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($kondisiLabels) ?>,
            datasets: [{
                data: <?= json_encode(array_map('intval', $kondisiData)) ?>,
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                borderColor: 'transparent',
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#4b6355',
                        font: { family: 'Inter', size: 12 },
                        padding: 16,
                        usePointStyle: true,
                        pointStyleWidth: 10
                    }
                }
            },
            cutout: '65%'
        }
    });
}

// Horizontal Bar Chart - Aset Paling Sering Dipinjam
const ctxPinjam = document.getElementById('topPinjamChart');
if (ctxPinjam) {
    new Chart(ctxPinjam, {
        type: 'bar',
        data: {
            labels: <?= json_encode($topLabels) ?>,
            datasets: [{
                label: 'Jumlah Peminjaman',
                data: <?= json_encode($topData) ?>,
                backgroundColor: ['#6366f1', '#14b8a6', '#ec4899', '#f59e0b', '#10b981'],
                borderRadius: 6,
                borderSkipped: false,
                barThickness: 22
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ` ${ctx.parsed.x} kali dipinjam`
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { precision: 0, color: '#4b6355' },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                y: {
                    ticks: { color: '#4b6355', font: { family: 'Inter', size: 12 } },
                    grid: { display: false }
                }
            }
        }
    });
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
