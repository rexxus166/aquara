<?php
// Variabel untuk pesan (jika perlu)
$message = "";

// 1. Cek apakah ini permintaan POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 2. Ambil data dari form (gunakan 'nama' yang sudah dikoreksi)
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 3. Validasi Sederhana
    if ($password !== $confirm_password) {
        // Jika password tidak cocok
        // Kita kirim pesan error kembali ke halaman register
        header("Location: index.php?page=register&error=password_mismatch");
        exit;
    }

    // 4. Cek apakah email sudah ada
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Jika email sudah terdaftar
        $stmt_check->close();
        header("Location: index.php?page=register&error=email_exists");
        exit;
    }
    $stmt_check->close();

    // 5. Jika semua aman, HASH password (SANGAT PENTING)
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // 6. Tentukan role_id (default 'anggota' adalah 2, sesuai aquara.sql)
    $default_role_id = 2;

    // 7. Siapkan query INSERT
    $stmt_insert = $conn->prepare("INSERT INTO users (nama, email, password, role_id) VALUES (?, ?, ?, ?)");
    $stmt_insert->bind_param("sssi", $nama, $email, $password_hash, $default_role_id);

    // 8. Eksekusi query
    if ($stmt_insert->execute()) {
        // Jika registrasi berhasil
        // Arahkan ke halaman login dengan pesan sukses
        header("Location: index.php?page=login&status=register_success");
        exit;
    } else {
        // Jika ada error database
        header("Location: index.php?page=register&error=db_error");
        exit;
    }

    // Tutup statement
    $stmt_insert->close();

} else {
    // Jika file ini diakses langsung tanpa POST, tendang ke home
    header("Location: index.php?page=home");
    exit;
}

// Tutup koneksi
$conn->close();
?>