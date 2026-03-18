<?php
$pageTitle = 'Scan QR Code';
require_once __DIR__ . '/../includes/auth_check.php';
$pdo = getConnection();

// Handle manual search
$result = null;
$error = '';
if (isset($_GET['kode'])) {
    $kode = trim($_GET['kode']);
    $stmt = $pdo->prepare("SELECT a.*, k.nama_kategori, l.nama_lokasi FROM aset a 
        LEFT JOIN kategori k ON a.id_kategori = k.id 
        LEFT JOIN lokasi l ON a.id_lokasi = l.id 
        WHERE a.kode_aset = ? AND a.deleted_at IS NULL");
    $stmt->execute([$kode]);
    $result = $stmt->fetch();
    if (!$result) $error = 'Aset dengan kode "' . htmlspecialchars($kode) . '" tidak ditemukan.';
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-qrcode"></i> Scan QR Code</h2>
        <div class="breadcrumb">
            <a href="/inventaris-aset-man2hsu/pages/dashboard.php">Dashboard</a>
            <span class="separator">/</span>
            <span>Scan QR Code</span>
        </div>
    </div>
</div>

<div class="grid-2">
    <!-- Scanner -->
    <div class="card animate-fadeInUp">
        <div class="card-header">
            <h3><i class="fas fa-camera"></i> Scan dengan Kamera</h3>
        </div>
        <div class="card-body">
            <div id="qr-reader" style="width:100%; border-radius:var(--radius-md); overflow:hidden;"></div>
            <p class="text-muted mt-3 text-center" style="font-size:0.82rem;">
                Arahkan kamera ke QR Code aset untuk melihat detail
            </p>
        </div>
    </div>

    <!-- Manual Input -->
    <div class="card animate-fadeInUp">
        <div class="card-header">
            <h3><i class="fas fa-keyboard"></i> Input Manual</h3>
        </div>
        <div class="card-body">
            <form method="GET" class="search-bar" style="margin-bottom:0;">
                <input type="text" class="search-input" name="kode" placeholder="Masukkan kode aset (contoh: AST-2024-001)" value="<?= htmlspecialchars($_GET['kode'] ?? '') ?>">
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
            </form>
        </div>
    </div>
</div>

<!-- Result -->
<?php if ($error): ?>
<div class="alert alert-danger mt-4">
    <i class="fas fa-exclamation-circle"></i> <?= $error ?>
</div>
<?php endif; ?>

<?php if ($result): ?>
<div class="card mt-4 animate-fadeInUp">
    <div class="card-header">
        <h3><i class="fas fa-check-circle"></i> Aset Ditemukan</h3>
        <a href="/inventaris-aset-man2hsu/pages/aset/detail.php?id=<?= $result['id'] ?>" class="btn btn-sm btn-info">
            <i class="fas fa-eye"></i> Detail Lengkap
        </a>
    </div>
    <div class="card-body">
        <div class="detail-grid">
            <div class="detail-label">Kode Aset</div>
            <div class="detail-value"><span class="badge badge-primary"><?= htmlspecialchars($result['kode_aset']) ?></span></div>
            <div class="detail-label">Nama Aset</div>
            <div class="detail-value" style="font-weight:600;"><?= htmlspecialchars($result['nama_aset']) ?></div>
            <div class="detail-label">Kategori</div>
            <div class="detail-value"><?= htmlspecialchars($result['nama_kategori'] ?? '-') ?></div>
            <div class="detail-label">Lokasi</div>
            <div class="detail-value"><?= htmlspecialchars($result['nama_lokasi'] ?? '-') ?></div>
            <div class="detail-label">Kondisi</div>
            <div class="detail-value">
                <span class="badge badge-<?= $result['kondisi'] === 'Baik' ? 'success' : ($result['kondisi'] === 'Rusak Ringan' ? 'warning' : 'danger') ?>">
                    <?= $result['kondisi'] ?>
                </span>
            </div>
            <div class="detail-label">Jumlah</div>
            <div class="detail-value"><?= $result['jumlah'] ?></div>
            <div class="detail-label">Tahun Perolehan</div>
            <div class="detail-value"><?= $result['tahun_perolehan'] ?? '-' ?></div>
            <div class="detail-label">Nilai</div>
            <div class="detail-value"><?= $result['nilai_perolehan'] ? formatRupiah($result['nilai_perolehan']) : '-' ?></div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- html5-qrcode Library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const qrReader = document.getElementById('qr-reader');
    if (qrReader && typeof Html5Qrcode !== 'undefined') {
        const html5QrCode = new Html5Qrcode("qr-reader");
        html5QrCode.start(
            { facingMode: "environment" },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            (decodedText) => {
                html5QrCode.stop();
                try {
                    const data = JSON.parse(decodedText);
                    if (data.kode) {
                        window.location.href = '?kode=' + encodeURIComponent(data.kode);
                    } else if (data.url) {
                        window.location.href = data.url;
                    }
                } catch(e) {
                    // Try as plain text kode
                    window.location.href = '?kode=' + encodeURIComponent(decodedText);
                }
            },
            (errorMessage) => { /* ignore scan errors */ }
        ).catch(err => {
            qrReader.innerHTML = '<div class="empty-state"><div class="empty-icon">📷</div><p>Kamera tidak tersedia. Gunakan input manual di sebelah kanan.</p></div>';
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
