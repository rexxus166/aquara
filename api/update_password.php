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
$password_lama = $data['password_lama'] ?? '';
$password_baru = $data['password_baru'] ?? '';
$konfirmasi_password = $data['konfirmasi_password'] ?? '';

// 3. Validasi Input
if (empty($password_lama) || empty($password_baru) || empty($konfirmasi_password)) {
    $response['message'] = 'Semua field password harus diisi.';
    echo json_encode($response);
    exit;
}

if ($password_baru !== $konfirmasi_password) {
    $response['message'] = 'Password baru dan konfirmasi password tidak cocok.';
    echo json_encode($response);
    exit;
}

if (strlen($password_baru) < 6) {
    $response['message'] = 'Password baru minimal harus 6 karakter.';
    echo json_encode($response);
    exit;
}

// 4. Verifikasi Password Lama
$stmt_check = $conn->prepare("SELECT password FROM users WHERE id = ?");
$stmt_check->bind_param("i", $user_id);
$stmt_check->execute();
$result = $stmt_check->get_result();
$user = $result->fetch_assoc();
$stmt_check->close();

if ($user && password_verify($password_lama, $user['password'])) {
    // Password lama cocok!
    
    // 5. Hash Password Baru
    $password_hash_baru = password_hash($password_baru, PASSWORD_DEFAULT);
    
    // 6. Update Database
    $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt_update->bind_param("si", $password_hash_baru, $user_id);
    
    if ($stmt_update->execute()) {
        $response['success'] = true;
        $response['message'] = 'Password berhasil diubah!';
    } else {
        $response['message'] = 'Gagal mengupdate password di database.';
    }
    $stmt_update->close();
    
} else {
    // Password lama tidak cocok
    $response['message'] = 'Password lama yang Anda masukkan salah.';
}

$conn->close();
echo json_encode($response);
?>