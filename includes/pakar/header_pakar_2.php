<?php
$activeMenu = $activeMenu ?? '';

if (session_status() == PHP_SESSION_NONE) { session_start(); }

// 1. Ambil Nama User
$nama_user = isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama']) : 'User';

// 2. Tentukan Nama Role
$role_name = "Pakar"; 
if (isset($_SESSION['role_id'])) {
    if ($_SESSION['role_id'] == 1) $role_name = "Admin";
    else if ($_SESSION['role_id'] == 2) $role_name = "Anggota";
}

// 3. Tentukan Foto Profil
$foto_user = $_SESSION['foto_profil'] ?? null;
$path_foto_default = "/assets/img/profil/default_profile.png"; 

if (!empty($foto_user) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/aquara/uploads/profil/" . $foto_user)) {
    $path_foto_tampil = "/aquara/uploads/profil/" . htmlspecialchars($foto_user);
} else {
    $path_foto_tampil = $path_foto_default;
}
?>

<header class="site-header">
    <div class="container header-container">
        <a href="index_pakar.php?page=home_pakar" class="logo">
            <img src="/assets/img/aquara/logo.png" alt="Aquara Logo" class="logo-img">
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
                <p class="user-name"><?php echo $nama_user; ?></p>
                <p class="user-role"><?php echo $role_name; ?></p>
            </div>
            
            <img src="<?php echo $path_foto_tampil; ?>" alt="User Avatar" class="user-avatar">
            
            <img src="/assets/img/aquara/dropdown.png" alt="Dropdown Arrow" class="dropdown-arrow">

            <div class="dropdown-menu">
                <a href="index_pakar.php?page=profil_pakar">Profil Saya</a>
                <a href="../../index.php?page=logout" onclick="return confirm('Apakah Anda yakin ingin logout?');">Logout</a>
            </div>
        </div>
    </div>
</header>