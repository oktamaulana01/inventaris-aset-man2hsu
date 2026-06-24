<?php
$pageTitle = 'Lapor Aset Rusak';
require_once __DIR__ . '/../../includes/auth_check.php';
$pdo = getConnection();

// Ambil data aset yang kondisinya Baik
$asetList = $pdo->query("SELECT id, kode_aset, nama_aset, jumlah FROM aset WHERE kondisi = 'Baik' AND deleted_at IS NULL ORDER BY nama_aset")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validateCsrfToken();
    $idAset = $_POST['id_aset'];
    $jumlahRusak = intval($_POST['jumlah_rusak']);
    $kondisi = $_POST['kondisi'];
    $keterangan = trim($_POST['keterangan']);

    // Ambil data aset asli
    $stmt = $pdo->prepare("SELECT * FROM aset WHERE id = ?");
    $stmt->execute([$idAset]);
    $asetAsli = $stmt->fetch();

    if (!$asetAsli) {
        setFlash('danger', 'Aset tidak ditemukan!');
        header('Location: tambah_rusak.php');
        exit;
    }

    if ($jumlahRusak <= 0 || $jumlahRusak > $asetAsli['jumlah']) {
        setFlash('danger', 'Jumlah rusak tidak valid! Harus antara 1 sampai ' . $asetAsli['jumlah']);
        header('Location: tambah_rusak.php');
        exit;
    }

    if ($jumlahRusak == $asetAsli['jumlah']) {
        // Skenario A: Rusak semua stok aset tersebut
        $newKet = $asetAsli['keterangan'] ? $asetAsli['keterangan'] . "\n\nCatatan Rusak: " . $keterangan : "Catatan Rusak: " . $keterangan;
        $stmt = $pdo->prepare("UPDATE aset SET kondisi = ?, keterangan = ? WHERE id = ?");
        $stmt->execute([$kondisi, $newKet, $idAset]);
        logActivity($pdo, $_SESSION['user_id'], 'Ubah Kondisi Aset', "Mengubah seluruh stok {$asetAsli['nama_aset']} ({$asetAsli['kode_aset']}) menjadi $kondisi");
    } else {
        // Skenario B: Rusak sebagian -> Split Data Aset
        // 1. Kurangi stok barang yang kondisinya masih baik
        $pdo->prepare("UPDATE aset SET jumlah = jumlah - ? WHERE id = ?")->execute([$jumlahRusak, $idAset]);
        
        // 2. Buat record baru untuk barang yang rusak
        $kodeAsetBaru = generateKodeAset($pdo);
        $newKet = "Pemisahan dari kode {$asetAsli['kode_aset']} karena rusak.\n\n" . ($keterangan ? "Catatan Rusak: " . $keterangan : "");
        
        $stmt = $pdo->prepare("INSERT INTO aset (kode_aset, nama_aset, id_kategori, id_lokasi, jumlah, kondisi, tahun_perolehan, nilai_perolehan, sumber_dana, gambar, keterangan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $kodeAsetBaru, 
            $asetAsli['nama_aset'], 
            $asetAsli['id_kategori'], 
            $asetAsli['id_lokasi'], 
            $jumlahRusak, 
            $kondisi, 
            $asetAsli['tahun_perolehan'], 
            $asetAsli['nilai_perolehan'], 
            $asetAsli['sumber_dana'], 
            $asetAsli['gambar'], 
            $newKet
        ]);
        logActivity($pdo, $_SESSION['user_id'], 'Split Aset Rusak', "Memisah $jumlahRusak unit {$asetAsli['nama_aset']} yang rusak menjadi kode baru $kodeAsetBaru ($kondisi)");
    }
    
    setFlash('success', 'Laporan aset rusak berhasil diproses!');
    header('Location: ' . BASE_URL . '/pages/aset/index.php');
    exit;
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="page-header">
    <div>
        <h2><i class="fas fa-plus-circle text-warning"></i> Lapor Aset Rusak</h2>
        <div class="breadcrumb">
            <a href="<?= BASE_URL ?>/pages/dashboard.php">Dashboard</a>
            <span class="separator">/</span>
            <a href="<?= BASE_URL ?>/pages/aset/index.php">Data Aset</a>
            <span class="separator">/</span>
            <span>Lapor Rusak</span>
        </div>
    </div>
</div>

<div class="card animate-fadeInUp">
    <div class="card-header">
        <h3 class="text-warning">Form Lapor Aset Rusak</h3>
    </div>
    <div class="card-body">
        <form method="POST">
            <?= generateCsrfToken() ?>
            <div class="grid-2">
                <div class="form-group" style="grid-column: span 2;">
                    <label>Pilih Aset (Yang saat ini kondisinya Baik) *</label>
                    <select class="form-control" name="id_aset" id="selectAset" required>
                        <option value="">-- Pilih Aset --</option>
                        <?php foreach ($asetList as $a): ?>
                            <option value="<?= $a['id'] ?>" data-max="<?= $a['jumlah'] ?>">
                                <?= htmlspecialchars($a['kode_aset'] . ' - ' . $a['nama_aset']) ?> (Tersedia: <?= $a['jumlah'] ?> unit)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Jumlah Rusak *</label>
                    <input type="number" class="form-control" name="jumlah_rusak" id="inputJumlah" value="1" min="1" required>
                    <small class="text-muted" id="hintJumlah">Pilih aset terlebih dahulu.</small>
                </div>
                
                <div class="form-group">
                    <label>Kondisi *</label>
                    <select class="form-control" name="kondisi" required>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
            </div>
            <div class="form-group mt-3">
                <label>Keterangan Rusak *</label>
                <textarea class="form-control" name="keterangan" placeholder="Jelaskan bagian mana yang rusak atau penyebab kerusakannya..." required></textarea>
            </div>
            <div class="btn-group mt-3">
                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Proses Laporan</button>
                <a href="<?= BASE_URL ?>/pages/aset/index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Batal</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('selectAset').addEventListener('change', function() {
    var selected = this.options[this.selectedIndex];
    var max = selected.getAttribute('data-max');
    var inputJumlah = document.getElementById('inputJumlah');
    var hintJumlah = document.getElementById('hintJumlah');
    
    if (max) {
        inputJumlah.max = max;
        hintJumlah.textContent = 'Maksimal jumlah yang bisa dilaporkan rusak: ' + max + ' unit.';
        if (parseInt(inputJumlah.value) > parseInt(max)) {
            inputJumlah.value = max;
        }
    } else {
        inputJumlah.max = 1;
        hintJumlah.textContent = 'Pilih aset terlebih dahulu.';
    }
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
