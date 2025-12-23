<?php
// AMBIL DATA DARI SESSION (Session sudah dimulai di index_anggota.php)
$activeMenu = $activeMenu ?? '';

// 1. Ambil Nama User
$nama_user = isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama']) : 'User';

// 2. Tentukan Nama Role
$role_name = "Pengunjung"; // Default
if (isset($_SESSION['role_id'])) {
  if ($_SESSION['role_id'] == 1) {
    $role_name = "Admin";
  } else if ($_SESSION['role_id'] == 2) {
    $role_name = "Anggota";
  } else if ($_SESSION['role_id'] == 3) {
    $role_name = "Pakar";
  }
}

// 3. Tentukan Foto Profil
$foto_user = $_SESSION['foto_profil'] ?? null;
$path_foto_default = "/assets/img/aquara/profil.png"; // Foto default Anda

if (!empty($foto_user)) {
  // 4. Cek apakah ini URL eksternal atau lokal
  $foto_user_clean = trim($foto_user);
  if (strpos($foto_user_clean, 'http') === 0) {
    $path_foto_tampil = $foto_user_clean;
  } else {
    // Gunakan path absolut web root
    $path_foto_tampil = "/uploads/profil/" . htmlspecialchars($foto_user_clean);
  }
} else {
  $path_foto_tampil = $path_foto_default;
}
?>

<link rel="stylesheet" href="/assets/css/header_footer_pakar.css">
<link rel="stylesheet" href="/assets/css/artikel_pakar_custom.css">

<style>
  .user-profile {
    position: relative;
    /* Diperlukan agar dropdown menu pas */
    cursor: pointer;
    /* Menunjukkan bisa di-klik */
  }

  .user-profile .dropdown-menu {
    display: none;
    /* Sembunyi secara default */
    position: absolute;
    top: 100%;
    /* Muncul di bawah profil */
    right: 0;
    background-color: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 150px;
    /* Atur lebar dropdown */
    z-index: 1000;
  }

  .user-profile:hover .dropdown-menu {
    display: block;
    /* Tampilkan saat di-hover */
  }

  .user-profile .dropdown-menu a {
    display: block;
    padding: 10px 15px;
    text-decoration: none;
    color: #333;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
  }

  .user-profile .dropdown-menu a:hover {
    background-color: #f4f4f4;
  }

  /* Perbaiki path link navbar (jika diperlukan) */
  .main-nav a {
    text-decoration: none;
    /* Pastikan tidak ada garis bawah default */
  }
</style>

<header class="site-header">
  <div class="container header-container">
    <a href="index_anggota.php?page=home_anggota" class="logo">
      <img src="/assets/img/aquara/logo.png" alt="Aquara Logo" class="logo-img">
      <span class="logo-text">AQUARA</span>
    </a>

    <nav class="main-nav">
      <ul>
        <li><a class="<?= ($activeMenu == 'home') ? 'active' : '' ?>" href="index_anggota.php?page=home_anggota">HOME</a></li>
        <li><a class="<?= ($activeMenu == 'artikel') ? 'active' : '' ?>" href="index_anggota.php?page=artikel_anggota">ARTIKEL</a></li>
        <li><a class="<?= ($activeMenu == 'forum') ? 'active' : '' ?>" href="index_anggota.php?page=forum_anggota">FORUM</a></li>
        <li><a class="<?= ($activeMenu == 'event') ? 'active' : '' ?>" href="index_anggota.php?page=event_anggota">EVENT</a></li>
        <li><a class="<?= ($activeMenu == 'konsultasi') ? 'active' : '' ?>" href="index_anggota.php?page=konsultasi_anggota">KONSULTASI</a></li>
        <li><a class="<?= ($activeMenu == 'kalkulator') ? 'active' : '' ?>" href="index_anggota.php?page=kalkulator_anggota">KALKULATOR</a></li>
      </ul>
    </nav>

    <div class="user-profile">
      <div class="user-info">
        <p class="user-name"><?php echo $nama_user; ?></p>
        <p class="user-role"><?php echo $role_name; ?></p>
      </div>

      <img src="<?php echo $path_foto_tampil; ?>" alt="User Avatar" class="user-avatar">

      <img src="/assets/img/aquara/dropdown.png" alt="Dropdown" class="dropdown-arrow">

      <div class="dropdown-menu">
        <a href="index_anggota.php?page=profil_anggota">Profil Saya</a>
        <a href="../../index.php?page=logout" onclick="return confirm('Apakah Anda yakin ingin logout?');">Logout</a>
      </div>

    </div>
  </div>
</header>