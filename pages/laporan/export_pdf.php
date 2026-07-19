<?php
require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$pdo = getConnection();

$type = $_GET['type'] ?? 'keseluruhan';
$filterKondisi = $_GET['kondisi'] ?? '';
$filterKategori = $_GET['kategori'] ?? '';
$filterStatus = $_GET['status'] ?? '';
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

// ── Helper: Format Rupiah ──
function formatRp($n) {
    return 'Rp ' . number_format($n, 0, ',', '.');
}

// ── Helper: Tanggal dalam Bahasa Indonesia ──
function tanggalIndonesia($format = 'd F Y', $timestamp = null) {
    $bulanInggris = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    $bulanIndonesia = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $tanggal = $timestamp ? date($format, $timestamp) : date($format);
    return str_replace($bulanInggris, $bulanIndonesia, $tanggal);
}

// ── Kop Surat HTML ──
$logoPath = realpath(__DIR__ . '/../../assets/uploads/logo_km.png');
$logoBase64 = '';
if ($logoPath && file_exists($logoPath)) {
    $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
}

$kopSurat = '
<div class="kop-surat">
    <table class="kop-table">
        <tr>
            <td class="kop-logo-cell">
                <img src="' . $logoBase64 . '" class="kop-logo">
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
    <div class="kop-border"></div>
</div>';

