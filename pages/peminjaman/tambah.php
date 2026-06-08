<?php
$pageTitle = 'Tambah Peminjaman';
require_once __DIR__ . '/../../includes/auth_check.php';
$pdo = getConnection();

$asetList = $pdo->query("
    SELECT a.id, a.kode_aset, a.nama_aset, a.jumlah,
           (SELECT COUNT(*) FROM peminjaman p WHERE p.id_aset = a.id AND p.status = 'Dipinjam') as terpinjam
    FROM aset a 
    WHERE a.deleted_at IS NULL AND a.kondisi = 'Baik'
    ORDER BY a.nama_aset
")->fetchAll();

$lokasiList = $pdo->query("SELECT * FROM lokasi ORDER BY nama_lokasi")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idAset = intval($_POST['id_aset']);
    
    // Validasi apakah stok aset masih ada
    $cekStok = $pdo->prepare("
        SELECT a.jumlah, 
               (SELECT COUNT(*) FROM peminjaman p WHERE p.id_aset = a.id AND p.status = 'Dipinjam') as terpinjam
        FROM aset a WHERE a.id = ?
    ");
    $cekStok->execute([$idAset]);
    $stok = $cekStok->fetch();
    if ($stok && $stok['terpinjam'] >= $stok['jumlah']) {
        setFlash('danger', 'Maaf, semua unit dari aset tersebut saat ini sedang dipinjam oleh orang lain.');
        header('Location: tambah.php'); exit;
    }
    
    $peminjam = trim($_POST['nama_peminjam']);
    $tglPinjam = $_POST['tanggal_pinjam'];
    $tglKembali = $_POST['tanggal_kembali_rencana'];
    $ket = trim($_POST['keterangan']);
    $idLokasi = !empty($_POST['id_lokasi']) ? intval($_POST['id_lokasi']) : null;
    
    $stmt = $pdo->prepare("INSERT INTO peminjaman (id_aset, nama_peminjam, id_lokasi, tanggal_pinjam, tanggal_kembali_rencana, keterangan, id_user) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$idAset, $peminjam, $idLokasi, $tglPinjam, $tglKembali, $ket, $_SESSION['user_id']]);
    
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
                        <?php $sisa = $a['jumlah'] - $a['terpinjam']; ?>
                        <option value="<?= $a['id'] ?>" <?= $sisa <= 0 ? 'disabled' : '' ?>>
                            [<?= $a['kode_aset'] ?>] <?= htmlspecialchars($a['nama_aset']) ?> <?= $sisa <= 0 ? '(Stok Habis Terpinjam)' : "(Tersisa: $sisa)" ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Nama Peminjam *</label><input type="text" class="form-control" name="nama_peminjam" required placeholder="Nama lengkap peminjam"></div>
            <div class="form-group"><label>Tanggal Pinjam *</label><input type="date" class="form-control" name="tanggal_pinjam" value="<?= date('Y-m-d') ?>" required></div>
            <div class="form-group"><label>Rencana Tanggal Kembali *</label><input type="date" class="form-control" name="tanggal_kembali_rencana" required></div>
        </div>
            <div class="form-group">
                <label>Lokasi / Ruangan Penggunaan *</label>
                <select class="form-control" name="id_lokasi" required>
                    <option value="">-- Pilih Ruangan --</option>
                    <?php foreach ($lokasiList as $l): ?>
                        <option value="<?= $l['id'] ?>"><?= htmlspecialchars($l['nama_lokasi']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Keperluan / Keterangan</label><input type="text" class="form-control" name="keterangan" placeholder="Keterangan opsional"></div>
        </div>
        <div class="btn-group" style="margin-top:20px;"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button><a href="index.php" class="btn btn-secondary">Batal</a></div>
    </form>
</div></div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
