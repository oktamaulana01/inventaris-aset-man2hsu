<?php
$pageTitle = 'Tambah Mutasi Aset';
require_once __DIR__ . '/../../includes/auth_check.php';
requireStaff();
$pdo = getConnection();

$selectedAsetId = intval($_GET['id_aset'] ?? 0);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrfToken();
    
    $idAset = intval($_POST['id_aset'] ?? 0);
    $idLokasiTujuan = intval($_POST['id_lokasi_tujuan'] ?? 0);
    $tglMutasi = $_POST['tanggal_mutasi'] ?? date('Y-m-d');
    $keterangan = trim($_POST['keterangan'] ?? '');

    // Validasi
    if ($idAset <= 0) {
        $errors[] = 'Pilih aset yang akan dimutasi.';
    }
    
    // Check asset data
    $stmtAset = $pdo->prepare("SELECT a.*, l.nama_lokasi FROM aset a LEFT JOIN lokasi l ON a.id_lokasi = l.id WHERE a.id = ? AND a.deleted_at IS NULL");
    $stmtAset->execute([$idAset]);
    $aset = $stmtAset->fetch();

    if (!$aset) {
        $errors[] = 'Data aset tidak ditemukan.';
    } elseif ($aset['status_penghapusan'] === 'pending') {
        $errors[] = 'Aset ini sedang dalam proses pengajuan penghapusan dan tidak dapat dimutasi.';
    }

    if ($idLokasiTujuan <= 0) {
        $errors[] = 'Pilih lokasi tujuan baru.';
    } elseif ($aset && $idLokasiTujuan == $aset['id_lokasi']) {
        $errors[] = 'Lokasi tujuan harus berbeda dengan lokasi asal saat ini.';
    }

    if (empty($errors)) {
        $idLokasiAsal = $aset['id_lokasi'];

        // Transaction
        $pdo->beginTransaction();
        try {
            // Insert mutasi_aset record
            $stmtInsert = $pdo->prepare("
                INSERT INTO mutasi_aset (id_aset, id_lokasi_asal, id_lokasi_tujuan, tanggal_mutasi, keterangan, id_user)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmtInsert->execute([$idAset, $idLokasiAsal, $idLokasiTujuan, $tglMutasi, $keterangan ?: null, $_SESSION['user_id']]);

            // Update aset current location
            $stmtUpdateAset = $pdo->prepare("UPDATE aset SET id_lokasi = ? WHERE id = ?");
            $stmtUpdateAset->execute([$idLokasiTujuan, $idAset]);

            // Get target location name for log
            $stmtTujuan = $pdo->prepare("SELECT nama_lokasi FROM lokasi WHERE id = ?");
            $stmtTujuan->execute([$idLokasiTujuan]);
            $namaTujuan = $stmtTujuan->fetchColumn() ?: 'Lokasi Baru';

            logActivity($pdo, $_SESSION['user_id'], 'Mutasi Aset', "Mutasi aset {$aset['nama_aset']} ({$aset['kode_aset']}) dari " . ($aset['nama_lokasi'] ?? 'Tanpa Lokasi') . " ke $namaTujuan");

            // Telegram Notification
            require_once __DIR__ . '/../../config/mailer.php';
            $msg = "🔄 <b>Mutasi Aset Sekolah</b>\n\n" .
                   "Aset: <b>" . htmlspecialchars($aset['nama_aset']) . " (" . htmlspecialchars($aset['kode_aset']) . ")</b>\n" .
                   "Dari Ruangan: <b>" . htmlspecialchars($aset['nama_lokasi'] ?? 'Tanpa Lokasi') . "</b>\n" .
                   "Ke Ruangan: <b>" . htmlspecialchars($namaTujuan) . "</b>\n" .
                   "Tanggal Mutasi: " . date('d/m/Y', strtotime($tglMutasi)) . "\n" .
                   "Alasan: " . htmlspecialchars($keterangan ?: '-') . "\n\n" .
                   "Petugas: " . htmlspecialchars($_SESSION['user_nama']);
            sendTelegramNotification($pdo, $msg);

            $pdo->commit();
            setFlash('success', "Mutasi lokasi aset {$aset['nama_aset']} berhasil dilakukan!");
            header('Location: ' . BASE_URL . '/mutasi');
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Gagal menyimpan transaksi mutasi: ' . $e->getMessage();
        }
    }
}

