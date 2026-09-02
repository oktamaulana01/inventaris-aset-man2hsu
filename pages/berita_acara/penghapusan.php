<?php
require_once __DIR__ . '/../../includes/auth_check.php';
requireStaff();
$pdo = getConnection();

$id = intval($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT a.*, 
           k.nama_kategori,
           l.nama_lokasi
    FROM aset a
    LEFT JOIN kategori k ON a.id_kategori = k.id
    LEFT JOIN lokasi l ON a.id_lokasi = l.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die('Data aset tidak ditemukan.');
}

if (!function_exists('tglIndo')) {
    function tglIndo($date) {
        if (!$date || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') return '-';
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $d = date('d', strtotime($date));
        $m = (int)date('m', strtotime($date));
        $y = date('Y', strtotime($date));
        return $d . ' ' . $bulan[$m] . ' ' . $y;
    }
}

if (!function_exists('hariIndo')) {
    function hariIndo($date) {
        $hari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $h = date('l', strtotime($date));
        return $hari[$h] ?? $h;
    }
}

if (!function_exists('formatRupiah')) {
    function formatRupiah($val) {
        return 'Rp ' . number_format((float)$val, 0, ',', '.');
    }
}

$tglHapus = $data['deleted_at'] ?: date('Y-m-d H:i:s');
$nomorBA = 'BA.HAPUS/' . sprintf('%03d', $data['id']) . '/MAN.2.HSU/' . date('Y', strtotime($tglHapus));
$tglHapusText = tglIndo($tglHapus);
$hariHapusText = hariIndo($tglHapus);

// Logo MAN 2 / Kemenag
$logoPath = realpath(__DIR__ . '/../../assets/uploads/logo_km.png');
if (!$logoPath || !file_exists($logoPath)) {
    $logoPath = realpath(__DIR__ . '/../../assets/img/logo.png');
}
$logoSrc = '';
if ($logoPath && file_exists($logoPath)) {
    $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
}

// Foto bukti fisik kerusakan
$buktiSrc = '';
if (!empty($data['bukti_hapus'])) {
    $buktiPath = realpath(__DIR__ . '/../../assets/uploads/bukti_hapus/' . $data['bukti_hapus']);
    if (!$buktiPath || !file_exists($buktiPath)) {
        $buktiPath = realpath(__DIR__ . '/../../assets/uploads/' . $data['bukti_hapus']);
    }
    if ($buktiPath && file_exists($buktiPath)) {
        $ext = strtolower(pathinfo($buktiPath, PATHINFO_EXTENSION));
        $mime = ($ext === 'png') ? 'image/png' : 'image/jpeg';
        $buktiSrc = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($buktiPath));
    }
}

