<?php
// Sesuaikan dengan config website kamu
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'aquara';

$koneksi = mysqli_connect($host, $user, $pass, $db);
header('Content-Type: application/json');

if (!$koneksi) {
    echo json_encode(["message" => "Gagal Koneksi"]);
    exit();
}
?>