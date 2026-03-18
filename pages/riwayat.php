<?php
$pageTitle = 'Riwayat Aktivitas';
require_once __DIR__ . '/../includes/auth_check.php';
requireAdmin();
$pdo = getConnection();

$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20; $offset = ($page - 1) * $perPage;
$total = $pdo->query("SELECT COUNT(*) FROM riwayat_aktivitas")->fetchColumn();
$totalPages = ceil($total / $perPage);

$riwayat = $pdo->query("SELECT r.*, u.nama FROM riwayat_aktivitas r 
    LEFT JOIN users u ON r.id_user = u.id 
    ORDER BY r.created_at DESC LIMIT $perPage OFFSET $offset")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>
<div class="page-header"><div><h2><i class="fas fa-clock-rotate-left"></i> Riwayat Aktivitas</h2></div></div>
<div class="card animate-fadeInUp"><div class="card-header"><h3>Log Aktivitas (<?= $total ?> total)</h3></div><div class="card-body"><div class="table-wrapper">
    <table><thead><tr><th>No</th><th>Waktu</th><th>Pengguna</th><th>Aktivitas</th><th>Keterangan</th></tr></thead>
    <tbody>
        <?php foreach ($riwayat as $i => $r): ?>
        <tr>
            <td><?= $offset + $i + 1 ?></td>
            <td style="white-space:nowrap;"><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
            <td><?= htmlspecialchars($r['nama'] ?? 'System') ?></td>
            <td><span class="badge badge-info"><?= htmlspecialchars($r['aktivitas']) ?></span></td>
            <td><?= htmlspecialchars($r['keterangan'] ?? '-') ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody></table>
</div>
<?php if ($totalPages > 1): ?>
<div class="pagination">
    <?php if ($page > 1): ?><a href="?page=<?= $page-1 ?>">&laquo;</a><?php endif; ?>
    <?php for ($p = max(1,$page-2); $p <= min($totalPages,$page+2); $p++): ?>
        <?php if ($p == $page): ?><span class="active"><?= $p ?></span>
        <?php else: ?><a href="?page=<?= $p ?>"><?= $p ?></a><?php endif; ?>
    <?php endfor; ?>
    <?php if ($page < $totalPages): ?><a href="?page=<?= $page+1 ?>">&raquo;</a><?php endif; ?>
</div>
<?php endif; ?>
</div></div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
