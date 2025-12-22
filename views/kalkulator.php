<?php
$activeMenu = 'kalkulator';

$this->layout('layout', ['title' => 'Kalkulator - AQUARA', 'activeMenu' => $activeMenu]) ?>

<?php $this->start('styles') ?>
<link rel="stylesheet" href="assets/css/kalkulator.css?v=<?= time() ?>">
<link rel="stylesheet" href="assets/css/event_pakar2.css?v=<?= time() ?>">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    /* CSS Tambahan agar form terlihat 'read-only' dan menarik untuk pengunjung */
    .kalkulator-form-wrapper {
        position: relative;
    }
    .overlay-login {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(3px);
        z-index: 10;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        text-align: center;
        padding: 20px;
    }
    .overlay-login h3 {
        color: #013746;
        margin-bottom: 15px;
    }
    .btn-login-overlay {
        background: #3f8686;
        color: white;
        padding: 12px 30px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: bold;
        box-shadow: 0 5px 15px rgba(63, 134, 134, 0.4);
        transition: transform 0.3s;
    }
    .btn-login-overlay:hover {
        transform: scale(1.05);
        background: #2c6666;
        color: white;
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
        <h1 class="hero-title">KALKULATOR</h1>
    </div>
</section>

<section id="section-kalkulator" class="kalkulator-section">
    <div class="container">
        <div class="kalkulator-intro">
            <div class="intro-icon">
                <i class="bi bi-calculator-fill"></i>
            </div>
            <h2 class="intro-title">Kalkulator Budidaya Ikan</h2>
            <p class="intro-description">Hitung estimasi biaya produksi dan proyeksi keuntungan budidaya ikan air tawar Anda.</p>
        </div>

        <div class="kalkulator-content">
            
            <div class="kalkulator-form-wrapper">
                
                <div class="overlay-login">
                    <h3>Ingin Menghitung Proyeksi Budidaya Anda?</h3>
                    <p style="color: #555; margin-bottom: 25px;">Fitur kalkulator lengkap hanya tersedia untuk anggota AQUARA.</p>
                    <a href="?page=login" class="btn-login-overlay">
                        <i class="bi bi-lock-fill"></i> Login untuk Mengakses
                    </a>
                </div>

                <div class="form-card" style="filter: blur(2px); pointer-events: none;">
                    <div class="form-header">
                        <i class="bi bi-clipboard-data"></i>
                        <h3>Data Budidaya</h3>
                    </div>
                    <form id="kalkulatorForm" class="kalkulator-form">
                        <div class="form-group">
                            <label><i class="bi bi-rulers"></i> Luas Kolam (m²)</label>
                            <input type="number" placeholder="Contoh: 100" disabled>
                        </div>
                        <div class="form-group">
                            <label><i class="bi bi-water"></i> Jenis Ikan</label>
                            <select disabled><option>Pilih jenis ikan</option></select>
                        </div>
                        <div class="form-group">
                            <label><i class="bi bi-egg"></i> Jumlah Bibit (Ekor)</label>
                            <input type="number" placeholder="Contoh: 5000" disabled>
                        </div>
                        <div class="form-group">
                            <label><i class="bi bi-cash-coin"></i> Harga Bibit (Rp)</label>
                            <input type="number" placeholder="Contoh: 500" disabled>
                        </div>
                        <div class="form-group">
                            <label><i class="bi bi-basket"></i> Harga Pakan (Rp)</label>
                            <input type="number" placeholder="Contoh: 8000" disabled>
                        </div>
                        <div class="form-group">
                            <label><i class="bi bi-calendar-check"></i> Durasi (Bulan)</label>
                            <input type="number" placeholder="Contoh: 3" disabled>
                        </div>
                        <button type="button" class="btn-hitung" disabled>
                            <i class="bi bi-calculator"></i> Hitung Proyeksi
                        </button>
                    </form>
                </div>
            </div>

            <div class="hasil-wrapper">
                <div class="hasil-card">
                    <div class="hasil-header">
                        <i class="bi bi-graph-up-arrow"></i>
                        <h3>Hasil Perhitungan</h3>
                    </div>
                    <div class="hasil-content">
                        <div class="hasil-placeholder">
                            <i class="bi bi-lock-fill" style="font-size: 50px; opacity: 0.3;"></i>
                            <p>Hasil perhitungan akan muncul di sini setelah Anda login.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php $this->end() ?>