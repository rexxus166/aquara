<?php
$activeMenu = 'forum';
// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

$message = "";
$message_type = "";

// --- PROSES FORM SUBMIT ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul = trim($_POST['judul']);
    $deskripsi = trim($_POST['deskripsi']);
    $user_id = $_SESSION['user_id'];
    $gambar = null;

    // Validasi sederhana
    if (empty($judul) || empty($deskripsi)) {
        $message = "Judul dan Deskripsi wajib diisi!";
        $message_type = "danger";
    } else {
        // Proses Upload Gambar (Opsional)
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($ext), $allowed)) {
                $gambar_nama = uniqid('topic_', true) . "." . $ext;
                $upload_dir = "../../uploads/forum/";
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                
                if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_dir . $gambar_nama)) {
                    $gambar = $gambar_nama;
                }
            } else {
                $message = "Format gambar tidak valid! (Hanya JPG, PNG, GIF)";
                $message_type = "danger";
            }
        }

        // Jika tidak ada error upload, simpan ke database
        if (empty($message)) {
            $stmt = $conn->prepare("INSERT INTO forum_topics (user_id, judul, deskripsi, gambar, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->bind_param("isss", $user_id, $judul, $deskripsi, $gambar);

            if ($stmt->execute()) {
                // Redirect ke halaman forum setelah berhasil
                echo "<script>alert('Topik berhasil dibuat!'); window.location.href='index_anggota.php?page=forum_anggota';</script>";
                exit;
            } else {
                $message = "Gagal membuat topik: " . $conn->error;
                $message_type = "danger";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Topik Baru - AQUARA</title>
    <link rel="stylesheet" href="../../assets/css/header_footer_pakar.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }
        .form-header {
            text-align: center; margin-bottom: 30px;
        }
        .form-header h2 {
            color: #3f8686; font-weight: 700; margin-bottom: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;
        }
        .form-input, .form-textarea {
            width: 100%; padding: 12px 15px; border: 2px solid #eee; border-radius: 10px;
            font-family: inherit; font-size: 15px; transition: border-color 0.3s;
        }
        .form-input:focus, .form-textarea:focus {
            outline: none; border-color: #3f8686;
        }
        .form-textarea {
            resize: vertical; height: 150px;
        }
        .btn-submit {
            width: 100%; padding: 15px; background: #3f8686; color: white; border: none;
            border-radius: 10px; font-size: 16px; font-weight: 700; cursor: pointer;
            transition: background 0.3s;
        }
        .btn-submit:hover {
            background: #2c6666;
        }
        .alert {
            padding: 15px; border-radius: 10px; margin-bottom: 20px;
        }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<?php include '../../includes/anggota/header_home_anggota.php'; ?>

<main class="form-container">
    <div class="form-header">
        <h2>Buat Topik Diskusi Baru</h2>
        <p>Ajukan pertanyaan atau mulai diskusi dengan komunitas.</p>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label class="form-label">Judul Topik</label>
            <input type="text" name="judul" class="form-input" placeholder="Contoh: Bagaimana cara mengatasi lele kembung?" required>
        </div>

        <div class="form-group">
            <label class="form-label">Isi Diskusi / Pertanyaan</label>
            <textarea name="deskripsi" class="form-textarea" placeholder="Jelaskan detail pertanyaan atau diskusi Anda di sini..." required></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Upload Gambar (Opsional)</label>
            <input type="file" name="gambar" class="form-input" accept="image/*">
            <small style="color: #777;">Format: JPG, PNG, GIF. Maksimal 2MB.</small>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <a href="index_anggota.php?page=forum_anggota" style="padding: 15px 30px; background: #eee; color: #555; text-decoration: none; border-radius: 10px; font-weight: 600;">Batal</a>
            <button type="submit" class="btn-submit">Terbitkan Topik</button>
        </div>
    </form>
</main>

<?php include '../../includes/anggota/footer_konsultasi_anggota.php'; ?>

</body>
</html>