// Download PDF via Dompdf if requested
if (isset($_GET['download']) && $_GET['download'] === 'pdf') {
    require_once __DIR__ . '/../../vendor/autoload.php';
    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $dompdf = new \Dompdf\Dompdf($options);
    
    ob_start();
    include __DIR__ . '/_template_penghapusan.php';
    $htmlContent = ob_get_clean();
    
    $dompdf->loadHtml($htmlContent);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream('Berita_Acara_Penghapusan_' . $data['id'] . '.pdf', ['Attachment' => true]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Penghapusan Aset #<?= $data['kode_aset'] ?> - MAN 2 HSU</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: "Times New Roman", Times, serif; background: #e2e8f0; color: #000; padding: 20px 0; }
        
        .no-print-bar {
            max-width: 210mm;
            margin: 0 auto 15px auto;
            background: #1e293b;
            color: #fff;
            padding: 12px 20px;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #10b981;
            color: #fff;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-family: sans-serif;
            font-size: 13px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-action:hover { background: #059669; }
        .btn-secondary { background: #64748b; }
        .btn-secondary:hover { background: #475569; }
        
        .page-sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #fff;
            padding: 18mm 20mm 18mm 20mm;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            position: relative;
        }
        
        /* Kop Surat */
        .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 2px; }
        .kop-logo { width: 75px; height: auto; }
        .kop-logo-cell { width: 85px; vertical-align: middle; text-align: left; }
        .kop-text-cell { text-align: center; vertical-align: middle; }
        .kop-line1 { font-size: 11pt; font-weight: bold; letter-spacing: 0.5px; }
        .kop-line2 { font-size: 11pt; font-weight: bold; letter-spacing: 0.5px; }
        .kop-line3 { font-size: 12pt; font-weight: bold; margin-top: 1px; }
        .kop-line4 { font-size: 8.5pt; font-style: italic; line-height: 1.25; margin-top: 2px; }
        .kop-divider {
            border-top: 2.5pt solid #000;
            border-bottom: 0.75pt solid #000;
            height: 2pt;
            margin: 4px 0 14px 0;
        }
        
        .ba-title { text-align: center; font-size: 12pt; font-weight: bold; text-decoration: underline; text-transform: uppercase; margin-bottom: 2px; }
        .ba-number { text-align: center; font-size: 10pt; margin-bottom: 14px; }
        
        .ba-content { font-size: 10pt; line-height: 1.4; text-align: justify; }
        .ba-content p { margin-bottom: 6px; text-indent: 28px; }
        .ba-content p.no-indent { text-indent: 0; }
        
        table.table-barang { width: 100%; border-collapse: collapse; margin: 8px 0 10px 0; font-size: 9pt; }
        table.table-barang th, table.table-barang td { border: 1px solid #000; padding: 4px 6px; }
        table.table-barang th { background: #f1f5f9; text-align: center; font-weight: bold; }
        
        .box-bukti {
            border: 1px solid #94a3b8;
            background: #f8fafc;
            border-radius: 6px;
            padding: 8px 12px;
            margin: 8px 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .box-bukti img {
            max-width: 140px;
            max-height: 100px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            object-fit: cover;
        }
        
        .table-ttd { width: 100%; border-collapse: collapse; margin-top: 18px; font-size: 10pt; }
        .table-ttd td { text-align: center; vertical-align: top; width: 50%; padding: 4px; }
        .ttd-space { height: 50px; }
        .ttd-name { font-weight: bold; text-decoration: underline; }
        .ttd-nip { font-size: 9pt; }
        
        @media print {
            body { background: #fff; padding: 0; }
            .no-print-bar { display: none !important; }
            .page-sheet { box-shadow: none; margin: 0; width: 100%; padding: 10mm 15mm; }
        }
    </style>
</head>
<body>

<div class="no-print-bar">
    <div style="font-family:sans-serif; font-size:14px; display:flex; align-items:center; gap:8px;">
        <i class="fas fa-file-contract text-emerald-400"></i>
        <span><strong>Berita Acara Penghapusan Aset</strong> (No: <?= $nomorBA ?>)</span>
    </div>
    <div style="display:flex; gap:10px;">
        <button onclick="window.print()" class="btn-action"><i class="fas fa-print"></i> Cetak / Print</button>
        <a href="?id=<?= $data['id'] ?>&download=pdf" class="btn-action"><i class="fas fa-file-pdf"></i> Unduh PDF</a>
        <a href="<?= BASE_URL ?>/laporan/penghapusan-aset" class="btn-action btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</div>

<div class="page-sheet">
    <!-- Kop Surat Resmi -->
    <table class="kop-table">
        <tr>
            <td class="kop-logo-cell">
                <?php if ($logoSrc): ?>
                    <img src="<?= $logoSrc ?>" class="kop-logo" alt="Logo Kemenag">
                <?php endif; ?>
            </td>
            <td class="kop-text-cell">
                <div class="kop-line1">KEMENTERIAN AGAMA REPUBLIK INDONESIA</div>
                <div class="kop-line2">KANTOR KEMENTERIAN AGAMA KABUPATEN HULU SUNGAI UTARA</div>
                <div class="kop-line3">MADRASAH ALIYAH NEGERI 2 HULU SUNGAI UTARA</div>
                <div class="kop-line4">
                    Jalan Sukmaraga No. 045 Kel. Sungai Malang Kec. Amuntai Tengah 71418<br>
                    Fax./Telp. (0527) 61400 e-mail: man2amuntai@kemenag.go.id
                </div>
            </td>
        </tr>
    </table>
    <div class="kop-divider"></div>

    <!-- Judul Berita Acara -->
    <div class="ba-title">BERITA ACARA PENGHAPUSAN DAN PEMUSNAHAN BARANG INVENTARIS/ASET</div>
    <div class="ba-number">Nomor: <?= $nomorBA ?></div>

    <div class="ba-content">
        <p>Pada hari ini <strong><?= $hariHapusText ?></strong> tanggal <strong><?= $tglHapusText ?></strong>, berdasarkan hasil pemeriksaan fisik dan evaluasi teknis kondisi barang inventaris/aset milik Madrasah Aliyah Negeri 2 Hulu Sungai Utara, Tim Pengelola Sarana dan Prasarana menyatakan bahwa barang berikut telah memenuhi kriteria untuk dihapuskan dari daftar inventaris aktif madrasah:</p>

        <table class="table-barang">
            <thead>
                <tr>
                    <th style="width:25px;">No</th>
                    <th>Kode Aset</th>
                    <th>Nama Barang / Aset</th>
                    <th>Kategori</th>
                    <th>Tahun</th>
                    <th>Nilai Perolehan</th>
                    <th>Sumber Dana</th>
                    <th>Kondisi Terakhir</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align:center;">1</td>
                    <td style="font-family:monospace; font-weight:bold;"><?= htmlspecialchars($data['kode_aset']) ?></td>
                    <td><strong><?= htmlspecialchars($data['nama_aset']) ?></strong></td>
                    <td><?= htmlspecialchars($data['nama_kategori'] ?? '-') ?></td>
                    <td style="text-align:center;"><?= $data['tahun_perolehan'] ?: '-' ?></td>
                    <td style="text-align:right; font-weight:bold;"><?= formatRupiah($data['nilai_perolehan']) ?></td>
                    <td style="text-align:center;"><?= htmlspecialchars($data['sumber_dana'] ?: '-') ?></td>
                    <td style="text-align:center; font-weight:bold; color:#b91c1c;"><?= htmlspecialchars($data['kondisi'] ?: 'Rusak Berat') ?></td>
                </tr>
            </tbody>
        </table>

        <p class="no-indent"><strong>Alasan / Dasar Penghapusan:</strong></p>
        <p style="text-indent:0; background:#fef2f2; padding:6px 10px; border:1px solid #fecaca; border-radius:4px; font-style:italic; margin-bottom:8px; color:#991b1b;">
            "<?= htmlspecialchars($data['alasan_hapus'] ?: 'Barang mengalami kerusakan berat/usang dan biaya perbaikan melebihi nilai ekonomisnya sehingga tidak dapat digunakan kembali untuk kegiatan operasional madrasah.') ?>"
        </p>

        <?php if ($buktiSrc): ?>
        <p class="no-indent"><strong>Bukti Fisik Kerusakan Aset:</strong></p>
        <div class="box-bukti">
            <img src="<?= $buktiSrc ?>" alt="Foto Bukti Kerusakan">
            <div style="font-size:9pt; line-height:1.3; color:#334155;">
                <strong>Keterangan Foto Bukti:</strong><br>
                Foto dokumentasi fisik kondisi barang saat pemeriksaan lapangan oleh tim inventarisasi sarpras MAN 2 HSU.
            </div>
        </div>
        <?php endif; ?>

        <p>Demikian Berita Acara Penghapusan dan Pemusnahan Barang Inventaris ini dibuat dengan sebenarnya dalam rangkap secukupnya untuk dipergunakan sebagai dokumen pertanggungjawaban pengelolaan Barang Milik Negara (BMN) di lingkungan Madrasah Aliyah Negeri 2 Hulu Sungai Utara.</p>
    </div>

    <!-- Tanda Tangan -->
    <table class="table-ttd">
        <tr>
            <td>
                Pengurus Barang / Petugas Sarpras,<br>
                <div class="ttd-space"></div>
                <div class="ttd-name">Petugas Inventaris Sarpras</div>
                <div class="ttd-nip">NIP. .................................................</div>
            </td>
            <td>
                Kepala Urusan Tata Usaha,<br>
                <div class="ttd-space"></div>
                <div class="ttd-name">H. Supian, S.Ag</div>
                <div class="ttd-nip">NIP. 197508142006041012</div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top:16px;">
                Amuntai, <?= $tglHapusText ?><br>
                Menyetujui / Mengetahui,<br>
                <strong>Kepala MAN 2 Hulu Sungai Utara</strong>
                <div class="ttd-space"></div>
                <div class="ttd-name">Drs. H. Khairan Ali, M.M.Pd</div>
                <div class="ttd-nip">NIP. 196805121994031004</div>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
