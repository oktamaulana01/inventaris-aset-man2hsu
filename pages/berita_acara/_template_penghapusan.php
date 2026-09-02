<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 15mm 15mm 15mm 15mm; }
        body { font-family: "Times New Roman", Times, serif; font-size: 10pt; color: #000; line-height: 1.35; }
        .kop-table { width: 100%; border-collapse: collapse; margin-bottom: 2px; }
        .kop-logo { width: 70px; height: auto; }
        .kop-logo-cell { width: 80px; vertical-align: middle; text-align: left; }
        .kop-text-cell { text-align: center; vertical-align: middle; }
        .kop-line1 { font-size: 11pt; font-weight: bold; }
        .kop-line2 { font-size: 11pt; font-weight: bold; }
        .kop-line3 { font-size: 12pt; font-weight: bold; }
        .kop-line4 { font-size: 8pt; font-style: italic; line-height: 1.2; margin-top: 2px; }
        .kop-divider {
            border-top: 2pt solid #000;
            border-bottom: 0.5pt solid #000;
            height: 1.5pt;
            margin: 4px 0 12px 0;
        }
        .ba-title { text-align: center; font-size: 11pt; font-weight: bold; text-decoration: underline; text-transform: uppercase; margin-bottom: 2px; }
        .ba-number { text-align: center; font-size: 9pt; margin-bottom: 10px; }
        .ba-content { font-size: 9pt; text-align: justify; }
        .ba-content p { margin-bottom: 5px; text-indent: 20px; }
        table.table-barang { width: 100%; border-collapse: collapse; margin: 6px 0; font-size: 8.5pt; }
        table.table-barang th, table.table-barang td { border: 1px solid #000; padding: 3px 5px; }
        table.table-barang th { background: #f2f2f2; text-align: center; font-weight: bold; }
        .box-bukti {
            border: 1px solid #ccc;
            padding: 5px;
            margin: 5px 0;
        }
        .box-bukti img {
            max-width: 120px;
            max-height: 80px;
        }
        .table-ttd { width: 100%; border-collapse: collapse; margin-top: 14px; font-size: 9pt; }
        .table-ttd td { text-align: center; vertical-align: top; width: 50%; padding: 2px; }
        .ttd-space { height: 42px; }
        .ttd-name { font-weight: bold; text-decoration: underline; }
        .ttd-nip { font-size: 8pt; }
    </style>
</head>
<body>
    <table class="kop-table">
        <tr>
            <td class="kop-logo-cell">
                <?php if ($logoSrc): ?>
                    <img src="<?= $logoSrc ?>" class="kop-logo">
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

    <div class="ba-title">BERITA ACARA PENGHAPUSAN DAN PEMUSNAHAN BARANG INVENTARIS/ASET</div>
    <div class="ba-number">Nomor: <?= $nomorBA ?></div>

    <div class="ba-content">
        <p>Pada hari ini <strong><?= $hariHapusText ?></strong> tanggal <strong><?= $tglHapusText ?></strong>, berdasarkan hasil pemeriksaan fisik dan evaluasi teknis kondisi barang inventaris/aset milik Madrasah Aliyah Negeri 2 Hulu Sungai Utara, Tim Pengelola Sarana dan Prasarana menyatakan bahwa barang berikut telah memenuhi kriteria untuk dihapuskan dari daftar inventaris aktif madrasah:</p>

        <table class="table-barang">
            <thead>
                <tr>
                    <th style="width:20px;">No</th>
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
                    <td style="font-weight:bold; text-align:center;"><?= htmlspecialchars($data['kode_aset']) ?></td>
                    <td><strong><?= htmlspecialchars($data['nama_aset']) ?></strong></td>
                    <td><?= htmlspecialchars($data['nama_kategori'] ?? '-') ?></td>
                    <td style="text-align:center;"><?= $data['tahun_perolehan'] ?: '-' ?></td>
                    <td style="text-align:right;"><?= formatRupiah($data['nilai_perolehan']) ?></td>
                    <td style="text-align:center;"><?= htmlspecialchars($data['sumber_dana'] ?: '-') ?></td>
                    <td style="text-align:center; font-weight:bold;"><?= htmlspecialchars($data['kondisi'] ?: 'Rusak Berat') ?></td>
                </tr>
            </tbody>
        </table>

        <p style="text-indent:0;"><strong>Alasan / Dasar Penghapusan:</strong></p>
        <p style="text-indent:0; background:#f9f9f9; padding:4px 6px; border:1px solid #ccc; font-style:italic; margin-bottom:5px;">
            "<?= htmlspecialchars($data['alasan_hapus'] ?: 'Barang mengalami kerusakan berat/usang dan biaya perbaikan melebihi nilai ekonomisnya.') ?>"
        </p>

        <?php if ($buktiSrc): ?>
        <table class="box-bukti" style="width:100%;">
            <tr>
                <td style="width:125px;"><img src="<?= $buktiSrc ?>"></td>
                <td style="vertical-align:middle; font-size:8.5pt;"><strong>Dokumentasi Fisik Kerusakan:</strong><br>Foto kondisi fisik barang saat pemeriksaan lapangan tim inventarisasi sarpras MAN 2 HSU.</td>
            </tr>
        </table>
        <?php endif; ?>

        <p>Demikian Berita Acara Penghapusan ini dibuat dengan sebenarnya untuk dipergunakan sebagai dokumen pertanggungjawaban pengelolaan Barang Milik Negara (BMN) di lingkungan MAN 2 HSU.</p>
    </div>

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
                <div class="ttd-name">AGUS FITRI HIDAYAT, S.HI, MM</div>
                <div class="ttd-nip">NIP. 197908202007011011</div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top:12px;">
                Amuntai, <?= $tglHapusText ?><br>
                Menyetujui / Mengetahui,<br>
                <strong>Kepala MAN 2 Hulu Sungai Utara</strong>
                <div class="ttd-space"></div>
                <div class="ttd-name">Irwan, S.Pd., M.Si.</div>
                <div class="ttd-nip">NIP. 197803112002121002</div>
            </td>
        </tr>
    </table>
</body>
</html>
