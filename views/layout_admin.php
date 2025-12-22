<?php
// Layout utama admin panel
$current_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-primary" style="background-color: #1a5f5f !important;">
    <div class="container-fluid">
        <a class="navbar-brand" href="?page=dashboard">
            <i class="fas fa-fish me-2"></i>AQUARA Admin
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="?page=pengaturan">
                        <i class="fas fa-user me-1"></i>Profil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="wrapper">
    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar bg-dark sidebar-fixed">
        <div class="position-sticky">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'dashboard' ? 'active' : ''; ?>" href="?page=dashboard">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'data_pengguna' ? 'active' : ''; ?>" href="?page=data_pengguna">
                        <i class="fas fa-users me-2"></i>Data Pengguna
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'data_pakar' ? 'active' : ''; ?>" href="?page=data_pakar">
                        <i class="fas fa-user-tie me-2"></i>Data Pakar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'artikel_admin' ? 'active' : ''; ?>" href="?page=artikel_admin">
                        <i class="fas fa-newspaper me-2"></i>Artikel
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_page == 'forum_admin') ? 'active' : ''; ?>" href="?page=forum_admin">
                        <i class="fas fa-comments me-2"></i>Forum Diskusi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'konsultasi_admin' ? 'active' : ''; ?>" href="?page=konsultasi_admin">
                        <i class="fas fa-comments me-2"></i>Konsultasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'event_admin' ? 'active' : ''; ?>" href="?page=event_admin">
                        <i class="fas fa-calendar-alt me-2"></i>Event
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page == 'pengaturan' ? 'active' : ''; ?>" href="?page=pengaturan">
                        <i class="fas fa-cog me-2"></i>Pengaturan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Konten Utama -->
    <div id="content" class="content">
        <div class="container-fluid mt-5 pt-3">
            <?php
            if (file_exists($include_path)) {
                include $include_path;
            } else {
                echo '<div class="alert alert-warning">Halaman tidak ditemukan.</div>';
            }
            ?>
        </div>
    </div>
</div>
