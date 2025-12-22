<?php
require_once 'koneksi.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

// Cek Method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
    exit;
}

// Ambil data
$user_id = $_POST['user_id'] ?? '';
$judul = $_POST['judul'] ?? '';
$deskripsi = $_POST['deskripsi'] ?? '';

if (empty($user_id) || empty($judul) || empty($deskripsi)) {
    http_response_code(400);
    echo json_encode(["message" => "Data tidak lengkap"]);
    exit;
}

// Upload Gambar
$nama_gambar = null;
if (isset($_FILES['gambar']['name']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "../uploads/forum/";
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
    
    $file_ext = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
    $nama_gambar = "topic_" . uniqid() . "." . $file_ext;
    
    move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_dir . $nama_gambar);
}

// Insert Database (GANTI $conn JADI $koneksi)
$sql = "INSERT INTO forum_topics (user_id, judul, deskripsi, gambar, created_at) VALUES (?, ?, ?, ?, NOW())";
$stmt = $koneksi->prepare($sql); // Pakai $koneksi
$stmt->bind_param("isss", $user_id, $judul, $deskripsi, $nama_gambar);

if ($stmt->execute()) {
    http_response_code(201);
    echo json_encode(["message" => "Topik berhasil dibuat"]);
} else {
    http_response_code(500);
    echo json_encode(["message" => "Gagal membuat topik"]);
}
?>