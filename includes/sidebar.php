        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <img src="<?= BASE_URL ?>/assets/uploads/logo.png.PNG" alt="Logo MAN 2 HSU" class="logo-img">
                </div>
                <div class="sidebar-brand">
                    <h2>Inventaris Aset</h2>
                    <small>MAN 2 HSU</small>
                </div>
            </div>

            <nav class="sidebar-nav">
            <?php if ($_SESSION['user_role'] === 'guru'): ?>
                <!-- ========== MENU GURU ========== -->
                <div class="nav-section">
                    <div class="nav-section-title">Menu Utama</div>
                    <a href="<?= BASE_URL ?>/guru/dashboard" 
                       class="nav-link <?= ($currentDir === 'guru' && $currentPage === 'dashboard') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-home"></i></span>
                        Dashboard
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Peminjaman</div>
                    <a href="<?= BASE_URL ?>/guru/katalog" 
                       class="nav-link <?= ($currentDir === 'guru' && $currentPage === 'katalog') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-boxes-stacked"></i></span>
                        Katalog Aset
                    </a>
                    <a href="<?= BASE_URL ?>/guru/pinjam" 
                       class="nav-link <?= ($currentDir === 'guru' && $currentPage === 'pinjam') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-hand-holding-hand"></i></span>
                        Ajukan Peminjaman
                    </a>
                    <a href="<?= BASE_URL ?>/guru/riwayat" 
                       class="nav-link <?= ($currentDir === 'guru' && $currentPage === 'riwayat') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-clock-rotate-left"></i></span>
                        Riwayat Peminjaman
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Akun</div>
                    <a href="<?= BASE_URL ?>/guru/profil" 
                       class="nav-link <?= ($currentDir === 'guru' && $currentPage === 'profil') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-user-circle"></i></span>
                        Profil Saya
                    </a>
                </div>
            <?php else: ?>
                <!-- ========== MENU ADMIN/PETUGAS ========== -->
                <!-- Menu Utama -->
                <div class="nav-section">
                    <div class="nav-section-title">Menu Utama</div>
                    <a href="<?= BASE_URL ?>/dashboard" 
                       class="nav-link <?= ($currentPage === 'dashboard') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                        Dashboard
                    </a>
                    <a href="<?= BASE_URL ?>/scan-qr" 
                       class="nav-link <?= ($currentPage === 'scan_qr') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-qrcode"></i></span>
                        Scan QR Code
                    </a>
                </div>

                <!-- Master Data -->
                <div class="nav-section">
                    <div class="nav-section-title">Master Data</div>
                    <a href="<?= BASE_URL ?>/aset" 
                       class="nav-link <?= ($currentDir === 'aset') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-boxes-stacked"></i></span>
                        Data Aset
                    </a>
                    <a href="<?= BASE_URL ?>/kategori" 
                       class="nav-link <?= ($currentDir === 'kategori') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-tags"></i></span>
                        Kategori
                    </a>
                    <a href="<?= BASE_URL ?>/lokasi" 
                       class="nav-link <?= ($currentDir === 'lokasi') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-location-dot"></i></span>
                        Lokasi / Ruangan
                    </a>
                </div>

                <!-- Transaksi -->
                <div class="nav-section">
                    <div class="nav-section-title">Transaksi</div>
                    <a href="<?= BASE_URL ?>/peminjaman" 
                       class="nav-link <?= ($currentDir === 'peminjaman') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-handshake"></i></span>
                        Peminjaman
                    </a>
                </div>

                <!-- Laporan -->
                <div class="nav-section has-dropdown <?= ($currentDir === 'laporan') ? 'open' : '' ?>">
                    <div class="nav-dropdown-toggle" onclick="this.parentElement.classList.toggle('open')">
                        <span class="nav-dropdown-title">
                            <i class="fas fa-folder-open nav-icon"></i>
                            Laporan
                        </span>
                        <i class="fas fa-chevron-down caret"></i>
                    </div>
                    <div class="nav-dropdown-menu">
                        <a href="<?= BASE_URL ?>/laporan/keseluruhan" 
                           class="nav-link <?= ($currentDir === 'laporan' && $currentPage === 'aset_keseluruhan') ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-file-lines"></i></span>
                            Inventaris Keseluruhan
                        </a>
                        <a href="<?= BASE_URL ?>/laporan/kategori" 
                           class="nav-link <?= ($currentDir === 'laporan' && $currentPage === 'aset_per_kategori') ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-layer-group"></i></span>
                            Aset per Kategori
                        </a>
                        <a href="<?= BASE_URL ?>/laporan/lokasi" 
                           class="nav-link <?= ($currentDir === 'laporan' && $currentPage === 'aset_per_lokasi') ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-map-location-dot"></i></span>
                            Aset per Lokasi
                        </a>
                        <a href="<?= BASE_URL ?>/laporan/kondisi" 
                           class="nav-link <?= ($currentDir === 'laporan' && $currentPage === 'kondisi_aset') ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-clipboard-check"></i></span>
                            Kondisi Aset
                        </a>
                        <a href="<?= BASE_URL ?>/laporan/peminjaman" 
                           class="nav-link <?= ($currentDir === 'laporan' && $currentPage === 'peminjaman') ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-hand-holding"></i></span>
                            Laporan Peminjaman
                        </a>
                        <a href="<?= BASE_URL ?>/laporan/riwayat" 
                           class="nav-link <?= ($currentDir === 'laporan' && $currentPage === 'riwayat_aset') ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-history"></i></span>
                            Riwayat per Aset
                        </a>
                        <a href="<?= BASE_URL ?>/laporan/masuk" 
                           class="nav-link <?= ($currentDir === 'laporan' && $currentPage === 'aset_masuk') ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-arrow-right-to-bracket"></i></span>
                            Aset Masuk
                        </a>
                        <a href="<?= BASE_URL ?>/laporan/mutasi" 
                           class="nav-link <?= ($currentDir === 'laporan' && $currentPage === 'mutasi_aset') ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-right-left"></i></span>
                            Mutasi Aset
                        </a>
                        <a href="<?= BASE_URL ?>/laporan/penghapusan" 
                           class="nav-link <?= ($currentDir === 'laporan' && $currentPage === 'penghapusan_aset') ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-trash-can"></i></span>
                            Penghapusan Aset
                        </a>
                    </div>
                </div>

                <!-- Admin Only -->
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <div class="nav-section">
                    <div class="nav-section-title">Administrasi</div>
                    <a href="<?= BASE_URL ?>/pengguna" 
                       class="nav-link <?= ($currentDir === 'pengguna') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-users-gear"></i></span>
                        Manajemen Pengguna
                    </a>
                    <a href="<?= BASE_URL ?>/riwayat" 
                       class="nav-link <?= ($currentPage === 'riwayat') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-clock-rotate-left"></i></span>
                        Riwayat Aktivitas
                    </a>
                </div>
                <?php endif; ?>

                <!-- Sistem & Notifikasi (Admin & Petugas) -->
                <div class="nav-section">
                    <div class="nav-section-title">Sistem & Notifikasi</div>
                    <a href="<?= BASE_URL ?>/pengaturan-email" 
                       class="nav-link <?= ($currentDir === 'pengaturan' && $currentPage === 'index') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-envelope-circle-check"></i></span>
                        Pengaturan Email
                    </a>
                    <a href="<?= BASE_URL ?>/log-notifikasi" 
                       class="nav-link <?= ($currentDir === 'pengaturan' && $currentPage === 'log_email') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-rectangle-list"></i></span>
                        Log Notifikasi
                    </a>
                </div>
            <?php endif; ?>
            </nav>

            <div class="sidebar-footer">
                <div class="sidebar-user">
                    <div class="user-avatar">
                        <?= strtoupper(substr($_SESSION['user_nama'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div class="user-info">
                        <div class="user-name"><?= htmlspecialchars($_SESSION['user_nama'] ?? 'User') ?></div>
                        <div class="user-role"><?= htmlspecialchars($_SESSION['user_role'] ?? 'petugas') ?></div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Topbar -->
            <header class="topbar">
                <div class="topbar-left">
                    <button class="topbar-toggle" onclick="toggleSidebar()">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="topbar-title">
                        <h1><?= $pageTitle ?? 'Dashboard' ?></h1>
                    </div>
                </div>
                <div class="topbar-right">
                    <?php if ($_SESSION['user_role'] === 'guru'): ?>
                        <a href="<?= BASE_URL ?>/guru/profil" class="topbar-btn" title="Profil">
                            <i class="fas fa-user"></i>
                        </a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/profil" class="topbar-btn" title="Profil">
                            <i class="fas fa-user"></i>
                        </a>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/logout" class="topbar-btn btn-logout" title="Logout">
                        <i class="fas fa-right-from-bracket"></i>
                    </a>
                </div>
            </header>

            <!-- Page Content -->
            <div class="page-content">
                <?php 
                // Flash messages
                $flash = getFlash();
                if ($flash): ?>
                    <div class="alert alert-<?= $flash['type'] ?>">
                        <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'danger' ? 'exclamation-circle' : 'info-circle') ?>"></i>
                        <?= htmlspecialchars($flash['message']) ?>
                    </div>
                <?php endif; ?>
