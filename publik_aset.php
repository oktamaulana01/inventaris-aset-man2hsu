<?php
/**
 * Halaman Publik Detail Aset
 * Diakses melalui QR Code scan — TANPA perlu login
 * URL: /inventaris-aset-man2hsu/publik_aset.php?kode=AST-2024-001
 */
require_once __DIR__ . '/config/database.php';
$pdo = getConnection();

$kode = trim($_GET['kode'] ?? '');
$aset = null;
$error = false;

if (!empty($kode)) {
    $stmt = $pdo->prepare("SELECT a.*, k.nama_kategori, l.nama_lokasi FROM aset a 
        LEFT JOIN kategori k ON a.id_kategori = k.id 
        LEFT JOIN lokasi l ON a.id_lokasi = l.id 
        WHERE a.kode_aset = ? AND a.deleted_at IS NULL");
    $stmt->execute([$kode]);
    $aset = $stmt->fetch();
}

if (!$aset) {
    $error = true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $aset ? htmlspecialchars($aset['nama_aset']) . ' — ' : 'Aset Tidak Ditemukan — ' ?>Inventaris MAN 2 HSU</title>
    <meta name="description" content="<?= $aset ? 'Detail aset: ' . htmlspecialchars($aset['nama_aset']) : 'Halaman detail aset inventaris' ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(145deg, #e8f5e9 0%, #f0f5f1 30%, #e3f2fd 100%);
            min-height: 100vh;
            color: #1a2e22;
            padding: 16px;
        }

        .container {
            max-width: 560px;
            margin: 0 auto;
        }

        /* ── Header Branding ── */
        .brand-header {
            text-align: center;
            padding: 24px 16px 20px;
            margin-bottom: 16px;
        }
        .brand-logo {
            width: 56px; height: 56px;
            background: linear-gradient(135deg, #1e7256, #28956e);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 12px;
            box-shadow: 0 4px 16px rgba(30, 114, 86, 0.3);
        }
        .brand-header h1 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e7256;
            margin-bottom: 4px;
        }
        .brand-header p {
            font-size: 0.78rem;
            color: #7a9485;
            font-weight: 500;
        }

        /* ── Card ── */
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 16px;
            border: 1px solid rgba(224, 231, 227, 0.6);
            animation: fadeUp 0.5s ease;
        }
        .card-img {
            width: 100%;
            aspect-ratio: 4/3;
            object-fit: cover;
            display: block;
            background: #f0f5f1;
        }
        .card-no-img {
            width: 100%;
            aspect-ratio: 4/3;
            background: linear-gradient(145deg, #e8f5e9, #f0f5f1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #7a9485;
        }
        .card-no-img i { font-size: 3rem; margin-bottom: 12px; opacity: 0.5; }
        .card-no-img span { font-size: 0.85rem; font-weight: 500; }

        .card-body { padding: 20px 20px 24px; }

        /* ── Asset Title ── */
        .asset-code {
            display: inline-block;
            background: linear-gradient(135deg, #1e7256, #28956e);
            color: #fff;
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 10px;
        }
        .asset-name {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1a2e22;
            line-height: 1.3;
            margin-bottom: 16px;
        }

        /* ── Detail Grid ── */
        .detail-list { list-style: none; }
        .detail-list li {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 12px 0;
            border-bottom: 1px solid #f0f5f1;
            font-size: 0.88rem;
        }
        .detail-list li:last-child { border-bottom: none; }
        .detail-list .label {
            color: #7a9485;
            font-weight: 500;
            flex-shrink: 0;
            margin-right: 12px;
        }
        .detail-list .label i {
            width: 18px;
            text-align: center;
            margin-right: 6px;
            color: #1e7256;
            font-size: 0.8rem;
        }
        .detail-list .value {
            text-align: right;
            font-weight: 600;
            color: #1a2e22;
            word-break: break-word;
        }

        /* ── Badges ── */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger  { background: #fee2e2; color: #991b1b; }

        /* ── Footer ── */
        .footer-note {
            text-align: center;
            padding: 16px;
            font-size: 0.72rem;
            color: #7a9485;
            line-height: 1.5;
        }
        .footer-note a { color: #1e7256; text-decoration: none; font-weight: 600; }

        /* ── Error Page ── */
        .error-card {
            text-align: center;
            padding: 48px 24px;
        }
        .error-card i {
            font-size: 3.5rem;
            color: #ef4444;
            margin-bottom: 16px;
            opacity: 0.6;
        }
        .error-card h2 {
            font-size: 1.2rem;
            color: #1a2e22;
            margin-bottom: 8px;
        }
        .error-card p {
            font-size: 0.88rem;
            color: #7a9485;
            line-height: 1.5;
        }

        /* ── Animation ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
    </style>
</head>
<body>

<div class="container">

    <!-- Branding -->
    <div class="brand-header">
        <div class="brand-logo"><i class="fas fa-school"></i></div>
        <h1>MAN 2 Hulu Sungai Utara</h1>
        <p>Sistem Informasi Inventarisasi Aset</p>
    </div>

    <?php if ($error): ?>
    <!-- Error: Aset Tidak Ditemukan -->
    <div class="card">
        <div class="error-card">
            <i class="fas fa-exclamation-triangle"></i>
            <h2>Aset Tidak Ditemukan</h2>
            <p>Kode aset <strong>"<?= htmlspecialchars($kode) ?>"</strong> tidak ditemukan dalam sistem atau sudah dihapus.</p>
        </div>
    </div>

    <?php else: ?>
    <!-- Gambar Aset -->
    <div class="card">
        <?php if ($aset['gambar']): ?>
            <img src="<?= BASE_URL ?>/assets/uploads/<?= htmlspecialchars($aset['gambar']) ?>" 
                 alt="<?= htmlspecialchars($aset['nama_aset']) ?>" class="card-img">
        <?php else: ?>
            <div class="card-no-img">
                <i class="fas fa-cube"></i>
                <span>Tidak ada gambar</span>
            </div>
        <?php endif; ?>

        <div class="card-body">
            <span class="asset-code"><?= htmlspecialchars($aset['kode_aset']) ?></span>
            <h2 class="asset-name"><?= htmlspecialchars($aset['nama_aset']) ?></h2>

            <ul class="detail-list">
                <li>
                    <span class="label"><i class="fas fa-tags"></i> Kategori</span>
                    <span class="value"><?= htmlspecialchars($aset['nama_kategori'] ?? '-') ?></span>
                </li>
                <li>
                    <span class="label"><i class="fas fa-map-marker-alt"></i> Lokasi</span>
                    <span class="value"><?= htmlspecialchars($aset['nama_lokasi'] ?? '-') ?></span>
                </li>
                <li>
                    <span class="label"><i class="fas fa-heart-pulse"></i> Kondisi</span>
                    <span class="value">
                        <?php
                        $kondisiBadge = match($aset['kondisi']) {
                            'Baik' => 'badge-success',
                            'Rusak Ringan' => 'badge-warning',
                            'Rusak Berat' => 'badge-danger',
                            default => 'badge-success'
                        };
                        ?>
                        <span class="badge <?= $kondisiBadge ?>"><?= $aset['kondisi'] ?></span>
                    </span>
                </li>
                <li>
                    <span class="label"><i class="fas fa-layer-group"></i> Jumlah</span>
                    <span class="value"><?= $aset['jumlah'] ?> unit</span>
                </li>
                <?php if ($aset['tahun_perolehan']): ?>
                <li>
                    <span class="label"><i class="fas fa-calendar"></i> Tahun Perolehan</span>
                    <span class="value"><?= $aset['tahun_perolehan'] ?></span>
                </li>
                <?php endif; ?>
                <?php if ($aset['nilai_perolehan'] && $aset['nilai_perolehan'] > 0): ?>
                <li>
                    <span class="label"><i class="fas fa-money-bill-wave"></i> Nilai</span>
                    <span class="value"><?= formatRupiah($aset['nilai_perolehan']) ?></span>
                </li>
                <?php endif; ?>
                <?php if ($aset['sumber_dana']): ?>
                <li>
                    <span class="label"><i class="fas fa-wallet"></i> Sumber Dana</span>
                    <span class="value"><?= htmlspecialchars($aset['sumber_dana']) ?></span>
                </li>
                <?php endif; ?>
                <?php if ($aset['keterangan']): ?>
                <li>
                    <span class="label"><i class="fas fa-sticky-note"></i> Keterangan</span>
                    <span class="value"><?= htmlspecialchars($aset['keterangan']) ?></span>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <div class="footer-note">
        &copy; <?= date('Y') ?> <a href="<?= BASE_URL ?>">Sistem Inventarisasi Aset</a><br>
        MAN 2 Hulu Sungai Utara
    </div>

</div>

</body>
</html>