// Fetch all active assets with location info
$asetList = $pdo->query("
    SELECT a.id, a.kode_aset, a.nama_aset, a.id_lokasi, l.nama_lokasi
    FROM aset a
    LEFT JOIN lokasi l ON a.id_lokasi = l.id
    WHERE a.deleted_at IS NULL
    ORDER BY a.nama_aset ASC
")->fetchAll();

// Fetch all locations
$lokasiList = $pdo->query("SELECT id, nama_lokasi FROM lokasi ORDER BY nama_lokasi ASC")->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-arrows-split-up-and-left"></i> Tambah Mutasi Aset</h2>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>/dashboard">Dashboard</a>
            <span class="separator">/</span>
            <a href="<?= BASE_URL ?>/mutasi">Mutasi Aset</a>
            <span class="separator">/</span>
            <span>Tambah Mutasi</span>
        </div>
    </div>
</div>

<div class="card animate-fadeInUp" style="max-width: 800px;">
    <div class="card-header">
        <h3><i class="fas fa-right-left" style="color:var(--accent-primary);margin-right:8px;"></i> Form Mutasi Perpindahan Ruangan</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger mb-4">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <?php foreach ($errors as $err): ?>
                        <div><?= htmlspecialchars($err) ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" id="form-mutasi">
            <?= generateCsrfToken() ?>

            <!-- Pilih Aset -->
            <div class="form-group mb-3">
                <label><i class="fas fa-box" style="margin-right:4px;"></i> Pilih Aset <span style="color:red;">*</span></label>
                <select class="form-control" name="id_aset" id="select-aset" required onchange="updateLokasiAsal()">
                    <option value="">-- Pilih Aset --</option>
                    <?php foreach ($asetList as $a): ?>
                        <option value="<?= $a['id'] ?>" 
                                data-lokasi-id="<?= $a['id_lokasi'] ?? 0 ?>" 
                                data-lokasi-nama="<?= htmlspecialchars($a['nama_lokasi'] ?? 'Belum ada lokasi') ?>"
                                <?= ($selectedAsetId == $a['id'] || (isset($_POST['id_aset']) && $_POST['id_aset'] == $a['id'])) ? 'selected' : '' ?>>
                            [<?= htmlspecialchars($a['kode_aset']) ?>] <?= htmlspecialchars($a['nama_aset']) ?> — (Saat ini di: <?= htmlspecialchars($a['nama_lokasi'] ?? 'Tanpa Lokasi') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Info Lokasi Asal -->
            <div class="card mb-4" id="box-lokasi-asal" style="background:rgba(30,114,86,0.04); border-color:rgba(30,114,86,0.15);">
                <div class="card-body" style="padding: 14px 18px;">
                    <div style="font-size:0.85rem; color:var(--text-secondary);">
                        <i class="fas fa-location-dot" style="color:var(--accent-primary); margin-right:6px;"></i> 
                        Lokasi Asal Saat Ini: <strong id="text-lokasi-asal" style="color:var(--text-primary); font-size:0.95rem;">-</strong>
                    </div>
                </div>
            </div>

            <div class="grid-2">
                <!-- Lokasi Tujuan -->
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt" style="margin-right:4px;"></i> Lokasi Tujuan Baru <span style="color:red;">*</span></label>
                    <select class="form-control" name="id_lokasi_tujuan" id="select-lokasi-tujuan" required>
                        <option value="">-- Pilih Lokasi Tujuan --</option>
                        <?php foreach ($lokasiList as $l): ?>
                            <option value="<?= $l['id'] ?>" <?= (isset($_POST['id_lokasi_tujuan']) && $_POST['id_lokasi_tujuan'] == $l['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($l['nama_lokasi']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tanggal Mutasi -->
                <div class="form-group">
                    <label><i class="fas fa-calendar" style="margin-right:4px;"></i> Tanggal Mutasi <span style="color:red;">*</span></label>
                    <input type="date" class="form-control" name="tanggal_mutasi" value="<?= htmlspecialchars($_POST['tanggal_mutasi'] ?? date('Y-m-d')) ?>" required>
                </div>
            </div>

            <!-- Keterangan -->
            <div class="form-group mt-3">
                <label><i class="fas fa-sticky-note" style="margin-right:4px;"></i> Keterangan / Alasan Mutasi</label>
                <textarea class="form-control" name="keterangan" rows="3" placeholder="Contoh: Pemindahan unit untuk keperluan laboratorium baru, peremajaan fasilitas kelas, dsb."><?= htmlspecialchars($_POST['keterangan'] ?? '') ?></textarea>
            </div>

            <div class="btn-group mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Mutasi</button>
                <a href="<?= BASE_URL ?>/mutasi" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
function updateLokasiAsal() {
    const select = document.getElementById('select-aset');
    const selectedOption = select.options[select.selectedIndex];
    const textAsal = document.getElementById('text-lokasi-asal');
    
    if (selectedOption && selectedOption.value) {
        const namaLokasi = selectedOption.getAttribute('data-lokasi-nama');
        textAsal.textContent = namaLokasi;
    } else {
        textAsal.textContent = '-';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    updateLokasiAsal();
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
