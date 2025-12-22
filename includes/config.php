<?php
/*
  File: config.php
  Konfigurasi koneksi database
*/

// Deteksi Environment
$serverName = $_SERVER['SERVER_NAME'];

if ($serverName == 'localhost' || $serverName == '127.0.0.1') {
  // 1. LOCAL (Laragon/XAMPP)
  define('DB_HOST', 'localhost');
  define('DB_USERNAME', 'root');
  define('DB_NAME', 'aquara');
  define('DB_PASSWORD', '');
} else {
  // 2. PRODUCTION (Hosting)
  define('DB_HOST', 'localhost');
  define('DB_USERNAME', 'iqbfbjty_aquara');
  define('DB_NAME', 'iqbfbjty_aquara');
  define('DB_PASSWORD', 'Gz*N~)dSB9.?2;jC');
}
define('BASE_URL', 'https://aquara.miomidev.com');

// Membuat koneksi
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}
