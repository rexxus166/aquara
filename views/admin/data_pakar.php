<?php
// ==================================================================
// 1. HANDLER TAMBAH PAKAR BARU
// ==================================================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_pakar'])) {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id = 3; // Asumsi ID untuk role Pakar adalah 3

    // Cek apakah email sudah terdaftar
    $cek_email = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($cek_email->num_rows > 0) {
        echo "<script>alert('Email sudah terdaftar! Gunakan email lain.'); window.location.href='index.php?page=data_pakar';</script>";
        exit;
    }

    // Insert ke database
    $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $nama, $email, $password, $role_id);

    if ($stmt->execute()) {
        echo "<script>alert('Pakar baru berhasil ditambahkan!'); window.location.href='index.php?page=data_pakar';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan pakar: " . $conn->error . "'); window.location.href='index.php?page=data_pakar';</script>";
    }
    $stmt->close();
    exit();
}

// ==================================================================
// 2. HANDLER HAPUS PAKAR
// ==================================================================
if (isset($_GET['hapus_id'])) {
    $id_hapus = $_GET['hapus_id'];

    // Hapus data pakar (pastikan ON DELETE CASCADE sudah aktif di database untuk tabel terkait)
    $stmt_hapus = $conn->prepare("DELETE FROM users WHERE id = ? AND role_id = 3");
    $stmt_hapus->bind_param("i", $id_hapus);

    if ($stmt_hapus->execute()) {
        // Cek apakah ada baris yang terhapus (untuk memastikan ID valid dan benar role pakar)
        if ($stmt_hapus->affected_rows > 0) {
            echo "<script>alert('Data pakar berhasil dihapus!'); window.location.href='index.php?page=data_pakar';</script>";
        } else {
            echo "<script>alert('Data tidak ditemukan atau bukan data pakar.'); window.location.href='index.php?page=data_pakar';</script>";
        }
    } else {
        // Jika gagal karena foreign key (dan belum di-CASCADE), error akan muncul disini
        echo "<script>alert('Gagal menghapus! Pastikan pakar ini tidak memiliki data terkait yang penting.'); window.location.href='index.php?page=data_pakar';</script>";
    }
    $stmt_hapus->close();
    exit();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Data Pakar</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPakar">
        <i class="fas fa-plus me-1"></i> Tambah Pakar
    </button>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" id="dataTablePakar" width="100%" cellspacing="0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nama Pakar</th>
                        <th>Email</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query ambil data HANYA yang role-nya Pakar (role_id = 3)
                    $query = "SELECT * FROM users WHERE role_id = 3 ORDER BY nama ASC";
                    $result = $conn->query($query);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                    ?>
                            <tr>
                                <td><?= $row['id']; ?></td>
                                <td><?= htmlspecialchars($row['nama']); ?></td>
                                <td><?= htmlspecialchars($row['email']); ?></td>
                                <td>
                                    <a href="index.php?page=edit_pengguna&id=<?= $row['id']; ?>" 
                                        class="btn btn-sm btn-warning text-white" title="Edit Pakar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?page=data_pakar&hapus_id=<?= $row['id']; ?>" 
                                        class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Yakin ingin menghapus pakar <?= htmlspecialchars($row['nama']); ?>?')"
                                        title="Hapus Pakar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>Belum ada data pakar.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahPakar" tabindex="-1" aria-labelledby="modalTambahPakarLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTambahPakarLabel">Tambah Pakar Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="namaPakar" class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" id="namaPakar" name="nama" required placeholder="Contoh: Dr. Budi Santoso, S.Pi">
                    </div>
                    <div class="mb-3">
                        <label for="emailPakar" class="form-label">Email</label>
                        <input type="email" class="form-control" id="emailPakar" name="email" required placeholder="email@aquara.com">
                    </div>
                    <div class="mb-3">
                        <label for="passwordPakar" class="form-label">Password Awal</label>
                        <input type="password" class="form-control" id="passwordPakar" name="password" required minlength="6" placeholder="Minimal 6 karakter">
                        <small class="text-muted">Password ini bisa diubah pakar nanti.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah_pakar" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>