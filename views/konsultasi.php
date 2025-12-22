<?php
$activeMenu = 'konsultasi';

$this->layout('layout', ['title' => 'Konsultasi - AQUARA', 'activeMenu' => $activeMenu]) ?>

<?php $this->start('styles') ?>
<link rel="stylesheet" href="assets/css/konsultasi.css?v=<?= time() ?>">
<link rel="stylesheet" href="assets/css/event_pakar2.css?v=<?= time() ?>">
<style>
    /* CSS Tambahan agar form terlihat 'read-only' untuk pengunjung */
    .konsultasi-form input:disabled, 
    .konsultasi-form select:disabled, 
    .konsultasi-form textarea:disabled {
        background-color: #f9f9f9;
        cursor: not-allowed;
        opacity: 0.7;
    }
</style>
<?php $this->stop() ?>

<?php $this->start('body') ?>

<section id="section-hero" class="hero-section">
    <div class="hero-bg">
        <div class="hero-bg-top"></div>
        <div class="hero-bg-bottom"></div>
    </div>
    <div class="container">
        <h1 class="hero-title">KONSULTASI</h1>
        <p class="hero-subtitle">Dapatkan solusi langsung dari para ahli kami.</p>
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
                            <circle cx="30" cy="30" r="30" fill="#3f8686"/><circle cx="30" cy="25" r="10" fill="white"/><path d="M15 45C15 37.268 21.268 31 29 31H31C38.732 31 45 37.268 45 45" fill="white"/>
                        </svg>
                    </div>
                    <h4 class="ahli-name">Dr. Ahmad Budiman</h4>
                    <p class="ahli-specialty">Budidaya Lele & Nila</p>
                    <p class="ahli-experience">Pengalaman 8 tahun</p>
                    <button class="btn-konsultasi" onclick="alertLogin()">Konsultasi</button>
                </div>
                <div class="ahli-card">
                    <div class="ahli-avatar">
                        <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                            <circle cx="30" cy="30" r="30" fill="#3f8686"/><circle cx="30" cy="25" r="10" fill="white"/><path d="M15 45C15 37.268 21.268 31 29 31H31C38.732 31 45 37.268 45 45" fill="white"/>
                        </svg>
                    </div>
                    <h4 class="ahli-name">Dr. Herlina Muti</h4>
                    <p class="ahli-specialty">Budidaya Gurame & Bawal</p>
                    <p class="ahli-experience">Pengalaman 6 tahun</p>
                    <button class="btn-konsultasi" onclick="alertLogin()">Konsultasi</button>
                </div>
                <div class="ahli-card">
                    <div class="ahli-avatar">
                        <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
                            <circle cx="30" cy="30" r="30" fill="#3f8686"/><circle cx="30" cy="25" r="10" fill="white"/><path d="M15 45C15 37.268 21.268 31 29 31H31C38.732 31 45 37.268 45 45" fill="white"/>
                        </svg>
                    </div>
                    <h4 class="ahli-name">Dr. Alfatih Ronaldo</h4>
                    <p class="ahli-specialty">Penyakit Ikan & Kapang</p>
                    <p class="ahli-experience">Pengalaman 8 tahun</p>
                    <button class="btn-konsultasi" onclick="alertLogin()">Konsultasi</button>
                </div>
            </div>
        </div>

        <div class="form-konsultasi-section">
            <h3 class="section-subtitle">Form Konsultasi</h3>
            <div class="form-container" style="position: relative;">
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.6); z-index: 10; display: flex; align-items: center; justify-content: center; border-radius: 15px;">
                    <a href="?page=login" class="btn-submit" style="text-decoration: none; box-shadow: 0 5px 20px rgba(0,0,0,0.2);">
                        Login untuk Memulai Konsultasi
                    </a>
                </div>

                <form class="konsultasi-form" style="filter: blur(2px);">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" placeholder="Nama Anda" disabled>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" placeholder="email@contoh.com" disabled>
                    </div>
                    <div class="form-group">
                        <label>Nomor Telepon</label>
                        <input type="tel" placeholder="0812xxxxxxx" disabled>
                    </div>
                    <div class="form-group">
                        <label>Ahli (Opsional)</label>
                        <select disabled>
                            <option>Pilih ahli...</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Topik Konsultasi</label>
                        <input type="text" placeholder="Contoh: Penyakit ikan" disabled>
                    </div>
                    <div class="form-group">
                        <label>Pertanyaan</label>
                        <textarea rows="5" placeholder="Tulis pertanyaan Anda di sini..." disabled></textarea>
                    </div>
                    <button type="button" class="btn-submit" disabled>Kirim Konsultasi</button>
                </form>
            </div>
        </div>

        <div class="riwayat-konsultasi-section" style="margin-top: 40px; text-align: center; color: #999;">
            <h3 class="section-subtitle">Riwayat Konsultasi</h3>
            <div style="padding: 40px; background: #f9f9f9; border-radius: 12px;">
                <i class="bi bi-lock-fill" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
                <p>Silakan login untuk melihat riwayat konsultasi Anda.</p>
            </div>
        </div>

    </div>
</section>

<script>
// Fungsi sederhana untuk alert login
function alertLogin() {
    if (confirm('Fitur ini khusus untuk anggota. Ingin login sekarang?')) {
        window.location.href = '?page=login';
    }
}
</script>

<?php $this->end() ?>