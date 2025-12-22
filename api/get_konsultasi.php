<?php
// Set header untuk memberitahu browser bahwa ini adalah file JSON
header('Content-Type: application/json');

// Memulai session untuk mengambil ID user
session_start();

// Panggil file koneksi.
// Kita harus mundur satu folder ('../') untuk menemukan 'includes'
require '../includes/config.php';

// Array untuk respons JSON
$response = ['success' => false, 'konsultasi' => [], 'message' => 'Gagal memuat riwayat.'];

// 1. Cek apakah user sudah login dan adalah anggota
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    $response['message'] = 'Sesi tidak valid. Silakan login kembali.';
    echo json_encode($response);
    exit;
}

$anggota_id = $_SESSION['user_id'];

// 2. Siapkan Query SQL
// Kita akan JOIN tabel 'konsultasi' dengan tabel 'users' (sebagai 'pakar')
// untuk mendapatkan nama pakar berdasarkan 'pakar_id'
$stmt = $conn->prepare(
    "SELECT 
        k.id, 
        k.pesan, 
        k.jawaban,              -- <-- Kolom baru yang kita buat
        k.status, 
        k.created_at AS tanggal,
        k.tanggal_jawaban,      -- <-- Kolom baru yang kita buat
        pakar.nama AS ahli        -- Mengambil 'nama' dari 'users' dan menamainya 'ahli'
    FROM 
        konsultasi AS k
    LEFT JOIN 
        users AS pakar ON k.pakar_id = pakar.id
    WHERE 
        k.anggota_id = ?
    ORDER BY 
        k.created_at DESC"
);

if (!$stmt) {
    $response['message'] = 'Query prepare failed: ' . $conn->error;
    echo json_encode($response);
    exit;
}

$stmt->bind_param("i", $anggota_id);
$stmt->execute();
$result = $stmt->get_result();

$konsultasi_list = [];

// 3. Loop data dan format untuk JSON
while ($row = $result->fetch_assoc()) {
    
    // Kita pisahkan kembali "Topik" dan "Pertanyaan"
    $pesan_lengkap = $row['pesan'];
    $topik = 'Tidak ada topik';
    $pertanyaan = $pesan_lengkap;

    // Cek apakah format "Topik: ..." ada di awal
    if (strpos($pesan_lengkap, "Topik: ") === 0) {
        $parts = explode("\n\n", $pesan_lengkap, 2);
        if (count($parts) == 2) {
            $topik = str_replace("Topik: ", "", $parts[0]);
            $pertanyaan = $parts[1];
        }
    }

    // Masukkan ke array sesuai format yang diinginkan JS
    $konsultasi_list[] = [
        'id' => $row['id'],
        'topik' => $topik,
        'pertanyaan' => $pertanyaan,
        'jawaban' => $row['jawaban'], // Data dari kolom baru
        'status' => $row['status'],
        'tanggal' => $row['tanggal'],
        'tanggal_jawaban' => $row['tanggal_jawaban'], // Data dari kolom baru
        'ahli' => $row['ahli'] // Nama pakar dari JOIN
    ];
}

$response['success'] = true;
$response['konsultasi'] = $konsultasi_list;

$stmt->close();
$conn->close();

// 4. Kembalikan respons JSON
echo json_encode($response);
?>