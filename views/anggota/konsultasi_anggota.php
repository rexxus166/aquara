<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konsultasi - AQUARA</title>
    <link rel="stylesheet" href="../../assets/css/konsultasi.css">
    <link rel="stylesheet" href="../../assets/css/event_pakar2.css">
</head>
<body>

<?php 
$activeMenu = 'konsultasi';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit();
}

if (isset($_GET['action']) && $_GET['action'] == 'cancel' && isset($_GET['id'])) {
    $id_batal = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];

    // Update status menjadi 'dibatalkan' hanya jika milik user tersebut dan masih pending
    $stmt = $conn->prepare("UPDATE konsultasi SET status = 'dibatalkan' WHERE id = ? AND anggota_id = ? AND status = 'pending'");
    $stmt->bind_param("ii", $id_batal, $user_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Konsultasi berhasil dibatalkan.'); window.location.href='index_anggota.php?page=konsultasi_anggota';</script>";
    } else {
        echo "<script>alert('Gagal membatalkan konsultasi.'); window.location.href='index_anggota.php?page=konsultasi_anggota';</script>";
    }
    $stmt->close();
    exit;
}

include '../../includes/anggota/header_konsultasi_anggota.php'; 
?>

<section id="section-hero" class="hero-section">
    <div class="hero-bg">
        <div class="hero-bg-top"></div>
        <div class="hero-bg-bottom"></div>
    </div>
    <div class="container">
        <h1 class="hero-title">KONSULTASI</h1>
        <p class="hero-subtitle">Selamat datang, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</p>
    </div>
</section>

<section id="section-konsultasi" class="konsultasi-section">
    <div class="container">
        <div class="konsultasi-intro">
            <div class="intro-icon">
                <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="60" cy="60" r="60" fill="#3f8686" opacity="0.1"/>
                    <path d="M40 50C40 44.4772 44.4772 40 50 40H70C75.5228 40 80 44.4772 80 50V65C80 70.5228 75.5228 75 70 75H55L45 82V75H50C44.4772 75 40 70.5228 40 65V50Z" stroke="#3f8686" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M52 55H68M52 62H62" stroke="#3f8686" stroke-width="3" stroke-linecap="round"/>
                </svg>
            </div>
            <h2 class="intro-title">Konsultasi dengan Ahli</h2>
            <p class="intro-description">Dapatkan solusi terbaik dari pakar pertanian berpengalaman untuk mengoptimalkan hasil budidaya Anda.</p>
        </div>

        <div class="ahli-section">
            <h3 class="section-subtitle">Tim Ahli Kami</h3>
            <div class="ahli-grid">
                <div class="ahli-card">
                    <div class="ahli-avatar">
                        <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                            <circle cx="30" cy="30" r="30" fill="#3f8686"/>
                            <circle cx="30" cy="25" r="10" fill="white"/>
                            <path d="M15 45C15 37.268 21.268 31 29 31H31C38.732 31 45 37.268 45 45" fill="white"/>
                        </svg>
                    </div>
                    <h4 class="ahli-name">Dr. Ahmad Budiman</h4>
                    <p class="ahli-specialty">Budidaya Lele & Nila</p>
                    <p class="ahli-experience">Pengalaman 8 tahun</p>
                    <button class="btn-konsultasi" onclick="selectAhli('Dr. Ahmad Budiman')">Konsultasi</button>
                </div>

                <div class="ahli-card">
                    <div class="ahli-avatar">
                        <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                            <circle cx="30" cy="30" r="30" fill="#3f8686"/>
                            <circle cx="30" cy="25" r="10" fill="white"/>
                            <path d="M15 45C15 37.268 21.268 31 29 31H31C38.732 31 45 37.268 45 45" fill="white"/>
                        </svg>
                    </div>
                    <h4 class="ahli-name">Dr. Herlina Muti</h4>
                    <p class="ahli-specialty">Budidaya Gurame & Bawal</p>
                    <p class="ahli-experience">Pengalaman 6 tahun</p>
                    <button class="btn-konsultasi" onclick="selectAhli('Dr. Herlina Muti')">Konsultasi</button>
                </div>

                <div class="ahli-card">
                    <div class="ahli-avatar">
                        <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                            <circle cx="30" cy="30" r="30" fill="#3f8686"/>
                            <circle cx="30" cy="25" r="10" fill="white"/>
                            <path d="M15 45C15 37.268 21.268 31 29 31H31C38.732 31 45 37.268 45 45" fill="white"/>
                        </svg>
                    </div>
                    <h4 class="ahli-name">Dr. Alfatih Ronaldo</h4>
                    <p class="ahli-specialty">Penyakit Ikan & Kapang</p>
                    <p class="ahli-experience">Pengalaman 8 tahun</p>
                    <button class="btn-konsultasi" onclick="selectAhli('Dr. Alfatih Ronaldo')">Konsultasi</button>
                </div>
            </div>
        </div>

        <div class="form-konsultasi-section">
            <h3 class="section-subtitle">Form Konsultasi</h3>
            <div class="form-container">
                <form id="konsultasiForm" class="konsultasi-form">
                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($_SESSION['nama']); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="telepon">Nomor Telepon</label>
                        <input type="tel" id="telepon" name="telepon" value="<?php echo htmlspecialchars($_SESSION['telepon'] ?? ''); ?>" placeholder="Masukkan nomor telepon Anda" required>
                    </div>

                    <div class="form-group">
                        <label for="ahli">Ahli (Opsional)</label>
                        <select id="ahli" name="ahli">
                            <option value="">Pilih ahli yang ingin dikonsultasikan</option>
                            <option value="Dr. Ahmad Budiman">Dr. Ahmad Budiman</option>
                            <option value="Dr. Herlina Muti">Dr. Herlina Muti</option>
                            <option value="Dr. Alfatih Ronaldo">Dr. Alfatih Ronaldo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="topik">Topik Konsultasi</label>
                        <input type="text" id="topik" name="topik" placeholder="Contoh: Budidaya ikan lele" required>
                    </div>

                    <div class="form-group">
                        <label for="pertanyaan">Pertanyaan</label>
                        <textarea id="pertanyaan" name="pertanyaan" rows="5" placeholder="Jelaskan pertanyaan atau permasalahan yang ingin Anda konsultasikan secara detail..." required></textarea>
                    </div>

                    <button type="submit" class="btn-submit">Kirim Konsultasi</button>
                </form>
            </div>
        </div>

        <!-- Riwayat Konsultasi -->
        <div class="riwayat-konsultasi-section" style="margin-top: 40px;">
            <h3 class="section-subtitle">Riwayat Konsultasi</h3>
            <div id="riwayatContainer" class="riwayat-container">
                <!-- Akan diisi oleh JavaScript -->
            </div>
        </div>
    </div>
</section>

<?php include '../../includes/anggota/footer_konsultasi_anggota.php'; ?>

<script src="../../assets/js/anggota/konsultasi_anggota.js"></script>
</body>
</html>