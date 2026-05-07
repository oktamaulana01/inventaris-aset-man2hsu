<?php
$pageTitle = 'Tambah Pengguna';
require_once __DIR__ . '/../../includes/auth_check.php';
requireAdmin();
$pdo = getConnection();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']); $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $nip = trim($_POST['nip'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $noTelepon = trim($_POST['no_telepon'] ?? '');
    $pdo->prepare("INSERT INTO users (nama, username, password, role, nip, jabatan, no_telepon) VALUES (?, ?, ?, ?, ?, ?, ?)")->execute([$nama, $username, $password, $role, $nip ?: null, $jabatan ?: null, $noTelepon ?: null]);
    logActivity($pdo, $_SESSION['user_id'], 'Tambah User', "Menambah pengguna: $nama ($role)");
    setFlash('success', 'Pengguna berhasil ditambahkan!'); header('Location: index.php'); exit;
}
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header"><div><h2><i class="fas fa-user-plus"></i> Tambah Pengguna</h2></div></div>
<div class="card animate-fadeInUp"><div class="card-body">
    <form method="POST">
        <div class="grid-2">
            <div class="form-group"><label>Nama Lengkap *</label><input type="text" class="form-control" name="nama" required></div>
            <div class="form-group"><label>Username *</label><input type="text" class="form-control" name="username" required></div>
            <div class="form-group"><label>Password *</label><input type="password" class="form-control" name="password" required minlength="6"></div>
            <div class="form-group"><label>Role *</label>
                <select class="form-control" name="role" required id="roleSelect" onchange="toggleGuruFields()">
                    <option value="petugas">Petugas</option>
                    <option value="admin">Admin</option>
                    <option value="guru">Guru / Karyawan</option>
                </select>
            </div>
        </div>
        <div id="guruFields" style="display:none;">
            <hr style="border-color:var(--border-glass); margin:16px 0;">
            <p style="font-size:0.85rem; font-weight:600; color:var(--accent-primary); margin-bottom:12px;"><i class="fas fa-chalkboard-teacher"></i> Data Guru / Karyawan</p>
            <div class="grid-2">
                <div class="form-group"><label>NIP</label><input type="text" class="form-control" name="nip" placeholder="Nomor Induk Pegawai"></div>
                <div class="form-group"><label>Jabatan</label><input type="text" class="form-control" name="jabatan" placeholder="Contoh: Guru Matematika"></div>
                <div class="form-group"><label>No. Telepon</label><input type="text" class="form-control" name="no_telepon" placeholder="08xxxxxxxxxx"></div>
            </div>
        </div>
        <div class="btn-group" style="margin-top:16px;"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn btn-secondary">Batal</a></div>
    </form>
</div></div>
<script>
function toggleGuruFields() {
    document.getElementById('guruFields').style.display = document.getElementById('roleSelect').value === 'guru' ? 'block' : 'none';
}
</script>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>

