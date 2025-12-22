<?php
header('Content-Type: application/json');
session_start();
require '../includes/config.php';

$response = ['success' => false, 'message' => 'Terjadi kesalahan.'];

// 1. Cek login
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Sesi tidak valid. Silakan login kembali.';
    echo json_encode($response);
    exit;
}

// 2. Cek metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 3. Ambil data
    $anggota_id = $_SESSION['user_id'];
    $telepon = $_POST['telepon'] ?? '';
    $nama_ahli = $_POST['ahli'] ?? '';
    $topik = $_POST['topik'] ?? '';
    $pertanyaan = $_POST['pertanyaan'] ?? '';
    
    // 4. Validasi Sederhana
    if (empty($telepon) || empty($topik) || empty($pertanyaan)) {
        $response['message'] = 'Semua field wajib diisi (kecuali ahli).';
        echo json_encode($response);
        exit;
    }

    // =========================================================
    // 5. KOREKSI FINAL: Temukan 'pakar_id'
    // =========================================================
    
    $pakar_id = null;
    if (!empty($nama_ahli)) {
        
        // --- KODE BARU YANG LEBIH PINTAR ---
        // 1. Buang semua gelar (Dr, Drs, Ir) + titik + spasi
        //    'i' di akhir artinya (case-insensitive)
        $nama_bersih = preg_replace('/^(Dr|Drs|Ir)\.?\s*/i', '', $nama_ahli);
        
        // 2. Hapus spasi ekstra di awal/akhir jika ada
        $nama_bersih = trim($nama_bersih);
        // --- AKHIR KODE BARU ---

        // Cari di tabel 'users' pakai nama yang sudah bersih
        $stmt_pakar = $conn->prepare("SELECT id FROM users WHERE nama = ? AND role_id = 3");
        $stmt_pakar->bind_param("s", $nama_bersih); // Pakai $nama_bersih
        $stmt_pakar->execute();
        $result_pakar = $stmt_pakar->get_result();
        
        if ($result_pakar->num_rows > 0) {
            $pakar = $result_pakar->fetch_assoc();
            $pakar_id = (int)$pakar['id'];
        }
        $stmt_pakar->close();
    }
    
    // Jika user memilih nama, tapi nama itu tidak ditemukan
    if (!empty($nama_ahli) && $pakar_id === null) {
        $response['message'] = 'Ahli yang Anda pilih tidak valid. (Debug: Gagal menemukan "' . $nama_bersih . '")';
        echo json_encode($response);
        exit;
    }

    // Jika tidak memilih ahli, pilih acak
    if ($pakar_id === null) {
        $stmt_rand_pakar = $conn->prepare("SELECT id FROM users WHERE role_id = 3 ORDER BY RAND() LIMIT 1");
        $stmt_pakar->execute();
        $result_pakar = $stmt_rand_pakar->get_result();
        if ($result_pakar->num_rows > 0) {
            $pakar = $result_pakar->fetch_assoc();
            $pakar_id = (int)$pakar['id'];
        } else {
            $response['message'] = 'Tidak ada pakar yang tersedia saat ini.';
            echo json_encode($response);
            exit;
        }
        $stmt_rand_pakar->close();
    }
    // =========================================================
    // AKHIR DARI BAGIAN 5
    // =========================================================

    // 6. Gabungkan pesan
    $pesan_lengkap = "Topik: " . $topik . "\n\n" . $pertanyaan;

    // 7. Masukkan ke database 'konsultasi'
    $pengirim = 'anggota';
    $stmt_insert = $conn->prepare("INSERT INTO konsultasi (anggota_id, pakar_id, pesan, pengirim) VALUES (?, ?, ?, ?)");
    $stmt_insert->bind_param("iiss", $anggota_id, $pakar_id, $pesan_lengkap, $pengirim);

    // 8. Eksekusi dan kirim respons
    if ($stmt_insert->execute()) {
        $response['success'] = true;
        $response['message'] = 'Konsultasi berhasil dikirim.';
        $response['konsultasi_id'] = $conn->insert_id;
    } else {
        $response['message'] = 'Gagal menyimpan ke database: ' . $conn->error;
    }

    $stmt_insert->close();
    $conn->close();

} else {
    $response['message'] = 'Metode request tidak valid.';
}

// 9. Kembalikan respons JSON
echo json_encode($response);
?>