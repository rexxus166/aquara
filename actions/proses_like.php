<?php
session_start();
require_once '../includes/config.php'; // Pastikan path ini benar ke file koneksi database Anda

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Login diperlukan']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $post_id = intval($_POST['post_id']);
    $user_id = $_SESSION['user_id'];

    // 1. Cek apakah user sudah like postingan ini
    $check_stmt = $conn->prepare("SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?");
    $check_stmt->bind_param("ii", $post_id, $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    $already_liked = $result->num_rows > 0;
    $check_stmt->close();

    if ($already_liked) {
        // UNLIKE: Jika sudah like, hapus datanya
        $stmt = $conn->prepare("DELETE FROM post_likes WHERE post_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $post_id, $user_id);
        $stmt->execute();
        $user_liked = false;
    } else {
        // LIKE: Jika belum, tambahkan datanya
        $stmt = $conn->prepare("INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $post_id, $user_id);
        $stmt->execute();
        $user_liked = true;
    }

    // 2. Hitung total like terbaru untuk postingan ini
    $count_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM post_likes WHERE post_id = ?");
    $count_stmt->bind_param("i", $post_id);
    $count_stmt->execute();
    $total_likes = $count_stmt->get_result()->fetch_assoc()['total'];

    echo json_encode([
        'status' => 'success',
        'new_likes' => $total_likes,
        'user_liked' => $user_liked
    ]);
    exit;
}