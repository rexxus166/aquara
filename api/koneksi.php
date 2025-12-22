<?php
// Sesuaikan dengan config website kamu
$serverName = $_SERVER['SERVER_NAME'];

if ($serverName == 'localhost' || $serverName == '127.0.0.1') {
    // LOCAL
    $host = 'localhost';
    $user = 'root';
    $pass = '';
    $db   = 'aquara';
} else {
    // PRODUCTION
    $host = 'localhost';
    $user = 'iqbfbjty_aquara';
    $pass = 'Gz*N~)dSB9.?2;jC';
    $db   = 'iqbfbjty_aquara';
}

$koneksi = mysqli_connect($host, $user, $pass, $db);
header('Content-Type: application/json');

if (!$koneksi) {
    echo json_encode(["message" => "Gagal Koneksi"]);
    exit();
}
