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

// 2. Cek apakah ada file yang diupload
if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {

    $file = $_FILES['foto_profil'];

    // 3. Validasi File
    $allowed_types = ['image/jpeg', 'image/png'];
    $max_size = 2 * 1024 * 1024; // 2 MB

    if (!in_array($file['type'], $allowed_types)) {
        $response['message'] = 'Format file tidak valid. Hanya JPG/PNG yang diizinkan.';
        echo json_encode($response);
        exit;
    }

    if ($file['size'] > $max_size) {
        $response['message'] = 'Ukuran file terlalu besar. Maksimal 2 MB.';
        echo json_encode($response);
        exit;
    }

    // 4. Siapkan Path dan Nama File Baru
    $upload_dir = __DIR__ . '/../uploads/profil/'; // Path absolut
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

    // Buat nama file unik: "user_ID_timestamp.ext"
    // Contoh: "user_3_1678886400.jpg"
    $new_filename = 'user_' . $user_id . '_' . time() . '.' . $extension;
    $upload_path = $upload_dir . $new_filename;

    // 5. Pindahkan File
    // Pastikan folder ada
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $upload_path)) {

        // 6. Hapus foto lama (jika ada)
        $stmt_old_foto = $conn->prepare("SELECT foto_profil FROM users WHERE id = ?");
        $stmt_old_foto->bind_param("i", $user_id);
        $stmt_old_foto->execute();
        $result_old_foto = $stmt_old_foto->get_result();
        $user_data = $result_old_foto->fetch_assoc();

        if ($user_data && !empty($user_data['foto_profil'])) {
            // Jangan coba hapus jika foto lama adalah URL eksternal (Google Login)
            if (!filter_var($user_data['foto_profil'], FILTER_VALIDATE_URL)) {
                $old_file_path = $upload_dir . $user_data['foto_profil'];
                if (file_exists($old_file_path)) {
                    unlink($old_file_path); // Hapus file lama dari server
                }
            }
        }
        $stmt_old_foto->close();

        // 7. Update Database
        $stmt_update = $conn->prepare("UPDATE users SET foto_profil = ? WHERE id = ?");
        $stmt_update->bind_param("si", $new_filename, $user_id);

        if ($stmt_update->execute()) {
            // 8. Update Session
            $_SESSION['foto_profil'] = $new_filename;

            $response['success'] = true;
            $response['message'] = 'Foto profil berhasil diupdate!';
            // Kirim balik path foto yang baru agar JS bisa update tampilan
            $response['newFotoPath'] = '/aquara/uploads/profil/' . $new_filename;
        } else {
            $response['message'] = 'Gagal mengupdate database.';
        }
        $stmt_update->close();
    } else {
        $error = error_get_last();
        $response['message'] = 'Gagal memindahkan file yang diupload. Error: ' . ($error['message'] ?? 'Unknown');
        $response['debug_path'] = $upload_path;
    }
} else {
    $response['message'] = 'Tidak ada file yang dipilih atau terjadi error upload.';
}

$conn->close();
echo json_encode($response);
