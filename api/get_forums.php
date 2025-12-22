<?php
require_once 'koneksi.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Kita butuh ID user yang sedang login untuk cek "is_liked"
$current_user_id = $_GET['user_id'] ?? '0';

// QUERY CANGGIH: Join User + Hitung Komentar + Hitung Like + Cek Status Like
$sql = "SELECT 
            f.id, f.judul, f.deskripsi, f.gambar, f.created_at, f.user_id, 
            u.nama, u.foto_profil,
            (SELECT COUNT(*) FROM forum_replies WHERE topic_id = f.id) AS total_komentar,
            (SELECT COUNT(*) FROM post_likes WHERE post_id = f.id) AS total_likes,
            (SELECT COUNT(*) FROM post_likes WHERE post_id = f.id AND user_id = '$current_user_id') AS is_liked_by_me
        FROM forum_topics f 
        JOIN users u ON f.user_id = u.id 
        ORDER BY f.created_at DESC";

$result = $koneksi->query($sql);
$forums = array();

// GANTI IP SESUAI LAPTOP KAMU
$base_url = "https://aquara.miomidev.com";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Fix URL Foto & Gambar (Sama seperti sebelumnya)
        if (!empty($row['foto_profil'])) {
            $row['foto_profil_url'] = $base_url . "/uploads/profil/" . $row['foto_profil'];
        } else {
            $row['foto_profil_url'] = "https://ui-avatars.com/api/?name=" . urlencode($row['nama']) . "&background=random";
        }

        if (!empty($row['gambar'])) {
            $row['gambar_url'] = $base_url . "/uploads/forum/" . $row['gambar'];
        } else {
            $row['gambar_url'] = null;
        }

        $forums[] = $row;
    }
}

echo json_encode($forums);
