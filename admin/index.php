<?php
// Entry point admin panel AQUARA
session_start();

// ===================================
// HUBUNGKAN KE DATABASE
// ===================================
require '../includes/config.php';

// Menyamakan variabel agar kode selanjutnya berjalan lancar
// Karena di config.php kamu pakai $conn, kita samakan di sini.
$koneksi = $conn; 
// ===================================

// Cek login admin
// Pastikan user sudah login DAN memiliki role_id = 1 (Admin)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['role_id'] != 1) {
    header('Location: login.php');
    exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// Validasi page untuk keamanan
$allowed_pages = ['dashboard', 'data_pengguna', 'edit_pengguna', 'data_pakar', 'artikel_admin', 'konsultasi_admin', 'event_admin', 'pengaturan', 'forum_admin', 'forum_detail_admin'];
if (!in_array($page, $allowed_pages)) {
    $page = 'dashboard';
}

// ==================================================================
// HANDLER HAPUS DATA
// ==================================================================

// ==================================================================

$include_path = "../views/admin/{$page}.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - AQUARA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <?php 
    if (file_exists($include_path)) {
        include '../views/layout_admin.php'; 
    } else {
        echo "<div class='alert alert-danger m-3'>Halaman tidak ditemukan: $include_path</div>";
    }
    ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/admin.js"></script>
</body>
</html>