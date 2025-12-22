<?php
$activeMenu = 'artikel';

$this->layout('layout', ['title' => 'Artikel - AQUARA', 'activeMenu' => $activeMenu]) ?>

<?php $this->start('styles') ?>
<link rel="stylesheet" href="/assets/css/artikel.css?v=<?= time() ?>">

<?php $this->stop() ?>

<?php $this->start('body') ?>
<section id="hero" class="hero-section" style="padding: 40px 0 !important; min-height: auto !important;">
  <div class="container">
    <h1 class="hero-title" style="margin: 0 !important;">Artikel</h1>
  </div>
</section>

<section id="articles" class="articles-section">
  <div class="container">
    <div class="article-grid">
      
      <?php
      // --- KONEKSI DATABASE & QUERY ---
      global $conn;
      if (!isset($conn)) { include_once __DIR__ . '/../includes/config.php'; }

      if (isset($conn) && $conn) {
          // Query ambil data artikel terbaru dari database
          $sql = "SELECT a.*, u.nama AS penulis 
                  FROM articles a 
                  LEFT JOIN users u ON a.user_id = u.id 
                  ORDER BY a.created_at DESC";
          $result = $conn->query($sql);

          if ($result && $result->num_rows > 0) {
              while ($artikel = $result->fetch_assoc()) {
                  // Persiapan Data agar sesuai tampilan
                  $judul = htmlspecialchars($artikel['judul']);
                  // Jika Anda punya kolom kategori di DB, ganti 'Umum' dengan $artikel['kategori']
                  $kategori = 'Informasi'; 
                  $penulis = htmlspecialchars($artikel['penulis'] ?? 'Admin');
                  $tanggal = date('d M Y', strtotime($artikel['created_at']));
                  $excerpt = substr(strip_tags($artikel['konten']), 0, 100) . "...";
                  
                  // Path gambar relatif dari root
                  $gambar_path = "uploads/articles/" . $artikel['gambar'];
                  $gambar = (!empty($artikel['gambar']) && file_exists($gambar_path)) ? $gambar_path : "assets/img/aquara/ar1.png";
      ?>
                  <a href="?page=login" class="article-card" onclick="return confirm('Maaf, Anda harus login untuk membaca artikel ini sepenuhnya. Ingin login sekarang?');">
                    <div class="card-image-container">
                      <img src="<?= $gambar ?>" alt="<?= $judul ?>" class="card-image">
                      <div class="card-tag"><?= $kategori ?></div>
                    </div>
                    <div class="card-content">
                      <h3 class="card-title"><?= $judul ?></h3>
                      <p class="card-excerpt"><?= $excerpt ?></p>
                      <p class="card-meta">by <strong><?= $penulis ?></strong> - <?= $tanggal ?></p>
                    </div>
                  </a>
      <?php
              }
          } else {
              echo '<p style="grid-column: 1/-1; text-align: center; color: #999; padding: 40px;">Belum ada artikel tersedia.</p>';
          }
      }
      ?>

    </div>
  </div>
</section>

<section id="cta" class="cta-section">
  <div class="container cta-container">
    <h2 class="cta-title">Anda ingin Konsultasi, Pelatihan, Pendampingan dan Kerja Sama di Bidang Perikanan?</h2>
    <a href="?page=login" class="btn-cta" onclick="return confirm('Silakan login terlebih dahulu untuk melakukan konsultasi.');">KONSULTASI</a>
  </div>
</section>

<?php $this->end() ?>