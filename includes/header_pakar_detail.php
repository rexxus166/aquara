<?php
$activeMenu = $activeMenu ?? '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Detail Artikel Pakar - Aquara</title>
  <link rel="stylesheet" href="../../assets/css/artikel_detail.css">
  <link rel="stylesheet" href="../../assets/css/artikel_pakar_custom.css">
</head>
<body>

<header class="site-header">
  <div class="container header-container">
    <a href="index_pakar.php?page=home_pakar" class="logo">
      <img src="../../assets/img/aquara/logo.png" alt="Aquara Logo" class="logo-img">
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

    <div class="user-profile">
      <div class="user-info">
        <span class="user-name">Dr. Ahmad Budiman</span>
        <span class="user-role">Pakar/Ahli</span>
      </div>
      <img src="../../assets/img/aquara/profil.png" alt="User Avatar" class="user-avatar">
      <img src="../../assets/img/aquara/dropdown.png" alt="Dropdown" class="dropdown-arrow">
    </div>
  </div>
</header>
