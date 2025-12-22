<?php
// 1. HANDLER HAPUS
if (isset($_GET['hapus_id'])) {
    $id_hapus = $_GET['hapus_id'];
    $stmt = $conn->prepare("DELETE FROM konsultasi WHERE id = ?");
    $stmt->bind_param("i", $id_hapus);
    if ($stmt->execute()) {
        echo "<script>alert('Data konsultasi berhasil dihapus!'); window.location.href='index.php?page=konsultasi_admin';</script>";
    } else {
        echo "<script>alert('Gagal menghapus: " . $conn->error . "'); window.location.href='index.php?page=konsultasi_admin';</script>";
    }
    $stmt->close();
    exit();
}

// 2. HANDLER BALAS KONSULTASI
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['balas_konsultasi'])) {
    $konsultasi_id = $_POST['konsultasi_id'];
    $jawaban = $_POST['jawaban'];
    $pakar_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE konsultasi SET jawaban = ?, pakar_id = ?, status = 'answered', tanggal_jawaban = NOW() WHERE id = ?");
    $stmt->bind_param("sii", $jawaban, $pakar_id, $konsultasi_id);

    if ($stmt->execute()) {
        echo "<script>alert('Jawaban berhasil dikirim!'); window.location.href='index.php?page=konsultasi_admin';</script>";
    } else {
        echo "<script>alert('Gagal mengirim jawaban: " . $conn->error . "');</script>";
    }
    $stmt->close();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Manajemen Konsultasi</h2>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTableKonsultasi" width="100%" cellspacing="0">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Pengguna</th>
                        <th>Pesan / Pertanyaan</th>
                        <th>Status</th>
                        <th style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT k.*, u.nama AS nama_pengguna 
                              FROM konsultasi k 
                              LEFT JOIN users u ON k.anggota_id = u.id 
                              ORDER BY k.created_at DESC";
                    $result = $conn->query($query);

                    if ($result && $result->num_rows > 0) {
                        $no = 1;
                        while ($row = $result->fetch_assoc()) {
                            if ($row['status'] == 'answered') {
                                $status_badge = '<span class="badge bg-success">Terjawab</span>';
                            } elseif ($row['status'] == 'closed') {
                                $status_badge = '<span class="badge bg-secondary">Ditutup</span>';
                            } else {
                                $status_badge = '<span class="badge bg-warning text-dark">Pending</span>';
                            }
                            $tanggal = date('d/m/Y', strtotime($row['created_at']));
                            $cuplikan_pesan = htmlspecialchars(substr($row['pesan'], 0, 50)) . '...';
                    ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $tanggal; ?></td>
                                <td><?= htmlspecialchars($row['nama_pengguna'] ?? 'User Terhapus'); ?></td>
                                <td><?= $cuplikan_pesan; ?></td>
                                <td><?= $status_badge; ?></td>
                                <td>
                                    <textarea id="raw-pesan-<?= $row['id']; ?>" style="display:none;"><?= htmlspecialchars($row['pesan']); ?></textarea>
                                    <textarea id="raw-jawaban-<?= $row['id']; ?>" style="display:none;"><?= htmlspecialchars($row['jawaban'] ?? 'Belum ada jawaban dari pakar.'); ?></textarea>

                                    <button type="button" class="btn btn-sm btn-info text-white btn-detail" 
                                            data-bs-toggle="modal" data-bs-target="#modalDetail"
                                            data-id="<?= $row['id']; ?>"
                                            data-nama="<?= htmlspecialchars($row['nama_pengguna'] ?? '-'); ?>"
                                            title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <?php if ($row['status'] == 'pending'): ?>
                                    <button type="button" class="btn btn-sm btn-success btn-balas" 
                                            data-bs-toggle="modal" data-bs-target="#modalBalas"
                                            data-id="<?= $row['id']; ?>"
                                            data-nama="<?= htmlspecialchars($row['nama_pengguna'] ?? '-'); ?>"
                                            title="Balas">
                                        <i class="fas fa-reply"></i>
                                    </button>
                                    <?php else: ?>
                                    <button class="btn btn-sm btn-secondary" disabled><i class="fas fa-check"></i></button>
                                    <?php endif; ?>

                                    <a href="index.php?page=konsultasi_admin&hapus_id=<?= $row['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Yakin hapus data ini?')"
                                       title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>Belum ada data konsultasi.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Konsultasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Dari:</strong> <span id="detailNama"></span></p>
                <h6>Pesan/Pertanyaan:</h6>
                <div class="alert alert-light border" id="detailPesan" style="white-space: pre-wrap; max-height: 300px; overflow-y: auto;"></div>
                <hr>
                <h6>Jawaban Pakar:</h6>
                <div class="alert alert-success border" id="detailJawaban" style="white-space: pre-wrap; max-height: 300px; overflow-y: auto;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBalas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Balas Konsultasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="konsultasi_id" id="balasId">
                    <p>Menjawab pertanyaan dari: <strong id="balasNama"></strong></p>
                    
                    <div class="mb-3">
                         <label class="form-label">Pertanyaan User:</label>
                         <div class="alert alert-secondary p-2" id="balasPesanUser" style="max-height: 150px; overflow-y: auto; font-size: 0.9rem; white-space: pre-wrap;"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jawaban Anda:</label>
                        <textarea class="form-control" name="jawaban" rows="6" required placeholder="Tulis jawaban di sini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="balas_konsultasi" class="btn btn-success">Kirim Jawaban</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Handler Tombol Detail
    const detailBtns = document.querySelectorAll('.btn-detail');
    detailBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            // Ambil data dari textarea tersembunyi berdasarkan ID
            const pesanFull = document.getElementById('raw-pesan-' + id).value;
            const jawabanFull = document.getElementById('raw-jawaban-' + id).value;
            
            document.getElementById('detailNama').textContent = this.dataset.nama;
            document.getElementById('detailPesan').textContent = pesanFull;
            document.getElementById('detailJawaban').textContent = jawabanFull;
        });
    });

    // Handler Tombol Balas
    const balasBtns = document.querySelectorAll('.btn-balas');
    balasBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const pesanFull = document.getElementById('raw-pesan-' + id).value;

            document.getElementById('balasId').value = id;
            document.getElementById('balasNama').textContent = this.dataset.nama;
            // Tampilkan juga pertanyaan di modal balas biar enak
            document.getElementById('balasPesanUser').textContent = pesanFull;
        });
    });
});
</script>