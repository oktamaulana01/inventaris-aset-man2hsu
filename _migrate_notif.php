<?php
require_once __DIR__ . '/config/database.php';
$pdo = getConnection();

// Tabel email_notifications
$pdo->exec("CREATE TABLE IF NOT EXISTS email_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_peminjaman INT NOT NULL,
    tipe ENUM('reminder','due','overdue') NOT NULL,
    email_tujuan VARCHAR(255) NOT NULL,
    status ENUM('sent','failed') NOT NULL,
    pesan_error TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_peminjaman) REFERENCES peminjaman(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
echo "OK: tabel email_notifications\n";

// Tabel pengaturan
$pdo->exec("CREATE TABLE IF NOT EXISTS pengaturan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kunci VARCHAR(100) NOT NULL UNIQUE,
    nilai TEXT NULL,
    keterangan VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
echo "OK: tabel pengaturan\n";

// Data default SMTP
$defaults = [
    ['smtp_host', 'smtp.gmail.com', 'SMTP Server Host'],
    ['smtp_port', '587', 'SMTP Server Port'],
    ['smtp_username', '', 'Email pengirim (Gmail)'],
    ['smtp_password', '', 'App Password Gmail'],
    ['smtp_sender_name', 'MAN 2 HSU - Inventaris Aset', 'Nama pengirim'],
    ['smtp_sender_email', '', 'Alamat email pengirim'],
    ['notif_h_minus_1', '1', 'Kirim reminder H-1 (1=aktif, 0=nonaktif)'],
    ['notif_h_0', '1', 'Kirim reminder hari H (1=aktif, 0=nonaktif)'],
    ['notif_h_plus_1', '1', 'Kirim reminder H+1/overdue (1=aktif, 0=nonaktif)'],
];

$stmt = $pdo->prepare("INSERT IGNORE INTO pengaturan (kunci, nilai, keterangan) VALUES (?, ?, ?)");
foreach ($defaults as $d) {
    $stmt->execute($d);
}
echo "OK: data default pengaturan\n";
echo "\nSemua migrasi berhasil!\n";
?>
