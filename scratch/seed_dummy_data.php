<?php
require_once 'c:/laragon/www/inventaris-aset-man2hsu/config/database.php';
$pdo = getConnection();

echo "=== MEMULAI SEEDING DATA DUMMY ===\n\n";

// 1. SEED GURU (USERS)
$gurus = [
    [
        'nama' => 'Ahmad Fauzi, S.Pd',
        'username' => 'fauzi',
        'email' => 'fauzi.man2@gmail.com',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'guru',
        'nip' => '198504122010011015',
        'jabatan' => 'Guru Matematika',
        'no_telepon' => '081234567890'
    ],
    [
        'nama' => 'Nurul Hidayah, M.Pd',
        'username' => 'nurul',
        'email' => 'nurul.man2@gmail.com',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'guru',
        'nip' => '198807202014022003',
        'jabatan' => 'Guru Fisika',
        'no_telepon' => '082198765432'
    ],
    [
        'nama' => 'Budi Santoso, S.Kom',
        'username' => 'budi',
        'email' => 'budi.man2@gmail.com',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'guru',
        'nip' => '199203152019031008',
        'jabatan' => 'Guru Informatika',
        'no_telepon' => '085712345678'
    ],
    [
        'nama' => 'Hj. Mardiana, S.Pd',
        'username' => 'mardiana',
        'email' => 'mardiana.man2@gmail.com',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'guru',
        'nip' => '197811052005012004',
        'jabatan' => 'Guru Bahasa Indonesia',
        'no_telepon' => '081345678901'
    ],
    [
        'nama' => 'Hendra Setiawan, S.Pd',
        'username' => 'hendra',
        'email' => 'hendra.man2@gmail.com',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'guru',
        'nip' => '199406182020121002',
        'jabatan' => 'Guru Bahasa Inggris',
        'no_telepon' => '087812345678'
    ]
];

$guruCount = 0;
foreach ($gurus as $g) {
    $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmtCheck->execute([$g['username']]);
    if (!$stmtCheck->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO users (nama, username, email, password, role, nip, jabatan, no_telepon) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $g['nama'], $g['username'], $g['email'], $g['password'],
            $g['role'], $g['nip'], $g['jabatan'], $g['no_telepon']
        ]);
        $guruCount++;
        echo " [+] Menambahkan Guru: {$g['nama']} ({$g['username']})\n";
    }
}
echo "Total Guru baru ditambahkan: $guruCount\n\n";

// 2. SEED ASET
// Kategori: 1: Mebeler, 2: Elektronik, 3: Alat Laboratorium, 4: Buku & Pustaka, 5: Alat Olahraga, 6: Kendaraan, 7: Alat Kantor, 8: Bangunan
// Lokasi: 1: Ruang Kepala, 2: Ruang Guru, 3: Ruang TU, 4: Kelas X-A, 5: Kelas X-B, 6: Kelas XI-A, 7: Kelas XI-B, 8: Kelas XII-A, 9: Kelas XII-B, 10: Perpustakaan, 11: Lab Komputer, 12: Lab IPA, 13: Mushalla, 14: Aula, 15: Gudang

