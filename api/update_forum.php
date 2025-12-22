<?php
require_once 'koneksi.php'; // Atau koneksi_mobile.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

$id = $_POST['id'] ?? '';
$judul = $_POST['judul'] ?? '';
$deskripsi = $_POST['deskripsi'] ?? '';

if (empty($id) || empty($judul) || empty($deskripsi)) {
    echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
    exit;
}

// 1. UPDATE DATA TEKS DULU
$sql = "UPDATE forum_topics SET judul = ?, deskripsi = ?, updated_at = NOW() WHERE id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("ssi", $judul, $deskripsi, $id);
$update_text = $stmt->execute();

// 2. CEK APAKAH ADA GAMBAR BARU?
if (isset($_FILES['gambar']['name']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
    $target_dir = "../uploads/forum/";
    if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
    
    $file_ext = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
    $nama_gambar = "topic_" . uniqid() . "." . $file_ext;
    
    if(move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_dir . $nama_gambar)) {
        // Update Kolom Gambar di Database
        $sql_img = "UPDATE forum_topics SET gambar = ? WHERE id = ?";
        $stmt_img = $koneksi->prepare($sql_img);
        $stmt_img->bind_param("si", $nama_gambar, $id);
        $stmt_img->execute();
    }
}

if ($update_text) {
    echo json_encode(["status" => "success", "message" => "Topik berhasil diupdate"]);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal update"]);
}
?>