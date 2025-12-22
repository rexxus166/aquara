<?php
session_start();
require '../../includes/config.php';
// index_pakar.php
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true || $_SESSION['role_id'] != 2) {
header("Location: index.php?page=login");
    exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
// Routing ke halaman yang sesuai
switch ($page) {
    case 'artikel_anggota':
        include 'artikel_anggota.php';
        break;

    case 'artikel_detail_anggota':
        include 'artikel_detail_anggota.php';
        break;

    case 'forum_anggota':
        include 'forum_anggota.php';
        break;

    case 'forum_detail':
        include 'forum_detail.php';
        break;

    case 'tambah_topik':
        include 'tambah_topik.php';
        break;

    case 'event_anggota':
        include 'event_anggota.php';
        break;

    case 'konsultasi_anggota':
        include 'konsultasi_anggota.php';
        break;

    case 'kalkulator_anggota':
        include 'kalkulator_anggota.php';
        break;

    case 'profil_anggota':
        include 'profil_anggota.php';
        break;

    case 'home':
    default:
        include 'home_anggota.php'; // nanti kamu buat halaman home khusus pakar
        break;
}
?>
