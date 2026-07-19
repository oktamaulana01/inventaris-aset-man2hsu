<?php
$pageTitle = 'Dashboard Guru';
require_once __DIR__ . '/../../includes/auth_check.php';
requireGuru();
$pdo = getConnection();

$userId = $_SESSION['user_id'];

// Statistik
$totalAset = $pdo->query("SELECT COUNT(*) FROM aset WHERE deleted_at IS NULL AND kondisi = 'Baik'")->fetchColumn();
$peminjamanAktif = $pdo->prepare("SELECT COUNT(*) FROM peminjaman WHERE id_peminjam = ? AND status = 'Dipinjam'");
$peminjamanAktif->execute([$userId]);
$peminjamanAktif = $peminjamanAktif->fetchColumn();

$totalPeminjaman = $pdo->prepare("SELECT COUNT(*) FROM peminjaman WHERE id_peminjam = ?");
$totalPeminjaman->execute([$userId]);
$totalPeminjaman = $totalPeminjaman->fetchColumn();

$totalDikembalikan = $pdo->prepare("SELECT COUNT(*) FROM peminjaman WHERE id_peminjam = ? AND status = 'Dikembalikan'");
$totalDikembalikan->execute([$userId]);
$totalDikembalikan = $totalDikembalikan->fetchColumn();

// Aset terbaru (yang bisa dipinjam)
$asetTerbaru = $pdo->query("SELECT a.*, k.nama_kategori, l.nama_lokasi FROM aset a 
    LEFT JOIN kategori k ON a.id_kategori = k.id 
    LEFT JOIN lokasi l ON a.id_lokasi = l.id 
    WHERE a.deleted_at IS NULL AND a.kondisi = 'Baik'
    ORDER BY a.created_at DESC LIMIT 5")->fetchAll();

// Peminjaman aktif milik guru ini
$pinjamanAktif = $pdo->prepare("SELECT p.*, a.nama_aset, a.kode_aset FROM peminjaman p 
    JOIN aset a ON p.id_aset = a.id 
    WHERE p.id_peminjam = ? AND p.status = 'Dipinjam' 
    ORDER BY p.tanggal_pinjam DESC LIMIT 5");
$pinjamanAktif->execute([$userId]);
$pinjamanAktif = $pinjamanAktif->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<!-- Welcome Card -->
<div class="card mb-4 animate-fadeInUp">
    <div class="card-body" style="padding: 28px;">
        <div style="display:flex; align-items:center; gap:20px; flex-wrap:wrap;">
            <div class="stat-icon purple" style="width:64px;height:64px;font-size:1.6rem;border-radius:16px;">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div style="flex:1; min-width:200px;">
                <h2 style="font-size:1.4rem; font-weight:700; color:var(--text-primary); margin-bottom:4px;">
                    Selamat Datang, <?= htmlspecialchars($_SESSION['user_nama']) ?>!
                </h2>
                <p style="font-size:0.88rem; color:var(--text-secondary); margin-bottom:8px;">
                    Anda login sebagai <span class="badge badge-info">Guru / Karyawan</span>
                </p>
                <div style="display:flex; gap:20px; flex-wrap:wrap; font-size:0.82rem; color:var(--text-muted);">
                    <?php if ($_SESSION['user_nip']): ?>
                        <span><i class="fas fa-id-card" style="margin-right:4px;"></i> NIP: <?= htmlspecialchars($_SESSION['user_nip']) ?></span>
                    <?php endif; ?>
                    <?php if ($_SESSION['user_jabatan']): ?>
                        <span><i class="fas fa-briefcase" style="margin-right:4px;"></i> <?= htmlspecialchars($_SESSION['user_jabatan']) ?></span>
                    <?php endif; ?>
                    <?php if ($_SESSION['user_no_telepon']): ?>
                        <span><i class="fas fa-phone" style="margin-right:4px;"></i> <?= htmlspecialchars($_SESSION['user_no_telepon']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/guru/pinjam" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajukan Peminjaman
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card purple">
        <div class="stat-icon purple"><i class="fas fa-boxes-stacked"></i></div>
        <div class="stat-info">
            <h3 class="stat-number" data-target="<?= $totalAset ?>">0</h3>
            <p>Aset Tersedia</p>
        </div>
    </div>
    <div class="stat-card amber">
        <div class="stat-icon amber"><i class="fas fa-handshake"></i></div>
        <div class="stat-info">
            <h3 class="stat-number" data-target="<?= $peminjamanAktif ?>">0</h3>
            <p>Sedang Dipinjam</p>
        </div>
    </div>
    <div class="stat-card teal">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
            <h3 class="stat-number" data-target="<?= $totalDikembalikan ?>">0</h3>
            <p>Sudah Dikembalikan</p>
        </div>
    </div>
    <div class="stat-card pink">
        <div class="stat-icon blue"><i class="fas fa-clipboard-list"></i></div>
        <div class="stat-info">
            <h3 class="stat-number" data-target="<?= $totalPeminjaman ?>">0</h3>
            <p>Total Peminjaman</p>
        </div>
    </div>
</div>

<!-- Peminjaman Aktif Saya -->
<div class="card mb-4 animate-fadeInUp">
    <div class="card-header">
        <h3><i class="fas fa-handshake" style="color:var(--warning);margin-right:8px;"></i> Peminjaman Aktif Saya</h3>
        <a href="<?= BASE_URL ?>/guru/riwayat" class="btn btn-sm btn-secondary">Lihat Semua</a>
    </div>
    <div class="card-body">
        <?php if (empty($pinjamanAktif)): ?>
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-inbox"></i></div>
                <p>Tidak ada peminjaman aktif saat ini</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Kode Aset</th>
                            <th>Nama Aset</th>
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
                            <td><?= date('d/m/Y', strtotime($p['tanggal_pinjam'])) ?></td>
                            <td>
                                <?php
                                $batas = strtotime($p['tanggal_kembali_rencana']);
                                $now = time();
                                $isOverdue = $now > $batas;
                                ?>
                                <span style="<?= $isOverdue ? 'color:var(--danger);font-weight:600;' : '' ?>">
                                    <?= date('d/m/Y', $batas) ?>
                                    <?php if ($isOverdue): ?>
                                        <i class="fas fa-exclamation-triangle" title="Terlambat!"></i>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td><span class="badge badge-warning">Dipinjam</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Aset Terbaru Tersedia -->
<div class="card animate-fadeInUp">
    <div class="card-header">
        <h3><i class="fas fa-boxes-stacked" style="color:var(--accent-primary);margin-right:8px;"></i> Aset Terbaru Tersedia</h3>
        <a href="<?= BASE_URL ?>/guru/katalog" class="btn btn-sm btn-secondary">Lihat Katalog</a>
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
                        <th>Jumlah</th>
                        <th>Aksi</th>
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
                            <td><?= $a['jumlah'] ?></td>
                            <td>
                                <a href="<?= BASE_URL ?>/guru/pinjam?id_aset=<?= $a['id'] ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-hand-holding-hand"></i> Pinjam
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
