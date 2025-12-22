<?php
// Set header untuk memberitahu browser bahwa ini adalah file JSON
header('Content-Type: application/json');
session_start();
require '../includes/config.php';

$response = ['success' => false, 'message' => 'Terjadi kesalahan.'];

// 1. Cek login
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    $response['message'] = 'Sesi tidak valid.';
    echo json_encode($response);
    exit;
}

// 2. Ambil data JSON yang dikirim oleh JavaScript
$data = json_decode(file_get_contents('php://input'), true);
$konsultasi_id = $data['id'] ?? null;
$anggota_id = $_SESSION['user_id'];

if ($konsultasi_id) {
    // 3. Update database
    // Kita pastikan user HANYA bisa membatalkan konsultasi miliknya sendiri
    // DAN yang statusnya masih 'pending'
    $stmt = $conn->prepare("UPDATE konsultasi 
                        SET status = 'dibatalkan' 
                        WHERE id = ? 
                        AND anggota_id = ? 
                        AND status = 'pending'");
    
    $stmt->bind_param("ii", $konsultasi_id, $anggota_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = 'Konsultasi berhasil dibatalkan.';
        } else {
            $response['message'] = 'Gagal membatalkan: Konsultasi tidak ditemukan atau sudah diproses.';
        }
    } else {
        $response['message'] = 'Gagal mengeksekusi query.';
    }
    $stmt->close();
} else {
    $response['message'] = 'ID konsultasi tidak valid.';
}

$conn->close();
echo json_encode($response);
?>