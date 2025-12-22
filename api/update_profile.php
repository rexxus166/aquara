<?php
include 'koneksi.php';

// Header agar JSON terbaca sempurna di Flutter
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$id = $_POST['id'];
$nama = $_POST['nama'];
$email = $_POST['email'];
$password = $_POST['password'];

// 1. UPDATE DATA TEKS (Nama & Email)
$query = "UPDATE users SET nama='$nama', email='$email' WHERE id='$id'";
mysqli_query($koneksi, $query);

// 2. JIKA ADA PASSWORD BARU (Wajib di-Hash agar bisa Login)
if (!empty($password)) {
    $passHash = password_hash($password, PASSWORD_DEFAULT);
    $queryPass = "UPDATE users SET password='$passHash' WHERE id='$id'";
    mysqli_query($koneksi, $queryPass);
}

// Siapkan Array Respon Standar (Agar sesuai dengan Flutter)
$response = [
    "status" => "success",  // <-- PENTING: Flutter baca 'status', bukan 'success'
    "message" => "Profil berhasil diupdate"
];

// 3. JIKA ADA UPLOAD FOTO
if (isset($_FILES['foto'])) {
    $namaFile = time() . "_" . $_FILES['foto']['name'];
    $tmpName = $_FILES['foto']['tmp_name'];
    $folder = "../uploads/profil/" . $namaFile;

    if (move_uploaded_file($tmpName, $folder)) {
        $queryFoto = "UPDATE users SET foto_profil='$namaFile' WHERE id='$id'";
        mysqli_query($koneksi, $queryFoto);

        // Masukkan URL foto baru ke dalam respon JSON
        // Sesuaikan IP ini dengan IP Laptop Anda
        $response['foto_url'] = "https://aquara.miomidev.com/uploads/profil/" . $namaFile;
    }
}

// Kirim Respon Final ke Flutter
echo json_encode($response);
