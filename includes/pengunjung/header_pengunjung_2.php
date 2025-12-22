<?php
$activeMenu = $activeMenu ?? '';
?>

<link rel="stylesheet" href="/assets/css/header_footer_pakar.css">
<link rel="stylesheet" href="/assets/css/artikel_pakar_custom.css">

<style>
     /* Style Header Pengunjung (Sama dengan Anggota) */
    .site-header {
        background-color: #013746; /* Warna latar hijau tua */
        box-shadow: 0px -19px 62.8px 4px #0b4d68;
        padding: 0px 0;
        position: sticky;
        top: 0;
        z-index: 1000;
        font-family: 'Poppins', sans-serif;
    }

    .header-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
   /* TOMBOL MASUK (Pengganti Profil) */
    .btn-masuk {
        background-color: white;
        color: #013746; /* Warna teks hijau tua */
        padding: 10px 25px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 700; /* Tebal seperti di gambar */
        font-size: 16px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .btn-masuk:hover {
        background-color: #f0f0f0;
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }
  /* Perbaiki path link navbar (jika diperlukan) */
  .main-nav a {
    text-decoration: none; /* Pastikan tidak ada garis bawah default */
  }
</style>

<header class="site-header">
  <div class="container header-container">
    <a href="index.php?page=home" class="logo">
      <img src="/assets/img/aquara/logo.png" alt="Aquara Logo" class="logo-img">
      <span class="logo-text">AQUARA</span>
    </a>

    <nav class="main-nav">
      <ul>
        <li><a class="<?= ($activeMenu=='home')?'active':'' ?>" href="index.php?page=home">HOME</a></li>
        <li><a class="<?= ($activeMenu=='artikel')?'active':'' ?>" href="index.php?page=artikel">ARTIKEL</a></li>
        <li><a class="<?= ($activeMenu=='forum')?'active':'' ?>" href="index.php?page=forum">FORUM</a></li>
        <li><a class="<?= ($activeMenu=='event')?'active':'' ?>" href="index.php?page=event">EVENT</a></li>
        <li><a class="<?= ($activeMenu=='konsultasi')?'active':'' ?>" href="index.php?page=konsultasi">KONSULTASI</a></li>
        <li><a class="<?= ($activeMenu=='kalkulator')?'active':'' ?>" href="index.php?page=kalkulator">KALKULATOR</a></li>
      </ul>
    </nav>

    <a href="index.php?page=login" class="btn-masuk">Masuk</a>
    </div>
  </div>
</header>