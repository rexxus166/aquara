<?php
require_once 'koneksi.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$topic_id = $_GET['topic_id'] ?? '';

if (empty($topic_id)) {
    echo json_encode([]);
    exit;
}

// Ambil komentar join dengan user
$sql = "SELECT r.id, r.konten, r.created_at, u.nama, u.foto_profil 
        FROM forum_replies r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.topic_id = ? 
        ORDER BY r.created_at ASC";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $topic_id);
$stmt->execute();
$result = $stmt->get_result();

$comments = array();
// --- GANTI IP DI BAWAH INI SESUAI LAPTOP ---
$base_url = "http://192.168.43.63:8080/aquara"; 

while ($row = $result->fetch_assoc()) {
    if (!empty($row['foto_profil'])) {
        $row['foto_profil_url'] = $base_url . "/uploads/profil/" . $row['foto_profil'];
    } else {
        $row['foto_profil_url'] = "https://ui-avatars.com/api/?name=" . urlencode($row['nama']) . "&background=random";
    }
    $comments[] = $row;
}

echo json_encode($comments);
?>