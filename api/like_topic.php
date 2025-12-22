<?php
require_once 'koneksi.php'; // Atau koneksi_mobile.php sesuai yang kamu pakai
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

$topic_id = $_POST['topic_id'] ?? '';
$user_id  = $_POST['user_id'] ?? '';

if (empty($topic_id) || empty($user_id)) {
    echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
    exit;
}

// 1. Cek apakah user sudah like topik ini?
$check = $koneksi->query("SELECT id FROM post_likes WHERE post_id = '$topic_id' AND user_id = '$user_id'");

if ($check->num_rows > 0) {
    // SUDAH LIKE -> LAKUKAN UNLIKE (HAPUS)
    $koneksi->query("DELETE FROM post_likes WHERE post_id = '$topic_id' AND user_id = '$user_id'");
    $action = "unliked";
} else {
    // BELUM LIKE -> LAKUKAN LIKE (INSERT)
    $koneksi->query("INSERT INTO post_likes (post_id, user_id) VALUES ('$topic_id', '$user_id')");
    $action = "liked";
}

// 2. Hitung total like terbaru untuk dikirim balik ke HP
$total = $koneksi->query("SELECT COUNT(*) as jum FROM post_likes WHERE post_id = '$topic_id'")->fetch_assoc();

echo json_encode([
    "status" => "success",
    "action" => $action,
    "total_likes" => $total['jum']
]);
?>