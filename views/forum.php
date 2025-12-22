<?php
$activeMenu = 'forum';

$this->layout('layout', ['title' => 'Forum Diskusi - AQUARA', 'activeMenu' => $activeMenu]) ?>

<?php $this->start('styles') ?>
<link rel="stylesheet" href="assets/css/header_footer_pakar.css?v=<?= time() ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    /* --- CSS FORUM FEED (DUPLIKAT DARI PAKAR AGAR SAMA PERSIS) --- */
    .forum-container { max-width: 850px; margin: 40px auto; padding: 0 20px; }
    
    .forum-header-section {
        text-align: center; margin-bottom: 40px; padding: 40px 20px;
        background: linear-gradient(135deg, #3f8686, #2c6666); color: white;
        border-radius: 20px; box-shadow: 0 10px 30px rgba(63, 134, 134, 0.2);
    }
    .forum-header-section h1 { font-size: 36px; margin-bottom: 10px; }
    .forum-header-section p { font-size: 16px; opacity: 0.9; max-width: 600px; margin: 0 auto 25px; }
    .btn-buat-topik {
        background: white; color: #3f8686; padding: 12px 30px; border-radius: 50px;
        font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
        transition: all 0.3s; box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        cursor: pointer; /* Tambahan agar terlihat bisa diklik */
    }
    .btn-buat-topik:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }

    .search-bar {
        position: relative; max-width: 600px; margin: -30px auto 40px; z-index: 10;
    }
    .search-bar input {
        width: 100%; padding: 18px 25px; padding-right: 60px; border: none; border-radius: 50px;
        font-size: 16px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }
    .search-bar button {
        position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
        width: 45px; height: 45px; background: #3f8686; color: white; border: none;
        border-radius: 50%; cursor: pointer; transition: all 0.3s;
    }
    .search-bar button:hover { background: #2c6666; }

    /* --- POST CARD STYLES --- */
    .post-card {
        background: white; border-radius: 16px; padding: 25px; margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.03); border: 1px solid #f0f0f0;
        transition: all 0.3s;
    }
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
    
    .post-actions {
        display: flex; gap: 15px; padding-top: 15px; border-top: 1px solid #f5f5f5;
    }
    .action-btn {
        background: none; border: none; color: #777; cursor: pointer; font-size: 14px;
        display: flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 50px;
        transition: all 0.2s; text-decoration: none;
    }
    .action-btn:hover { background: #f5f5f5; color: #3f8686; }
</style>
<?php $this->stop() ?>

<?php $this->start('body') ?>

<main class="forum-container">
    <div class="forum-header-section">
        <h1>Forum Diskusi</h1>
        <p>Tempat bertanya, berbagi pengalaman, dan berdiskusi seputar dunia budidaya perikanan.</p>
        <a href="?page=login" class="btn-buat-topik" onclick="return confirm('Silakan login untuk membuat topik baru.');">
            <i class="bi bi-plus-lg"></i> Buat Topik Baru
        </a>
    </div>

    <form class="search-bar" method="GET" action="index.php">
        <input type="hidden" name="page" value="forum">
        <input type="text" name="search" placeholder="Cari topik diskusi..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit"><i class="bi bi-search"></i></button>
    </form>

    <div class="forum-feed">
        <?php
        // --- KONEKSI & QUERY DATABASE ---
        global $conn;
        if (!isset($conn)) {
             $config_file = __DIR__ . '/../includes/config.php';
             if (file_exists($config_file)) include_once $config_file;
        }

        if (isset($conn) && $conn) {
            $search = $_GET['search'] ?? '';
            $sql = "SELECT ft.id, ft.judul, ft.deskripsi, ft.created_at,
                           u.nama AS nama_user, u.foto_profil, r.role_name,
                           (SELECT COUNT(*) FROM forum_replies WHERE topic_id = ft.id) AS jumlah_komentar,
                           (SELECT COUNT(*) FROM post_likes WHERE post_id = ft.id) AS jumlah_suka
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
                $stmt->bind_param("ss", $search_param, $search_param);
            }
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows > 0) {
                while ($topic = $result->fetch_assoc()) {
                    // Helper Waktu (Sederhana)
                    $time = strtotime($topic['created_at']);
                    $diff = time() - $time;
                    $ago = ($diff < 60) ? 'Baru saja' : (floor($diff / 86400) > 0 ? floor($diff / 86400) . ' hari lalu' : 'Hari ini');

                    $foto = !empty($topic['foto_profil']) ? 'uploads/profil/' . $topic['foto_profil'] : 'assets/img/profil/default_profile.png';
        ?>
                    <article class="post-card">
                        <div class="post-header">
                            <img src="<?= htmlspecialchars($foto) ?>" alt="Avatar" class="post-avatar" onerror="this.src='assets/img/profil/default_profile.png'">
                            <div class="author-info">
                                <h4>
                                    <?= htmlspecialchars($topic['nama_user']) ?>
                                    <span class="author-role"><?= htmlspecialchars($topic['role_name'] ?? 'Anggota') ?></span>
                                </h4>
                            </div>
                            <span class="post-time"><?= $ago ?></span>
                        </div>

                        <div class="post-content">
                            <h3>
                                <a href="?page=login" onclick="return confirm('Silakan login untuk melihat detail diskusi.');">
                                    <?= htmlspecialchars($topic['judul']) ?>
                                </a>
                            </h3>
                            <p class="post-preview">
                                <?= htmlspecialchars(substr($topic['deskripsi'], 0, 150)) . (strlen($topic['deskripsi']) > 150 ? '...' : '') ?>
                            </p>
                        </div>

                        <div class="post-actions">
                            <a href="?page=login" class="action-btn" onclick="return confirm('Silakan login untuk melihat komentar.');">
                                <i class="bi bi-chat-dots"></i> <?= $topic['jumlah_komentar'] ?>
                            </a>
                            
                            <button class="action-btn" onclick="if(confirm('Silakan login untuk menyukai topik ini.')) window.location.href='?page=login';">
                                <i class="bi bi-hand-thumbs-up"></i> <?= $topic['jumlah_suka'] ?>
                            </button>

                            <button class="action-btn" onclick="if(confirm('Silakan login untuk membagikan topik ini.')) window.location.href='?page=login';">
                                <i class="bi bi-share"></i>
                            </button>
                        </div>
                    </article>
        <?php
                }
            } else {
                echo '<div style="text-align: center; padding: 50px; color: #999;"><i class="bi bi-inbox" style="font-size: 48px; opacity: 0.5;"></i><p>Belum ada topik diskusi.</p></div>';
            }
        } else {
            echo '<p style="text-align: center; color: red;">Gagal terhubung ke database forum.</p>';
        }
        ?>
    </div>
</main>

<?php $this->end() ?>