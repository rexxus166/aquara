<?php
// (Koneksi database sudah ada dari index_anggota.php)
$activeMenu = 'event';

// 1. Ambil event mendatang
$events = [];
$query = "SELECT * FROM events WHERE tanggal_mulai >= NOW() ORDER BY tanggal_mulai ASC";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}

// 2. Helper tanggal Indo
if (!function_exists('formatTanggalEventIndo')) {
    function formatTanggalEventIndo($datetime) {
        $date = new DateTime($datetime);
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 
            6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 
            11 => 'November', 12 => 'Desember'
        ];
        return $date->format('d') . ' ' . $bulan[intval($date->format('m'))] . ' ' . $date->format('Y');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event - AQUARA</title>
    <link rel="stylesheet" href="../../assets/css/event_pakar.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="../../assets/css/event_pakar2.css?v=<?php echo time(); ?>">
    <style>
        /* CSS Tambahan untuk Banner */
        .event-banner {
            width: 100%;
            height: 180px; /* Atur tinggi banner sesuai keinginan */
            object-fit: cover;
            border-radius: 10px 10px 0 0; /* Melengkung di atas saja */
            display: block;
        }
        /* Penyesuaian kartu agar banner pas */
        .event-card {
            padding-top: 0 !important; /* Hapus padding atas kartu agar banner nempel */
            overflow: hidden; /* Agar sudut gambar ikut melengkung sesuai kartu */
        }
        .card-content-wrapper {
            padding: 20px; /* Bungkus konten teks dengan padding */
            position: relative; /* Agar tag lokasi bisa diposisikan absolut relatif terhadap ini */
        }
        /* Geser posisi tag lokasi agar tidak menutupi banner terlalu banyak */
        .card-tag {
            top: 15px;
            right: 15px;
        }
    </style>
</head>
<body>

<?php include '../../includes/anggota/header_event_anggota.php'; ?>

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
        <div class="section-title">
            <h2>Event & Pelatihan Mendatang</h2>
            <p>Tingkatkan skill dan networking dengan mengikuti event-event berkualitas.</p>
        </div>
        <div class="events-grid">
            <?php if (count($events) > 0): ?>
                <?php foreach ($events as $event): ?>
                    <div class="event-card" id="event-<?php echo $event['id']; ?>">
                        
                        <?php 
                        // Cek apakah ada file gambar dan filenya benar-benar ada di folder
                        $gambar_path = "../../uploads/events/" . $event['gambar'];
                        if (!empty($event['gambar']) && file_exists($gambar_path)) {
                            // Jika ada gambar, tampilkan
                            echo '<img src="' . htmlspecialchars($gambar_path) . '" alt="gambar Event" class="event-gambar">';
                        } else {
                            // OPSI: Jika tidak ada gambar, bisa pakai gambar default atau div warna
                            echo '<div class="event-gambar" style="background: linear-gradient(135deg, #3f8686, #63c9c9); display: flex; align-items: center; justify-content: center; color: white; font-size: 40px;"><i class="fas fa-calendar-alt opacity-25"></i></div>';
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
                        </div> </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 50px 20px; background: #f9f9f9; border-radius: 10px;">
                    <h3 style="color: #777;">Belum Ada Event Mendatang</h3>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include '../../includes/anggota/footer_event_anggota.php'; ?>
<script src="../../assets/js/pakar/event_pakar.js?v=<?php echo time(); ?>"></script>
</body>
</html>