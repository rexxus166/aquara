<?php
$activeMenu = 'artikel';

// pages/artikel_pakar.php
include '../../includes/artikel_pakar_data.php';
$artikelList = getAllArtikelPakar();
?>

<?php include '../../includes/pakar/header_pakar.php'; ?>

<link rel="stylesheet" href="/aquara/assets/css/artikel.css">

<section id="hero" class="hero-section">
  <div class="container">
    <h1 class="hero-title">Artikel</h1>
  </div>
</section>

<section id="articles" class="articles-section">
  <div class="container">
    <div class="article-grid">
      
      <?php foreach($artikelList as $artikel): ?>
      <a href="index_pakar.php?page=artikel_detail_pakar&id=<?php echo $artikel['id']; ?>" class="article-card">
        <div class="card-image-container">
          <img src="../../<?php echo $artikel['gambar']; ?>" alt="<?php echo $artikel['judul']; ?>" class="card-image">
          <div class="card-tag"><?php echo $artikel['kategori']; ?></div>
        </div>
        <div class="card-content">
          <h3 class="card-title"><?php echo $artikel['judul']; ?></h3>
          <p class="card-excerpt"><?php echo $artikel['excerpt']; ?></p>
          <p class="card-meta">by <strong><?php echo $artikel['penulis']; ?></strong> - <?php echo $artikel['tanggal']; ?></p>
        </div>
      </a>
      <?php endforeach; ?>

    </div>
  </div>
</section>

<section id="cta" class="cta-section">
  <div class="container cta-container">
    <h2 class="cta-title">Menjawab Konsultasi Anggota AQUARA?</h2>
    <a href="index_pakar.php?page=konsultasi_pakar" class="btn-cta">JAWAB KONSULTASI?</a>
  </div>
</section>

<?php include '../../includes/pakar/footer_pakar.php'; ?>
