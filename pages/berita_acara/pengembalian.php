<?php
require_once __DIR__ . '/../../includes/auth_check.php';
requireStaff();
$pdo = getConnection();

$id = intval($_GET['id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT p.*, 
           a.nama_aset, a.kode_aset, a.kondisi as kondisi_awal, a.jumlah as stok_aset,
           k.nama_kategori,
           l.nama_lokasi as lokasi_awal,
           u.nama as nama_guru, u.nip as nip_guru, u.jabatan as jabatan_guru, u.no_telepon as telp_guru,
           petugas.nama as nama_petugas, petugas.jabatan as jabatan_petugas
    FROM peminjaman p
    JOIN aset a ON p.id_aset = a.id
    LEFT JOIN kategori k ON a.id_kategori = k.id
    LEFT JOIN lokasi l ON a.id_lokasi = l.id
    LEFT JOIN users u ON p.id_peminjam = u.id
    LEFT JOIN users petugas ON p.id_user = petugas.id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die('Data peminjaman/pengembalian tidak ditemukan.');
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

$tglKembali = $data['tanggal_kembali_aktual'] ?: date('Y-m-d');
$nomorBA = 'BA.KEMBALI/' . sprintf('%03d', $data['id']) . '/MAN.2.HSU/' . date('Y', strtotime($tglKembali));
$tglKembaliText = tglIndo($tglKembali);
$hariKembaliText = hariIndo($tglKembali);
$tglPinjamText = tglIndo($data['tanggal_pinjam']);
$tglRencanaText = tglIndo($data['tanggal_kembali_rencana']);

// Logo MAN 2 / Kemenag
$logoPath = realpath(__DIR__ . '/../../assets/uploads/logo_km.png');
if (!$logoPath || !file_exists($logoPath)) {
    $logoPath = realpath(__DIR__ . '/../../assets/img/logo.png');
}
$logoSrc = '';
if ($logoPath && file_exists($logoPath)) {
    $logoSrc = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
}

// Download PDF via Dompdf if requested
if (isset($_GET['download']) && $_GET['download'] === 'pdf') {
    require_once __DIR__ . '/../../vendor/autoload.php';
    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $dompdf = new \Dompdf\Dompdf($options);
    
    ob_start();
    include __DIR__ . '/_template_pengembalian.php';
    $htmlContent = ob_get_clean();
    
    $dompdf->loadHtml($htmlContent);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream('Berita_Acara_Pengembalian_' . $data['id'] . '.pdf', ['Attachment' => true]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Pengembalian Aset #<?= $data['id'] ?> - MAN 2 HSU</title>
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
            padding: 20mm 20mm 20mm 20mm;
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
            margin: 4px 0 16px 0;
        }
        
        .ba-title { text-align: center; font-size: 12pt; font-weight: bold; text-decoration: underline; text-transform: uppercase; margin-bottom: 2px; }
        .ba-number { text-align: center; font-size: 10pt; margin-bottom: 16px; }
        
        .ba-content { font-size: 10pt; line-height: 1.45; text-align: justify; }
        .ba-content p { margin-bottom: 8px; text-indent: 28px; }
        .ba-content p.no-indent { text-indent: 0; }
        
        .table-pihak { width: 100%; border-collapse: collapse; margin: 4px 0 8px 18px; font-size: 10pt; }
        .table-pihak td { padding: 2px 4px; vertical-align: top; }
        .table-pihak td.lbl { width: 140px; }
        .table-pihak td.sep { width: 10px; }
        
        table.table-barang { width: 100%; border-collapse: collapse; margin: 10px 0 12px 0; font-size: 9.5pt; }
        table.table-barang th, table.table-barang td { border: 1px solid #000; padding: 5px 6px; }
        table.table-barang th { background: #f1f5f9; text-align: center; font-weight: bold; }
        
        .table-ttd { width: 100%; border-collapse: collapse; margin-top: 25px; font-size: 10pt; }
        .table-ttd td { text-align: center; vertical-align: top; width: 50%; padding: 4px; }
        .ttd-space { height: 60px; }
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
        <span><strong>Berita Acara Pengembalian Aset</strong> (No: <?= $nomorBA ?>)</span>
    </div>
    <div style="display:flex; gap:10px;">
        <button onclick="window.print()" class="btn-action"><i class="fas fa-print"></i> Cetak / Print</button>
        <a href="?id=<?= $data['id'] ?>&download=pdf" class="btn-action"><i class="fas fa-file-pdf"></i> Unduh PDF</a>
        <a href="<?= BASE_URL ?>/peminjaman" class="btn-action btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
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
    <div class="ba-title">BERITA ACARA PENGEMBALIAN DAN PEMERIKSAAN FISIK ASET</div>
    <div class="ba-number">Nomor: <?= $nomorBA ?></div>

    <div class="ba-content">
        <p>Pada hari ini <strong><?= $hariKembaliText ?></strong> tanggal <strong><?= $tglKembaliText ?></strong>, bertempat di Madrasah Aliyah Negeri 2 Hulu Sungai Utara, telah dilakukan proses serah terima pengembalian dan pemeriksaan kondisi fisik barang inventaris madrasah oleh pihak-pihak sebagai berikut:</p>

        <table class="table-pihak">
            <tr>
                <td class="lbl">1. Nama</td>
                <td class="sep">:</td>
                <td><strong><?= htmlspecialchars($data['nama_guru'] ?? $data['nama_peminjam']) ?></strong></td>
            </tr>
            <tr>
                <td class="lbl">&nbsp;&nbsp;&nbsp;&nbsp;NIP</td>
                <td class="sep">:</td>
                <td><?= htmlspecialchars($data['nip_guru'] ?: '-') ?></td>
            </tr>
            <tr>
                <td class="lbl">&nbsp;&nbsp;&nbsp;&nbsp;Jabatan</td>
                <td class="sep">:</td>
                <td><?= htmlspecialchars($data['jabatan_guru'] ?: 'Guru / Tenaga Pendidik') ?></td>
            </tr>
            <tr>
                <td colspan="3" style="padding-top:4px; font-style:italic;">Selaku peminjam yang menyerahkan kembali barang, selanjutnya disebut <strong>PIHAK PERTAMA</strong>.</td>
            </tr>
        </table>

        <table class="table-pihak" style="margin-top:6px;">
            <tr>
                <td class="lbl">2. Nama</td>
                <td class="sep">:</td>
                <td><strong><?= htmlspecialchars($data['nama_petugas'] ?? 'Petugas Sarana & Prasarana') ?></strong></td>
            </tr>
            <tr>
                <td class="lbl">&nbsp;&nbsp;&nbsp;&nbsp;Jabatan</td>
                <td class="sep">:</td>
                <td><?= htmlspecialchars($data['jabatan_petugas'] ?? 'Petugas Sarpras MAN 2 HSU') ?></td>
            </tr>
            <tr>
                <td class="lbl">&nbsp;&nbsp;&nbsp;&nbsp;Unit Kerja</td>
                <td class="sep">:</td>
                <td>MAN 2 Hulu Sungai Utara</td>
            </tr>
            <tr>
                <td colspan="3" style="padding-top:4px; font-style:italic;">Selaku petugas pengelola barang yang menerima dan memeriksa, selanjutnya disebut <strong>PIHAK KEDUA</strong>.</td>
            </tr>
        </table>

        <p style="margin-top:8px;">PIHAK PERTAMA telah menyerahkan kembali barang inventaris yang sebelumnya dipinjam sejak tanggal <strong><?= $tglPinjamText ?></strong>, dan PIHAK KEDUA telah melakukan pemeriksaan kondisi fisik terhadap barang tersebut dengan hasil rincian sebagai berikut:</p>

        <table class="table-barang">
            <thead>
                <tr>
                    <th style="width:30px;">No</th>
                    <th>Kode Aset</th>
                    <th>Nama Barang / Aset</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Kembali</th>
                    <th>Kondisi Pengembalian</th>
                    <th>Catatan Pemeriksaan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align:center;">1</td>
                    <td style="font-family:monospace; font-weight:bold;"><?= htmlspecialchars($data['kode_aset']) ?></td>
                    <td><strong><?= htmlspecialchars($data['nama_aset']) ?></strong></td>
                    <td style="text-align:center; font-size:9pt;"><?= $tglPinjamText ?></td>
                    <td style="text-align:center; font-size:9pt;"><?= $tglKembaliText ?></td>
                    <td style="text-align:center; font-weight:bold; color:<?= $data['kondisi_saat_dikembalikan'] === 'Baik' ? '#047857' : '#b91c1c' ?>;">
                        <?= htmlspecialchars($data['kondisi_saat_dikembalikan'] ?: 'Baik') ?>
                    </td>
                    <td><?= htmlspecialchars($data['catatan_pengembalian'] ?: 'Barang diterima lengkap dan berfungsi normal.') ?></td>
                </tr>
            </tbody>
        </table>

        <p class="no-indent"><strong>Pernyataan Bersama:</strong></p>
        <p>Dengan ditandatanganinya Berita Acara ini, maka kewajiban peminjaman atas barang inventaris di atas oleh PIHAK PERTAMA dinyatakan <strong>SELESAI</strong>. Barang tersebut telah diterima kembali dan dicatat ke dalam sistem inventarisasi aktif Madrasah Aliyah Negeri 2 Hulu Sungai Utara.</p>

        <p>Demikian Berita Acara Pengembalian dan Pemeriksaan Fisik Aset ini dibuat dengan sebenarnya dalam rangkap secukupnya untuk dipergunakan sebagaimana mestinya.</p>
    </div>

    <!-- Tanda Tangan -->
    <table class="table-ttd">
        <tr>
            <td>
                Yang Menyerahkan Kembali,<br>
                <strong>PIHAK PERTAMA</strong>
                <div class="ttd-space"></div>
                <div class="ttd-name"><?= htmlspecialchars($data['nama_guru'] ?? $data['nama_peminjam']) ?></div>
                <div class="ttd-nip">NIP. <?= htmlspecialchars($data['nip_guru'] ?: '..........................................') ?></div>
            </td>
            <td>
                Amuntai, <?= $tglKembaliText ?><br>
                Yang Menerima & Memeriksa,<br>
                <strong>PIHAK KEDUA</strong>
                <div class="ttd-space"></div>
                <div class="ttd-name"><?= htmlspecialchars($data['nama_petugas'] ?? 'Petugas Sarpras') ?></div>
                <div class="ttd-nip">NIP. <?= htmlspecialchars($data['nip_petugas'] ?? '..........................................') ?></div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top:20px;">
                Mengetahui,<br>
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
