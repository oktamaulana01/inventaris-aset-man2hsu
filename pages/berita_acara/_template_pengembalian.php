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
            margin: 4px 0 14px 0;
        }
        .ba-title { text-align: center; font-size: 11.5pt; font-weight: bold; text-decoration: underline; text-transform: uppercase; margin-bottom: 2px; }
        .ba-number { text-align: center; font-size: 9.5pt; margin-bottom: 12px; }
        .ba-content { font-size: 9.5pt; text-align: justify; }
        .ba-content p { margin-bottom: 6px; text-indent: 24px; }
        .table-pihak { width: 100%; border-collapse: collapse; margin: 2px 0 4px 15px; font-size: 9.5pt; }
        .table-pihak td { padding: 1px 3px; vertical-align: top; }
        .table-pihak td.lbl { width: 130px; }
        .table-pihak td.sep { width: 8px; }
        table.table-barang { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 9pt; }
        table.table-barang th, table.table-barang td { border: 1px solid #000; padding: 4px 6px; }
        table.table-barang th { background: #f2f2f2; text-align: center; font-weight: bold; }
        .table-ttd { width: 100%; border-collapse: collapse; margin-top: 18px; font-size: 9.5pt; }
        .table-ttd td { text-align: center; vertical-align: top; width: 50%; padding: 2px; }
        .ttd-space { height: 50px; }
        .ttd-name { font-weight: bold; text-decoration: underline; }
        .ttd-nip { font-size: 8.5pt; }
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

    <div class="ba-title">BERITA ACARA PENGEMBALIAN DAN PEMERIKSAAN FISIK ASET</div>
    <div class="ba-number">Nomor: <?= $nomorBA ?></div>

    <div class="ba-content">
        <p>Pada hari ini <strong><?= $hariKembaliText ?></strong> tanggal <strong><?= $tglKembaliText ?></strong>, bertempat di Madrasah Aliyah Negeri 2 Hulu Sungai Utara, telah dilakukan serah terima pengembalian dan pemeriksaan kondisi fisik barang inventaris madrasah oleh pihak-pihak sebagai berikut:</p>

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
                <td colspan="3" style="font-style:italic;">Selaku peminjam yang menyerahkan kembali barang, selanjutnya disebut <strong>PIHAK PERTAMA</strong>.</td>
            </tr>
        </table>

        <table class="table-pihak" style="margin-top:4px;">
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
                <td colspan="3" style="font-style:italic;">Selaku petugas pengelola barang yang menerima dan memeriksa, selanjutnya disebut <strong>PIHAK KEDUA</strong>.</td>
            </tr>
        </table>

        <p style="margin-top:6px;">PIHAK PERTAMA telah menyerahkan kembali barang inventaris yang dipinjam sejak tanggal <strong><?= $tglPinjamText ?></strong>, dan PIHAK KEDUA telah melakukan pemeriksaan fisik dengan hasil rincian:</p>

        <table class="table-barang">
            <thead>
                <tr>
                    <th style="width:25px;">No</th>
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
                    <td style="font-weight:bold; text-align:center;"><?= htmlspecialchars($data['kode_aset']) ?></td>
                    <td><strong><?= htmlspecialchars($data['nama_aset']) ?></strong></td>
                    <td style="text-align:center; font-size:8.5pt;"><?= $tglPinjamText ?></td>
                    <td style="text-align:center; font-size:8.5pt;"><?= $tglKembaliText ?></td>
                    <td style="text-align:center; font-weight:bold;"><?= htmlspecialchars($data['kondisi_saat_dikembalikan'] ?: 'Baik') ?></td>
                    <td><?= htmlspecialchars($data['catatan_pengembalian'] ?: 'Barang diterima lengkap dan berfungsi normal.') ?></td>
                </tr>
            </tbody>
        </table>

        <p style="text-indent:0;"><strong>Pernyataan Bersama:</strong></p>
        <p>Dengan ditandatanganinya Berita Acara ini, maka kewajiban peminjaman atas barang inventaris di atas oleh PIHAK PERTAMA dinyatakan <strong>SELESAI</strong>. Barang tersebut telah diterima kembali dan dicatat ke dalam sistem inventarisasi aktif Madrasah Aliyah Negeri 2 Hulu Sungai Utara.</p>

        <p>Demikian Berita Acara Pengembalian dan Pemeriksaan Fisik Aset ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.</p>
    </div>

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
            <td colspan="2" style="padding-top:15px;">
                Mengetahui,<br>
                <strong>Kepala MAN 2 Hulu Sungai Utara</strong>
                <div class="ttd-space"></div>
                <div class="ttd-name">Drs. H. Khairan Ali, M.M.Pd</div>
                <div class="ttd-nip">NIP. 196805121994031004</div>
            </td>
        </tr>
    </table>
</body>
</html>
