<?php
header('Content-Type: application/json');
session_start();
require '../includes/config.php';

$response = ['success' => false, 'message' => 'Terjadi kesalahan.'];

// 1. Cek Login
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Sesi tidak valid. Silakan login kembali.';
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];

// 2. Ambil data JSON yang dikirim oleh JavaScript
$data = json_decode(file_get_contents('php://input'), true);
$nama = $data['nama'] ?? '';
$email = $data['email'] ?? '';

// 3. Validasi Sederhana
if (empty($nama) || empty($email)) {
    $response['message'] = 'Nama dan Email tidak boleh kosong.';
    echo json_encode($response);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['message'] = 'Format email tidak valid.';
    echo json_encode($response);
    exit;
}

// 4. Cek apakah email baru sudah dipakai oleh user LAIN
$stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
$stmt_check->bind_param("si", $email, $user_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // Email sudah dipakai
    $response['message'] = 'Email ini sudah terdaftar di akun lain.';
    echo json_encode($response);
    exit;
}
$stmt_check->close();

// 5. Jika semua aman, UPDATE database
$stmt_update = $conn->prepare("UPDATE users SET nama = ?, email = ? WHERE id = ?");
$stmt_update->bind_param("ssi", $nama, $email, $user_id);

if ($stmt_update->execute()) {
    // 6. UPDATE Session agar header langsung berubah
    $_SESSION['nama'] = $nama;
    $_SESSION['email'] = $email;

    $response['success'] = true;
    $response['message'] = 'Informasi profil berhasil disimpan!';
    // Kirim balik data baru untuk update tampilan
    $response['newData'] = [
        'nama' => $nama,
        'email' => $email
    ];
} else {
    $response['message'] = 'Gagal mengupdate database.';
}

$stmt_update->close();
$conn->close();
echo json_encode($response);
?>