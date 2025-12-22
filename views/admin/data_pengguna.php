<?php
// ==================================================================
// 1. HANDLER HAPUS DATA PENGGUNA
// Taruh kode ini PALING ATAS sebelum HTML dimulai
// ==================================================================
if (isset($_GET['hapus_id'])) {
    $id_hapus = $_GET['hapus_id'];

    // Mencegah admin menghapus dirinya sendiri secara tidak sengaja
    if ($id_hapus == $_SESSION['user_id']) {
        echo "<script>alert('Anda tidak bisa menghapus akun sendiri yang sedang login!'); window.location.href='index.php?page=data_pengguna';</script>";
        exit;
    }

    // Siapkan query hapus (gunakan prepared statement agar aman)
    $stmt_hapus = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt_hapus->bind_param("i", $id_hapus);

    if ($stmt_hapus->execute()) {
        echo "<script>alert('Data pengguna berhasil dihapus!'); window.location.href='index.php?page=data_pengguna';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data! Error: " . $conn->error . "'); window.location.href='index.php?page=data_pengguna';</script>";
    }
    $stmt_hapus->close();
    exit(); // Penting agar sisa halaman tidak dimuat saat proses hapus
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Data Pengguna (Anggota)</h2>
    </div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Tanggal Bergabung</th>
                        <th style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query untuk mengambil data anggota (misal role_id = 2 adalah anggota biasa)
                    // Sesuaikan 'role_id = 2' dengan struktur databasemu jika berbeda.
                    $query = "SELECT * FROM users WHERE role_id = 2 ORDER BY created_at DESC";
                    $result = $conn->query($query);

                    if ($result->num_rows > 0) {
                        $no = 1; // Jika ingin pakai nomor urut, ganti $row['id'] di kolom pertama dengan $no++
                        while ($row = $result->fetch_assoc()) {
                            $tgl_join = date('d-m-Y', strtotime($row['created_at']));
                    ?>
                            <tr>
                                <td><?= $row['id']; ?></td>
                                <td><?= htmlspecialchars($row['nama']); ?></td>
                                <td><?= htmlspecialchars($row['email']); ?></td>
                                <td><?= $tgl_join; ?></td>
                                <td>
                                    <a href="index.php?page=edit_pengguna&id=<?= $row['id']; ?>" 
                                        class="btn btn-sm btn-warning text-white" 
                                        title="Edit User">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <a href="index.php?page=data_pengguna&hapus_id=<?= $row['id']; ?>" 
                                        class="btn btn-sm btn-danger" 
                                        onclick="return confirm('Yakin ingin menghapus pengguna <?= htmlspecialchars($row['nama']); ?>? Data yang dihapus tidak bisa dikembalikan.')"
                                        title="Hapus User">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>Tidak ada data pengguna.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>