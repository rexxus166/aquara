<?php
// --- PROSES TAMBAH ARTIKEL ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_artikel'])) {
    $judul = $_POST['judul'];
    $kategori_id = $_POST['kategori_id'];
    $konten = $_POST['konten'];
    $user_id = $_SESSION['user_id']; // ID Admin yang sedang login

    // Upload Gambar
    $gambar_nama = null;
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../uploads/articles/";
        // Pastikan folder uploads/articles ada
        if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
        
        $file_ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar_nama = "artikel_" . time() . "." . $file_ext;
        $target_file = $target_dir . $gambar_nama;
        
        move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file);
    }

    $stmt = $conn->prepare("INSERT INTO articles (user_id, category_id, judul, konten, gambar) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $user_id, $kategori_id, $judul, $konten, $gambar_nama);
    
    if ($stmt->execute()) {
        echo "<script>alert('Artikel berhasil diterbitkan!'); window.location.href='index.php?page=artikel_admin';</script>";
    } else {
        echo "<script>alert('Gagal menerbitkan artikel: " . $conn->error . "');</script>";
    }
    $stmt->close();
}

// --- PROSES HAPUS ARTIKEL ---
if (isset($_GET['hapus_id'])) {
    $hapus_id = $_GET['hapus_id'];
    
    // Cek koneksi database dulu
    if (!$conn) {
        die("Koneksi database terputus saat mau menghapus.");
    }

    // 1. Ambil info gambar
    $stmt_cek = $conn->prepare("SELECT gambar FROM articles WHERE id = ?");
    if (!$stmt_cek) {
        die("Error prepare cek gambar: " . $conn->error);
    }
    $stmt_cek->bind_param("i", $hapus_id);
    $stmt_cek->execute();
    $result_cek = $stmt_cek->get_result();
    $row_cek = $result_cek->fetch_assoc();
    $stmt_cek->close(); // Tutup statement cek
    
    // 2. Proses Hapus Database
    $stmt_hapus = $conn->prepare("DELETE FROM articles WHERE id = ?");
    if (!$stmt_hapus) {
        die("Error prepare delete: " . $conn->error);
    }
    $stmt_hapus->bind_param("i", $hapus_id);
    
    if ($stmt_hapus->execute()) {
        // Sukses hapus data di DB, sekarang hapus filenya
        if ($row_cek['gambar'] && file_exists("../uploads/articles/" . $row_cek['gambar'])) {
            unlink("../uploads/articles/" . $row_cek['gambar']);
        }
        
        // Redirect sukses
        echo "<script>
            alert('Artikel berhasil dihapus!');
            window.location.href='index.php?page=artikel_admin';
        </script>";
        exit(); // PENTING: Stop script agar tidak lanjut menampilkan tabel di bawahnya saat proses delete
    } else {
        // Gagal hapus
        echo "<script>
            alert('Gagal menghapus artikel! Error: " . addslashes($stmt_hapus->error) . "');
            window.location.href='index.php?page=artikel_admin';
        </script>";
        exit();
    }
    $stmt_hapus->close();
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Manajemen Artikel</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahArtikel">
        <i class="fas fa-plus me-1"></i>Tulis Artikel Baru
    </button>
</div>

<div class="table-responsive">
    <table id="tabelArtikel" class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>Tanggal</th>
                <th>Judul</th>
                <th>Kategori</th>
                <th>Penulis</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT a.*, c.nama AS kategori_nama, u.nama AS penulis_nama 
                    FROM articles a 
                    LEFT JOIN categories c ON a.category_id = c.id 
                    LEFT JOIN users u ON a.user_id = u.id 
                    ORDER BY a.created_at DESC";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $tanggal = date('d/m/Y', strtotime($row['created_at']));
                echo "<tr>
                    <td>{$tanggal}</td>
                    <td>" . htmlspecialchars(substr($row['judul'], 0, 50)) . "...</td>
                    <td><span class='badge bg-info'>{$row['kategori_nama']}</span></td>
                    <td>{$row['penulis_nama']}</td>
                    <td>
                        <a href='index.php?page=artikel_admin&hapus_id={$row['id']}' 
                           class='btn btn-sm btn-danger' 
                           onclick='return confirm(\"Yakin hapus artikel ini?\")'>
                           <i class='fas fa-trash'></i>
                        </a>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="modalTambahArtikel" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tulis Artikel Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Judul Artikel</label>
                        <input type="text" name="judul" class="form-control" required placeholder="Contoh: Cara Budidaya Lele untuk Pemula">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori</label>
                            <select name="kategori_id" class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                <?php
                                $res_kat = $conn->query("SELECT * FROM categories");
                                while ($kat = $res_kat->fetch_assoc()) {
                                    echo "<option value='{$kat['id']}'>{$kat['nama']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gambar Unggulan</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Isi Artikel (Bisa pakai HTML sederhana)</label>
                        <textarea name="konten" class="form-control" rows="10" required placeholder="<p>Tulis isi artikel Anda di sini...</p>"></textarea>
                        <small class="text-muted">Tips: Gunakan tag &lt;p&gt; untuk paragraf, &lt;h2&gt; untuk sub-judul.</small>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_artikel" class="btn btn-primary px-4">Terbitkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>