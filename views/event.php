<?php
$activeMenu = 'event';

$this->layout('layout', ['title' => 'Event - AQUARA', 'activeMenu' => $activeMenu]) ?>

<?php $this->start('styles') ?>
<link rel="stylesheet" href="assets/css/event_pakar.css?v=<?= time() ?>">
<link rel="stylesheet" href="assets/css/event_pakar2.css?v=<?= time() ?>">
<style>
    /* CSS Banner Tambahan (Sama seperti di file pakar/anggota) */
    .event-banner {
        width: 100%; height: 180px; object-fit: cover;
        border-radius: 10px 10px 0 0; display: block;
    }
    .event-card { padding-top: 0 !important; overflow: hidden; }
    .card-content-wrapper { padding: 20px; position: relative; }
    .card-tag { top: 15px; right: 15px; }
</style>
<?php $this->stop() ?>

<?php $this->start('body') ?>

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
            <?php
            // --- KONEKSI DATABASE ---
            global $conn;
            if (!isset($conn)) { include_once __DIR__ . '/../includes/config.php'; }

            // --- HELPER FORMAT TANGGAL INDO ---
            if (!function_exists('formatTanggalEventIndo')) {
                function formatTanggalEventIndo($datetime) {
                    $date = new DateTime($datetime);
                    $bulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                    return $date->format('d') . ' ' . $bulan[intval($date->format('m'))] . ' ' . $date->format('Y');
                }
            }

            if (isset($conn) && $conn) {
                // Query ambil event mendatang
                $query = "SELECT * FROM events WHERE tanggal_mulai >= NOW() ORDER BY tanggal_mulai ASC";
                $result = $conn->query($query);

                if ($result && $result->num_rows > 0) {
                    while ($event = $result->fetch_assoc()) {
                        $gambar_path = "uploads/events/" . $event['gambar'];
                        $gambar = (!empty($event['gambar']) && file_exists($gambar_path)) ? $gambar_path : "";
                        $tipe_class = (strtolower($event['tipe']) == 'online') ? 'online' : 'offline';
            ?>
                        <div class="event-card">
                            
                            <?php if ($gambar): ?>
                                <img src="<?= htmlspecialchars($gambar) ?>" alt="Event" class="event-banner">
                            <?php else: ?>
                                <div class="event-banner" style="background: linear-gradient(135deg, #3f8686, #63c9c9); display: flex; align-items: center; justify-content: center; color: white; font-size: 40px;">
                                    <i class="bi bi-calendar-event opacity-50"></i>
                                </div>
                            <?php endif; ?>

                            <div class="card-content-wrapper">
                                <div class="card-tag <?= $tipe_class ?>">
                                    <img src="assets/img/pakar/lokasi.png" alt="loc" onerror="this.style.display='none'">
                                    <span><?= htmlspecialchars($event['tipe']) ?></span>
                                </div>
                                
                                <h3><?= htmlspecialchars($event['judul']) ?></h3>
                                
                                <div class="card-details">
                                    <div class="detail-item">
                                        <img src="assets/img/pakar/kalender_pakar.png" alt="cal" onerror="this.style.display='none'">
                                        <p>
                                            <?= formatTanggalEventIndo($event['tanggal_mulai']) ?> 
                                            <span class="time-info">• <?= date('H:i', strtotime($event['tanggal_mulai'])) ?> WIB</span>
                                        </p>
                                    </div>
                                    <div class="detail-item">
                                        <img src="assets/img/pakar/lokasi_pakar.png" alt="loc" onerror="this.style.display='none'">
                                        <p><?= htmlspecialchars($event['lokasi']) ?></p>
                                    </div>
                                </div>
                                
                                <a href="?page=login" class="btn-description" onclick="return confirm('Silakan login untuk melihat detail event ini.');" style="text-align: center; display: block; text-decoration: none;">
                                    Deskripsi Event
                                </a>
                                
                            </div> </div>
            <?php
                    }
                } else {
                    echo '<div style="grid-column: 1 / -1; text-align: center; padding: 50px 20px; background: #f9f9f9; border-radius: 10px;"><h3 style="color: #777;">Belum Ada Event Mendatang</h3></div>';
                }
            } else {
                echo '<p style="grid-column: 1/-1; text-align: center; color: red;">Gagal terhubung ke database event.</p>';
            }
            ?>
        </div>
    </div>
</section>

<?php $this->end() ?>