<?php
// Set header untuk memberitahu browser bahwa ini adalah file JSON
header('Content-Type: application/json');

// Memulai session untuk mengambil ID user
session_start();

// Panggil file koneksi.
require '../includes/config.php';

// Array untuk respons JSON
$response = ['success' => false, 'message' => 'Terjadi kesalahan.'];

// 1. Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Sesi tidak valid. Silakan login kembali untuk membuat topik.';
    echo json_encode($response);
    exit;
}

// 2. Ambil data JSON yang dikirim oleh JavaScript
$data = json_decode(file_get_contents('php://input'), true);
$judul = $data['judul'] ?? '';
$deskripsi = $data['deskripsi'] ?? '';
$user_id = $_SESSION['user_id'];

// 3. Validasi Sederhana
if (empty($judul)) {
    $response['message'] = 'Judul pertanyaan tidak boleh kosong.';
    echo json_encode($response);
    exit;
}

// 4. Masukkan ke database 'forum_topics'
//    Database Anda: user_id, judul, deskripsi
try {
    $stmt = $conn->prepare("INSERT INTO forum_topics (user_id, judul, deskripsi) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $judul, $deskripsi);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Pertanyaan berhasil diposting!';
    } else {
        $response['message'] = 'Gagal menyimpan ke database: ' . $conn->error;
    }
    $stmt->close();
    
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

$conn->close();

// 5. Kembalikan respons JSON
echo json_encode($response);
?>