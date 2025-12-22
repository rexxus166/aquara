<?php
// pages/artikel_pakar.php
$activeMenu = 'artikel';
include '../../includes/artikel_pakar_data.php';
$artikelList = getAllArtikelPakar();
?>

<?php include '../../includes/anggota/header_artikel_anggota.php'; ?>

<section id="hero" class="hero-section">
  <div class="container">
    <h1 class="hero-title">Artikel</h1>
  </div>
</section>

<section id="articles" class="articles-section">
  <div class="container">
    <div class="article-grid">
      
      <?php foreach($artikelList as $artikel): ?>
      <a href="index_anggota.php?page=artikel_detail_anggota&id=<?php echo $artikel['id']; ?>" class="article-card">
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
    <h2 class="cta-title">Anda ingin Konsultasi, Pelatihan, Pendampingan dan Kerja Sama di Bidang Perikanan?</h2>
    <a href="index_anggota.php?page=konsultasi_anggota" class="btn-cta">KONSULTASI</a>
  </div>
</section>

<?php include '../../includes/anggota/footer_artikel_anggota.php'; ?>
