<?php
$activeMenu = $activeMenu ?? '';
?>

<link rel="stylesheet" href="assets/css/header_footer_pakar.css">
<style>
    /* Style Header Pengunjung (Sama dengan Anggota) */
    .site-header {
        background-color: #013746; /* Warna latar hijau tua */
        padding: 15px 0;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        position: sticky;
        top: 0;
        z-index: 1000;
        font-family: 'Poppins', sans-serif;
    }

    .header-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 70px; /* Tinggi tetap agar konsisten */
    }

    /* LOGO */
    .logo {
        display: flex;
        align-items: center;
        text-decoration: none;
        gap: 10px;
    }
    .logo-img {
        height: 40px;
        width: auto;
    }
    .logo-text {
        font-size: 24px;
        font-weight: bold;
        color: white;
        letter-spacing: 1px;
    }

    /* NAVIGASI */
    .main-nav ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        gap: 25px;
    }
    .main-nav a {
        text-decoration: none;
        color: white;
        font-weight: 500;
        font-size: 14px;
        text-transform: uppercase;
        transition: color 0.3s;
    }
    .main-nav a:hover {
        color: #63c9c9; /* Warna aksen saat hover */
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
</style>

<header class="site-header">
    <div class="header-container">
        <a href="index.php?page=home" class="logo">
            <img src="assets/img/aquara/logo.png" alt="AQUARA Logo" class="logo-img">
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
</header>