$newAssets = [
    [
        'kode_aset' => 'AST-2026-015',
        'nama_aset' => 'Laptop ASUS ExpertBook B1400',
        'id_kategori' => 2,
        'id_lokasi' => 11,
        'jumlah' => 15,
        'kondisi' => 'Baik',
        'tahun_perolehan' => 2026,
        'nilai_perolehan' => 8500000,
        'sumber_dana' => 'BOS Reguler',
        'keterangan' => 'Laptop pembelajaran dan ujian berbasis komputer di Lab Komputer'
    ],
    [
        'kode_aset' => 'AST-2026-016',
        'nama_aset' => 'Smart TV Samsung 55 Inch Crystal UHD',
        'id_kategori' => 2,
        'id_lokasi' => 14,
        'jumlah' => 1,
        'kondisi' => 'Baik',
        'tahun_perolehan' => 2026,
        'nilai_perolehan' => 7200000,
        'sumber_dana' => 'Komite Madrasah',
        'keterangan' => 'Smart TV presentasi, video conference, dan acara di Aula'
    ],
    [
        'kode_aset' => 'AST-2025-017',
        'nama_aset' => 'Sound System Portable Baretone 15 Inch',
        'id_kategori' => 2,
        'id_lokasi' => 2,
        'jumlah' => 2,
        'kondisi' => 'Baik',
        'tahun_perolehan' => 2025,
        'nilai_perolehan' => 3500000,
        'sumber_dana' => 'BOS Reguler',
        'keterangan' => 'Speaker portable + 2 wireless mic untuk upacara dan rapat'
    ],
    [
        'kode_aset' => 'AST-2025-018',
        'nama_aset' => 'Meja Rapat Kayu Jati Oval',
        'id_kategori' => 1,
        'id_lokasi' => 1,
        'jumlah' => 1,
        'kondisi' => 'Baik',
        'tahun_perolehan' => 2025,
        'nilai_perolehan' => 4800000,
        'sumber_dana' => 'APBN',
        'keterangan' => 'Meja rapat pimpinan kayu jati ukuran 300x120 cm'
    ],
    [
        'kode_aset' => 'AST-2024-019',
        'nama_aset' => 'Set Kursi & Meja Siswa Ergonomis',
        'id_kategori' => 1,
        'id_lokasi' => 4,
        'jumlah' => 36,
        'kondisi' => 'Baik',
        'tahun_perolehan' => 2024,
        'nilai_perolehan' => 350000,
        'sumber_dana' => 'BOS Reguler',
        'keterangan' => 'Satu set meja kursi belajar siswa bahan kayu lapis dan besi'
    ],
    [
        'kode_aset' => 'AST-2024-020',
        'nama_aset' => 'Lemari Arsip Besi 4 Pintu Lion',
        'id_kategori' => 1,
        'id_lokasi' => 3,
        'jumlah' => 2,
        'kondisi' => 'Baik',
        'tahun_perolehan' => 2024,
        'nilai_perolehan' => 2750000,
        'sumber_dana' => 'APBN',
        'keterangan' => 'Lemari filing cabinet tahan api untuk arsip ijazah dan kepegawaian'
    ],
    [
        'kode_aset' => 'AST-2025-021',
        'nama_aset' => 'Mikroskop Binokuler Olympus CX23',
        'id_kategori' => 3,
        'id_lokasi' => 12,
        'jumlah' => 5,
        'kondisi' => 'Baik',
        'tahun_perolehan' => 2025,
        'nilai_perolehan' => 12500000,
        'sumber_dana' => 'Hibah Kemenag',
        'keterangan' => 'Mikroskop praktikum biologi siswa resolusi tinggi'
    ],
    [
        'kode_aset' => 'AST-2024-022',
        'nama_aset' => 'Torso Model Anatomi Tubuh Manusia',
        'id_kategori' => 3,
        'id_lokasi' => 12,
        'jumlah' => 2,
        'kondisi' => 'Baik',
        'tahun_perolehan' => 2024,
        'nilai_perolehan' => 1850000,
        'sumber_dana' => 'BOS Reguler',
        'keterangan' => 'Model organ tubuh manusia lengkap ukuran dewasa untuk peraga IPA'
    ],
    [
        'kode_aset' => 'AST-2025-023',
        'nama_aset' => 'Paket Ensiklopedia Islam Tematik (10 Jilid)',
        'id_kategori' => 4,
        'id_lokasi' => 10,
        'jumlah' => 3,
        'kondisi' => 'Baik',
        'tahun_perolehan' => 2025,
        'nilai_perolehan' => 4500000,
        'sumber_dana' => 'Komite Madrasah',
        'keterangan' => 'Referensi sejarah peradaban Islam dan sains Islam perpustakaan'
    ],
    [
        'kode_aset' => 'AST-2026-024',
        'nama_aset' => 'Set Raket Badminton Yonex Nanoray + Net',
        'id_kategori' => 5,
        'id_lokasi' => 15,
        'jumlah' => 8,
        'kondisi' => 'Baik',
        'tahun_perolehan' => 2026,
        'nilai_perolehan' => 450000,
        'sumber_dana' => 'BOS Reguler',
        'keterangan' => 'Raket dan net bulutangkis untuk ekstrakurikuler olahraga'
    ],
    [
        'kode_aset' => 'AST-2024-025',
        'nama_aset' => 'Matras Senam Lantai 2x1 Meter',
        'id_kategori' => 5,
        'id_lokasi' => 15,
        'jumlah' => 4,
        'kondisi' => 'Rusak Ringan',
        'tahun_perolehan' => 2024,
        'nilai_perolehan' => 1200000,
        'sumber_dana' => 'BOS Reguler',
        'keterangan' => 'Busa matras senam ketebalan 10cm, jahitan tepi luar terkelupas sedikit'
    ],
    [
        'kode_aset' => 'AST-2025-026',
        'nama_aset' => 'Mesin Fotokopi Multifungsi Kyocera M2040dn',
        'id_kategori' => 7,
        'id_lokasi' => 3,
        'jumlah' => 1,
        'kondisi' => 'Baik',
        'tahun_perolehan' => 2025,
        'nilai_perolehan' => 8900000,
        'sumber_dana' => 'APBN',
        'keterangan' => 'Mesin cetak & scan dokumen operasional administrasi madrasah'
    ],
    [
        'kode_aset' => 'AST-2023-027',
        'nama_aset' => 'Sepeda Motor Honda Vario 125 Operasional',
        'id_kategori' => 6,
        'id_lokasi' => 3,
        'jumlah' => 1,
        'kondisi' => 'Baik',
        'tahun_perolehan' => 2023,
        'nilai_perolehan' => 22500000,
        'sumber_dana' => 'APBN',
        'keterangan' => 'Kendaraan dinas roda dua untuk dinas luar tata usaha (DA 4567 XY)'
    ],
    [
        'kode_aset' => 'AST-2025-028',
        'nama_aset' => 'Router Switch Mikrotik Cloud Router CRS328',
        'id_kategori' => 2,
        'id_lokasi' => 11,
        'jumlah' => 2,
        'kondisi' => 'Baik',
        'tahun_perolehan' => 2025,
        'nilai_perolehan' => 5600000,
        'sumber_dana' => 'BOS Reguler',
        'keterangan' => 'Switch jaringan utama 24 Port Gigabit PoE untuk internet sekolah'
    ],
    [
        'kode_aset' => 'AST-2024-029',
        'nama_aset' => 'AC Split Daikin 1.5 PK Inverter',
        'id_kategori' => 2,
        'id_lokasi' => 2,
        'jumlah' => 2,
        'kondisi' => 'Rusak Ringan',
        'tahun_perolehan' => 2024,
        'nilai_perolehan' => 5200000,
        'sumber_dana' => 'Komite Madrasah',
        'keterangan' => 'Pendingin udara ruang guru, pendinginan kurang maksimal perlu servis rutin'
    ],
    [
        'kode_aset' => 'AST-2023-030',
        'nama_aset' => 'Mimbar Podium Kayu Jati Ukir Jepara',
        'id_kategori' => 1,
        'id_lokasi' => 13,
        'jumlah' => 1,
        'kondisi' => 'Baik',
        'tahun_perolehan' => 2023,
        'nilai_perolehan' => 3200000,
        'sumber_dana' => 'Hibah Kemenag',
        'keterangan' => 'Podium khutbah jumat dan ceramah di Mushalla madrasah'
    ],
    [
        'kode_aset' => 'AST-2022-031',
        'nama_aset' => 'Papan Tulis Whiteboard Magnetik 120x240cm',
        'id_kategori' => 1,
        'id_lokasi' => 6,
        'jumlah' => 6,
        'kondisi' => 'Baik',
        'tahun_perolehan' => 2022,
        'nilai_perolehan' => 850000,
        'sumber_dana' => 'BOS Reguler',
        'keterangan' => 'Whiteboard gantung kelas XI-A dan kelas sekitarnya'
    ],
    [
        'kode_aset' => 'AST-2024-032',
        'nama_aset' => 'Genset Silent Perkins 15 KVA',
        'id_kategori' => 2,
        'id_lokasi' => 15,
        'jumlah' => 1,
        'kondisi' => 'Rusak Berat',
        'tahun_perolehan' => 2024,
        'nilai_perolehan' => 38000000,
        'sumber_dana' => 'APBN',
        'keterangan' => 'Generator cadangan listrik darurat, modul dinamo starter terbakar'
    ]
];

