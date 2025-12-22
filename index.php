<?php
// =======================================================
// 1. SETUP DASAR
// =======================================================

// session_start() HARUS di baris paling atas
session_start(); 

// Panggil file konfigurasi dan autoload
require __DIR__ . '/includes/config.php';
require __DIR__ . '/vendor/autoload.php';

use League\Plates\Engine;

// Tentukan halaman default
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// =======================================================
// 2. TANGANI AKSI MURNI (LOGOUT)
// =======================================================

if ($page == 'logout') {
    // Ini adalah logika logout yang benar
    $_SESSION = array();
    session_destroy();
    header("Location: index.php?page=home");
    exit; // Hentikan script
}

// =======================================================
// 3. TANGANI HALAMAN "CAMPURAN" (Logika + Tampilan)
// =======================================================

// Halaman-halaman ini ada di /views/ tapi punya logic PHP di atasnya.
// Kita harus memanggil (require) BUKAN me-render.

if ($page == 'login') {
    // Panggil file login dari folder /views/
    require __DIR__ . '/views/login.php'; 
    exit; // Hentikan script

} elseif ($page == 'register') {
    // Panggil file register dari folder /views/
    require __DIR__ . '/views/register.php'; 
    exit; // Hentikan script

} elseif ($page == 'proses_register') {
    // Panggil file aksi register dari folder /views/
    require __DIR__ . '/views/proses_register.php'; 
    exit; // Hentikan script

} elseif ($page == 'lupa_password') {
    // Panggil file lupa_password dari folder /views/
    require __DIR__ . '/views/lupa_password.php'; 
    exit; // Hentikan script
}

// =======================================================
// 4. TANGANI HALAMAN TAMPILAN MURNI (via Plates)
// =======================================================

// Jika script sampai di sini, berarti $page adalah halaman tampilan murni.
$templates = new Engine(__DIR__ . '/views');

switch ($page) {
    case 'home':
    case 'artikel':
    case 'artikel_detail':
    case 'forum':
    case 'event':
    case 'konsultasi':
    case 'kalkulator':
        // Halaman ini murni tampilan, jadi kita render
        echo $templates->render($page);
        break;

    default:
        // Jika halaman tidak ada di daftar, tampilkan 404
        // Pastikan Anda punya file views/404.php
        if ($templates->exists('404')) {
            echo $templates->render('404');
        } else {
            die("Halaman '$page' tidak ditemukan.");
        }
        break;
}
?>