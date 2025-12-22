<?php
// Set header untuk memberitahu browser bahwa ini adalah file JSON
header('Content-Type: application/json');
session_start();
require '../includes/config.php';

$response = ['success' => false, 'message' => 'Gagal memuat detail.'];

// 1. Cek login
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    $response['message'] = 'Sesi tidak valid.';
    echo json_encode($response);
    exit;
}

// 2. Ambil ID dari URL (?id=...)
$konsultasi_id = $_GET['id'] ?? null;
$anggota_id = $_SESSION['user_id'];

if ($konsultasi_id) {
    // 3. Siapkan Query SQL
    // Query ini sama dengan get_konsultasi.php, tapi dengan filter ID
    // Kita juga pastikan user HANYA bisa melihat detail miliknya sendiri
    $stmt = $conn->prepare(
        "SELECT 
            k.id, k.pesan, k.jawaban, k.status, 
            k.created_at AS tanggal, k.tanggal_jawaban,
            pakar.nama AS ahli
        FROM 
            konsultasi AS k
        LEFT JOIN 
            users AS pakar ON k.pakar_id = pakar.id
        WHERE 
            k.id = ? AND k.anggota_id = ?"
    );
    
    $stmt->bind_param("ii", $konsultasi_id, $anggota_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();

        // 4. Format data (pisahkan topik dan pertanyaan)
        $pesan_lengkap = $row['pesan'];
        $topik = 'Tidak ada topik';
        $pertanyaan = $pesan_lengkap;

        if (strpos($pesan_lengkap, "Topik: ") === 0) {
            $parts = explode("\n\n", $pesan_lengkap, 2);
            if (count($parts) == 2) {
                $topik = str_replace("Topik: ", "", $parts[0]);
                $pertanyaan = $parts[1];
            }
        }
        
        $konsultasi_detail = [
            'id' => $row['id'],
            'topik' => $topik,
            'pertanyaan' => $pertanyaan,
            'jawaban' => $row['jawaban'],
            'status' => $row['status'],
            'tanggal' => $row['tanggal'],
            'tanggal_jawaban' => $row['tanggal_jawaban'],
            'ahli' => $row['ahli']
        ];

        $response['success'] = true;
        $response['konsultasi'] = $konsultasi_detail;

    } else {
        $response['message'] = 'Konsultasi tidak ditemukan atau Anda tidak memiliki akses.';
    }
    $stmt->close();
} else {
    $response['message'] = 'ID konsultasi tidak valid.';
}

$conn->close();
echo json_encode($response);
?>