$asetCount = 0;
foreach ($newAssets as $a) {
    $stmtCheck = $pdo->prepare("SELECT id FROM aset WHERE kode_aset = ?");
    $stmtCheck->execute([$a['kode_aset']]);
    if (!$stmtCheck->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO aset (kode_aset, nama_aset, id_kategori, id_lokasi, jumlah, kondisi, tahun_perolehan, nilai_perolehan, sumber_dana, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $a['kode_aset'], $a['nama_aset'], $a['id_kategori'], $a['id_lokasi'],
            $a['jumlah'], $a['kondisi'], $a['tahun_perolehan'], $a['nilai_perolehan'],
            $a['sumber_dana'], $a['keterangan']
        ]);
        $asetCount++;
        echo " [+] Menambahkan Aset: [{$a['kode_aset']}] {$a['nama_aset']}\n";
    }
}
echo "Total Aset baru ditambahkan: $asetCount\n\n";

// 3. SEED PEMINJAMAN
// Get list of active teachers and assets
$allUsers = $pdo->query("SELECT id, nama, role FROM users WHERE role = 'guru'")->fetchAll(PDO::FETCH_ASSOC);
$allAssets = $pdo->query("SELECT id, kode_aset, nama_aset FROM aset")->fetchAll(PDO::FETCH_ASSOC);

