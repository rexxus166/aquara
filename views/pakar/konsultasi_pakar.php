<?php
$activeMenu = 'konsultasi';

// PASTIKAN SESSION DAN KONEKSI DATABASE
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once '../../includes/config.php';

// CEK LOGIN PAKAR (Role ID 3)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: ../../index.php?page=login");
    exit;
}

$pakar_id = $_SESSION['user_id'];
$pakar_nama = $_SESSION['nama']; // Ambil nama pakar yang sedang login

// QUERY DATABASE YANG DIPERBAIKI
// Menampilkan konsultasi jika:
// 1. Sudah dijawab oleh pakar ini (k.pakar_id = ?)
// 2. ATAU Belum dijawab DAN ditujukan khusus ke nama pakar ini (k.ahli = ?)
// 3. ATAU Belum dijawab DAN tidak ditujukan ke siapa-siapa (umum)

$sql = "SELECT k.*, u.nama AS nama_anggota, u.email AS email_anggota, u.telepon AS telepon_user, u.foto_profil AS foto_anggota
        FROM konsultasi k
        LEFT JOIN users u ON k.anggota_id = u.id
        WHERE (
            (k.pakar_id = ?) 
            OR (k.status = 'pending' AND k.ahli = ?)
            OR (k.status = 'pending' AND (k.ahli IS NULL OR k.ahli = ''))
        )
        AND k.status != 'dibatalkan'
        ORDER BY k.created_at DESC";

