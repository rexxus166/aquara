<?php
// 1. AMBIL ID PENGGUNA YANG MAU DIEDIT
if (!isset($_GET['id'])) {
    // Jika tidak ada ID di URL, kembalikan ke halaman data pengguna
    echo "<script>window.location.href='index.php?page=data_pengguna';</script>";
    exit;
}
$id_edit = $_GET['id'];

// 2. PROSES UPDATE DATA JIKA TOMBOL SIMPAN DITEKAN
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    $nama_baru = $_POST['nama'];
    $email_baru = $_POST['email'];
    $role_baru = $_POST['role_id'];
    
    // Opsional: Update password jika diisi
    $password_query = "";
    if (!empty($_POST['password'])) {
        $password_baru = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $password_query = ", password = '$password_baru'";
    }

    // Query Update
    // PERHATIAN: Gunakan prepared statement untuk keamanan yang lebih baik di production
    $query_update = "UPDATE users SET 
                        nama = '$nama_baru', 
                        email = '$email_baru', 
                        role_id = '$role_baru' 
                        $password_query
                     WHERE id = '$id_edit'";

    if ($conn->query($query_update)) {
        echo "<script>alert('Data pengguna berhasil diperbarui!'); window.location.href='index.php?page=data_pengguna';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data: " . $conn->error . "');</script>";
    }
}

// 3. AMBIL DATA PENGGUNA LAMA UNTUK DITAMPILKAN DI FORM
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id_edit);
$stmt->execute();
$result = $stmt->get_result();
$data_user = $result->fetch_assoc();

// Jika user tidak ditemukan dengan ID tersebut
if (!$data_user) {
    echo "<script>alert('Pengguna tidak ditemukan!'); window.location.href='index.php?page=data_pengguna';</script>";
    exit;
}
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="text-dark mb-0">Edit Pengguna</h3>
        <a href="index.php?page=data_pengguna" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Data Pengguna</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama" name="nama" 
                           value="<?= htmlspecialchars($data_user['nama']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Alamat Email</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?= htmlspecialchars($data_user['email']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="role_id" class="form-label">Peran (Role)</label>
                    <select class="form-select" id="role_id" name="role_id" required>
                        <option value="1" <?= ($data_user['role_id'] == 1) ? 'selected' : ''; ?>>Admin</option>
                        <option value="2" <?= ($data_user['role_id'] == 2) ? 'selected' : ''; ?>>Anggota (User Biasa)</option>
                        <option value="3" <?= ($data_user['role_id'] == 3) ? 'selected' : ''; ?>>Pakar</option>
                        </select>
                </div>

                <hr>
                <div class="mb-3">
                    <label for="password" class="form-label text-danger">Ubah Password (Opsional)</label>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Kosongkan jika tidak ingin mengubah password">
                    <small class="text-muted">Hanya isi jika ingin mengganti password pengguna ini.</small>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                    <button type="submit" name="update_user" class="btn btn-primary px-4">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>