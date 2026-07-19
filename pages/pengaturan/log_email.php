<?php
$pageTitle = 'Log Notifikasi Email';
require_once __DIR__ . '/../../includes/auth_check.php';
requireStaff();
$pdo = getConnection();

// Filter
$filterTipe = $_GET['tipe'] ?? '';
$filterStatus = $_GET['status'] ?? '';

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;
$offset = ($page - 1) * $perPage;

$where = "WHERE 1=1";
$params = [];

if ($filterTipe) {
    $where .= " AND en.tipe = ?";
    $params[] = $filterTipe;
}
if ($filterStatus) {
    $where .= " AND en.status = ?";
    $params[] = $filterStatus;
}

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM email_notifications en $where");
$countStmt->execute($params);
$total = $countStmt->fetchColumn();
$totalPages = ceil($total / $perPage);

$stmt = $pdo->prepare("
    SELECT en.*, p.nama_peminjam, a.nama_aset, a.kode_aset
    FROM email_notifications en
    JOIN peminjaman p ON en.id_peminjaman = p.id
    JOIN aset a ON p.id_aset = a.id
    $where
    ORDER BY en.created_at DESC
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$logs = $stmt->fetchAll();

// Statistik
$statSent = $pdo->query("SELECT COUNT(*) FROM email_notifications WHERE status = 'sent'")->fetchColumn();
$statFailed = $pdo->query("SELECT COUNT(*) FROM email_notifications WHERE status = 'failed'")->fetchColumn();
$statToday = $pdo->query("SELECT COUNT(*) FROM email_notifications WHERE DATE(created_at) = CURDATE() AND status = 'sent'")->fetchColumn();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-envelope-open-text"></i> Log Notifikasi Email</h2>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>/dashboard">Dashboard</a>
            <span class="separator">/</span>
            <a href="<?= BASE_URL ?>/pengaturan-email">Pengaturan</a>
            <span class="separator">/</span>
            <span>Log Email</span>
        </div>
    </div>
    <a href="<?= BASE_URL ?>/pengaturan-email" class="btn btn-secondary"><i class="fas fa-gear"></i> Pengaturan SMTP</a>
</div>

<!-- Stats -->
<div class="stats-grid" style="grid-template-columns:repeat(3, 1fr);">
    <div class="stat-card animate-fadeInUp">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info"><h3><?= $statSent ?></h3><p>Email Terkirim</p></div>
    </div>
    <div class="stat-card animate-fadeInUp">
        <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
        <div class="stat-info"><h3><?= $statFailed ?></h3><p>Gagal Kirim</p></div>
    </div>
    <div class="stat-card animate-fadeInUp">
        <div class="stat-icon blue"><i class="fas fa-calendar-day"></i></div>
        <div class="stat-info"><h3><?= $statToday ?></h3><p>Terkirim Hari Ini</p></div>
    </div>
</div>

<!-- Filter -->
<div class="card mb-4"><div class="card-body">
    <form method="GET" class="search-bar">
        <select class="form-control" name="tipe" style="max-width:180px;">
            <option value="">Semua Tipe</option>
            <option value="reminder" <?= $filterTipe === 'reminder' ? 'selected' : '' ?>>Reminder (H-1)</option>
            <option value="due" <?= $filterTipe === 'due' ? 'selected' : '' ?>>Due (H+0)</option>
            <option value="overdue" <?= $filterTipe === 'overdue' ? 'selected' : '' ?>>Overdue (H+1)</option>
        </select>
        <select class="form-control" name="status" style="max-width:160px;">
            <option value="">Semua Status</option>
            <option value="sent" <?= $filterStatus === 'sent' ? 'selected' : '' ?>>Terkirim</option>
            <option value="failed" <?= $filterStatus === 'failed' ? 'selected' : '' ?>>Gagal</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
        <a href="<?= BASE_URL ?>/log-notifikasi" class="btn btn-secondary btn-sm">Reset</a>
    </form>
</div></div>

<!-- Tabel Log -->
<div class="card animate-fadeInUp"><div class="card-header"><h3>Riwayat Notifikasi (<?= $total ?> total)</h3></div><div class="card-body"><div class="table-wrapper">
    <table><thead><tr>
        <th>No</th><th>Waktu</th><th>Tipe</th><th>Aset</th><th>Peminjam</th><th>Email Tujuan</th><th>Status</th><th>Keterangan</th>
    </tr></thead>
    <tbody>
        <?php if (empty($logs)): ?>
            <tr><td colspan="8" class="text-center text-muted">Belum ada log notifikasi</td></tr>
        <?php else: foreach ($logs as $i => $log): ?>
        <tr>
            <td><?= $offset + $i + 1 ?></td>
            <td style="white-space:nowrap;"><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
            <td>
                <?php
                $tipeBadge = match($log['tipe']) {
                    'reminder' => '<span class="badge badge-warning">H-1</span>',
                    'due' => '<span class="badge badge-danger" style="background:#ef4444;">H+0</span>',
                    'overdue' => '<span class="badge badge-danger">Overdue</span>',
                    default => '<span class="badge badge-info">' . $log['tipe'] . '</span>'
                };
                echo $tipeBadge;
                ?>
            </td>
            <td>
                <span class="badge badge-primary" style="font-size:0.72rem;"><?= htmlspecialchars($log['kode_aset']) ?></span><br>
                <span style="font-size:0.82rem;"><?= htmlspecialchars($log['nama_aset']) ?></span>
            </td>
            <td><?= htmlspecialchars($log['nama_peminjam']) ?></td>
            <td style="font-size:0.82rem;"><?= htmlspecialchars($log['email_tujuan']) ?></td>
            <td>
                <?php if ($log['status'] === 'sent'): ?>
                    <span class="badge badge-success"><i class="fas fa-check"></i> Terkirim</span>
                <?php else: ?>
                    <span class="badge badge-danger"><i class="fas fa-times"></i> Gagal</span>
                <?php endif; ?>
            </td>
            <td style="font-size:0.78rem;color:var(--text-muted);max-width:200px;overflow:hidden;text-overflow:ellipsis;">
                <?= htmlspecialchars($log['pesan_error'] ?? '-') ?>
            </td>
        </tr>
        <?php endforeach; endif; ?>
    </tbody></table>
</div>

<?php if ($totalPages > 1): ?>
<div class="pagination">
    <?php if ($page > 1): ?><a href="?page=<?= $page-1 ?>&tipe=<?= $filterTipe ?>&status=<?= $filterStatus ?>">&laquo;</a><?php endif; ?>
    <?php for ($p = max(1,$page-2); $p <= min($totalPages,$page+2); $p++): ?>
        <?php if ($p == $page): ?><span class="active"><?= $p ?></span>
        <?php else: ?><a href="?page=<?= $p ?>&tipe=<?= $filterTipe ?>&status=<?= $filterStatus ?>"><?= $p ?></a><?php endif; ?>
    <?php endfor; ?>
    <?php if ($page < $totalPages): ?><a href="?page=<?= $page+1 ?>&tipe=<?= $filterTipe ?>&status=<?= $filterStatus ?>">&raquo;</a><?php endif; ?>
</div>
<?php endif; ?>

</div></div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
