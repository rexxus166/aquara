<?php
$activeMenu = 'forum';

$user_id_login = $_SESSION['user_id'];
$search = $_GET['search'] ?? '';

// --- HANDLER HAPUS TOPIK (FITUR TAMBAHAN) ---
if (isset($_GET['action']) && $_GET['action'] == 'delete_topic' && isset($_GET['id'])) {
    $id_hapus = $_GET['id'];
    // Pastikan yang menghapus adalah pemilik topik
    $stmt = $conn->prepare("DELETE FROM forum_topics WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id_hapus, $user_id_login);
    if ($stmt->execute()) {
        echo "<script>alert('Topik berhasil dihapus!'); window.location.href='index_pakar.php?page=forum_pakar';</script>";
    } else {
        echo "<script>alert('Gagal menghapus topik. Anda bukan pemiliknya.');</script>";
    }
    $stmt->close();
}

// --- QUERY UTAMA ---
$sql = "SELECT 
            ft.id, ft.judul, ft.deskripsi, ft.created_at, ft.gambar, ft.user_id,
            u.nama AS nama_user, u.foto_profil, r.role_name,
            (SELECT COUNT(*) FROM forum_replies WHERE topic_id = ft.id) AS jumlah_komentar,
            (SELECT COUNT(*) FROM post_likes WHERE post_id = ft.id) AS jumlah_suka,
            (SELECT COUNT(*) FROM post_likes WHERE post_id = ft.id AND user_id = ?) AS is_liked
        FROM forum_topics ft
        JOIN users u ON ft.user_id = u.id
        LEFT JOIN roles r ON u.role_id = r.id";

if (!empty($search)) {
    $sql .= " WHERE ft.judul LIKE ? OR ft.deskripsi LIKE ?";
}

$sql .= " ORDER BY ft.created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($search)) {
    $search_param = "%{$search}%";
    $stmt->bind_param("iss", $user_id_login, $search_param, $search_param);
} else {
    $stmt->bind_param("i", $user_id_login);
}

$stmt->execute();
$result = $stmt->get_result();
$topics = [];
while ($row = $result->fetch_assoc()) {
    $topics[] = $row;
}
$stmt->close();

