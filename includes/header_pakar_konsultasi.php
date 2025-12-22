<?php
$activeMenu = $activeMenu ?? '';
?>

<link rel="stylesheet" href="/aquara/assets/css/header_footer_pakar.css">
<link rel="stylesheet" href="/aquara/assets/css/artikel_pakar_custom.css">
<link rel="stylesheet" href="/aquara/assets/css/konsultasi_pakar_2.css">

<header class="site-header">
  <div class="container header-container">
    <a href="index_pakar.php?page=home_pakar" class="logo">
      <img src="/aquara/assets/img/aquara/logo.png" alt="Aquara Logo" class="logo-img">
      <span class="logo-text">AQUARA</span>
    </a>

    <nav class="main-nav">
      <ul>
        <li><a class="<?= ($activeMenu=='home')?'active':'' ?>" href="index_pakar.php?page=home_pakar">HOME</a></li>
        <li><a class="<?= ($activeMenu=='artikel')?'active':'' ?>" href="index_pakar.php?page=artikel_pakar">ARTIKEL</a></li>
        <li><a class="<?= ($activeMenu=='forum')?'active':'' ?>" href="index_pakar.php?page=forum_pakar">FORUM</a></li>
        <li><a class="<?= ($activeMenu=='event')?'active':'' ?>" href="index_pakar.php?page=event_pakar">EVENT</a></li>
        <li><a class="<?= ($activeMenu=='konsultasi')?'active':'' ?>" href="index_pakar.php?page=konsultasi_pakar">KONSULTASI</a></li>
        <li><a class="<?= ($activeMenu=='kalkulator')?'active':'' ?>" href="index_pakar.php?page=kalkulator_pakar">KALKULATOR</a></li>
      </ul>
    </nav>

    <!-- Bagian profil pakar -->
    <div class="user-profile">
      <div class="user-info">
        <p class="user-name">Dr. Ahmad Budiman</p>
        <p class="user-role">Pakar/Ahli</p>
      </div>
      <img src="/aquara/assets/img/aquara/profil.png" alt="User Avatar" class="user-avatar">
      <img src="/aquara/assets/img/aquara/dropdown.png" alt="Dropdown" class="dropdown-arrow">
    </div>
  </div>
</header>