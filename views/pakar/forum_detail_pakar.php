<?php
$activeMenu = 'forum';

// PASTIKAN SESSION DAN KONEKSI
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once '../../includes/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: ../../index.php?page=login");
    exit;
}

// 1. Ambil ID Topik
$topic_id = $_GET['id'] ?? 0;
if ($topic_id == 0) {
    echo "Topik tidak valid.";
    exit;
}

// 2. Ambil ID user
$user_id = $_SESSION['user_id'];
$message = "";
$message_type = "";

// --- HANDLER: HAPUS TOPIK ---
if (isset($_GET['action']) && $_GET['action'] == 'delete_topic') {
    $stmt = $conn->prepare("DELETE FROM forum_topics WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $topic_id, $user_id);
    if ($stmt->execute()) {
        echo "<script>alert('Topik berhasil dihapus!'); window.location.href='index_pakar.php?page=forum_pakar';</script>";
        exit;
    }
}

// --- HANDLER: HAPUS KOMENTAR ---
if (isset($_GET['action']) && $_GET['action'] == 'delete_reply' && isset($_GET['reply_id'])) {
    $reply_id = $_GET['reply_id'];
    $stmt = $conn->prepare("DELETE FROM forum_replies WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $reply_id, $user_id);
    if ($stmt->execute()) {
        echo "<script>window.location.href='index_pakar.php?page=forum_detail_pakar&id=$topic_id';</script>";
        exit;
    }
}

// --- HANDLER: EDIT TOPIK (DENGAN GAMBAR) ---
if (isset($_POST['edit_topik'])) {
    $judul_baru = $_POST['judul'];
    $deskripsi_baru = $_POST['deskripsi'];
    
    // Cek apakah ada gambar baru yang diupload
    if (!empty($_FILES['gambar']['name'])) {
        $file_name = $_FILES['gambar']['name'];
        $file_tmp = $_FILES['gambar']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($file_ext, $allowed)) {
            $new_file_name = uniqid('topic_', true) . '.' . $file_ext;
            $upload_path = '../../uploads/forum/' . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $upload_path)) {
                // Update Judul, Deskripsi, DAN Gambar
                $stmt = $conn->prepare("UPDATE forum_topics SET judul=?, deskripsi=?, gambar=? WHERE id=? AND user_id=?");
                $stmt->bind_param("sssii", $judul_baru, $deskripsi_baru, $new_file_name, $topic_id, $user_id);
            }
        }
    } else {
        // Update HANYA Judul dan Deskripsi (Gambar tetap lama)
        $stmt = $conn->prepare("UPDATE forum_topics SET judul=?, deskripsi=? WHERE id=? AND user_id=?");
        $stmt->bind_param("ssii", $judul_baru, $deskripsi_baru, $topic_id, $user_id);
    }

    $stmt->execute();
    echo "<script>window.location.href='index_pakar.php?page=forum_detail_pakar&id=$topic_id';</script>";
    exit;
}

// 3. (PROSES FORM KOMENTAR)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['komentar'])) {
    $komentar_text = trim($_POST['komentar']);
    if (!empty($komentar_text)) {
        $stmt_insert = $conn->prepare("INSERT INTO forum_replies (topic_id, user_id, konten) VALUES (?, ?, ?)");
        $stmt_insert->bind_param("iis", $topic_id, $user_id, $komentar_text);
        if ($stmt_insert->execute()) {
            echo "<script>window.location.href='index_pakar.php?page=forum_detail_pakar&id={$topic_id}&status=success';</script>";
            exit;
        } else {
            $message = "Gagal memposting komentar.";
            $message_type = "error";
        }
        $stmt_insert->close();
    } else {
        $message = "Komentar tidak boleh kosong.";
        $message_type = "error";
    }
}

if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $message = "Komentar berhasil diposting!";
    $message_type = "success";
}

