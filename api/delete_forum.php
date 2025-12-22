<?php
require_once 'koneksi.php'; // Atau koneksi_mobile.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

$id = $_POST['id'] ?? '';

if (empty($id)) {
    echo json_encode(["status" => "error", "message" => "ID tidak ditemukan"]);
    exit;
}

// Hapus topik (Komentar & Like otomatis terhapus karena CASCADE di database)
$sql = "DELETE FROM forum_topics WHERE id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Topik berhasil dihapus"]);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal menghapus"]);
}
?>