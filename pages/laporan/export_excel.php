<?php
require_once __DIR__ . '/../../includes/auth_check.php';
$pdo = getConnection();

$type = $_GET['type'] ?? 'keseluruhan';
$filterKondisi = $_GET['kondisi'] ?? '';
$filterKategori = $_GET['kategori'] ?? '';
$filterStatus = $_GET['status'] ?? '';
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

// Set headers for CSV download
$filename = 'laporan_' . $type . '_' . date('Y-m-d_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
// BOM for Excel UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

switch ($type) {
    case 'keseluruhan':
        fputcsv($output, ['LAPORAN INVENTARIS ASET KESELURUHAN - MAN 2 HSU']);
        fputcsv($output, ['Tanggal: ' . date('d/m/Y')]);
        fputcsv($output, []);
        fputcsv($output, ['No', 'Kode Aset', 'Nama Aset', 'Kategori', 'Lokasi', 'Jumlah', 'Kondisi', 'Tahun', 'Nilai Per Unit', 'Total Nilai', 'Sumber Dana']);
        
        $where = "WHERE a.deleted_at IS NULL";
        $params = [];
        if ($filterKondisi) { $where .= " AND a.kondisi = ?"; $params[] = $filterKondisi; }
        if ($filterKategori) { $where .= " AND a.id_kategori = ?"; $params[] = $filterKategori; }
        
        $stmt = $pdo->prepare("SELECT a.*, k.nama_kategori, l.nama_lokasi FROM aset a 
            LEFT JOIN kategori k ON a.id_kategori = k.id LEFT JOIN lokasi l ON a.id_lokasi = l.id 
            $where ORDER BY a.kode_aset");
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        $no = 1; $total = 0;
        foreach ($data as $d) {
            $totalNilai = $d['nilai_perolehan'] * $d['jumlah'];
            $total += $totalNilai;
            fputcsv($output, [$no++, $d['kode_aset'], $d['nama_aset'], $d['nama_kategori'] ?? '-', $d['nama_lokasi'] ?? '-', $d['jumlah'], $d['kondisi'], $d['tahun_perolehan'] ?? '-', $d['nilai_perolehan'], $totalNilai, $d['sumber_dana'] ?? '-']);
        }
        fputcsv($output, []);
        fputcsv($output, ['', '', '', '', '', '', '', '', 'TOTAL', $total]);
        break;

    case 'per_kategori':
        fputcsv($output, ['LAPORAN ASET PER KATEGORI - MAN 2 HSU']);
        fputcsv($output, ['Tanggal: ' . date('d/m/Y')]);
        fputcsv($output, []);
        fputcsv($output, ['No', 'Kategori', 'Total Aset', 'Total Unit', 'Baik', 'Rusak Ringan', 'Rusak Berat', 'Total Nilai']);
        
        $data = $pdo->query("SELECT k.nama_kategori, COUNT(a.id) as total_aset, SUM(a.jumlah) as total_unit, COALESCE(SUM(a.nilai_perolehan * a.jumlah), 0) as total_nilai, SUM(CASE WHEN a.kondisi='Baik' THEN 1 ELSE 0 END) as baik, SUM(CASE WHEN a.kondisi='Rusak Ringan' THEN 1 ELSE 0 END) as rusak_ringan, SUM(CASE WHEN a.kondisi='Rusak Berat' THEN 1 ELSE 0 END) as rusak_berat FROM kategori k LEFT JOIN aset a ON k.id = a.id_kategori AND a.deleted_at IS NULL GROUP BY k.id, k.nama_kategori ORDER BY k.nama_kategori")->fetchAll();
        $no = 1;
        foreach ($data as $d) { fputcsv($output, [$no++, $d['nama_kategori'], $d['total_aset'], $d['total_unit'] ?? 0, $d['baik'] ?? 0, $d['rusak_ringan'] ?? 0, $d['rusak_berat'] ?? 0, $d['total_nilai']]); }
        break;

    case 'per_lokasi':
        fputcsv($output, ['LAPORAN ASET PER LOKASI - MAN 2 HSU']);
        fputcsv($output, ['Tanggal: ' . date('d/m/Y')]);
        fputcsv($output, []);
        fputcsv($output, ['No', 'Lokasi', 'Total Aset', 'Total Unit', 'Baik', 'Rusak Ringan', 'Rusak Berat', 'Total Nilai']);
        
        $data = $pdo->query("SELECT l.nama_lokasi, COUNT(a.id) as total_aset, SUM(a.jumlah) as total_unit, COALESCE(SUM(a.nilai_perolehan * a.jumlah), 0) as total_nilai, SUM(CASE WHEN a.kondisi='Baik' THEN 1 ELSE 0 END) as baik, SUM(CASE WHEN a.kondisi='Rusak Ringan' THEN 1 ELSE 0 END) as rusak_ringan, SUM(CASE WHEN a.kondisi='Rusak Berat' THEN 1 ELSE 0 END) as rusak_berat FROM lokasi l LEFT JOIN aset a ON l.id = a.id_lokasi AND a.deleted_at IS NULL GROUP BY l.id, l.nama_lokasi ORDER BY l.nama_lokasi")->fetchAll();
        $no = 1;
        foreach ($data as $d) { fputcsv($output, [$no++, $d['nama_lokasi'], $d['total_aset'], $d['total_unit'] ?? 0, $d['baik'] ?? 0, $d['rusak_ringan'] ?? 0, $d['rusak_berat'] ?? 0, $d['total_nilai']]); }
        break;

    case 'kondisi':
        fputcsv($output, ['LAPORAN KONDISI ASET - MAN 2 HSU']);
        fputcsv($output, ['Tanggal: ' . date('d/m/Y')]);
        fputcsv($output, []);
        fputcsv($output, ['No', 'Kode', 'Nama Aset', 'Kategori', 'Lokasi', 'Jumlah', 'Kondisi', 'Tahun']);
        
        $where = "WHERE a.deleted_at IS NULL";
        $params = [];
        if ($filterKondisi) { $where .= " AND a.kondisi = ?"; $params[] = $filterKondisi; }
        $stmt = $pdo->prepare("SELECT a.*, k.nama_kategori, l.nama_lokasi FROM aset a LEFT JOIN kategori k ON a.id_kategori = k.id LEFT JOIN lokasi l ON a.id_lokasi = l.id $where ORDER BY a.kondisi, a.nama_aset");
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        $no = 1;
        foreach ($data as $d) { fputcsv($output, [$no++, $d['kode_aset'], $d['nama_aset'], $d['nama_kategori'] ?? '-', $d['nama_lokasi'] ?? '-', $d['jumlah'], $d['kondisi'], $d['tahun_perolehan'] ?? '-']); }
        break;

    case 'peminjaman':
        fputcsv($output, ['LAPORAN PEMINJAMAN ASET - MAN 2 HSU']);
        fputcsv($output, ['Tanggal: ' . date('d/m/Y')]);
        fputcsv($output, []);
        fputcsv($output, ['No', 'Kode Aset', 'Nama Aset', 'Peminjam', 'Tgl Pinjam', 'Batas Kembali', 'Tgl Kembali Aktual', 'Status']);
        
        $where = "WHERE 1=1"; $params = [];
        if ($filterStatus) { $where .= " AND p.status = ?"; $params[] = $filterStatus; }
        if ($startDate) { $where .= " AND p.tanggal_pinjam >= ?"; $params[] = $startDate; }
        if ($endDate) { $where .= " AND p.tanggal_pinjam <= ?"; $params[] = $endDate; }
        $stmt = $pdo->prepare("SELECT p.*, a.nama_aset, a.kode_aset FROM peminjaman p JOIN aset a ON p.id_aset = a.id $where ORDER BY p.tanggal_pinjam DESC");
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        $no = 1;
        foreach ($data as $d) { fputcsv($output, [$no++, $d['kode_aset'], $d['nama_aset'], $d['nama_peminjam'], $d['tanggal_pinjam'], $d['tanggal_kembali_rencana'], $d['tanggal_kembali_aktual'] ?? '-', $d['status']]); }
        break;

    case 'aset_masuk':
        fputcsv($output, ['LAPORAN ASET MASUK - MAN 2 HSU']);
        fputcsv($output, ['Tanggal: ' . date('d/m/Y')]);
        fputcsv($output, []);
        fputcsv($output, ['No', 'Tgl Masuk', 'Kode', 'Nama Aset', 'Kategori', 'Lokasi', 'Jumlah', 'Sumber Dana', 'Nilai']);
        
        $where = "WHERE a.deleted_at IS NULL"; $params = [];
        if ($startDate) { $where .= " AND DATE(a.created_at) >= ?"; $params[] = $startDate; }
        if ($endDate) { $where .= " AND DATE(a.created_at) <= ?"; $params[] = $endDate; }
        $stmt = $pdo->prepare("SELECT a.*, k.nama_kategori, l.nama_lokasi FROM aset a LEFT JOIN kategori k ON a.id_kategori = k.id LEFT JOIN lokasi l ON a.id_lokasi = l.id $where ORDER BY a.created_at DESC");
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        $no = 1;
        foreach ($data as $d) { fputcsv($output, [$no++, date('d/m/Y', strtotime($d['created_at'])), $d['kode_aset'], $d['nama_aset'], $d['nama_kategori'] ?? '-', $d['nama_lokasi'] ?? '-', $d['jumlah'], $d['sumber_dana'] ?? '-', $d['nilai_perolehan'] * $d['jumlah']]); }
        break;

    case 'mutasi':
        fputcsv($output, ['LAPORAN MUTASI ASET - MAN 2 HSU']);
        fputcsv($output, ['Tanggal: ' . date('d/m/Y')]);
        fputcsv($output, []);
        fputcsv($output, ['No', 'Tanggal', 'Kode Aset', 'Nama Aset', 'Dari Lokasi', 'Ke Lokasi', 'Oleh', 'Keterangan']);
        
        $where = "WHERE 1=1"; $params = [];
        if ($startDate) { $where .= " AND m.tanggal_mutasi >= ?"; $params[] = $startDate; }
        if ($endDate) { $where .= " AND m.tanggal_mutasi <= ?"; $params[] = $endDate; }
        $stmt = $pdo->prepare("SELECT m.*, a.kode_aset, a.nama_aset, la.nama_lokasi as lokasi_asal, lt.nama_lokasi as lokasi_tujuan, u.nama as oleh FROM mutasi_aset m JOIN aset a ON m.id_aset = a.id LEFT JOIN lokasi la ON m.id_lokasi_asal = la.id LEFT JOIN lokasi lt ON m.id_lokasi_tujuan = lt.id LEFT JOIN users u ON m.id_user = u.id $where ORDER BY m.tanggal_mutasi DESC");
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        $no = 1;
        foreach ($data as $d) { fputcsv($output, [$no++, $d['tanggal_mutasi'], $d['kode_aset'], $d['nama_aset'], $d['lokasi_asal'] ?? '-', $d['lokasi_tujuan'] ?? '-', $d['oleh'] ?? '-', $d['keterangan'] ?? '-']); }
        break;

    case 'penghapusan':
        fputcsv($output, ['LAPORAN PENGHAPUSAN ASET - MAN 2 HSU']);
        fputcsv($output, ['Tanggal: ' . date('d/m/Y')]);
        fputcsv($output, []);
        fputcsv($output, ['No', 'Tgl Hapus', 'Kode', 'Nama Aset', 'Kategori', 'Kondisi', 'Alasan Hapus', 'Bukti Foto']);
        
        $where = "WHERE a.deleted_at IS NOT NULL"; $params = [];
        if ($startDate) { $where .= " AND DATE(a.deleted_at) >= ?"; $params[] = $startDate; }
        if ($endDate) { $where .= " AND DATE(a.deleted_at) <= ?"; $params[] = $endDate; }
        $stmt = $pdo->prepare("SELECT a.*, k.nama_kategori, l.nama_lokasi FROM aset a LEFT JOIN kategori k ON a.id_kategori = k.id LEFT JOIN lokasi l ON a.id_lokasi = l.id $where ORDER BY a.deleted_at DESC");
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        $no = 1;
        foreach ($data as $d) { fputcsv($output, [$no++, date('d/m/Y', strtotime($d['deleted_at'])), $d['kode_aset'], $d['nama_aset'], $d['nama_kategori'] ?? '-', $d['kondisi'], $d['alasan_hapus'] ?? '-', $d['bukti_hapus'] ? 'Terlampir' : '-']); }
        break;
    case 'riwayat_aset':
        $filterAsetId = $_GET['id_aset'] ?? '';
        if (!$filterAsetId) die('Silakan pilih aset terlebih dahulu.');
        
        $stmtInfo = $pdo->prepare("SELECT kode_aset, nama_aset FROM aset WHERE id = ?");
        $stmtInfo->execute([$filterAsetId]);
        $asetInfo = $stmtInfo->fetch();
        
        fputcsv($output, ['RIWAYAT PEMINJAMAN ASET: ' . strtoupper($asetInfo['nama_aset'])]);
        fputcsv($output, ['Tanggal: ' . date('d/m/Y')]);
        fputcsv($output, []);
        fputcsv($output, ['No', 'Nama Peminjam', 'Tgl Pinjam', 'Tgl Kembali (Rencana)', 'Tgl Kembali (Aktual)', 'Kondisi Pengembalian', 'Status']);
        
        $where = "WHERE p.id_aset = ?"; 
        $params = [$filterAsetId];
        
        if ($startDate) { $where .= " AND p.tanggal_pinjam >= ?"; $params[] = $startDate; }
        if ($endDate) { $where .= " AND p.tanggal_pinjam <= ?"; $params[] = $endDate; }
        
        $stmt = $pdo->prepare("SELECT p.* FROM peminjaman p $where ORDER BY p.tanggal_pinjam DESC");
        $stmt->execute($params);
        $data = $stmt->fetchAll();
        
        $no = 1;
        foreach ($data as $d) { 
            fputcsv($output, [
                $no++, 
                $d['nama_peminjam'], 
                $d['tanggal_pinjam'], 
                $d['tanggal_kembali_rencana'], 
                $d['tanggal_kembali_aktual'] ?? '-', 
                $d['kondisi_saat_dikembalikan'] ?? '-', 
                $d['status']
            ]); 
        }
        break;
}

fclose($output);
exit;
?>