// ── CSS ──
$css = '
<style>
    @page { margin: 15mm 15mm 15mm 15mm; } /* Margin disesuaikan agar lebih luas */
    body { font-family: "Times New Roman", Times, serif; font-size: 10pt; color: #000; } /* Font standar surat dinas */
    
    .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 5px; }
    .kop-logo-cell { width: 80px; vertical-align: middle; padding-right: 10px; }
    .kop-logo { width: 75px; height: auto; }
    .kop-text-cell { text-align: center; vertical-align: middle; } /* Padding kanan untuk balancing logo */
    
    .kop-line1 { font-size: 12pt; font-weight: bold; margin-bottom: -2px; }
    .kop-line2 { font-size: 12pt; font-weight: bold; margin-bottom: -2px; }
    .kop-line3 { font-size: 12pt; font-weight: bold; margin-bottom: 2px; }
    .kop-line4 { font-size: 9pt; font-weight: normal; font-style: italic; line-height: 1.2; }
    
    /* Garis Double Khas Surat Dinas */
    .kop-border { 
        border-top: 2.5pt solid #000; 
        border-bottom: 0.5pt solid #000; 
        height: 1.5pt; 
        margin: 5px 0 20px 0; 
    }
    .report-title { text-align: center; font-size: 12pt; font-weight: bold; margin-bottom: 3px; text-transform: uppercase; }
    .report-date { text-align: center; font-size: 9pt; color: #555; margin-bottom: 15px; }
    table.data { width: 100%; border-collapse: collapse; margin-top: 8px; }
    table.data th { background: #f2f2f2; color: #000; padding: 6px 8px; font-size: 8pt; text-align: left; text-transform: uppercase; letter-spacing: 0.3px; border: 1px solid #333; }
    table.data td { padding: 5px 8px; font-size: 8.5pt; border: 1px solid #ccc; vertical-align: top; }
    table.data tr:nth-child(even) td { background: #ffffff; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .total-row td { background: #f5f5f5 !important; color: #000; font-weight: bold; border-top: 2px solid #000; border-bottom: 2px solid #000; }
    .badge { padding: 0; border-radius: 0; font-size: 8.5pt; font-weight: normal; background: transparent !important; color: #000 !important; border: none !important; }
    .badge-success, .badge-warning, .badge-danger, .badge-info, .badge-primary, .badge-secondary { background: transparent !important; color: #000 !important; border: none !important; }
    .summary-box { display: inline-block; border: 1px solid #ccc; border-radius: 6px; padding: 8px 16px; margin: 0 8px 12px 0; text-align: center; }
    .summary-box .num { font-size: 16pt; font-weight: bold; color: #000; }
    .summary-box .lbl { font-size: 7.5pt; color: #666; }
    .footer { margin-top: 20px; text-align: right; font-size: 8pt; color: #888; }
</style>';

// ── Generate report content ──
$title = '';
$tableHtml = '';
$orientation = 'portrait';

switch ($type) {
    case 'keseluruhan':
        $title = 'LAPORAN INVENTARIS ASET KESELURUHAN';
        $orientation = 'landscape';
        $where = "WHERE a.deleted_at IS NULL"; $params = [];
        if ($filterKondisi) { $where .= " AND a.kondisi = ?"; $params[] = $filterKondisi; }
        if ($filterKategori) { $where .= " AND a.id_kategori = ?"; $params[] = $filterKategori; }
        $stmt = $pdo->prepare("SELECT a.*, k.nama_kategori, l.nama_lokasi FROM aset a LEFT JOIN kategori k ON a.id_kategori = k.id LEFT JOIN lokasi l ON a.id_lokasi = l.id $where ORDER BY a.kode_aset");
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        $tableHtml = '<table class="data"><thead><tr><th>No</th><th>Kode</th><th>Nama Aset</th><th>Kategori</th><th>Lokasi</th><th>Jumlah</th><th>Kondisi</th><th>Tahun</th><th style="text-align:right;">Nilai</th></tr></thead><tbody>';
        $total = 0;
        foreach ($data as $i => $a) {
            $nilai = $a['nilai_perolehan'] * $a['jumlah'];
            $total += $nilai;
            $badge = $a['kondisi'] === 'Baik' ? 'success' : ($a['kondisi'] === 'Rusak Ringan' ? 'warning' : 'danger');
            $tableHtml .= '<tr><td class="text-center">' . ($i+1) . '</td><td>' . htmlspecialchars($a['kode_aset']) . '</td><td>' . htmlspecialchars($a['nama_aset']) . '</td><td>' . htmlspecialchars($a['nama_kategori'] ?? '-') . '</td><td>' . htmlspecialchars($a['nama_lokasi'] ?? '-') . '</td><td class="text-center">' . $a['jumlah'] . '</td><td class="text-center"><span class="badge badge-' . $badge . '">' . $a['kondisi'] . '</span></td><td class="text-center">' . ($a['tahun_perolehan'] ?? '-') . '</td><td class="text-right">' . formatRp($nilai) . '</td></tr>';
        }
        $tableHtml .= '<tr class="total-row"><td colspan="8" class="text-right">TOTAL</td><td class="text-right">' . formatRp($total) . '</td></tr>';
        $tableHtml .= '</tbody></table>';
        break;

    case 'per_kategori':
        $title = 'LAPORAN ASET PER KATEGORI';
        $data = $pdo->query("SELECT k.nama_kategori, COUNT(a.id) as total_aset, SUM(a.jumlah) as total_unit, COALESCE(SUM(a.nilai_perolehan * a.jumlah), 0) as total_nilai, SUM(CASE WHEN a.kondisi='Baik' THEN 1 ELSE 0 END) as baik, SUM(CASE WHEN a.kondisi='Rusak Ringan' THEN 1 ELSE 0 END) as rusak_ringan, SUM(CASE WHEN a.kondisi='Rusak Berat' THEN 1 ELSE 0 END) as rusak_berat FROM kategori k LEFT JOIN aset a ON k.id = a.id_kategori AND a.deleted_at IS NULL GROUP BY k.id, k.nama_kategori ORDER BY k.nama_kategori")->fetchAll();
        $tableHtml = '<table class="data"><thead><tr><th>No</th><th>Kategori</th><th>Total Aset</th><th>Total Unit</th><th>Baik</th><th>Rusak Ringan</th><th>Rusak Berat</th><th style="text-align:right;">Total Nilai</th></tr></thead><tbody>';
        $grandTotal = 0;
        foreach ($data as $i => $d) {
            $grandTotal += $d['total_nilai'];
            $tableHtml .= '<tr><td class="text-center">' . ($i+1) . '</td><td>' . htmlspecialchars($d['nama_kategori']) . '</td><td class="text-center">' . $d['total_aset'] . '</td><td class="text-center">' . ($d['total_unit'] ?? 0) . '</td><td class="text-center">' . ($d['baik'] ?? 0) . '</td><td class="text-center">' . ($d['rusak_ringan'] ?? 0) . '</td><td class="text-center">' . ($d['rusak_berat'] ?? 0) . '</td><td class="text-right">' . formatRp($d['total_nilai']) . '</td></tr>';
        }
        $tableHtml .= '<tr class="total-row"><td colspan="7" class="text-right">GRAND TOTAL</td><td class="text-right">' . formatRp($grandTotal) . '</td></tr>';
        $tableHtml .= '</tbody></table>';
        break;

    case 'per_lokasi':
        $title = 'LAPORAN ASET PER LOKASI / RUANGAN';
        $data = $pdo->query("SELECT l.nama_lokasi, COUNT(a.id) as total_aset, SUM(a.jumlah) as total_unit, COALESCE(SUM(a.nilai_perolehan * a.jumlah), 0) as total_nilai, SUM(CASE WHEN a.kondisi='Baik' THEN 1 ELSE 0 END) as baik, SUM(CASE WHEN a.kondisi='Rusak Ringan' THEN 1 ELSE 0 END) as rusak_ringan, SUM(CASE WHEN a.kondisi='Rusak Berat' THEN 1 ELSE 0 END) as rusak_berat FROM lokasi l LEFT JOIN aset a ON l.id = a.id_lokasi AND a.deleted_at IS NULL GROUP BY l.id, l.nama_lokasi ORDER BY l.nama_lokasi")->fetchAll();
        $tableHtml = '<table class="data"><thead><tr><th>No</th><th>Lokasi</th><th>Total Aset</th><th>Total Unit</th><th>Baik</th><th>Rusak Ringan</th><th>Rusak Berat</th><th style="text-align:right;">Total Nilai</th></tr></thead><tbody>';
        $grandTotal = 0;
        foreach ($data as $i => $d) {
            $grandTotal += $d['total_nilai'];
            $tableHtml .= '<tr><td class="text-center">' . ($i+1) . '</td><td>' . htmlspecialchars($d['nama_lokasi']) . '</td><td class="text-center">' . $d['total_aset'] . '</td><td class="text-center">' . ($d['total_unit'] ?? 0) . '</td><td class="text-center">' . ($d['baik'] ?? 0) . '</td><td class="text-center">' . ($d['rusak_ringan'] ?? 0) . '</td><td class="text-center">' . ($d['rusak_berat'] ?? 0) . '</td><td class="text-right">' . formatRp($d['total_nilai']) . '</td></tr>';
        }
        $tableHtml .= '<tr class="total-row"><td colspan="7" class="text-right">GRAND TOTAL</td><td class="text-right">' . formatRp($grandTotal) . '</td></tr>';
        $tableHtml .= '</tbody></table>';
        break;

    case 'kondisi':
        $title = 'LAPORAN KONDISI ASET';
        $orientation = 'landscape';
        $where = "WHERE a.deleted_at IS NULL"; $params = [];
        if ($filterKondisi) { $where .= " AND a.kondisi = ?"; $params[] = $filterKondisi; }
        $stmt = $pdo->prepare("SELECT a.*, k.nama_kategori, l.nama_lokasi FROM aset a LEFT JOIN kategori k ON a.id_kategori = k.id LEFT JOIN lokasi l ON a.id_lokasi = l.id $where ORDER BY a.kondisi, a.nama_aset");
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        $tableHtml = '<table class="data"><thead><tr><th>No</th><th>Kode</th><th>Nama Aset</th><th>Kategori</th><th>Lokasi</th><th>Jumlah</th><th>Kondisi</th><th>Tahun</th></tr></thead><tbody>';
        foreach ($data as $i => $a) {
            $badge = $a['kondisi'] === 'Baik' ? 'success' : ($a['kondisi'] === 'Rusak Ringan' ? 'warning' : 'danger');
            $tableHtml .= '<tr><td class="text-center">' . ($i+1) . '</td><td>' . htmlspecialchars($a['kode_aset']) . '</td><td>' . htmlspecialchars($a['nama_aset']) . '</td><td>' . htmlspecialchars($a['nama_kategori'] ?? '-') . '</td><td>' . htmlspecialchars($a['nama_lokasi'] ?? '-') . '</td><td class="text-center">' . $a['jumlah'] . '</td><td class="text-center"><span class="badge badge-' . $badge . '">' . $a['kondisi'] . '</span></td><td class="text-center">' . ($a['tahun_perolehan'] ?? '-') . '</td></tr>';
        }
        $tableHtml .= '</tbody></table>';
        break;

    case 'peminjaman':
        $title = 'LAPORAN PEMINJAMAN ASET';
        $orientation = 'landscape';
        $where = "WHERE 1=1"; $params = [];
        if ($filterStatus) { $where .= " AND p.status = ?"; $params[] = $filterStatus; }
        if ($startDate) { $where .= " AND p.tanggal_pinjam >= ?"; $params[] = $startDate; }
        if ($endDate) { $where .= " AND p.tanggal_pinjam <= ?"; $params[] = $endDate; }
        $stmt = $pdo->prepare("SELECT p.*, a.nama_aset, a.kode_aset FROM peminjaman p JOIN aset a ON p.id_aset = a.id $where ORDER BY p.tanggal_pinjam DESC");
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        $tableHtml = '<table class="data"><thead><tr><th>No</th><th>Kode</th><th>Nama Aset</th><th>Peminjam</th><th>Tgl Pinjam</th><th>Batas</th><th>Tgl Kembali</th><th>Status</th></tr></thead><tbody>';
        foreach ($data as $i => $p) {
            $badge = $p['status'] === 'Dipinjam' ? 'warning' : 'success';
            $tableHtml .= '<tr><td class="text-center">' . ($i+1) . '</td><td>' . htmlspecialchars($p['kode_aset']) . '</td><td>' . htmlspecialchars($p['nama_aset']) . '</td><td>' . htmlspecialchars($p['nama_peminjam']) . '</td><td class="text-center">' . date('d/m/Y', strtotime($p['tanggal_pinjam'])) . '</td><td class="text-center">' . date('d/m/Y', strtotime($p['tanggal_kembali_rencana'])) . '</td><td class="text-center">' . ($p['tanggal_kembali_aktual'] ? date('d/m/Y', strtotime($p['tanggal_kembali_aktual'])) : '-') . '</td><td class="text-center"><span class="badge badge-' . $badge . '">' . $p['status'] . '</span></td></tr>';
        }
        $tableHtml .= '</tbody></table>';
        break;

    case 'aset_masuk':
        $title = 'LAPORAN ASET MASUK';
        $orientation = 'landscape';
        $where = "WHERE a.deleted_at IS NULL"; $params = [];
        if ($startDate) { $where .= " AND DATE(a.created_at) >= ?"; $params[] = $startDate; }
        if ($endDate) { $where .= " AND DATE(a.created_at) <= ?"; $params[] = $endDate; }
        $stmt = $pdo->prepare("SELECT a.*, k.nama_kategori, l.nama_lokasi FROM aset a LEFT JOIN kategori k ON a.id_kategori = k.id LEFT JOIN lokasi l ON a.id_lokasi = l.id $where ORDER BY a.created_at DESC");
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        $tableHtml = '<table class="data"><thead><tr><th>No</th><th>Tgl Masuk</th><th>Kode</th><th>Nama Aset</th><th>Kategori</th><th>Lokasi</th><th>Jumlah</th><th>Sumber Dana</th><th style="text-align:right;">Nilai</th></tr></thead><tbody>';
        $total = 0;
        foreach ($data as $i => $a) {
            $nilai = $a['nilai_perolehan'] * $a['jumlah'];
            $total += $nilai;
            $tableHtml .= '<tr><td class="text-center">' . ($i+1) . '</td><td class="text-center">' . date('d/m/Y', strtotime($a['created_at'])) . '</td><td>' . htmlspecialchars($a['kode_aset']) . '</td><td>' . htmlspecialchars($a['nama_aset']) . '</td><td>' . htmlspecialchars($a['nama_kategori'] ?? '-') . '</td><td>' . htmlspecialchars($a['nama_lokasi'] ?? '-') . '</td><td class="text-center">' . $a['jumlah'] . '</td><td>' . htmlspecialchars($a['sumber_dana'] ?? '-') . '</td><td class="text-right">' . formatRp($nilai) . '</td></tr>';
        }
        $tableHtml .= '<tr class="total-row"><td colspan="8" class="text-right">TOTAL</td><td class="text-right">' . formatRp($total) . '</td></tr>';
        $tableHtml .= '</tbody></table>';
        break;

    case 'mutasi':
        $title = 'LAPORAN MUTASI / PERPINDAHAN ASET';
        $orientation = 'landscape';
        $where = "WHERE 1=1"; $params = [];
        if ($startDate) { $where .= " AND m.tanggal_mutasi >= ?"; $params[] = $startDate; }
        if ($endDate) { $where .= " AND m.tanggal_mutasi <= ?"; $params[] = $endDate; }
        $stmt = $pdo->prepare("SELECT m.*, a.kode_aset, a.nama_aset, la.nama_lokasi as lokasi_asal, lt.nama_lokasi as lokasi_tujuan, u.nama as oleh FROM mutasi_aset m JOIN aset a ON m.id_aset = a.id LEFT JOIN lokasi la ON m.id_lokasi_asal = la.id LEFT JOIN lokasi lt ON m.id_lokasi_tujuan = lt.id LEFT JOIN users u ON m.id_user = u.id $where ORDER BY m.tanggal_mutasi DESC");
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        $tableHtml = '<table class="data"><thead><tr><th>No</th><th>Tanggal</th><th>Kode Aset</th><th>Nama Aset</th><th>Dari Lokasi</th><th>Ke Lokasi</th><th>Oleh</th><th>Keterangan</th></tr></thead><tbody>';
        if (empty($data)) {
            $tableHtml .= '<tr><td colspan="8" class="text-center">Belum ada data mutasi</td></tr>';
        } else {
            foreach ($data as $i => $m) {
                $tableHtml .= '<tr><td class="text-center">' . ($i+1) . '</td><td class="text-center">' . date('d/m/Y', strtotime($m['tanggal_mutasi'])) . '</td><td>' . htmlspecialchars($m['kode_aset']) . '</td><td>' . htmlspecialchars($m['nama_aset']) . '</td><td>' . htmlspecialchars($m['lokasi_asal'] ?? '-') . '</td><td>' . htmlspecialchars($m['lokasi_tujuan'] ?? '-') . '</td><td>' . htmlspecialchars($m['oleh'] ?? '-') . '</td><td>' . htmlspecialchars($m['keterangan'] ?? '-') . '</td></tr>';
            }
        }
        $tableHtml .= '</tbody></table>';
        break;

    case 'penghapusan':
        $title = 'LAPORAN PENGHAPUSAN / PENGHAPUSBUKUAN ASET';
        $orientation = 'landscape';
        $where = "WHERE a.deleted_at IS NOT NULL"; $params = [];
        if ($startDate) { $where .= " AND DATE(a.deleted_at) >= ?"; $params[] = $startDate; }
        if ($endDate) { $where .= " AND DATE(a.deleted_at) <= ?"; $params[] = $endDate; }
        $stmt = $pdo->prepare("SELECT a.*, k.nama_kategori, l.nama_lokasi FROM aset a LEFT JOIN kategori k ON a.id_kategori = k.id LEFT JOIN lokasi l ON a.id_lokasi = l.id $where ORDER BY a.deleted_at DESC");
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        $tableHtml = '<table class="data"><thead><tr><th>No</th><th>Tgl Hapus</th><th>Kode</th><th>Nama Aset</th><th>Kategori</th><th>Kondisi</th><th>Alasan Hapus</th><th>Bukti Foto</th></tr></thead><tbody>';
        if (empty($data)) {
            $tableHtml .= '<tr><td colspan="8" class="text-center">Belum ada aset yang dihapus</td></tr>';
        } else {
            foreach ($data as $i => $a) {
                $badge = $a['kondisi'] === 'Baik' ? 'success' : ($a['kondisi'] === 'Rusak Ringan' ? 'warning' : 'danger');
                $bukti = $a['bukti_hapus'] ? 'Terlampir' : '-';
                $tableHtml .= '<tr><td class="text-center">' . ($i+1) . '</td><td class="text-center">' . date('d/m/Y', strtotime($a['deleted_at'])) . '</td><td>' . htmlspecialchars($a['kode_aset']) . '</td><td>' . htmlspecialchars($a['nama_aset']) . '</td><td>' . htmlspecialchars($a['nama_kategori'] ?? '-') . '</td><td class="text-center"><span class="badge badge-' . $badge . '">' . $a['kondisi'] . '</span></td><td>' . htmlspecialchars($a['alasan_hapus'] ?? '-') . '</td><td class="text-center">' . $bukti . '</td></tr>';
            }
        }
        $tableHtml .= '</tbody></table>';
        break;

    case 'riwayat_aset':
        $filterAsetId = $_GET['id_aset'] ?? '';
        if (!$filterAsetId) die('Silakan pilih aset terlebih dahulu.');
        
        $stmtInfo = $pdo->prepare("SELECT kode_aset, nama_aset FROM aset WHERE id = ?");
        $stmtInfo->execute([$filterAsetId]);
        $asetInfo = $stmtInfo->fetch();
        
        $title = 'RIWAYAT PEMINJAMAN ASET: ' . strtoupper($asetInfo['nama_aset']);
        $orientation = 'landscape';
        $where = "WHERE p.id_aset = ?"; 
        $params = [$filterAsetId];
        
        if ($startDate) { $where .= " AND p.tanggal_pinjam >= ?"; $params[] = $startDate; }
        if ($endDate) { $where .= " AND p.tanggal_pinjam <= ?"; $params[] = $endDate; }
        
        $stmt = $pdo->prepare("SELECT p.* FROM peminjaman p $where ORDER BY p.tanggal_pinjam DESC");
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        
        $tableHtml = '<table class="data"><thead><tr><th>No</th><th>Nama Peminjam</th><th>Tgl Pinjam</th><th>Tgl Kembali (Rencana)</th><th>Tgl Kembali (Aktual)</th><th>Kondisi Pengembalian</th><th>Status</th></tr></thead><tbody>';
        if (empty($data)) {
            $tableHtml .= '<tr><td colspan="7" class="text-center">Belum ada riwayat peminjaman untuk aset ini pada periode tersebut.</td></tr>';
        } else {
            foreach ($data as $i => $p) {
                $badge = $p['status'] === 'Dipinjam' ? 'warning' : ($p['status'] === 'Dikembalikan' ? 'success' : 'info');
                $tglAktual = $p['tanggal_kembali_aktual'] ? date('d/m/Y H:i', strtotime($p['tanggal_kembali_aktual'])) : '-';
                $tableHtml .= '<tr><td class="text-center">' . ($i+1) . '</td><td>' . htmlspecialchars($p['nama_peminjam']) . '</td><td class="text-center">' . date('d/m/Y H:i', strtotime($p['tanggal_pinjam'])) . '</td><td class="text-center">' . date('d/m/Y', strtotime($p['tanggal_kembali_rencana'])) . '</td><td class="text-center">' . $tglAktual . '</td><td>' . htmlspecialchars($p['kondisi_saat_dikembalikan'] ?? '-') . '</td><td class="text-center"><span class="badge badge-' . $badge . '">' . $p['status'] . '</span></td></tr>';
            }
        }
        $tableHtml .= '</tbody></table>';
        break;

    default:
        die('Tipe laporan tidak valid.');
}

// ── Build full HTML ──
$html = '<!DOCTYPE html><html><head><meta charset="UTF-8">' . $css . '</head><body>';
$html .= $kopSurat;
$html .= '<div class="report-title">' . $title . '</div>';
$html .= '<div class="report-date">MAN 2 Hulu Sungai Utara &mdash; ' . tanggalIndonesia('d F Y') . '</div>';
$html .= $tableHtml;
$html .= '<div class="footer">Dicetak pada: ' . date('d/m/Y H:i:s') . '</div>';
$html .= '</body></html>';

// ── Generate PDF ──
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'sans-serif');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', $orientation);
$dompdf->render();

$filename = 'Laporan_' . ucfirst(str_replace('_', ' ', $type)) . '_' . date('Y-m-d') . '.pdf';
$viewInBrowser = isset($_GET['view']) && $_GET['view'] == '1';
$dompdf->stream($filename, ['Attachment' => !$viewInBrowser]);
exit;