// 4. (AMBIL DATA TOPIK)
$query_topic = "SELECT ft.*, u.nama AS nama_user, u.foto_profil, r.role_name,
                (SELECT COUNT(*) FROM post_likes WHERE post_id = ft.id) AS jumlah_suka,
                (SELECT COUNT(*) FROM post_likes WHERE post_id = ft.id AND user_id = ?) AS is_liked
            FROM forum_topics ft
            JOIN users u ON ft.user_id = u.id
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE ft.id = ?";

$stmt_topic = $conn->prepare($query_topic);
$stmt_topic->bind_param("ii", $user_id, $topic_id);
$stmt_topic->execute();
$topic = $stmt_topic->get_result()->fetch_assoc();
$stmt_topic->close();

if (!$topic) {
    echo "<div style='padding: 50px; text-align: center;'>Topik tidak ditemukan atau telah dihapus.</div>";
    exit;
}

// 5. (AMBIL KOMENTAR)
$replies = [];
$stmt_replies = $conn->prepare("SELECT fr.*, u.nama AS nama_user, u.foto_profil, r.role_name 
                                FROM forum_replies fr 
                                JOIN users u ON fr.user_id = u.id 
                                LEFT JOIN roles r ON u.role_id = r.id 
                                WHERE fr.topic_id = ? 
                                ORDER BY fr.created_at ASC");
$stmt_replies->bind_param("i", $topic_id);
$stmt_replies->execute();
$result_replies = $stmt_replies->get_result();
while ($row = $result_replies->fetch_assoc()) {
    $replies[] = $row;
}
$stmt_replies->close();

// --- HELPER WAKTU ---
if (!function_exists('time_ago_detail')) {
    function time_ago_detail($datetime) {
        $time = strtotime($datetime);
        $diff = time() - $time;
        if ($diff < 60) return 'baru saja';
        $units = [31536000 => 'tahun', 2592000 => 'bulan', 604800 => 'minggu', 86400 => 'hari', 3600 => 'jam', 60 => 'menit'];
        foreach ($units as $val => $text) {
            if ($diff >= $val) return floor($diff / $val) . ' ' . $text . ' lalu';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($topic['judul']); ?> - Forum AQUARA</title>
    <link rel="stylesheet" href="../../assets/css/header_footer_pakar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        /* CSS SAMA PERSIS DENGAN ANGGOTA */
        .forum-container { max-width: 850px; margin: 40px auto; padding: 25px; background: #fff; border-radius: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        .post-card { border: 1px solid #eee; border-radius: 12px; overflow: hidden; }
        .post-main { padding: 25px; }
        .post-header-flex { display: flex; gap: 15px; margin-bottom: 20px; align-items: center; }
        .post-avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; border: 2px solid #eee; }
        .post-meta-info h4 { font-size: 16px; color: #2c3e50; margin: 0 0 5px; font-weight: 600; }
        .author-role { background: #eef2f7; color: #555; font-size: 11px; padding: 3px 10px; border-radius: 12px; font-weight: 500; display: inline-block; }
        .post-date { font-size: 13px; color: #999; margin-left: auto; }
        .post-title { font-size: 24px; color: #2c3e50; margin: 0 0 15px; font-weight: 700; line-height: 1.3; }
        .post-body { font-size: 16px; color: #444; line-height: 1.7; white-space: pre-wrap; }
        .post-image { max-width: 100%; border-radius: 10px; margin-top: 20px; border: 1px solid #eee; }
        .post-actions { display: flex; gap: 5px; padding: 15px 25px; background: #f9fbfc; border-top: 1px solid #eee; }
        .action-btn { background: none; border: none; color: #666; cursor: pointer; display: flex; align-items: center; gap: 8px; font-size: 14px; padding: 8px 16px; border-radius: 50px; transition: all 0.2s; font-weight: 500; }
        .action-btn:hover { background-color: #eef2f7; color: #3f8686; }
        .action-btn i { font-size: 18px; }
        .btn-like.liked { color: #3f8686; background-color: rgba(63, 134, 134, 0.1); font-weight: 600; }
        .btn-like.liked i { animation: pop 0.3s ease; }
        @keyframes pop { 50% { transform: scale(1.3); } }
        .replies-section { margin-top: 40px; }
        .replies-header { font-size: 20px; font-weight: 700; color: #2c3e50; margin-bottom: 25px; display: flex; align-items: center; gap: 10px; }
        .replies-header::before { content: ''; display: block; width: 4px; height: 24px; background: #3f8686; border-radius: 4px; }
        .reply-card { display: flex; gap: 15px; margin-bottom: 25px; }
        .reply-content-box { flex-grow: 1; background: #f8f9fa; padding: 20px; border-radius: 0 16px 16px 16px; position: relative; }
        .reply-author { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .reply-author h5 { font-size: 15px; margin: 0; color: #2c3e50; font-weight: 600; }
        .reply-date { font-size: 12px; color: #999; margin-left: auto; }
        .reply-body { font-size: 15px; color: #444; line-height: 1.6; }
        .form-komentar { margin-top: 50px; background: #f9fbfc; padding: 25px; border-radius: 16px; border: 1px solid #eee; }
        .form-komentar h3 { font-size: 18px; color: #2c3e50; margin-bottom: 20px; }
        .form-komentar textarea { width: 100%; height: 120px; padding: 15px; border: 2px solid #eee; border-radius: 12px; font-size: 15px; font-family: inherit; resize: vertical; transition: border-color 0.3s; }
        .form-komentar textarea:focus { outline: none; border-color: #3f8686; }
        .btn-submit-komentar { margin-top: 15px; padding: 12px 30px; background-color: #3f8686; color: white; border: none; border-radius: 50px; cursor: pointer; font-weight: 600; font-size: 16px; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-submit-komentar:hover { background-color: #2c6666; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(63, 134, 134, 0.3); }
        .message { padding: 15px; margin-bottom: 25px; border-radius: 10px; font-weight: 500; text-align: center; }
        .message-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<?php include '../../includes/pakar/header_pakar.php'; ?>

<main class="forum-container">

    <?php if (!empty($message)): ?>
        <div class="message <?php echo ($message_type == 'success') ? 'message-success' : 'message-error'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <article class="post-card">
        <div class="post-main">
            <div class="post-header-flex">
                <?php 
                $foto = !empty($topic['foto_profil']) ? '/aquara/uploads/profil/' . htmlspecialchars($topic['foto_profil']) : '../../assets/img/profil/default_profile.png';
                ?>
                <img src="<?php echo $foto; ?>" alt="Avatar" class="post-avatar" onerror="this.src='../../assets/img/profil/default_profile.png'">
                <div class="post-meta-info">
                    <h4><?php echo htmlspecialchars($topic['nama_user']); ?></h4>
                    <span class="author-role"><?php echo htmlspecialchars($topic['role_name'] ?? 'Pakar'); ?></span>
                </div>
                
                <div style="margin-left: auto; display: flex; align-items: center; gap: 10px;">
                    <span class="post-date" style="margin: 0;"><?php echo time_ago_detail($topic['created_at']); ?></span>
                    
                    <?php if ($topic['user_id'] == $user_id): ?>
                        <button onclick="document.getElementById('modalEditTopik').style.display='block'" class="action-btn" style="color: #f39c12; padding: 5px;" title="Edit Topik">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <a href="index_pakar.php?page=forum_detail_pakar&id=<?= $topic_id ?>&action=delete_topic" 
                           onclick="return confirm('Yakin ingin menghapus topik ini?')"
                           class="action-btn" style="color: #e74c3c; padding: 5px;" title="Hapus Topik">
                            <i class="bi bi-trash"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <h1 class="post-title"><?php echo htmlspecialchars($topic['judul']); ?></h1>
            <div class="post-body"><?php echo nl2br(htmlspecialchars($topic['deskripsi'])); ?></div>
            
            <?php if (!empty($topic['gambar'])): ?>
                <img src="../../uploads/forum/<?php echo htmlspecialchars($topic['gambar']); ?>" alt="Lampiran" class="post-image">
            <?php endif; ?>
        </div>

        <div class="post-actions">
            <button class="action-btn btn-like <?php echo ($topic['is_liked'] > 0) ? 'liked' : ''; ?>" 
                    data-id="<?php echo $topic['id']; ?>">
                <i class="bi bi-hand-thumbs-up<?php echo ($topic['is_liked'] > 0) ? '-fill' : ''; ?>"></i>
                <span id="likes-count"><?php echo $topic['jumlah_suka']; ?></span> Suka
            </button>
            
            <button class="action-btn btn-share" 
                    data-id="<?php echo $topic['id']; ?>" 
                    data-judul="<?php echo htmlspecialchars($topic['judul']); ?>">
                <i class="bi bi-share"></i> Bagikan
            </button>
        </div>
    </article>

    <section class="replies-section">
        <h3 class="replies-header">
            Komentar <span style="color: #999; font-weight: 400; margin-left: 5px;">(<?php echo count($replies); ?>)</span>
        </h3>
        
        <div class="replies-list">
            <?php if (count($replies) > 0): ?>
                <?php foreach ($replies as $reply): ?>
                    <?php 
                    $foto_reply = !empty($reply['foto_profil']) ? '/aquara/uploads/profil/' . htmlspecialchars($reply['foto_profil']) : '../../assets/img/profil/default_profile.png';
                    ?>
                    <div class="reply-card">
                        <img src="<?php echo $foto_reply; ?>" alt="Avatar" class="post-avatar" style="width: 40px; height: 40px;" onerror="this.src='../../assets/img/profil/default_profile.png'">
                        <div class="reply-content-box">
                            <div class="reply-author">
                                <h5><?php echo htmlspecialchars($reply['nama_user']); ?></h5>
                                <span class="author-role" style="font-size: 10px; padding: 2px 8px;"><?php echo htmlspecialchars($reply['role_name'] ?? 'Anggota'); ?></span>
                                <span class="reply-date"><?php echo time_ago_detail($reply['created_at']); ?></span>
                                
                                <?php if ($reply['user_id'] == $user_id): ?>
                                    <a href="index_pakar.php?page=forum_detail_pakar&id=<?= $topic_id ?>&action=delete_reply&reply_id=<?= $reply['id'] ?>" 
                                       onclick="return confirm('Hapus komentar ini?')"
                                       style="margin-left: 10px; color: #e74c3c; cursor: pointer;" title="Hapus Komentar">
                                       <i class="bi bi-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                            
                            <div class="reply-body">
                                <a href="#reply-form" onclick="balasKomentar('<?= htmlspecialchars($reply['nama_user']) ?>')" 
                                   style="font-size: 12px; color: #3f8686; text-decoration: none; float: right; font-weight: 600; cursor: pointer;">
                                    <i class="bi bi-reply-fill"></i> Balas
                                </a>
                                <?php echo nl2br(htmlspecialchars($reply['konten'])); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 30px; color: #999; background: #f9f9f9; border-radius: 12px;">
                    <i class="bi bi-chat-square-dots" style="font-size: 32px; display: block; margin-bottom: 10px; opacity: 0.5;"></i>
                    Belum ada komentar. Jadilah yang pertama menanggapi!
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="form-komentar" id="reply-form">
        <h3><i class="bi bi-pencil-square"></i> Tulis Komentar</h3>
        <form method="POST" action=""> 
            <textarea name="komentar" placeholder="Bagikan tanggapan atau jawaban Anda di sini..." required></textarea>
            <button type="submit" class="btn-submit-komentar">
                <i class="bi bi-send-fill"></i> Kirim Komentar
            </button>
        </form>
    </section>

</main>

<div id="modalEditTopik" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center;">
    <div style="background:white; width:90%; max-width:600px; margin: 50px auto; padding:30px; border-radius:15px; position:relative;">
        <h3 style="margin-top:0; color:#3f8686;">Edit Topik Diskusi</h3>
        <form method="POST" enctype="multipart/form-data">
            <div style="margin-bottom:15px;">
                <label style="font-weight:600; display:block; margin-bottom:5px;">Judul Topik</label>
                <input type="text" name="judul" value="<?= htmlspecialchars($topic['judul']) ?>" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px;" required>
            </div>
            <div style="margin-bottom:20px;">
                <label style="font-weight:600; display:block; margin-bottom:5px;">Deskripsi</label>
                <textarea name="deskripsi" rows="6" style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px;" required><?= htmlspecialchars($topic['deskripsi']) ?></textarea>
            </div>
            
            <div style="margin-bottom:20px;">
                <label style="font-weight:600; display:block; margin-bottom:5px;">Ganti Gambar (Opsional)</label>
                <input type="file" name="gambar" accept="image/*" style="width:100%; padding:10px; border:1px dashed #ddd; border-radius:8px;">
                <?php if (!empty($topic['gambar'])): ?>
                    <p style="font-size:12px; color:#777; margin-top:5px;">Gambar saat ini: <?= htmlspecialchars($topic['gambar']) ?></p>
                <?php endif; ?>
            </div>

            <div style="text-align:right;">
                <button type="button" onclick="document.getElementById('modalEditTopik').style.display='none'" style="background:#eee; color:#555; padding:10px 20px; border:none; border-radius:8px; cursor:pointer; margin-right:10px;">Batal</button>
                <button type="submit" name="edit_topik" style="background:#3f8686; color:white; padding:10px 25px; border:none; border-radius:8px; cursor:pointer; font-weight:bold;">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/pakar/footer_pakar.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handler Like
    const likeBtn = document.querySelector('.btn-like');
    if (likeBtn) {
        likeBtn.addEventListener('click', function() {
            const postId = this.dataset.id;
            const icon = this.querySelector('i');
            const countSpan = document.getElementById('likes-count');
            const currentBtn = this;

            if (currentBtn.disabled) return; 
            currentBtn.disabled = true;

            fetch('../../actions/proses_like.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'post_id=' + postId
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    countSpan.textContent = data.new_likes;
                    if (data.user_liked) {
                        currentBtn.classList.add('liked');
                        icon.classList.replace('bi-hand-thumbs-up', 'bi-hand-thumbs-up-fill');
                    } else {
                        currentBtn.classList.remove('liked');
                        icon.classList.replace('bi-hand-thumbs-up-fill', 'bi-hand-thumbs-up');
                    }
                } else {
                    if (data.message === 'Login diperlukan') {
                        window.location.href = '../../login.php';
                    } else {
                        alert(data.message);
                    }
                }
            })
            .catch(err => console.error('Error:', err))
            .finally(() => currentBtn.disabled = false);
        });
    }

    // Handler Share
    const shareBtn = document.querySelector('.btn-share');
    if (shareBtn) {
        shareBtn.addEventListener('click', function() {
            const url = window.location.origin + window.location.pathname.replace('index_pakar.php', '') + 'index_pakar.php?page=forum_detail_pakar&id=' + this.dataset.id;
            if (navigator.share) {
                navigator.share({ title: 'AQUARA Forum', text: this.dataset.judul, url: url });
            } else {
                navigator.clipboard.writeText(url).then(() => alert('Link topik disalin!'));
            }
        });
    }
    
    // Auto-hide notifikasi
    const message = document.querySelector('.message');
    if (message) {
        setTimeout(() => {
            message.style.opacity = '0';
            message.style.transition = 'opacity 0.5s ease';
            setTimeout(() => message.remove(), 500);
        }, 3000);
    }
});

// FITUR BARU: Balas Komentar (Mention)
function balasKomentar(namaUser) {
    const textarea = document.querySelector('textarea[name="komentar"]');
    textarea.value = '@' + namaUser + ' '; // Tambahkan mention
    textarea.focus(); // Arahkan kursor ke textarea
    
    // Scroll halus ke form komentar
    document.getElementById('reply-form').scrollIntoView({ 
        behavior: 'smooth' 
    });
}
</script>
</body>
</html>