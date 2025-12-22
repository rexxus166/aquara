<?php
$activeMenu = 'home';

// Cek login & role pakar (role_id = 3)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role_id'] != 3) {
    header("Location: ../../index.php?page=login");
    exit;
}

// Koneksi database (sudah ada di index_pakar.php, tapi untuk jaga-jaga kita require once)
require_once '../../includes/config.php'; 

$nama_user = htmlspecialchars($_SESSION['nama']);
?>

<?php include '../../includes/pakar/header_pakar.php'; ?>
<link rel="stylesheet" href="../../assets/css/anggota/home_section_anggota.css?v=<?php echo time(); ?>">

<section id="hero">
  <img src="../../assets/img/aquara/background1.png" alt="Underwater background" class="hero-bg">
  <div class="container hero-container">
    <div class="hero-content-left">
      <p class="welcome-badge">Selamat Datang di AQUARA, Pakar!</p>
      <h1 class="hero-title">Platform Edukasi<br>dan Komunitas<br>Perikanan Air Tawar</h1>
      <p class="hero-description">Platform ini berfokus pada pemberdayaan penambak ikan air tawar dengan menyediakan materi edukasi seputar teknik budidaya, membentuk komunitas online sebagai forum diskusi dan berbagi pengalaman, serta menghadirkan kalkulator budidaya untuk membantu menghitung estimasi biaya, kebutuhan pakan, dan potensi hasil panen. Selain itu, platform ini juga memfasilitasi konsultasi online antara penambak dengan pakar agar setiap kendala dalam proses budidaya dapat diatasi dengan lebih cepat dan tepat.</p>
    </div>
    <div class="hero-content-right">
      <img src="../../assets/img/aquara/logo.png" alt="Yin Yang Koi Fish Logo" class="hero-logo-large">
    </div>
  </div>
</section>

<section id="features">
  <div class="container features-container">
    <h2 class="section-title">Fitur AQUARA</h2>
    <div class="features-grid">
      <div class="feature-card">
        <img src="../../assets/img/aquara/forum_logo_fix.jpg" alt="Forum Icon" class="feature-icon">
        <h3 class="feature-title">FORUM</h3>
        <p class="feature-description">Berdiskusi dan berbagi pengetahuan dengan komunitas peternak</p>
      </div>
      <div class="feature-card">
        <img src="../../assets/img/aquara/logo_konsultasi_fix.png" alt="Konsultasi Icon" class="feature-icon">
        <h3 class="feature-title">KONSULTASI</h3>
        <p class="feature-description">Berikan solusi terbaik untuk permasalahan para pembudidaya</p>
      </div>
      <div class="feature-card">
        <img src="../../assets/img/aquara/logo_kalkulator_fix.png" alt="Kalkulator Icon" class="feature-icon">
        <h3 class="feature-title">KALKULATOR</h3>
        <p class="feature-description">Bantu analisis estimasi biaya dan keuntungan budidaya</p>
      </div>
    </div>
  </div>
</section>

<section id="articles">
  <div class="container articles-container">
    <h2 class="section-title-light">Artikel Terbaru</h2>
    <div class="articles-grid">
      
      <?php
      // Query artikel terbaru
      $sql_artikel = "SELECT id, judul, konten, gambar, created_at FROM articles ORDER BY created_at DESC LIMIT 4";
      $result_artikel = $conn->query($sql_artikel);

      if ($result_artikel && $result_artikel->num_rows > 0) {
          while ($row = $result_artikel->fetch_assoc()) {
              $gambar_path = "../../uploads/articles/" . $row['gambar'];
              if (empty($row['gambar']) || !file_exists($gambar_path)) {
                  $gambar_path = "../../assets/img/aquara/ar1.png"; 
              }
              $konten_bersih = strip_tags($row['konten']);
              $cuplikan = substr($konten_bersih, 0, 120) . "...";
      ?>
              <div class="article-card">
                <img src="<?php echo htmlspecialchars($gambar_path); ?>" alt="<?php echo htmlspecialchars($row['judul']); ?>" class="article-img">
                <div class="article-content">
                  <h3><?php echo htmlspecialchars($row['judul']); ?></h3>
                  <p><?php echo htmlspecialchars($cuplikan); ?></p>
                </div>
              </div>
      <?php
          }
      } else {
          echo "<p style='color: white; text-align: center; width: 100%;'>Belum ada artikel terbaru.</p>";
      }
      ?>

    </div>
    <a href="index_pakar.php?page=artikel_pakar" class="btn btn-more">
      <span>Lihat Semua Artikel</span>
      <img src="../../assets/img/aquara/panahkanan.png" alt="Arrow Right">
    </a>
  </div>
</section>

<?php include '../../includes/pakar/footer_pakar.php'; ?>