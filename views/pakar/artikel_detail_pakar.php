<?php
$activeMenu = 'artikel';

include '../../includes/artikel_pakar_data.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Ambil data artikel berdasarkan ID
$artikel = getArtikelPakarById($id);

// Jika artikel tidak ditemukan, redirect ke halaman artikel
if (!$artikel) {
    header('Location: index_pakar.php?page=artikel_pakar');
    exit;
}

// Ambil artikel terkait
$artikelTerkait = getArtikelPakarTerkait($id, 3);

include '../../includes/pakar/header_pakar.php';
?>

<link rel="stylesheet" href="/assets/css/artikel_detail.css">

<section class="breadcrumb-section">
  <div class="container">
    <nav class="breadcrumb">
      <a href="index_pakar.php?page=home_pakar">Home</a>
      <span class="separator">/</span>
      <a href="index_pakar.php?page=artikel_pakar">Artikel</a>
      <span class="separator">/</span>
      <span class="current">Detail Artikel</span>
    </nav>
  </div>
</section>

<section class="article-detail-section">
  <div class="container">
    <article class="article-detail">
      
      <div class="article-header">
        <div class="article-category">
          <span class="category-tag"><?php echo $artikel['kategori']; ?></span>
        </div>
        <h1 class="article-title"><?php echo $artikel['judul']; ?></h1>
        <div class="article-meta">
          <span class="meta-author">
            <img src="../../assets/img/aquara/admin-icon.png" alt="Admin" class="author-icon">
            <strong><?php echo $artikel['penulis']; ?></strong>
          </span>
          <span class="meta-date">
            <img src="../../assets/img/aquara/kalender.png" alt="Date" class="date-icon">
            <?php echo $artikel['tanggal']; ?>
          </span>
          <span class="meta-views">
            <img src="../../assets/img/aquara/views.png" alt="Views" class="view-icon">
            <?php echo $artikel['views']; ?> views
          </span>
        </div>
      </div>

      <div class="article-image">
        <img src="../../<?php echo $artikel['gambar']; ?>" alt="<?php echo $artikel['judul']; ?>">
      </div>

      <div class="article-content">
        <?php echo $artikel['konten']; ?>
      </div>

      <div class="article-footer">
        <a href="index_pakar.php?page=artikel_pakar" class="btn-back">← Kembali ke Artikel</a>
        <div class="share-buttons">
          <span>Bagikan:</span>
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>" target="_blank" class="share-btn facebook">Facebook</a>
          <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($artikel['judul']); ?>" target="_blank" class="share-btn twitter">Twitter</a>
          <a href="https://wa.me/?text=<?php echo urlencode($artikel['judul'] . ' - http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>" target="_blank" class="share-btn whatsapp">WhatsApp</a>
        </div>
      </div>

    </article>

    <section class="related-articles">
      <h2 class="section-title">Artikel Terkait dari Pakar</h2>
      <div class="related-grid">
        <?php foreach($artikelTerkait as $related): ?>
        <a href="index_pakar.php?page=artikel_detail_pakar&id=<?php echo $related['id']; ?>" class="related-card">
          <img src="../../<?php echo $related['gambar']; ?>" alt="<?php echo $related['judul']; ?>" class="related-image">
          <div class="related-content">
            <span class="related-tag"><?php echo $related['kategori']; ?></span>
            <h3 class="related-title"><?php echo $related['judul']; ?></h3>
            <p class="related-date"><?php echo $related['tanggal']; ?></p>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </section>
  </div>
</section>

<?php include '../../includes/pakar/footer_pakar.php'; ?>