$loans = [
    // Menunggu Konfirmasi (Pending loan requests)
    [
        'kode_aset' => 'AST-2025-017', // Sound system
        'guru_username' => 'fauzi',
        'lokasi_id' => 14, // Aula
        'tgl_pinjam' => '2026-08-22',
        'tgl_kembali_rencana' => '2026-08-23',
        'tgl_kembali_aktual' => null,
        'kondisi_kembali' => null,
        'catatan_kembali' => null,
        'status' => 'Menunggu Konfirmasi',
        'keterangan' => 'Peminjaman sound system untuk kegiatan Latihan Pidato Siswa di Aula'
    ],
    [
        'kode_aset' => 'AST-2026-015', // Laptop ASUS
        'guru_username' => 'budi',
        'lokasi_id' => 11, // Lab Komputer
        'tgl_pinjam' => '2026-08-22',
        'tgl_kembali_rencana' => '2026-08-25',
        'tgl_kembali_aktual' => null,
        'kondisi_kembali' => null,
        'catatan_kembali' => null,
        'status' => 'Menunggu Konfirmasi',
        'keterangan' => 'Peminjaman 5 unit laptop untuk bimbingan teknis Olimpiade Informatika'
    ],
    // Dipinjam (Active borrowings)
    [
        'kode_aset' => 'AST-2023-004', // Projector Epson
        'guru_username' => 'nurul',
        'lokasi_id' => 8, // Ruang Kelas XII-A
        'tgl_pinjam' => '2026-08-20',
        'tgl_kembali_rencana' => '2026-08-22',
        'tgl_kembali_aktual' => null,
        'kondisi_kembali' => null,
        'catatan_kembali' => null,
        'status' => 'Dipinjam',
        'keterangan' => 'Digunakan untuk media tayang pembelajaran materi Gelombang Elektromagnetik'
    ],
    [
        'kode_aset' => 'AST-2026-024', // Raket badminton
        'guru_username' => 'okta123',
        'lokasi_id' => 14, // Aula
        'tgl_pinjam' => '2026-08-21',
        'tgl_kembali_rencana' => '2026-08-24',
        'tgl_kembali_aktual' => null,
        'kondisi_kembali' => null,
        'catatan_kembali' => null,
        'status' => 'Dipinjam',
        'keterangan' => 'Peralatan latihan pertandingan bulutangkis antar madrasah'
    ],
    // Dikembalikan (Completed loans)
    [
        'kode_aset' => 'AST-2025-021', // Mikroskop
        'guru_username' => 'nurul',
        'lokasi_id' => 12, // Lab IPA
        'tgl_pinjam' => '2026-08-15',
        'tgl_kembali_rencana' => '2026-08-17',
        'tgl_kembali_aktual' => '2026-08-17',
        'kondisi_kembali' => 'Baik',
        'catatan_kembali' => 'Alat dikembalikan lengkap dengan kotak lensa dalam kondisi bersih dan berfungsi normal.',
        'status' => 'Dikembalikan',
        'keterangan' => 'Praktikum pengamatan sel tumbuhan kelas XI IPA'
    ],
    [
        'kode_aset' => 'AST-2024-003', // Komputer Desktop
        'guru_username' => 'budi',
        'lokasi_id' => 11, // Lab Komputer
        'tgl_pinjam' => '2026-08-10',
        'tgl_kembali_rencana' => '2026-08-12',
        'tgl_kembali_aktual' => '2026-08-12',
        'kondisi_kembali' => 'Baik',
        'catatan_kembali' => 'Komputer dikembalikan utuh dengan kabel power dan monitor.',
        'status' => 'Dikembalikan',
        'keterangan' => 'Instalasi server lokal simulasi ANBK'
    ],
    [
        'kode_aset' => 'AST-2024-022', // Torso Anatomi
        'guru_username' => 'rahmah',
        'lokasi_id' => 6, // Kelas XI-A
        'tgl_pinjam' => '2026-08-05',
        'tgl_kembali_rencana' => '2026-08-06',
        'tgl_kembali_aktual' => '2026-08-06',
        'kondisi_kembali' => 'Baik',
        'catatan_kembali' => 'Organ torso lengkap tanpa cacat.',
        'status' => 'Dikembalikan',
        'keterangan' => 'Peraga pembelajaran struktur pencernaan biologi manusia'
    ],
    // Ditolak (Rejected loan)
    [
        'kode_aset' => 'AST-2026-016', // Smart TV
        'guru_username' => 'hendra',
        'lokasi_id' => 4, // Kelas X-A
        'tgl_pinjam' => '2026-08-18',
        'tgl_kembali_rencana' => '2026-08-19',
        'tgl_kembali_aktual' => null,
        'kondisi_kembali' => null,
        'catatan_kembali' => null,
        'status' => 'Ditolak',
        'keterangan' => 'Permintaan ditolak karena Smart TV Aula sudah dijadwalkan untuk gladi bersih pelantikan OSIM'
    ]
];

