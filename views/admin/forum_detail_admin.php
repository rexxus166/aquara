<?php
// views/admin/forum_detail_admin.php

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID Topik tidak valid.</div>";
    exit;
}

$topic_id = $_GET['id'];

// 1. AMBIL DATA TOPIK
$query_topic = "SELECT ft.*, u.nama AS nama_penulis, u.foto_profil, r.role_name 
                FROM forum_topics ft
                JOIN users u ON ft.user_id = u.id
                LEFT JOIN roles r ON u.role_id = r.id
                WHERE ft.id = ?";
$stmt = $conn->prepare($query_topic);
$stmt->bind_param("i", $topic_id);
$stmt->execute();
$topic = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$topic) {
    echo "<div class='alert alert-warning'>Topik tidak ditemukan.</div>";
    exit;
}

// 2. AMBIL KOMENTAR/BALASAN
$replies = [];
$query_replies = "SELECT fr.*, u.nama AS nama_penulis, u.foto_profil, r.role_name 
                  FROM forum_replies fr
                  JOIN users u ON fr.user_id = u.id
                  LEFT JOIN roles r ON u.role_id = r.id
                  WHERE fr.topic_id = ? ORDER BY fr.created_at ASC";
$stmt_rep = $conn->prepare($query_replies);
$stmt_rep->bind_param("i", $topic_id);
$stmt_rep->execute();
$res_rep = $stmt_rep->get_result();
while ($row = $res_rep->fetch_assoc()) {
    $replies[] = $row;
}
$stmt_rep->close();
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h3 text-gray-800">Detail Topik Forum</h2>
    <a href="index.php?page=forum_admin" class="btn btn-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Topik Utama</h6>
        <span class="badge bg-info"><?= date('d M Y, H:i', strtotime($topic['created_at'])); ?></span>
    </div>
    <div class="card-body">
        <div class="d-flex align-items-center mb-4">
            <?php 
            // Path foto profil (sesuaikan jika perlu mundur folder ../)
            $foto_path = !empty($topic['foto_profil']) ? "../uploads/profil/" . $topic['foto_profil'] : "../assets/img/profil/default_profile.png"; 
            ?>
            <img src="<?= htmlspecialchars($foto_path); ?>" alt="Profil" class="rounded-circle mr-3" style="width: 50px; height: 50px; object-fit: cover; border: 2px solid #eee;">
            <div>
                <h5 class="mb-0 text-dark font-weight-bold"><?= htmlspecialchars($topic['nama_penulis']); ?></h5>
                <small class="text-muted"><?= htmlspecialchars($topic['role_name']); ?></small>
            </div>
        </div>
        
        <h3 class="h4 text-dark font-weight-bold mb-3"><?= htmlspecialchars($topic['judul']); ?></h3>
        <div class="text-dark" style="white-space: pre-wrap; line-height: 1.6;"><?= nl2br(htmlspecialchars($topic['deskripsi'])); ?></div>

        <?php if (!empty($topic['gambar'])): ?>
            <hr>
            <p class="font-weight-bold mb-2">Lampiran Gambar:</p>
            <img src="../uploads/forum/<?= htmlspecialchars($topic['gambar']); ?>" alt="Lampiran" class="img-fluid rounded" style="max-height: 400px; width: auto;">
        <?php endif; ?>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-secondary">Komentar & Balasan (<?= count($replies); ?>)</h6>
    </div>
    <div class="card-body">
        <?php if (count($replies) > 0): ?>
            <div class="list-group list-group-flush">
                <?php foreach ($replies as $reply): ?>
                    <div class="list-group-item p-3" style="background-color: #f8f9fc; border-radius: 8px; margin-bottom: 10px;">
                        <div class="d-flex w-100 justify-content-between">
                            <div class="d-flex align-items-center mb-2">
                                <strong><?= htmlspecialchars($reply['nama_penulis']); ?></strong>
                                <span class="badge bg-light text-dark ml-2 border"><?= $reply['role_name']; ?></span>
                            </div>
                            <small class="text-muted"><?= date('d/m/Y H:i', strtotime($reply['created_at'])); ?></small>
                        </div>
                        <p class="mb-1" style="white-space: pre-wrap;"><?= nl2br(htmlspecialchars($reply['konten'])); ?></p>
                        
                        </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-center text-muted my-4">Belum ada komentar di topik ini.</p>
        <?php endif; ?>
    </div>
</div>