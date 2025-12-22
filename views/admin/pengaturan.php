<?php
// 1. AMBIL DATA ADMIN SAAT INI DARI DATABASE
$id_admin = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id_admin);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// 2. PROSES FORM SAAT DISUBMIT
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['simpan_pengaturan'])) {
    $nama_baru = trim($_POST['nama']);
    $email_baru = trim($_POST['email']);
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    // Validasi dasar
    if (empty($nama_baru) || empty($email_baru)) {
        echo "<script>alert('Nama dan Email tidak boleh kosong!');</script>";
    } else {
        // Siapkan query update dasar (nama & email)
        $query_update = "UPDATE users SET nama = ?, email = ?";
        $params = [$nama_baru, $email_baru];
        $types = "ss";

        // Jika password diisi, tambahkan ke query update
        if (!empty($password_baru)) {
            if ($password_baru !== $konfirmasi_password) {
                echo "<script>alert('Konfirmasi password tidak cocok!'); window.location.href='index.php?page=pengaturan';</script>";
                exit();
            }
            // Hash password baru
            $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
            $query_update .= ", password = ?";
            $params[] = $password_hash;
            $types .= "s";
        }

        // Lengkapi query
        $query_update .= " WHERE id = ?";
        $params[] = $id_admin;
        $types .= "i";

        // Eksekusi Update
        $stmt_update = $conn->prepare($query_update);
        $stmt_update->bind_param($types, ...$params);

        if ($stmt_update->execute()) {
            // Update juga session nama jika berubah
            $_SESSION['nama_admin'] = $nama_baru; // Asumsi kamu simpan nama di session
            echo "<script>alert('Pengaturan berhasil disimpan!'); window.location.href='index.php?page=pengaturan';</script>";
        } else {
            echo "<script>alert('Gagal menyimpan pengaturan: " . $conn->error . "');</script>";
        }
        $stmt_update->close();
        // Refresh data admin setelah update agar form terisi data terbaru
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();
    }
}
$stmt->close();
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user-cog me-2"></i>Pengaturan Akun Admin</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="" id="formPengaturan">
                    <div class="mb-3">
                        <label for="namaAdmin" class="form-label">Nama Admin</label>
                        <input type="text" class="form-control" id="namaAdmin" name="nama" 
                                value="<?= htmlspecialchars($admin['nama']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="emailAdmin" class="form-label">Email</label>
                        <input type="email" class="form-control" id="emailAdmin" name="email" 
                                value="<?= htmlspecialchars($admin['email']); ?>" required>
                    </div>
                    
                    <hr class="my-4">
                    <h6 class="text-muted mb-3"><i class="fas fa-lock me-1"></i>Ganti Password (Opsional)</h6>
                    
                    <div class="mb-3">
                        <label for="passwordAdmin" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="passwordAdmin" name="password_baru" 
                                placeholder="Kosongkan jika tidak ingin mengubah password">
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="confirmPassword" name="konfirmasi_password" 
                                placeholder="Ulangi password baru">
                    </div>

                    <button type="submit" name="simpan_pengaturan" class="btn btn-primary w-100">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formPengaturan');
    const pass = document.getElementById('passwordAdmin');
    const confirm = document.getElementById('confirmPassword');

    form.addEventListener('submit', function(e) {
        if (pass.value && pass.value !== confirm.value) {
            e.preventDefault();
            alert('Password baru dan konfirmasi password tidak cocok!');
            confirm.focus();
        }
    });
});
</script>