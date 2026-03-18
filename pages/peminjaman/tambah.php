<?php
$pageTitle = 'Tambah Peminjaman';
require_once __DIR__ . '/../../includes/auth_check.php';
$pdo = getConnection();

$asetList = $pdo->query("SELECT id, kode_aset, nama_aset FROM aset WHERE deleted_at IS NULL ORDER BY nama_aset")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idAset = intval($_POST['id_aset']);
    $peminjam = trim($_POST['nama_peminjam']);
    $tglPinjam = $_POST['tanggal_pinjam'];
    $tglKembali = $_POST['tanggal_kembali_rencana'];
    $ket = trim($_POST['keterangan']);
    
    $stmt = $pdo->prepare("INSERT INTO peminjaman (id_aset, nama_peminjam, tanggal_pinjam, tanggal_kembali_rencana, keterangan, id_user) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$idAset, $peminjam, $tglPinjam, $tglKembali, $ket, $_SESSION['user_id']]);
    
    $asetNama = $pdo->query("SELECT nama_aset FROM aset WHERE id = $idAset")->fetchColumn();
    logActivity($pdo, $_SESSION['user_id'], 'Peminjaman', "Peminjaman aset: $asetNama oleh $peminjam");
    setFlash('success', 'Peminjaman berhasil dicatat!');
    header('Location: index.php'); exit;
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<div class="page-header"><div><h2><i class="fas fa-plus-circle"></i> Tambah Peminjaman</h2></div></div>
<div class="card animate-fadeInUp"><div class="card-body">
    <form method="POST">
        <div class="grid-2">
            <div class="form-group">
                <label>Pilih Aset *</label>
                <select class="form-control" name="id_aset" required>
                    <option value="">-- Pilih Aset --</option>
                    <?php foreach ($asetList as $a): ?>
                        <option value="<?= $a['id'] ?>">[<?= $a['kode_aset'] ?>] <?= htmlspecialchars($a['nama_aset']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Nama Peminjam *</label><input type="text" class="form-control" name="nama_peminjam" required placeholder="Nama lengkap peminjam"></div>
            <div class="form-group"><label>Tanggal Pinjam *</label><input type="date" class="form-control" name="tanggal_pinjam" value="<?= date('Y-m-d') ?>" required></div>
            <div class="form-group"><label>Rencana Tanggal Kembali *</label><input type="date" class="form-control" name="tanggal_kembali_rencana" required></div>
        </div>
        <div class="form-group"><label>Keterangan</label><textarea class="form-control" name="keterangan" placeholder="Keterangan tambahan..."></textarea></div>
        <div class="btn-group"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn btn-secondary">Batal</a></div>
    </form>
</div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
