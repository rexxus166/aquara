<?php
// api/pakar_jawab.php
session_start();
require_once '../includes/config.php'; // Pastikan path ke config.php benar

// Set header agar outputnya dianggap JSON oleh JavaScript
header('Content-Type: application/json');

// 1. CEK KEAMANAN (Hanya Pakar yang boleh akses)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Anda bukan Pakar.']);
    exit;
}

// 2. PROSES DATA JIKA REQUEST POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_konsultasi = $_POST['id_konsultasi'] ?? '';
    $jawaban = trim($_POST['jawaban'] ?? '');
    $pakar_id = $_SESSION['user_id'];

    // Validasi input
    if (empty($id_konsultasi) || empty($jawaban)) {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap. Jawaban wajib diisi.']);
        exit;
    }

    // 3. UPDATE DATABASE
    // Set jawaban, ubah status jadi 'answered' (atau 'dijawab' sesuai enum DB Anda), 
    // dan catat pakar mana yang menjawab.
    $stmt = $conn->prepare("UPDATE konsultasi SET jawaban = ?, status = 'answered', pakar_id = ?, tanggal_jawaban = NOW() WHERE id = ?");
    
    if ($stmt) {
        $stmt->bind_param("sii", $jawaban, $pakar_id, $id_konsultasi);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Jawaban berhasil disimpan.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal update database: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>