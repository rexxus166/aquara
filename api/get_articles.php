<?php
include 'koneksi.php';

// Ambil data artikel join kategori dan user
$query = "SELECT articles.*, categories.nama as kategori_nama, users.nama as penulis 
          FROM articles 
          JOIN categories ON articles.category_id = categories.id
          JOIN users ON articles.user_id = users.id
          ORDER BY created_at DESC";

$result = mysqli_query($koneksi, $query);
$response = [];

while ($row = mysqli_fetch_assoc($result)) {
    // Tambahkan URL lengkap untuk gambar agar bisa diakses di HP
    // Ganti 192.168.x.x dengan IP Laptop kamu jika pakai HP asli
    // Ganti 10.0.2.2 jika pakai Emulator Android Studio
    $row['gambar_url'] = "http://192.168.43.63:8080/aquara/uploads/articles/" . $row['gambar']; 
    $response[] = $row;
}

echo json_encode($response);
?>