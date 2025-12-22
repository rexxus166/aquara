<?php
// ==================================================================
// HANDLER HAPUS TOPIK
// ==================================================================
if (isset($_GET['hapus_id'])) {
    $id_hapus = $_GET['hapus_id'];

    // Ambil info gambar dulu sebelum hapus datanya
    $stmt_cek = $conn->prepare("SELECT gambar FROM forum_topics WHERE id = ?");
    $stmt_cek->bind_param("i", $id_hapus);
    $stmt_cek->execute();
    $res_cek = $stmt_cek->get_result();
    $row_cek = $res_cek->fetch_assoc();

    // Proses Hapus dari Database (Cascade akan otomatis hapus replies & likes jika sudah disetting)
    $stmt = $conn->prepare("DELETE FROM forum_topics WHERE id = ?");
    $stmt->bind_param("i", $id_hapus);
    
    if ($stmt->execute()) {
        // Hapus file gambar jika ada
        if (!empty($row_cek['gambar']) && file_exists("../uploads/forum/" . $row_cek['gambar'])) {
            unlink("../uploads/forum/" . $row_cek['gambar']);
        }
        echo "<script>alert('Topik forum berhasil dihapus!'); window.location.href='index.php?page=forum_admin';</script>";
    } else {
        echo "<script>alert('Gagal menghapus topik: " . $conn->error . "'); window.location.href='index.php?page=forum_admin';</script>";
    }
    $stmt->close();
    exit();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Manajemen Forum Diskusi</h2>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Topik Diskusi</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 15%;">Tanggal</th>
                        <th style="width: 20%;">Penulis</th>
                        <th>Judul Topik</th>
                        <th style="width: 10%;">Balasan</th>
                        <th style="width: 10%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query ambil topik beserta nama penulis dan jumlah balasan
                    $query = "SELECT ft.*, u.nama AS nama_penulis, 
                                     (SELECT COUNT(*) FROM forum_replies WHERE topic_id = ft.id) AS jml_balasan
                              FROM forum_topics ft
                              LEFT JOIN users u ON ft.user_id = u.id
                              ORDER BY ft.created_at DESC";
                    $result = $conn->query($query);

                    if ($result && $result->num_rows > 0) {
                        $no = 1;
                        while ($row = $result->fetch_assoc()) {
                            $tanggal = date('d/m/Y H:i', strtotime($row['created_at']));
                    ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $tanggal; ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($row['nama_penulis'] ?? 'User Terhapus'); ?></strong>
                                </td>
                                <td>
                                    <a href="index.php?page=forum_detail_admin&id=<?= $row['id']; ?>" style="text-decoration: none; font-weight: bold;">
                                            <?= htmlspecialchars($row['judul']); ?>
                                        <i class="fas fa-eye fa-xs ml-1 text-muted" title="Lihat Detail"></i>
                                    </a>
                                    <?php if (!empty($row['gambar'])): ?>
                                        <br><small class="text-muted"><i class="fas fa-image"></i> Ada lampiran gambar</small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary" style="font-size: 14px;">
                                        <?= $row['jml_balasan']; ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="index.php?page=forum_admin&hapus_id=<?= $row['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Yakin ingin menghapus topik ini beserta semua balasannya? Tindakan ini tidak dapat dibatalkan.')"
                                       title="Hapus Topik">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center py-4 text-muted'>Belum ada topik diskusi.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>