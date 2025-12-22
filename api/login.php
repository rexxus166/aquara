<?php
include 'koneksi.php';

$email = $_POST['email'];
$password = $_POST['password'];

// Cek user di tabel users
$query = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

if ($data) {
    // Verifikasi password (asumsi di web pakai password_verify, jika plain text sesuaikan)
    // Di sini saya pakai contoh password_verify sesuai standar keamanan
    if (password_verify($password, $data['password'])) {
        echo json_encode([
            "success" => true,
            "message" => "Login Berhasil",
            "data" => [
                "id" => $data['id'],
                "nama" => $data['nama'],
                "email" => $data['email'],
                "role_id" => $data['role_id'],
                "foto_url" => "https://aquara.miomidev.com/uploads/profil/" . ($data['foto_profil'] ? $data['foto_profil'] : 'default.png')
            ]
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Password Salah"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Email tidak ditemukan"]);
}
