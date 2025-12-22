<?php
/*
  File: config.php
  Konfigurasi koneksi database
*/

define('DB_HOST', 'localhost');    // Server database Anda
define('DB_USERNAME', 'root'); // Username database Anda
define('DB_PASSWORD', '');     // Password database Anda
define('DB_NAME', 'aquara');  // <-- DIUBAH: Sesuai file .sql Anda

// Membuat koneksi
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>