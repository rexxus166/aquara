<?php
$activeMenu = 'event';

// --- HANDLER HAPUS EVENT ---
if (isset($_GET['action']) && $_GET['action'] == 'delete_event' && isset($_GET['id'])) {
    $id_hapus = $_GET['id'];
    
    // Ambil nama file gambar dulu untuk dihapus dari folder
    $stmt_cek = $conn->prepare("SELECT gambar FROM events WHERE id = ?");
    $stmt_cek->bind_param("i", $id_hapus);
    $stmt_cek->execute();
    $res_cek = $stmt_cek->get_result();
    $row_cek = $res_cek->fetch_assoc();

    // Hapus dari database
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $id_hapus);
    
    if ($stmt->execute()) {
        // Hapus file fisik jika ada
        if (!empty($row_cek['gambar']) && file_exists("../../uploads/events/" . $row_cek['gambar'])) {
            unlink("../../uploads/events/" . $row_cek['gambar']);
        }
        echo "<script>alert('Event berhasil dihapus!'); window.location.href='index_pakar.php?page=event_pakar';</script>";
    } else {
        echo "<script>alert('Gagal menghapus event.');</script>";
    }
}

// --- HANDLER TAMBAH EVENT ---
if (isset($_POST['tambah_event'])) {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $tipe = $_POST['tipe'];
    $lokasi = $_POST['lokasi'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $link = $_POST['link_pendaftaran'];
    
    $gambar = '';
    if (!empty($_FILES['gambar']['name'])) {
        $file_name = time() . '_' . $_FILES['gambar']['name'];
        $tmp_name = $_FILES['gambar']['tmp_name'];
        $target_dir = "../../uploads/events/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        
        if (move_uploaded_file($tmp_name, $target_dir . $file_name)) {
            $gambar = $file_name;
        }
    }

    $stmt = $conn->prepare("INSERT INTO events (judul, deskripsi, gambar, tipe, lokasi, tanggal_mulai, link_pendaftaran) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $judul, $deskripsi, $gambar, $tipe, $lokasi, $tanggal_mulai, $link);
    
    if ($stmt->execute()) {
        echo "<script>alert('Event berhasil ditambahkan!'); window.location.href='index_pakar.php?page=event_pakar';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan event.');</script>";
    }
}

// AMBIL SEMUA EVENT
$events = [];
$query = "SELECT * FROM events ORDER BY id DESC";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

// Helper Tanggal
if (!function_exists('formatTanggalEventIndo')) {
    function formatTanggalEventIndo($datetime) {
        $date = new DateTime($datetime);
        $bulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        return $date->format('d') . ' ' . $bulan[intval($date->format('m'))] . ' ' . $date->format('Y');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Pakar - AQUARA</title>
    <link rel="stylesheet" href="../../assets/css/event_pakar.css?v=<?= time() ?>">
    <link rel="stylesheet" href="../../assets/css/event_pakar2.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* Tombol Tambah Event Modern */
.btn-tambah-event {
    background: linear-gradient(135deg, #3f8686, #2c6666); /* Gradasi biar elegan */
    color: white;
    padding: 12px 24px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: bold;
    font-size: 15px;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(63, 134, 134, 0.4); /* Bayangan halus */
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); /* Efek membal */
    position: relative;
    overflow: hidden;
}

/* Efek Hover (Melayang) */
.btn-tambah-event:hover {
    transform: translateY(-3px) scale(1.02); /* Naik sedikit & membesar */
    box-shadow: 0 8px 20px rgba(63, 134, 134, 0.6);
    background: linear-gradient(135deg, #4aaaaa, #368080);
}

/* Efek Klik (Tekan) */
.btn-tambah-event:active {
    transform: translateY(1px) scale(0.95); /* Turun & mengecil (efek pencet) */
    box-shadow: 0 2px 5px rgba(63, 134, 134, 0.4);
}

/* Ikon Tambah */
.btn-tambah-event i {
    font-size: 18px;
    transition: transform 0.3s;
}

/* Ikon Muter saat Hover */
.btn-tambah-event:hover i {
    transform: rotate(90deg);
}


/* Tombol Submit Modern (Dalam Modal) */
.btn-submit {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #3f8686, #2c6666);
    color: white;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    font-size: 16px;
    cursor: pointer;
    margin-top: 20px;
    box-shadow: 0 4px 10px rgba(63, 134, 134, 0.3);
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); /* Efek membal */
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Efek Hover */
.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(63, 134, 134, 0.5);
    background: linear-gradient(135deg, #4aaaaa, #368080);
}

/* Efek Klik */
.btn-submit:active {
    transform: translateY(1px) scale(0.98);
    box-shadow: 0 2px 5px rgba(63, 134, 134, 0.3);
}
        /* CSS PERBAIKAN GAMBAR & CARD */
        .event-card {
            position: relative; /* Untuk posisi tombol hapus */
            padding-top: 0 !important; 
            overflow: hidden;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        .event-card:hover { transform: translateY(-5px); }
        
        .event-banner { 
            width: 100%; 
            height: 200px; /* Tinggi fix agar rapi */
            object-fit: cover; /* KUNCI: Agar gambar full memenuhi kotak tanpa gepeng */
            object-position: center; /* Fokus tengah */
            border-radius: 15px 15px 0 0; 
            display: block; 
        }
        
        .card-content-wrapper { padding: 20px; position: relative; }
        .card-tag { top: 15px; right: 15px; z-index: 2; }
        
        /* Tombol Hapus (Tong Sampah) */
        .btn-delete-event {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(231, 76, 60, 0.9);
            color: white;
            width: 35px; height: 35px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            text-decoration: none;
            z-index: 3;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            transition: 0.2s;
        }
        .btn-delete-event:hover { background: #c0392b; transform: scale(1.1); }

        /* Modal & Lainnya */
        .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center; }
        .modal-content { background-color: #fefefe; margin: auto; padding: 30px; border: 1px solid #888; width: 90%; max-width: 600px; border-radius: 15px; position: relative; animation: slideDown 0.3s; }
        .close { position: absolute; right: 20px; top: 15px; font-size: 28px; cursor: pointer; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; }
        .btn-tambah-event { background-color: #3f8686; color: white; padding: 10px 20px; border-radius: 50px; text-decoration: none; font-weight: bold; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; border: none; }
        .btn-submit { width: 100%; padding: 12px; background: #3f8686; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 10px; }
    </style>
</head>
<body>

<?php include '../../includes/pakar/header_pakar.php'; ?>

<section id="section-hero" class="hero-section">
    <div class="hero-bg">
        <div class="hero-bg-top"></div>
        <div class="hero-bg-bottom"></div>
    </div>
    <div class="container">
        <h1 class="hero-title">EVENT</h1>
    </div>
</section>

<section id="section-events" class="events-section">
    <div class="container">
        <div class="section-title" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
            <div>
                <h2>Event & Pelatihan Mendatang</h2>
                <p>Kelola dan ikuti event berkualitas untuk peningkatan skill.</p>
            </div>
            <button onclick="document.getElementById('modalTambahEvent').style.display='flex'" class="btn-tambah-event">
                <i class="bi bi-plus-lg"></i> Tambah Event
            </button>
        </div>
        
        <div class="events-grid">
            <?php if (count($events) > 0): ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-card" id="event-<?php echo $event['id']; ?>">
                        
                        <a href="index_pakar.php?page=event_pakar&action=delete_event&id=<?php echo $event['id']; ?>" 
                           class="btn-delete-event" 
                           onclick="return confirm('Yakin ingin menghapus event ini?')"
                           title="Hapus Event">
                            <i class="bi bi-trash"></i>
                        </a>

                        <?php 
                        $gambar_path = "../../uploads/events/" . $event['gambar'];
                        if (!empty($event['gambar']) && file_exists($gambar_path)) {
                            echo '<img src="' . htmlspecialchars($gambar_path) . '" alt="Event Banner" class="event-banner">';
                        } else {
                            echo '<div class="event-banner" style="background: linear-gradient(135deg, #3f8686, #63c9c9); display: flex; align-items: center; justify-content: center; color: white; font-size: 40px;"><i class="bi bi-calendar-event opacity-50"></i></div>';
                        }
                        ?>

                        <div class="card-content-wrapper">
                            <?php $tipe_class = (strtolower($event['tipe']) == 'online') ? 'online' : 'offline'; ?>
                            <div class="card-tag <?php echo $tipe_class; ?>">
                                <img src="../../assets/img/pakar/lokasi.png" alt="loc" onerror="this.style.display='none'">
                                <span><?php echo htmlspecialchars($event['tipe']); ?></span>
                            </div>
                            
                            <h3><?php echo htmlspecialchars($event['judul']); ?></h3>
                            
                            <div class="card-details">
                                <div class="detail-item">
                                    <img src="../../assets/img/pakar/kalender_pakar.png" alt="cal" onerror="this.style.display='none'">
                                    <p>
                                        <?php echo formatTanggalEventIndo($event['tanggal_mulai']); ?> 
                                        <span class="time-info">
                                            • <?php echo date('H:i', strtotime($event['tanggal_mulai'])); ?> WIB
                                        </span>
                                    </p>
                                </div>
                                <div class="detail-item">
                                    <img src="../../assets/img/pakar/lokasi_pakar.png" alt="loc" onerror="this.style.display='none'">
                                    <p><?php echo htmlspecialchars($event['lokasi']); ?></p>
                                </div>
                            </div>
                            
                            <a href="#" class="btn-description">Deskripsi Event</a>
                            
                            <div class="event-description" style="display: none;">
                                <div style="background-color: #f9f9f9; padding: 20px; border-radius: 10px; margin-top: 15px; border: 1px solid #eee;">
                                    <h4 style="color: #3f8686; margin-bottom: 10px; font-size: 18px; font-weight: 600;">Detail Event</h4>
                                    <div style="color: #555; line-height: 1.6; font-size: 15px; margin-bottom: 20px; white-space: pre-wrap;"><?php echo htmlspecialchars($event['deskripsi']); ?></div>
                                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                                        <?php if (!empty($event['link_pendaftaran'])): ?>
                                            <a href="<?php echo htmlspecialchars($event['link_pendaftaran']); ?>" target="_blank" class="btn-daftar" style="flex: 1; min-width: 120px; background-color: #3f8686; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: 600; text-align: center; text-decoration: none; transition: background-color 0.2s;">Daftar Sekarang</a>
                                        <?php else: ?>
                                            <button disabled style="flex: 1; min-width: 120px; background-color: #ccc; color: #fff; border: none; padding: 10px 15px; border-radius: 6px; cursor: not-allowed; font-weight: 600;">Info Belum Tersedia</button>
                                        <?php endif; ?>
                                        <button type="button" class="btn-close-desc" style="flex: 1; min-width: 80px; background-color: #e0e0e0; color: #333; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; font-weight: 600; transition: background-color 0.2s;">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 50px 20px; background: #f9f9f9; border-radius: 10px;">
                    <h3 style="color: #777;">Belum Ada Event Mendatang</h3>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<div id="modalTambahEvent" class="modal">
    <div class="modal-content">
        <span class="close" onclick="document.getElementById('modalTambahEvent').style.display='none'">&times;</span>
        <h2 style="color: #3f8686; margin-top: 0; margin-bottom: 20px;">Buat Event Baru</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Judul Event</label>
                <input type="text" name="judul" required placeholder="Contoh: Webinar Budidaya Lele">
            </div>
            <div class="form-group">
                <label>Tipe Event</label>
                <select name="tipe">
                    <option value="Online">Online (Webinar/Zoom)</option>
                    <option value="Offline">Offline (Tatap Muka)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Tanggal & Waktu Mulai</label>
                <input type="datetime-local" name="tanggal_mulai" required>
            </div>
            <div class="form-group">
                <label>Lokasi (Alamat / Link Zoom)</label>
                <input type="text" name="lokasi" required placeholder="Contoh: Zoom Meeting atau Aula Desa">
            </div>
            <div class="form-group">
                <label>Link Pendaftaran (Opsional)</label>
                <input type="text" name="link_pendaftaran" placeholder="https://bit.ly/daftar-event">
            </div>
            <div class="form-group">
                <label>Gambar Banner</label>
                <input type="file" name="gambar" accept="image/*" required>
                <small style="color: #888;">Format: JPG, PNG, JPEG</small>
            </div>
            <div class="form-group">
                <label>Deskripsi Event</label>
                <textarea name="deskripsi" rows="5" required placeholder="Jelaskan detail acara..."></textarea>
            </div>
            <button type="submit" name="tambah_event" class="btn-submit">Terbitkan Event</button>
        </form>
    </div>
</div>

<?php include '../../includes/pakar/footer_pakar.php'; ?>
<script src="../../assets/js/pakar/event_pakar.js?v=<?php echo time(); ?>"></script>
<script>
    // Script Tutup Modal jika klik di luar
    window.onclick = function(event) {
        var modal = document.getElementById('modalTambahEvent');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
</body>
</html>