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
if (!$aset) { header('Location: index.php'); exit; }

// Generate QR Code using chillerlan/php-qrcode (offline, no external API)
$qrDir = __DIR__ . '/../../qrcodes/';
if (!is_dir($qrDir)) {
    mkdir($qrDir, 0755, true);
}

$qrData = json_encode([
    'kode' => $aset['kode_aset'],
    'nama' => $aset['nama_aset'],
    'kategori' => $aset['nama_kategori'] ?? '-',
    'lokasi' => $aset['nama_lokasi'] ?? '-',
    'kondisi' => $aset['kondisi'],
    'url' => 'http://localhost/inventaris-aset-man2hsu/pages/aset/detail.php?id=' . $aset['id']
]);

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

<div class="page-header">
    <div>
        <h2><i class="fas fa-qrcode"></i> QR Code Aset</h2>
        <div class="breadcrumb">
            <a href="/inventaris-aset-man2hsu/pages/dashboard.php">Dashboard</a>
            <span class="separator">/</span>
            <a href="/inventaris-aset-man2hsu/pages/aset/index.php">Data Aset</a>
            <span class="separator">/</span>
            <span>QR Code</span>
        </div>
    </div>
    <a href="index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

<div class="grid-2">
    <!-- QR Code Display -->
    <div class="card animate-fadeInUp">
        <div class="card-header">
            <h3><i class="fas fa-qrcode"></i> QR Code</h3>
            <button onclick="printQRCode('<?= htmlspecialchars($aset['kode_aset']) ?>')" class="btn btn-sm btn-primary no-print">
                <i class="fas fa-print"></i> Cetak QR Code
            </button>
        </div>
        <div class="card-body">
            <div class="qr-container" id="qr-print-area">
                <img src="/inventaris-aset-man2hsu/qrcodes/<?= $qrFilename ?>?t=<?= time() ?>" alt="QR Code <?= htmlspecialchars($aset['kode_aset']) ?>">
                <div class="qr-info">
                    <h3 style="font-size:1.1rem; color:var(--text-primary);"><?= htmlspecialchars($aset['kode_aset']) ?></h3>
                    <p><?= htmlspecialchars($aset['nama_aset']) ?></p>
                    <p style="font-size:0.75rem; margin-top:4px;">MAN 2 Hulu Sungai Utara</p>
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
                <a href="detail.php?id=<?= $aset['id'] ?>" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Lihat Detail Lengkap</a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
