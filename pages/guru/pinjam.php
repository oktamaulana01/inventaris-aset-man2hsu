<?php
$pageTitle = 'Ajukan Peminjaman';
require_once __DIR__ . '/../../includes/auth_check.php';
requireGuru();
$pdo = getConnection();

$asetList = $pdo->query("SELECT id, kode_aset, nama_aset FROM aset WHERE deleted_at IS NULL AND kondisi = 'Baik' ORDER BY nama_aset")->fetchAll();

// Pre-select aset jika dari query parameter
$preselectedAset = intval($_GET['id_aset'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idAset = intval($_POST['id_aset']);
    $tglPinjam = $_POST['tanggal_pinjam'];
    $tglKembali = $_POST['tanggal_kembali_rencana'];
    $ket = trim($_POST['keterangan']);
    $namaPeminjam = $_SESSION['user_nama']; // Otomatis dari session
    
    $stmt = $pdo->prepare("INSERT INTO peminjaman (id_aset, nama_peminjam, id_peminjam, tanggal_pinjam, tanggal_kembali_rencana, keterangan, id_user) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$idAset, $namaPeminjam, $_SESSION['user_id'], $tglPinjam, $tglKembali, $ket, $_SESSION['user_id']]);
    
    $asetNama = $pdo->query("SELECT nama_aset FROM aset WHERE id = $idAset")->fetchColumn();
    logActivity($pdo, $_SESSION['user_id'], 'Peminjaman', "Peminjaman aset: $asetNama oleh $namaPeminjam");
    setFlash('success', 'Peminjaman berhasil diajukan! Silakan ambil aset sesuai tanggal pinjam.');
    header('Location: riwayat.php'); exit;
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-hand-holding-hand"></i> Ajukan Peminjaman</h2>
        <div class="breadcrumb">
            <a href="/inventaris-aset-man2hsu/pages/guru/dashboard.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Ajukan Peminjaman</span>
        </div>
    </div>
</div>

<div class="card animate-fadeInUp">
    <div class="card-header">
        <h3><i class="fas fa-clipboard-list" style="color:var(--accent-primary);margin-right:8px;"></i> Form Peminjaman Aset</h3>
    </div>
    <div class="card-body">
        <!-- Info Peminjam -->
        <div class="card mb-4" style="background:rgba(30,114,86,0.04); border-color:rgba(30,114,86,0.15);">
            <div class="card-body" style="padding:16px 20px;">
                <p style="font-size:0.85rem; font-weight:600; color:var(--accent-primary); margin-bottom:8px;">
                    <i class="fas fa-user-circle"></i> Data Peminjam
                </p>
                <div style="display:flex; gap:24px; flex-wrap:wrap; font-size:0.88rem; color:var(--text-secondary);">
                    <span><strong>Nama:</strong> <?= htmlspecialchars($_SESSION['user_nama']) ?></span>
                    <?php if ($_SESSION['user_nip']): ?>
                        <span><strong>NIP:</strong> <?= htmlspecialchars($_SESSION['user_nip']) ?></span>
                    <?php endif; ?>
                    <?php if ($_SESSION['user_jabatan']): ?>
                        <span><strong>Jabatan:</strong> <?= htmlspecialchars($_SESSION['user_jabatan']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <form method="POST">
            <div class="grid-2">
                <div class="form-group">
                    <label><i class="fas fa-box" style="margin-right:4px;"></i> Pilih Aset *</label>
                    <select class="form-control" name="id_aset" required id="selectAset">
                        <option value="">-- Pilih Aset untuk Dipinjam --</option>
                        <?php foreach ($asetList as $a): ?>
                            <option value="<?= $a['id'] ?>" <?= $preselectedAset == $a['id'] ? 'selected' : '' ?>>
                                [<?= $a['kode_aset'] ?>] <?= htmlspecialchars($a['nama_aset']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-info-circle" style="margin-right:4px;"></i> Keperluan / Keterangan</label>
                    <input type="text" class="form-control" name="keterangan" placeholder="Contoh: Untuk pembelajaran di kelas XII-A">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-calendar-check" style="margin-right:4px;"></i> Tanggal Pinjam *</label>
                    <input type="date" class="form-control" name="tanggal_pinjam" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-calendar-xmark" style="margin-right:4px;"></i> Rencana Tanggal Kembali *</label>
                    <input type="date" class="form-control" name="tanggal_kembali_rencana" required min="<?= date('Y-m-d') ?>">
                </div>
            </div>
            <div class="btn-group" style="margin-top:8px;">
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Ajukan Peminjaman</button>
                <a href="dashboard.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