$loanCount = 0;
foreach ($loans as $l) {
    // Find asset ID
    $stmtAset = $pdo->prepare("SELECT id, nama_aset FROM aset WHERE kode_aset = ?");
    $stmtAset->execute([$l['kode_aset']]);
    $asetRow = $stmtAset->fetch(PDO::FETCH_ASSOC);

    // Find guru ID
    $stmtGuru = $pdo->prepare("SELECT id, nama FROM users WHERE username = ?");
    $stmtGuru->execute([$l['guru_username']]);
    $guruRow = $stmtGuru->fetch(PDO::FETCH_ASSOC);

    if ($asetRow && $guruRow) {
        $stmtInsert = $pdo->prepare("
            INSERT INTO peminjaman 
            (id_aset, nama_peminjam, id_peminjam, id_lokasi, tanggal_pinjam, tanggal_kembali_rencana, tanggal_kembali_aktual, kondisi_saat_dikembalikan, catatan_pengembalian, status, keterangan, id_user)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
        ");
        $stmtInsert->execute([
            $asetRow['id'],
            $guruRow['nama'],
            $guruRow['id'],
            $l['lokasi_id'],
            $l['tgl_pinjam'],
            $l['tgl_kembali_rencana'],
            $l['tgl_kembali_aktual'],
            $l['kondisi_kembali'],
            $l['catatan_kembali'],
            $l['status'],
            $l['keterangan']
        ]);
        $loanCount++;
        echo " [+] Menambahkan Peminjaman: {$asetRow['nama_aset']} oleh {$guruRow['nama']} ({$l['status']})\n";
    }
}
echo "Total Peminjaman baru ditambahkan: $loanCount\n\n";

// 4. SEED MUTASI ASET
$mutations = [
    [
        'kode_aset' => 'AST-2023-004', // Projector
        'lokasi_asal' => 2, // Ruang Guru
        'lokasi_tujuan' => 8, // Kelas XII-A
        'tanggal_mutasi' => '2026-08-10',
        'keterangan' => 'Pemindahan proyektor tetap untuk kebutuhan pembelajaran interaktif kelas XII-A'
    ],
    [
        'kode_aset' => 'AST-2025-017', // Sound system
        'lokasi_asal' => 15, // Gudang
        'lokasi_tujuan' => 2, // Ruang Guru
        'tanggal_mutasi' => '2026-08-01',
        'keterangan' => 'Penempatan sound system portabel di Ruang Guru agar mudah diakses saat rapat madrasah'
    ],
    [
        'kode_aset' => 'AST-2024-020', // Lemari Arsip
        'lokasi_asal' => 1, // Ruang Kepala
        'lokasi_tujuan' => 3, // Ruang TU
        'tanggal_mutasi' => '2026-07-25',
        'keterangan' => 'Pemindahan lemari arsip ke Ruang Tata Usaha untuk integrasi data kepegawaian'
    ]
];

$mutasiCount = 0;
foreach ($mutations as $m) {
    $stmtAset = $pdo->prepare("SELECT id, nama_aset FROM aset WHERE kode_aset = ?");
    $stmtAset->execute([$m['kode_aset']]);
    $asetRow = $stmtAset->fetch(PDO::FETCH_ASSOC);

    if ($asetRow) {
        $stmtInsert = $pdo->prepare("
            INSERT INTO mutasi_aset (id_aset, id_lokasi_asal, id_lokasi_tujuan, tanggal_mutasi, keterangan, id_user)
            VALUES (?, ?, ?, ?, ?, 1)
        ");
        $stmtInsert->execute([
            $asetRow['id'],
            $m['lokasi_asal'],
            $m['lokasi_tujuan'],
            $m['tanggal_mutasi'],
            $m['keterangan']
        ]);
        $mutasiCount++;
        echo " [+] Menambahkan Mutasi: {$asetRow['nama_aset']} ({$m['keterangan']})\n";
    }
}
echo "Total Mutasi baru ditambahkan: $mutasiCount\n\n";

// 5. RIWAYAT AKTIVITAS
$activities = [
    ['id_user' => 1, 'aktivitas' => 'Tambah Aset', 'keterangan' => 'Menambah aset baru: Laptop ASUS ExpertBook B1400 (AST-2026-015)'],
    ['id_user' => 1, 'aktivitas' => 'Tambah Aset', 'keterangan' => 'Menambah aset baru: Smart TV Samsung 55 Inch Crystal UHD (AST-2026-016)'],
    ['id_user' => 1, 'aktivitas' => 'Tambah Aset', 'keterangan' => 'Menambah aset baru: Sound System Portable Baretone 15 Inch (AST-2025-017)'],
    ['id_user' => 1, 'aktivitas' => 'Mutasi Aset', 'keterangan' => 'Mutasi aset: Projector Epson dari Ruang Guru ke Ruang Kelas XII-A'],
    ['id_user' => 1, 'aktivitas' => 'Konfirmasi', 'keterangan' => 'Menyetujui peminjaman aset: Projector Epson oleh Nurul Hidayah, M.Pd']
];

foreach ($activities as $act) {
    $stmtAct = $pdo->prepare("INSERT INTO riwayat_aktivitas (id_user, aktivitas, keterangan) VALUES (?, ?, ?)");
    $stmtAct->execute([$act['id_user'], $act['aktivitas'], $act['keterangan']]);
}
echo "Riwayat aktivitas berhasil disinkronkan.\n";
echo "\n=== SEEDING SELESAI DENGAN SUKSES! ===\n";
