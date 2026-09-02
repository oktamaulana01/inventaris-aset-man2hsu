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
        table.table-barang { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 9pt; }
        table.table-barang th, table.table-barang td { border: 1px solid #000; padding: 4px 6px; }
        table.table-barang th { background: #f2f2f2; text-align: center; font-weight: bold; }
        .table-ttd { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 9pt; }
        .table-ttd td { text-align: center; vertical-align: top; width: 50%; padding: 2px; }
        .ttd-space { height: 45px; }
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

    <div class="ba-title">BERITA ACARA MUTASI / PEMINDAHAN RUANGAN ASET</div>
    <div class="ba-number">Nomor: <?= $nomorBA ?></div>

    <div class="ba-content">
        <p>Pada hari ini <strong><?= $hariMutasiText ?></strong> tanggal <strong><?= $tglMutasiText ?></strong>, telah dilaksanakan proses mutasi / pemindahan lokasi penempatan barang inventaris milik Madrasah Aliyah Negeri 2 Hulu Sungai Utara dengan rincian data sebagai berikut:</p>

        <table class="table-barang">
            <thead>
                <tr>
                    <th style="width:25px;">No</th>
                    <th>Kode Aset</th>
                    <th>Nama Barang / Aset</th>
                    <th>Kategori</th>
                    <th>Lokasi Asal</th>
                    <th>Lokasi Tujuan Baru</th>
                    <th>Kondisi Fisik</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="text-align:center;">1</td>
                    <td style="font-weight:bold; text-align:center;"><?= htmlspecialchars($data['kode_aset']) ?></td>
                    <td><strong><?= htmlspecialchars($data['nama_aset']) ?></strong></td>
                    <td><?= htmlspecialchars($data['nama_kategori'] ?? '-') ?></td>
                    <td style="text-align:center;"><?= htmlspecialchars($data['lokasi_asal'] ?? '-') ?></td>
                    <td style="text-align:center; font-weight:bold;"><?= htmlspecialchars($data['lokasi_tujuan'] ?? '-') ?></td>
                    <td style="text-align:center;"><?= htmlspecialchars($data['kondisi_aset'] ?: 'Baik') ?></td>
                </tr>
            </tbody>
        </table>

        <p style="text-indent:0;"><strong>Alasan / Keterangan Pemindahan:</strong></p>
        <p style="text-indent:0; background:#f9f9f9; padding:5px 8px; border:1px solid #ccc; font-style:italic; margin-bottom:8px;">
            "<?= htmlspecialchars($data['keterangan'] ?: 'Pemindahan lokasi penataan sarana prasarana madrasah') ?>"
        </p>

        <p>Proses pemindahan fisik barang tersebut telah dilakukan pemeriksaan bersama antara pihak penanggung jawab ruangan asal, penanggung jawab ruangan tujuan baru, serta dicatat oleh Pengurus Barang Sarana dan Prasarana MAN 2 Hulu Sungai Utara.</p>

        <p>Demikian Berita Acara Mutasi Aset ini dibuat dengan sebenarnya untuk menjadi dasar pemutakhiran data buku inventaris ruangan (KIR) madrasah.</p>
    </div>

    <table class="table-ttd">
        <tr>
            <td>
                Penanggung Jawab Ruangan Asal,<br>
                (<?= htmlspecialchars($data['lokasi_asal'] ?? 'Ruang Asal') ?>)
                <div class="ttd-space"></div>
                <div class="ttd-name">......................................................</div>
                <div class="ttd-nip">NIP. .................................................</div>
            </td>
            <td>
                Penanggung Jawab Ruangan Tujuan,<br>
                (<?= htmlspecialchars($data['lokasi_tujuan'] ?? 'Ruang Tujuan') ?>)
                <div class="ttd-space"></div>
                <div class="ttd-name">......................................................</div>
                <div class="ttd-nip">NIP. .................................................</div>
            </td>
        </tr>
        <tr>
            <td style="padding-top:14px;">
                Petugas Inventaris / Sarpras,<br>
                <div class="ttd-space"></div>
                <div class="ttd-name"><?= htmlspecialchars($data['nama_petugas'] ?? 'Petugas Sarpras') ?></div>
                <div class="ttd-nip">NIP. <?= htmlspecialchars($data['nip_petugas'] ?? '..........................................') ?></div>
            </td>
            <td style="padding-top:14px;">
                Amuntai, <?= $tglMutasiText ?><br>
                Mengetahui,<br>
                <strong>Kepala MAN 2 Hulu Sungai Utara</strong>
                <div class="ttd-space"></div>
                <div class="ttd-name">Irwan, S.Pd., M.Si.</div>
                <div class="ttd-nip">NIP. 197803112002121002</div>
            </td>
        </tr>
    </table>
</body>
</html>
