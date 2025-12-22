<?php
require_once 'koneksi.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

$nama = $_POST['nama'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// 1. Validasi Input Kosong
if (empty($nama) || empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Semua kolom wajib diisi"]);
    exit;
}

// 2. Cek Apakah Email Sudah Terdaftar?
$cek = $koneksi->query("SELECT id FROM users WHERE email = '$email'");
if ($cek->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email sudah digunakan"]);
    exit;
}

// 3. Enkripsi Password (Biar aman & sesuai standar Login kamu)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$role_id = 2; // Default Role 2 = 'Anggota'

// 4. Masukkan ke Database
$sql = "INSERT INTO users (nama, email, password, role_id) VALUES (?, ?, ?, ?)";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("sssi", $nama, $email, $hashed_password, $role_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Registrasi berhasil! Silakan login."]);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal mendaftar"]);
}
?>