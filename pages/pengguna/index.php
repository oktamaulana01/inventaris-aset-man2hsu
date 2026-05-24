<?php
$pageTitle = 'Manajemen Pengguna';
require_once __DIR__ . '/../../includes/auth_check.php';
requireAdmin();
$pdo = getConnection();
$userList = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header">
    <div><h2><i class="fas fa-users-gear"></i> Manajemen Pengguna</h2></div>
    <a href="tambah.php" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Pengguna</a>
</div>
<div class="card animate-fadeInUp"><div class="card-header"><h3>Daftar Pengguna</h3></div><div class="card-body"><div class="table-wrapper">
    <table><thead><tr><th>No</th><th>Nama</th><th>Username</th><th>Email</th><th>Role</th><th>Terdaftar</th><th>Aksi</th></tr></thead>
    <tbody>
        <?php foreach ($userList as $i => $u): ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td style="font-weight:500;"><?= htmlspecialchars($u['nama']) ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['email'] ?? '-') ?></td>
            <td><span class="badge badge-<?= $u['role'] === 'admin' ? 'primary' : ($u['role'] === 'guru' ? 'success' : 'info') ?>"><?= ucfirst($u['role']) ?></span></td>
            <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
            <td><div class="btn-group">
                <a href="edit.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                <?php if ($u['id'] != $_SESSION['user_id']): ?>
                <a href="javascript:void(0)" onclick="confirmDelete('Hapus pengguna <?= htmlspecialchars($u['nama']) ?>?', 'hapus.php', '<?= $u['id'] ?>')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></a>
                <?php endif; ?>
            </div></td>
        </tr>
        <?php endforeach; ?>
    </tbody></table>
</div></div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
