<?php
require_once 'koneksi.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$topic_id = $_POST['topic_id'] ?? '';
$user_id = $_POST['user_id'] ?? '';
$konten = $_POST['konten'] ?? '';

if (empty($topic_id) || empty($user_id) || empty($konten)) {
    echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
    exit;
}

$sql = "INSERT INTO forum_replies (topic_id, user_id, konten, created_at) VALUES (?, ?, ?, NOW())";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("iis", $topic_id, $user_id, $konten);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Komentar terkirim"]);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal kirim komentar"]);
}
?>