$stmt = $conn->prepare($sql);
// Binding parameter: i = integer (id), s = string (nama)
$stmt->bind_param("is", $pakar_id, $pakar_nama);
$stmt->execute();
$result = $stmt->get_result();
$data_konsultasi = [];
while ($row = $result->fetch_assoc()) {
    $data_konsultasi[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konsultasi Masuk - AQUARA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/konsultasi_pakar.css?v=<?php echo time(); ?>">
</head>
<body>

<?php include '../../includes/pakar/header_pakar.php'; ?>

<section id="section-hero" class="hero-section">
    <div class="hero-bg">
        <div class="hero-bg-top"></div>
        <div class="hero-bg-bottom"></div>
    </div>
    <div class="container">
        <h1 class="hero-title">Konsultasi Masuk</h1>
    </div>
</section>

<section id="section-konsultasi" class="konsultasi-section">
    <div class="container">
        
        <div class="konsultasi-header">
            <h2 class="konsultasi-title">Daftar Pertanyaan Konsultasi</h2>
            <div class="konsultasi-filter">
                <button class="filter-btn active" data-filter="semua">
                    <i class="bi bi-grid-3x3-gap"></i> Semua
                </button>
                <button class="filter-btn" data-filter="belum-dijawab">
                    <i class="bi bi-clock-history"></i> Belum Dijawab
                </button>
                <button class="filter-btn" data-filter="sudah-dijawab">
                    <i class="bi bi-check-circle"></i> Sudah Dijawab
                </button>
            </div>
        </div>

        <div class="konsultasi-list">
            <?php if (count($data_konsultasi) > 0): ?>
                <?php foreach ($data_konsultasi as $row): 
                    // Cek status apakah sudah dijawab
                    // Kita cek berbagai kemungkinan status agar aman
                    $is_answered = ($row['status'] == 'answered' || $row['status'] == 'dijawab' || !empty($row['jawaban']));
                    
                    // Tentukan variabel tampilan berdasarkan status
                    $status_filter = $is_answered ? 'sudah-dijawab' : 'belum-dijawab';
                    $badge_class = $is_answered ? 'status-sudah' : 'status-belum';
                    $badge_text = $is_answered ? 'Sudah Dijawab' : 'Belum Dijawab';
                    $tanggal_masuk = date('d/m/Y H:i', strtotime($row['created_at']));
                    
                    // Data aman untuk HTML
                    $nama_anggota = htmlspecialchars($row['nama_anggota'] ?? 'Anonim');
                    $topik = htmlspecialchars($row['topik'] ?? 'Tanpa Topik');
                    // Cek kolom mana yang dipakai untuk pertanyaan di databasemu (pesan atau pertanyaan)
                    $pertanyaan = htmlspecialchars($row['pesan'] ?? $row['pertanyaan'] ?? '-');
                ?>
                
                <div class="konsultasi-card" data-status="<?php echo $status_filter; ?>">
                    <div class="card-header d-flex justify-content-between align-items-start py-3 px-4">
                        <div class="d-flex align-items-center">
                        <?php
                        // Path foto profil
                        $foto_anggota = $row['foto_anggota'] ?? null;
                        $path_foto_anggota = (!empty($foto_anggota) && file_exists($_SERVER['DOCUMENT_ROOT'] . "/aquara/uploads/profil/" . $foto_anggota)) ? "/aquara/uploads/profil/" . htmlspecialchars($foto_anggota) : "/aquara/assets/img/profil/default_profile.png";
                        ?>
                        <img src="<?php echo $path_foto_anggota; ?>" alt="Profil" 
                        class="rounded-circle border me-3" 
                        style="width: 50px; height: 50px; object-fit: cover; flex-shrink: 0;">
        
                    <div class="d-flex flex-column justify-content-center">
                <span class="fw-bold text-dark" style="font-size: 1.05rem; line-height: 1.2;">
            <?php echo htmlspecialchars($row['nama_anggota'] ?? 'Anonim'); ?>
            </span>
            <?php if (!empty($row['email_anggota'])): ?>
                <span class="text-muted small mt-1" style="display: flex; align-items: center; gap: 5px;">
                    <i class="bi bi-envelope"></i> <?php echo htmlspecialchars($row['email_anggota']); ?>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <span class="status-badge <?php echo $badge_class; ?> ms-3">
        <?php echo $badge_text; ?>
    </span>
</div>
                    <div class="card-body">
                        <?php if (!empty($row['ahli'])): ?>
                            <small class="text-muted mb-2 d-block">Ditujukan untuk: <strong><?php echo htmlspecialchars($row['ahli']); ?></strong></small>
                        <?php endif; ?>

                        <div class="mb-3">
                        <small class="text-muted"></small>
                        <h5 class="fw-bold text-primary mt-1"><?php echo str_replace(['"', 'Topik:'], '', $topik); ?></h5>
                        </div>
                        <p class="isi-pertanyaan">"<?php echo nl2br($pertanyaan); ?>"</p>
                        
                        <?php if ($is_answered): ?>
                            <div class="mt-3 p-3 bg-light border-start border-success border-4 rounded" style="font-size: 0.95rem;">
                                <strong class="text-success"><i class="bi bi-check2-all"></i> Jawaban Pakar:</strong><br>
                                <?php echo nl2br(htmlspecialchars($row['jawaban'])); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <span class="tanggal-masuk">Masuk: <?php echo $tanggal_masuk; ?> WIB</span>
                        
                        <?php if (!$is_answered): ?>
                            <button class="btn btn-jawab" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalJawab"
                                    data-id="<?php echo $row['id']; ?>"
                                    data-nama="<?php echo $nama_anggota; ?>"
                                    data-topik="<?php echo $topik; ?>"
                                    data-pertanyaan="<?php echo $pertanyaan; ?>">
                                <i class="bi bi-pencil-square"></i> Jawab Sekarang
                            </button>
                        <?php else: ?>
                             <button class="btn btn-secondary btn-sm" disabled style="opacity: 0.7; cursor: not-allowed;">
                                Selesai
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center p-5 text-muted" style="grid-column: 1/-1; background: #f8f9fa; border-radius: 15px;">
                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="mt-3">Belum ada data konsultasi yang masuk.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>

<div class="modal fade" id="modalJawab" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Jawab Konsultasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formJawabKonsultasi">
                <div class="modal-body">
                    <input type="hidden" id="modal_id_konsultasi" name="id_konsultasi">
                    
                    <div class="bg-light p-3 rounded mb-3 border">
                        <div class="mb-2 text-muted">Penanya: <strong id="modal_nama_penanya" class="text-dark">-</strong></div>
                        <div class="mb-2 text-muted">Topik: <strong id="modal_topik" class="text-primary">-</strong></div>
                        <hr class="my-2">
                        <div style="max-height: 150px; overflow-y: auto;">
                                <em>"<span id="modal_pertanyaan">Loading...</span>"</em>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="jawaban_pakar" class="form-label fw-bold text-primary">Jawaban & Solusi Anda:</label>
                        <textarea class="form-control" id="jawaban_pakar" name="jawaban" rows="8" required placeholder="Tuliskan solusi lengkap di sini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-send-fill me-2"></i> Kirim Jawaban
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/pakar/footer_pakar.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../assets/js/pakar/konsultasi_pakar2.js?v=<?php echo time(); ?>"></script>

</body>
</html>