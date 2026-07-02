<?php
$pageTitle = 'Generate QR Code';
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../includes/auth_check.php';
$pdo = getConnection();

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT a.*, k.nama_kategori, l.nama_lokasi FROM aset a 
    LEFT JOIN kategori k ON a.id_kategori = k.id 
    LEFT JOIN lokasi l ON a.id_lokasi = l.id 
    WHERE a.id = ? AND a.deleted_at IS NULL");
$stmt->execute([$id]);
$aset = $stmt->fetch();
if (!$aset) { header('Location: ' . BASE_URL . '/aset'); exit; }

// Generate QR Code using chillerlan/php-qrcode (offline, no external API)
$qrDir = __DIR__ . '/../../qrcodes/';
if (!is_dir($qrDir)) {
    mkdir($qrDir, 0755, true);
}

// QR Code berisi URL ke halaman publik detail aset
// Saat discan → langsung buka halaman web dengan gambar & detail aset
$qrData = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE_URL . '/publik_aset.php?kode=' . urlencode($aset['kode_aset']);

$qrFilename = 'qr_' . $aset['kode_aset'] . '.png';
$qrPath = $qrDir . $qrFilename;

// Generate QR Code as PNG
$options = new QROptions([
    'outputType'   => QRCode::OUTPUT_IMAGE_PNG,
    'scale'        => 10,
    'imageBase64'  => false,
]);

$qrImage = (new QRCode($options))->render($qrData);
file_put_contents($qrPath, $qrImage);

// Update database with QR Code path
$pdo->prepare("UPDATE aset SET qr_code_path = ? WHERE id = ?")->execute([$qrFilename, $id]);
logActivity($pdo, $_SESSION['user_id'], 'Generate QR', "Generate QR Code untuk: {$aset['nama_aset']} ({$aset['kode_aset']})");

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<script src="<?= BASE_URL ?>/assets/js/JsBarcode.all.min.js"></script>

<div class="page-header">
    <div>
        <h2><i class="fas fa-qrcode"></i> Label Barcode Aset</h2>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>/pages/dashboard.php">Dashboard</a>
            <span class="separator">/</span>
            <a href="<?= BASE_URL ?>/aset">Data Aset</a>
            <span class="separator">/</span>
            <span>Label Barcode</span>
        </div>
    </div>
    <a href="<?= BASE_URL ?>/aset" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<div class="grid-2">
    <!-- Barcode Display -->
    <div class="card animate-fadeInUp">
        <div class="card-header">
            <h3><i class="fas fa-barcode"></i> Label Code</h3>
            <button onclick="printQRCode('<?= htmlspecialchars($aset['kode_aset']) ?>')" class="btn btn-sm btn-primary no-print">
                <i class="fas fa-print"></i> Cetak Label
            </button>
        </div>
        <div class="card-body">
            <!-- Pilihan Tipe Barcode -->
            <div class="no-print" style="display: flex; gap: 10px; margin-bottom: 20px; border-bottom: 1px solid var(--border-color); padding-bottom: 15px;">
                <button type="button" class="btn btn-sm btn-primary" id="btn-qr" onclick="switchBarcode('qr')">
                    <i class="fas fa-qrcode"></i> QR Code (Link Detail)
                </button>
                <button type="button" class="btn btn-sm btn-secondary" id="btn-bar" onclick="switchBarcode('bar')">
                    <i class="fas fa-barcode"></i> Barcode Garis (Kode Aset)
                </button>
            </div>

            <div class="qr-container" id="qr-print-area">
                <!-- QR Code View -->
                <div id="qr-view" style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 150px;">
                    <img src="<?= BASE_URL ?>/qrcodes/<?= $qrFilename ?>?t=<?= time() ?>" alt="QR Code <?= htmlspecialchars($aset['kode_aset']) ?>" style="max-width: 150px; height: auto;">
                </div>
                <!-- Barcode Garis View -->
                <div id="bar-view" style="display: none; flex-direction: column; align-items: center; justify-content: center; min-height: 150px; padding: 10px 0;">
                    <svg id="barcode-elem" style="max-width: 100%;"></svg>
                </div>
                
                <div class="qr-info" style="text-align: center; margin-top: 15px;">
                    <h3 style="font-size:1.1rem; color:var(--text-primary); margin: 5px 0;"><?= htmlspecialchars($aset['kode_aset']) ?></h3>
                    <p style="margin: 3px 0; color: var(--text-secondary);"><?= htmlspecialchars($aset['nama_aset']) ?></p>
                    <p style="font-size:0.75rem; margin-top:4px; color: var(--text-muted);">MAN 2 Hulu Sungai Utara</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Asset Info -->
    <div class="card animate-fadeInUp">
        <div class="card-header">
            <h3><i class="fas fa-info-circle"></i> Informasi Aset</h3>
        </div>
        <div class="card-body">
            <div class="detail-grid">
                <div class="detail-label">Kode Aset</div>
                <div class="detail-value"><span class="badge badge-primary"><?= htmlspecialchars($aset['kode_aset']) ?></span></div>
                <div class="detail-label">Nama Aset</div>
                <div class="detail-value"><?= htmlspecialchars($aset['nama_aset']) ?></div>
                <div class="detail-label">Kategori</div>
                <div class="detail-value"><?= htmlspecialchars($aset['nama_kategori'] ?? '-') ?></div>
                <div class="detail-label">Lokasi</div>
                <div class="detail-value"><?= htmlspecialchars($aset['nama_lokasi'] ?? '-') ?></div>
                <div class="detail-label">Kondisi</div>
                <div class="detail-value">
                    <span class="badge badge-<?= $aset['kondisi'] === 'Baik' ? 'success' : ($aset['kondisi'] === 'Rusak Ringan' ? 'warning' : 'danger') ?>">
                        <?= $aset['kondisi'] ?>
                    </span>
                </div>
                <div class="detail-label">Jumlah</div>
                <div class="detail-value"><?= $aset['jumlah'] ?></div>
            </div>
            <div class="mt-4">
                <a href="<?= BASE_URL ?>/aset/detail?id=<?= $aset['id'] ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Lihat Detail Lengkap</a>
            </div>
        </div>
    </div>
</div>

<script>
function switchBarcode(type) {
    const qrView = document.getElementById('qr-view');
    const barView = document.getElementById('bar-view');
    const btnQr = document.getElementById('btn-qr');
    const btnBar = document.getElementById('btn-bar');

    if (type === 'qr') {
        qrView.style.display = 'flex';
        barView.style.display = 'none';
        btnQr.classList.remove('btn-secondary');
        btnQr.classList.add('btn-primary');
        btnBar.classList.remove('btn-primary');
        btnBar.classList.add('btn-secondary');
    } else {
        qrView.style.display = 'none';
        barView.style.display = 'flex';
        btnQr.classList.remove('btn-primary');
        btnQr.classList.add('btn-secondary');
        btnBar.classList.remove('btn-secondary');
        btnBar.classList.add('btn-primary');
        
        // Render/update barcode
        JsBarcode("#barcode-elem", "<?= htmlspecialchars($aset['kode_aset']) ?>", {
            format: "CODE128",
            width: 2,
            height: 70,
            displayValue: false
        });
    }
}

// Render barcode on load so it's ready if toggled or printed
document.addEventListener("DOMContentLoaded", function() {
    JsBarcode("#barcode-elem", "<?= htmlspecialchars($aset['kode_aset']) ?>", {
        format: "CODE128",
        width: 2,
        height: 70,
        displayValue: false
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
