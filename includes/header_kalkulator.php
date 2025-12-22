<?php
$activeMenu = $activeMenu ?? '';
?>

<header id="section-header" class="site-header">
    <div class="container header-container">
        <a class="logo">
            <img src="assets/img/aquara/logo.png" alt="Aquara Logo" class="logo-img">
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

        <div class="user-login">
            <a href="index.php?page=login" class="btn-masuk">Masuk</a>
        </div>
    </div>
</header>