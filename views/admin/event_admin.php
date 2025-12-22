<?php
// --- PROSES TAMBAH EVENT ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_event'])) {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $tipe = $_POST['tipe'];
    $lokasi = $_POST['lokasi'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $link = $_POST['link_pendaftaran'];

    // Upload Gambar Event (Opsional)
    $gambar_nama = null;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../uploads/events/";
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
        $file_ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar_nama = "event_" . time() . "." . $file_ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], $target_dir . $gambar_nama);
    }

    $stmt = $conn->prepare("INSERT INTO events (judul, deskripsi, tipe, lokasi, tanggal_mulai, link_pendaftaran, gambar) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $judul, $deskripsi, $tipe, $lokasi, $tanggal_mulai, $link, $gambar_nama);
    
    if ($stmt->execute()) {
        echo "<script>alert('Event berhasil ditambahkan!'); window.location.href='index.php?page=event_admin';</script>";
    } else {
        echo "<script>alert('Gagal menambah event.');</script>";
    }
    $stmt->close();
}

// --- PROSES HAPUS EVENT ---
if (isset($_GET['hapus_id'])) {
    $hapus_id = $_GET['hapus_id'];
    // Ambil nama gambar dulu
    $res = $conn->query("SELECT gambar FROM events WHERE id = $hapus_id");
    $row = $res->fetch_assoc();
    
    if ($conn->query("DELETE FROM events WHERE id = $hapus_id")) {
        if ($row['gambar'] && file_exists("../uploads/events/" . $row['gambar'])) {
            unlink("../uploads/events/" . $row['gambar']);
        }
        echo "<script>alert('Event berhasil dihapus!'); window.location.href='index.php?page=event_admin';</script>";
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Manajemen Event</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahEvent">
        <i class="fas fa-plus me-1"></i>Tambah Event Baru
    </button>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>Tanggal</th>
                <th>Nama Event</th>
                <th>Tipe</th>
                <th>Lokasi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM events ORDER BY tanggal_mulai DESC");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $tgl = date('d M Y, H:i', strtotime($row['tanggal_mulai']));
                    echo "<tr>
                        <td>{$tgl}</td>
                        <td><strong>" . htmlspecialchars($row['judul']) . "</strong></td>
                        <td><span class='badge " . ($row['tipe'] == 'Online' ? 'bg-success' : 'bg-primary') . "'>{$row['tipe']}</span></td>
                        <td>" . htmlspecialchars($row['lokasi']) . "</td>
                        <td>
                            <a href='index.php?page=event_admin&hapus_id={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Hapus event ini?\")'><i class='fas fa-trash'></i></a>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>Belum ada event.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalTambahEvent" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Event Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Nama Event</label>
                        <input type="text" name="judul" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipe Event</label>
                            <select name="tipe" class="form-select" required>
                                <option value="Online">Online (Webinar)</option>
                                <option value="Offline">Offline (Tatap Muka)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal & Waktu Mulai</label>
                            <input type="datetime-local" name="tanggal_mulai" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lokasi (atau Link Zoom jika Online)</label>
                        <input type="text" name="lokasi" class="form-control" required placeholder="Contoh: Auditorium Polindra atau Zoom Meeting Link">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link Pendaftaran (Google Form, dll)</label>
                        <input type="url" name="link_pendaftaran" class="form-control" placeholder="https://...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gambar Banner Event</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi Event</label>
                        <textarea name="deskripsi" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="text-end">
                         <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_event" class="btn btn-primary">Simpan Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>