<link rel="stylesheet" href="assets/css/register.css">
<main class="signup-container">
  <div class="branding-panel">
    <div class="branding-content">
      <a href="index.php?page=login" class="back-button">
        <img src="assets/img/aquara/loginKembali.png" alt="Back arrow" class="back-arrow-icon">
        <span>Kembali ke Halaman Login</span>
      </a>
      <div class="logo-container">
        <img src="assets/img/aquara/logo.png" alt="AQUARA Logo" class="logo-image">
      </div>
    </div>
  </div>

  <div class="form-panel">
    <div class="form-wrapper">
      <h1 class="form-title">Daftar ke AQUARA</h1>
      
      <!-- arahkan form ke file PHP untuk proses -->
      <form method="POST" action="index.php?page=proses_register"> 

        <!-- Tombol Google diarahkan langsung ke akun Google -->
        <!-- HANYA DIGANTI BAGIAN INI: dari simulation -> google_login.php -->
        <a href="views/google_login.php" class="google-btn">
          <img src="assets/img/aquara/google.png" alt="Google logo" class="google-icon">
          <span>Daftar dengan Google</span>
        </a>

        <div class="separator">
          <div class="line"></div>
          <span>Atau</span>
          <div class="line"></div>
        </div>

        <div class="form-group">
          <label for="email">Alamat Email</label>
          <div class="input-wrapper">
            <img src="assets/img/aquara/email.png" alt="Email icon" class="input-icon">
            <input type="email" id="email" name="email" placeholder="Contoh: Polindra25@gmail.com" required>
          </div>
        </div>

        <div class="form-group">
          <label for="nama">Nama Lengkap</label> <div class="input-wrapper">
              <img src="assets/img/aquara/usename.png" alt="Username icon" class="input-icon user-icon">
              <input type="text" id="nama" name="nama" placeholder="Masukan Nama Lengkap" required> 
        </div>
        </div>

        <div class="password-fields">
          <div class="form-group">
            <label for="password">Password</label>
            <div class="input-wrapper">
              <img src="assets/img/aquara/sandi.png" alt="Password icon" class="input-icon key-icon">
              <input type="password" id="password" name="password" placeholder="Password" required>
            </div>
          </div>
          <div class="form-group">
            <label for="confirm-password">Ketik Ulang Password</label>
            <div class="input-wrapper">
              <img src="assets/img/aquara/sandi.png" alt="Password icon" class="input-icon key-icon">
              <input type="password" id="confirm-password" name="confirm_password" placeholder="Ketik Ulang Password" required>
            </div>
          </div>
        </div>

        <button type="submit" class="submit-btn">Daftar Sekarang</button>
      </form>

      <p class="terms-text">
        Dengan menggunakan layanan kami, Anda berarti setuju atas Syarat & Ketentuan dan Kebijakan Privasi AQUARA
      </p>
    </div>
  </div>
</main>
