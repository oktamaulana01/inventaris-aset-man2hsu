import styles from "./page.module.css";

export default function Home() {
  return (
    <main className={styles.main}>
      <section className={styles.hero}>
        <div className={styles.heroBackground}></div>
        <div className={styles.heroContent}>
          <div className={styles.badge}>🚀 Sistem Informasi Modern</div>
          <h1 className={styles.title}>
            Manajemen Aset Lebih Cerdas & Cepat
          </h1>
          <p className={styles.description}>
            Platform digital terintegrasi untuk pendataan, pelacakan, dan peminjaman inventaris di lingkungan MAN 2 Hulu Sungai Utara. Semuanya dalam satu genggaman.
          </p>
          <div className={styles.ctaGroup}>
            <a href="http://localhost/inventaris-aset-man2hsu/login.php" className="btn-primary">
              Masuk ke Sistem
            </a>
            <a href="#features" className="btn-secondary">
              Pelajari Fitur
            </a>
          </div>
        </div>
      </section>

      <section id="features" className={styles.featuresSection}>
        <h2 className={styles.sectionTitle}>Fitur Unggulan</h2>
        <div className={styles.grid}>
          
          <div className={`${styles.card} glass`}>
            <div className={styles.iconWrapper}>📱</div>
            <h3 className={styles.cardTitle}>Scan QR Code</h3>
            <p className={styles.cardDesc}>
              Lacak status, kondisi, dan lokasi barang secara instan hanya dengan memindai kode QR yang tertempel di setiap aset.
            </p>
          </div>

          <div className={`${styles.card} glass`}>
            <div className={styles.iconWrapper}>🤝</div>
            <h3 className={styles.cardTitle}>Peminjaman Cerdas</h3>
            <p className={styles.cardDesc}>
              Alur peminjaman barang untuk guru yang terstruktur, lengkap dengan notifikasi persetujuan otomatis melalui sistem.
            </p>
          </div>

          <div className={`${styles.card} glass`}>
            <div className={styles.iconWrapper}>📊</div>
            <h3 className={styles.cardTitle}>Laporan Otomatis</h3>
            <p className={styles.cardDesc}>
              Hasilkan laporan inventaris, mutasi aset, hingga barang rusak secara otomatis dengan sekali klik. Siap diekspor ke PDF/Excel.
            </p>
          </div>

        </div>
      </section>

      <footer className={styles.footer}>
        <p>&copy; {new Date().getFullYear()} MAN 2 Hulu Sungai Utara. All rights reserved.</p>
        <p style={{fontSize: "0.8rem", marginTop: "0.5rem", opacity: 0.7}}>Developed as Next.js Headless Concept</p>
      </footer>
    </main>
  );
}
