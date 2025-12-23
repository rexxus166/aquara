<?php
ini_set('display_errors', 0); // Jangan tampilkan error di output (supaya JSON tidak rusak)
ini_set('log_errors', 1);     // Log error ke file log server
error_reporting(E_ALL);       // Laporkan semua error

header('Content-Type: application/json');
session_start();

try {
    require '../includes/config.php';

    $response = ['success' => false, 'message' => 'Terjadi kesalahan sistem.'];

    // 1. Cek login
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Sesi tidak valid. Silakan login kembali.');
    }

    // 2. Cek metode POST
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        throw new Exception('Metode request tidak valid.');
    }

    // 3. Ambil data
    $anggota_id = $_SESSION['user_id'];
    $telepon = $_POST['telepon'] ?? '';
    // $nama_ahli bisa kosong jika user memilih "Pilihkan untuk saya" / random
    $nama_ahli = $_POST['ahli'] ?? '';
    $topik = $_POST['topik'] ?? '';
    $pertanyaan = $_POST['pertanyaan'] ?? '';

    // 4. Validasi Sederhana
    if (empty($telepon) || empty($topik) || empty($pertanyaan)) {
        throw new Exception('Semua field wajib diisi (kecuali ahli).');
    }

    // =========================================================
    // 5. TENTUKAN PAKAR (SPESIFIK ATAU RANDOM)
    // =========================================================

    $pakar_id = null;

    // A. JIKA USER MEMILIH NAMA AHLI
    if (!empty($nama_ahli)) {

        // Bersihkan nama
        $nama_bersih = preg_replace('/^(Dr|Drs|Ir)\.?\s*/i', '', $nama_ahli);
        $nama_bersih = trim($nama_bersih);

        // Cari ID pakar berdasarkan nama
        $sql_pakar = "SELECT id FROM users WHERE nama = ? AND role_id = 3";
        $stmt_pakar = $conn->prepare($sql_pakar);

        if (!$stmt_pakar) {
            throw new Exception("Gagal prepare statement (cari pakar): " . $conn->error);
        }

        $stmt_pakar->bind_param("s", $nama_bersih);

        if (!$stmt_pakar->execute()) {
            throw new Exception("Gagal execute statement (cari pakar): " . $stmt_pakar->error);
        }

        $result_pakar = $stmt_pakar->get_result();

        if ($result_pakar->num_rows > 0) {
            $pakar = $result_pakar->fetch_assoc();
            $pakar_id = (int)$pakar['id'];
        }
        $stmt_pakar->close();

        // Jika user memilih nama spesifik, tapi tidak ketemu di database
        if ($pakar_id === null) {
            throw new Exception('Ahli yang Anda pilih tidak ditemukan di database.');
        }
    }

    // B. JIKA TIDAK ADA PAKAR DIPILIH (ATAU TIDAK KETEMU), PILIH ACAK
    // (Logic: Kalau user sengaja pilih acak, $nama_ahli kosong, jadi masuk sini.
    //  Kalau user pilih nama tapi gagal, baris di atas sudah throw Exception, jadi aman.)
    if ($pakar_id === null) {
        $sql_rand = "SELECT id FROM users WHERE role_id = 3 ORDER BY RAND() LIMIT 1";
        $stmt_rand_pakar = $conn->prepare($sql_rand);

        if (!$stmt_rand_pakar) {
            throw new Exception("Gagal prepare statement (random pakar): " . $conn->error);
        }

        if (!$stmt_rand_pakar->execute()) {
            throw new Exception("Gagal execute statement (random pakar): " . $stmt_rand_pakar->error);
        }

        $result_pakar = $stmt_rand_pakar->get_result();
        if ($result_pakar->num_rows > 0) {
            $pakar = $result_pakar->fetch_assoc();
            $pakar_id = (int)$pakar['id'];
        } else {
            throw new Exception('Tidak ada pakar yang tersedia saat ini.');
        }
        $stmt_rand_pakar->close();
    }

    // =========================================================
    // 6. SIMPAN KONSULTASI
    // =========================================================

    // Gabungkan pesan
    $pesan_lengkap = "Topik: " . $topik . "\n\n" . $pertanyaan;
    $pengirim = 'anggota';

    // UPDATE: Tambahkan kolom 'topik' ke dalam query INSERT
    $sql_insert = "INSERT INTO konsultasi (anggota_id, pakar_id, topik, pesan, pengirim) VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);

    if (!$stmt_insert) {
        throw new Exception("Gagal prepare statement (insert konsultasi): " . $conn->error);
    }

    // Bind param: i=int (anggota), i=int (pakar), s=string (topik), s=string (pesan), s=string (pengirim)
    $stmt_insert->bind_param("iisss", $anggota_id, $pakar_id, $topik, $pesan_lengkap, $pengirim);

    if ($stmt_insert->execute()) {
        $response['success'] = true;
        $response['message'] = 'Konsultasi berhasil dikirim.';
        $response['konsultasi_id'] = $conn->insert_id;
    } else {
        throw new Exception('Gagal menyimpan ke database: ' . $stmt_insert->error);
    }

    $stmt_insert->close();
    $conn->close();
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    // Opsional: $response['debug'] = $e->getTraceAsString();
}

// 9. Kembalikan respons JSON
echo json_encode($response);
