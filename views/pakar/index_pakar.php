<?php
session_start();

// 1. WAJIB INCLUDE KONEKSI DATABASE
require '../../includes/config.php';

// 2. CEK AKSES (HARUS LOGIN & ROLE_ID = 3 UNTUK PAKAR)
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role_id'] != 3) {
    // Arahkan kembali ke login utama jika mencoba bypass
    header("Location: ../../index.php?page=login");
    exit;
}

// 3. AMBIL HALAMAN
$page = isset($_GET['page']) ? $_GET['page'] : 'home_pakar';

// 4. ROUTING HALAMAN
switch ($page) {
    case 'home_pakar':
        include 'home_pakar.php';
        break;

    case 'artikel_pakar':
        include 'artikel_pakar.php';
        break;
    case 'artikel_detail_pakar':
        include 'artikel_detail_pakar.php';
        break;

    case 'forum_pakar':
        include 'forum_pakar.php';
        break;
    case 'forum_detail_pakar': // Tambahkan ini jika pakar bisa lihat detail
        include 'forum_detail_pakar.php';
        break;
    case 'tambah_topik_pakar': // Tambahkan ini jika pakar bisa buat topik
        include 'tambah_topik_pakar.php';
        break;

    case 'event_pakar':
        include 'event_pakar.php';
        break;

    case 'konsultasi_pakar':
        include 'konsultasi_pakar.php';
        break;
    // Tambahan untuk detail konsultasi jika nanti dipisah fiturnya
    case 'konsultasi_detail_pakar':
        include 'konsultasi_detail_pakar.php';
        break;

    case 'kalkulator_pakar':
        include 'kalkulator_pakar.php';
        break;

    case 'profil_pakar':
        include 'profil_pakar.php';
        break;

    default:
        include 'home_pakar.php'; // Default kembali ke home pakar
        break;
}
?>