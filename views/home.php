<?php
$activeMenu = 'home';

$this->layout('layout', ['activeMenu' => $activeMenu]);
?>

<?php $this->start('body') ?>

<section id="hero">
  <img src="assets/img/aquara/background1.png" alt="Underwater background" class="hero-bg">
  <div class="container hero-container">
    <div class="hero-content-left">
      <p class="welcome-badge">Selamat Datang di AQUARA</p>
      <h1 class="hero-title">Platform Edukasi<br>dan Komunitas<br>Perikanan Air Tawar</h1>
      <p class="hero-description">Platform ini berfokus pada pemberdayaan penambak ikan air tawar dengan menyediakan materi edukasi seputar teknik budidaya, membentuk komunitas online sebagai forum diskusi dan berbagi pengalaman, serta menghadirkan kalkulator budidaya untuk membantu menghitung estimasi biaya, kebutuhan pakan, dan potensi hasil panen.</p>
      <a href="?page=register" class="btn btn-register">
        <span>Daftar Sekarang</span>
        <img src="assets/img/aquara/register.png" alt="Arrow icon" onerror="this.style.display='none'">
      </a>
    </div>
    <div class="hero-content-right">
      <img src="assets/img/aquara/logo.png" alt="AQUARA Logo" class="hero-logo-large">
    </div>
  </div>
</section>

<section id="features">
  <div class="container features-container">
    <h2 class="section-title">Fitur AQUARA</h2>
    <div class="features-grid">
      <div class="feature-card">
        <img src="/assets/img/aquara/forum_logo_fix.jpg" alt="Forum Icon" class="feature-icon">
        <h3 class="feature-title">FORUM</h3>
        <p class="feature-description">Bergabunglah dengan komunitas peternak ikan air tawar untuk sharing tips</p>
      </div>
      <div class="feature-card">
        <img src="/assets/img/aquara/logo_konsultasi_fix.png" alt="Konsultasi Icon" class="feature-icon">
        <h3 class="feature-title">KONSULTASI</h3>
        <p class="feature-description">Dapatkan konsultasi langsung dari para ahli perikanan berpengalaman</p>
      </div>
      <div class="feature-card">
        <img src="/assets/img/aquara/logo_kalkulator_fix.png" alt="Kalkulator Icon" class="feature-icon">
        <h3 class="feature-title">KALKULATOR</h3>
        <p class="feature-description">Hitung keuntungan dan analisis bisnis perikanan Anda dengan mudah</p>
      </div>
    </div>
  </div>
</section>

<section id="articles">
  <div class="container articles-container">
    <h2 class="section-title-light">Artikel Terbaru</h2>
    <div class="articles-grid">
      
      <?php
      // --- PERBAIKAN KONEKSI DATABASE (ANTI-GAGAL) ---
      // 1. Coba ambil koneksi dari global scope (biasanya ini masalahnya)
      global $conn;

      // 2. Jika masih tidak ketemu, paksa include manual dengan path absolut yang pasti
      if (!isset($conn) || !$conn) {
          $real_config_path = realpath(__DIR__ . '/../includes/config.php');
          if ($real_config_path && file_exists($real_config_path)) {
              include_once $real_config_path;
          }
      }

      // 3. Cek final dan jalankan query
      if (isset($conn) && $conn instanceof mysqli && !$conn->connect_error) {
          $sql_artikel = "SELECT id, judul, konten, gambar, created_at FROM articles ORDER BY created_at DESC LIMIT 4";
          $result_artikel = $conn->query($sql_artikel);

          if ($result_artikel && $result_artikel->num_rows > 0) {
              while ($row = $result_artikel->fetch_assoc()) {
                  // Path gambar relatif dari root
                  $gambar_db = "uploads/articles/" . $row['gambar'];
                  // Cek fisik file
                  if (!empty($row['gambar']) && file_exists($gambar_db)) {
                      $gambar_final = $gambar_db;
                  } else {
                      $gambar_final = "assets/img/aquara/ar1.png"; // Gambar default
                  }

                  $konten_bersih = strip_tags($row['konten']);
                  $cuplikan = substr($konten_bersih, 0, 100) . "...";
      ?>
                  <div class="article-card">
                    <img src="<?php echo htmlspecialchars($gambar_final); ?>" alt="<?php echo htmlspecialchars($row['judul']); ?>" class="article-img" onerror="this.src='assets/img/aquara/ar1.png'">
                    <div class="article-content">
                      <h3><?php echo htmlspecialchars($row['judul']); ?></h3>
                      <p><?php echo htmlspecialchars($cuplikan); ?></p>
                    </div>
                  </div>
      <?php
              }
          } else {
              echo "<p style='color: white; text-align: center; width: 100%; opacity: 0.8;'>Belum ada artikel terbaru saat ini.</p>";
          }
      } else {
          // Debugging error jika masih gagal (Hanya muncul jika koneksi benar-benar mati)
          $error_msg = isset($conn) ? $conn->connect_error : "Variabel koneksi tidak ditemukan.";
          echo "<p style='color: #ffcccc; text-align: center; width: 100%; padding: 20px; background: rgba(255,0,0,0.1); border-radius: 8px;'>
                  Maaf, sedang terjadi gangguan koneksi ke server artikel.<br>
                  <small>(Debug: $error_msg)</small>
                </p>";
      }
      ?>

    </div>
    <a href="?page=artikel" class="btn btn-more">
      <span>Lihat Semua Artikel</span>
      <img src="assets/img/aquara/panahkanan.png" alt="Arrow Right">
    </a>
  </div>
</section>

<?php $this->end() ?>