// --- HELPER WAKTU ---
if (!function_exists('time_ago_singkat')) {
    function time_ago_singkat($datetime) {
        $time = strtotime($datetime);
        $diff = time() - $time;
        if ($diff < 60) return 'Baru saja';
        $units = [31536000 => 'thn', 2592000 => 'bln', 604800 => 'mgg', 86400 => 'hr', 3600 => 'jam', 60 => 'mnt'];
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
    <title>Forum Pakar - AQUARA</title>
    <link rel="stylesheet" href="../../assets/css/header_footer_pakar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* CSS SAMA PERSIS DENGAN ANGGOTA */
        .forum-container { max-width: 850px; margin: 40px auto; padding: 0 20px; }
        .forum-header-section { text-align: center; margin-bottom: 40px; padding: 40px 20px; background: linear-gradient(135deg, #3f8686, #2c6666); color: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(63, 134, 134, 0.2); }
        .forum-header-section h1 { font-size: 36px; margin-bottom: 10px; }
        .forum-header-section p { font-size: 16px; opacity: 0.9; max-width: 600px; margin: 0 auto 25px; }
        .btn-buat-topik { background: white; color: #3f8686; padding: 12px 30px; border-radius: 50px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .btn-buat-topik:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
        .search-bar { position: relative; max-width: 600px; margin: -30px auto 40px; z-index: 10; }
        .search-bar input { width: 100%; padding: 18px 25px; padding-right: 60px; border: none; border-radius: 50px; font-size: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); }
        .search-bar button { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 45px; height: 45px; background: #3f8686; color: white; border: none; border-radius: 50%; cursor: pointer; transition: all 0.3s; }
        .search-bar button:hover { background: #2c6666; }
        .post-card { background: white; border-radius: 16px; padding: 25px; margin-bottom: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.03); border: 1px solid #f0f0f0; transition: all 0.3s; }
        .post-card:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.06); transform: translateY(-3px); }
        .post-header { display: flex; align-items: center; gap: 12px; margin-bottom: 15px; }
        .post-avatar { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; }
        .author-info h4 { font-size: 15px; margin: 0; color: #2c3e50; }
        .author-role { font-size: 11px; background: #eef2f7; padding: 2px 8px; border-radius: 10px; color: #666; }
        .post-time { font-size: 12px; color: #999; margin-left: auto; }
        .post-content h3 { font-size: 18px; margin: 0 0 10px; color: #2c3e50; }
        .post-content h3 a { text-decoration: none; color: inherit; transition: color 0.2s; }
        .post-content h3 a:hover { color: #3f8686; }
        .post-preview { color: #555; line-height: 1.6; font-size: 15px; margin-bottom: 15px; }
        .post-actions { display: flex; gap: 15px; padding-top: 15px; border-top: 1px solid #f5f5f5; }
        .action-btn { background: none; border: none; color: #777; cursor: pointer; font-size: 14px; display: flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 50px; transition: all 0.2s; text-decoration: none; }
        .action-btn:hover { background: #f5f5f5; color: #3f8686; }
        .btn-like.liked { color: #3f8686; font-weight: 600; background: rgba(63, 134, 134, 0.1); font-weight: 600; }
        .btn-like.liked i { animation: pop 0.3s ease; }
        @keyframes pop { 50% { transform: scale(1.3); } }
    </style>
</head>
<body>

<?php include '../../includes/pakar/header_pakar.php'; ?>

<main class="forum-container">
    <div class="forum-header-section">
        <h1>Forum Diskusi</h1>
        <p>Tempat bertanya, berbagi pengalaman, dan berdiskusi seputar dunia budidaya perikanan.</p>
        <a href="index_pakar.php?page=tambah_topik_pakar" class="btn-buat-topik">
            <i class="bi bi-plus-lg"></i> Buat Topik Baru
        </a>
    </div>

    <form class="search-bar" method="GET" action="index_pakar.php">
        <input type="hidden" name="page" value="forum_pakar">
        <input type="text" name="search" placeholder="Cari topik diskusi..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit"><i class="bi bi-search"></i></button>
    </form>

    <div class="forum-feed">
        <?php if (count($topics) > 0): ?>
            <?php foreach ($topics as $topic): ?>
                <article class="post-card">
                    <div class="post-header">
                        <?php $foto = !empty($topic['foto_profil']) ? '/aquara/uploads/profil/' . htmlspecialchars($topic['foto_profil']) : '../../assets/img/profil/default_profile.png'; ?>
                        <img src="<?php echo $foto; ?>" alt="Avatar" class="post-avatar" onerror="this.src='../../assets/img/profil/default_profile.png'">
                        <div class="author-info">
                            <h4>
                                <?php echo htmlspecialchars($topic['nama_user']); ?>
                                <span class="author-role"><?php echo htmlspecialchars($topic['role_name'] ?? 'Pakar'); ?></span>
                            </h4>
                        </div>
                        
                        <?php if ($topic['user_id'] == $user_id_login): ?>
                            <div class="post-options" style="margin-left: auto; display: flex; align-items: center; gap: 10px;">
                                <span class="post-time" style="margin: 0;"><?php echo time_ago_singkat($topic['created_at']); ?></span>
                                <a href="index_pakar.php?page=forum_pakar&action=delete_topic&id=<?= $topic['id']; ?>" 
                                   onclick="return confirm('Yakin ingin menghapus topik ini?')"
                                   style="color: #e74c3c; text-decoration: none; font-size: 14px; background: #fff0f0; padding: 5px 10px; border-radius: 5px;">
                                   <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        <?php else: ?>
                            <span class="post-time"><?php echo time_ago_singkat($topic['created_at']); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="post-content">
                        <h3>
                            <a href="index_pakar.php?page=forum_detail_pakar&id=<?php echo $topic['id']; ?>">
                                <?php echo htmlspecialchars($topic['judul']); ?>
                            </a>
                        </h3>
                        <p class="post-preview">
                            <?php echo htmlspecialchars(substr($topic['deskripsi'], 0, 150)) . (strlen($topic['deskripsi']) > 150 ? '...' : ''); ?>
                        </p>
                    </div>

                    <div class="post-actions">
                        <a href="index_pakar.php?page=forum_detail_pakar&id=<?php echo $topic['id']; ?>" class="action-btn">
                            <i class="bi bi-chat-dots"></i> <?php echo $topic['jumlah_komentar']; ?>
                        </a>
                        
                        <button class="action-btn btn-like <?php echo ($topic['is_liked'] > 0) ? 'liked' : ''; ?>" 
                                data-id="<?php echo $topic['id']; ?>">
                            <i class="bi bi-hand-thumbs-up<?php echo ($topic['is_liked'] > 0) ? '-fill' : ''; ?>"></i>
                            <span class="likes-count"><?php echo $topic['jumlah_suka']; ?></span>
                        </button>

                        <button class="action-btn btn-share" 
                                data-id="<?php echo $topic['id']; ?>" 
                                data-judul="<?php echo htmlspecialchars($topic['judul']); ?>">
                            <i class="bi bi-share"></i>
                        </button>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 50px; color: #999;">
                <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.5; margin-bottom: 15px; display: block;"></i>
                <p>Belum ada topik diskusi yang ditemukan.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include '../../includes/pakar/footer_pakar.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handler Like
    document.querySelectorAll('.btn-like').forEach(btn => {
        btn.addEventListener('click', function() {
            const postId = this.dataset.id;
            const icon = this.querySelector('i');
            const countSpan = this.querySelector('.likes-count');
            const self = this;
            if (self.disabled) return;
            self.disabled = true;

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
                        self.classList.add('liked');
                        icon.classList.replace('bi-hand-thumbs-up', 'bi-hand-thumbs-up-fill');
                    } else {
                        self.classList.remove('liked');
                        icon.classList.replace('bi-hand-thumbs-up-fill', 'bi-hand-thumbs-up');
                    }
                }
            })
            .catch(e => console.error(e))
            .finally(() => self.disabled = false);
        });
    });

    // Handler Share
    document.querySelectorAll('.btn-share').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = window.location.origin + window.location.pathname.replace('index_pakar.php', '') + 'index_pakar.php?page=forum_detail_pakar&id=' + this.dataset.id;
            if (navigator.share) {
                navigator.share({ title: 'AQUARA Forum', text: this.dataset.judul, url: url });
            } else {
                navigator.clipboard.writeText(url).then(() => alert('Link topik disalin!'));
            }
        });
    });
});
</script>
</body>
</html>