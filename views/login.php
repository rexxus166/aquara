<?php
// (Session start sudah dihapus dari sini, karena index.php sudah menjalankannya)
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = $_POST['email'];
    $password_input = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, nama, email, foto_profil, password, role_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        
        $user = $result->fetch_assoc();
        $hashed_password_from_db = $user['password'];

        if (password_verify($password_input, $hashed_password_from_db)) {
            
            // 6. Simpan data ke session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['foto_profil'] = $user['foto_profil'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['logged_in'] = true;

            // 7. =========================================================
            //    LOGIKA PENGALIHAN SESUAI STRUKTUR FOLDER ANDA
            // =========================================================
            
            $role = $_SESSION['role_id'];

            // Role 'admin' (Sesuai aquara.sql = 1)
            // Mengarah ke dashboard admin di views/admin/
            if ($role == 1) { 
                header("Location: views/admin/dashboard.php");
                exit;

            // Role 'anggota' (Sesuai aquara.sql = 2)
            // Mengarah ke index anggota di views/anggota/
            } else if ($role == 2) { 
                header("Location: views/anggota/index_anggota.php");
                exit;

            // Role 'pakar' (Sesuai aquara.sql = 3)
            // Mengarah ke index pakar di views/pakar/
            } else if ($role == 3) { 
                header("Location: views/pakar/index_pakar.php");
                exit;

            } else {
                // Jika role tidak dikenal, ke home pengunjung
                header("Location: index.php?page=home");
                exit;
            }
            // =========================================================
            //    AKHIR DARI LOGIKA PENGALIHAN
            // =========================================================

        } else {
            $error_message = "Password yang Anda masukkan salah.";
        }
    } else {
        $error_message = "Email tidak terdaftar.";
    }
    $stmt->close();
}
$conn->close();

// === BAGIAN FRONTEND (HTML ANDA) ===
?>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&family=Alatsi&family=Amiri&family=Alike+Angular&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/login.css">

<section id="login-page">
    <div class="login-container">
        <aside class="promo-panel">
            <div class="promo-background"></div>
            <a href="index.php?page=home" class="back-link">
                <img src="assets/img/aquara/loginKembali.png" alt="Arrow left icon">
                <span>Kembali ke Halaman Depan</span>
            </a>
            <div class="logo-container">
                <img src="assets/img/aquara/logo.png" alt="AQUARA Logo" class="logo-image">
            </div>
        </aside>
        <main class="form-panel">
            <div class="form-content">
                <h1 class="form-title">Login ke AQUARA</h1>

                <?php
                // Tampilkan pesan error jika ada
                if (!empty($error_message)) {
                    echo '<div class="login-error" style="color: red; background: #ffebee; border: 1px solid red; padding: 10px; margin-bottom: 15px; border-radius: 4px;">' . htmlspecialchars($error_message) . '</div>';
                }
                ?>

                <form class="login-form" action="index.php?page=login" method="post"> 
                    <a href="views/google_login.php" class="google-login-btn">
                        <img src="assets/img/aquara/google.png" alt="Google icon">
                        <span>Masuk dengan Google</span>
                    </a>

                    <div class="separator">
                        <span>Atau</span>
                    </div>

                    <div class="form-group">
                        <label for="email">Email / Username</label>
                        <div class="input-wrapper">
                            <img src="assets/img/aquara/email.png" alt="" class="input-icon">
                            <input type="email" id="email" name="email" placeholder="Contoh: Polindra25@gmail.com" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrapper">
                            <img src="assets/img/aquara/sandi.png" alt="" class="input-icon password-icon">
                            <input type="password" id="password" name="password" placeholder="Password" required>
                        </div>
                    </div>

                    <div class="form-links">
                        <p class="create-account-link">Belum Mempunyai Akun? <a href="index.php?page=register">Buat Akun</a></p>
                        <a href="index.php?page=lupa_password" class="forgot-password-link">Lupa Password?</a>
                    </div>

                    <button type="submit" class="submit-btn">Masuk</button>
                </form>
                <p class="terms-text">
                    Dengan menggunakan layanan kami, Anda berarti setuju atas Syarat & Ketentuan dan Kebijakan Privasi AQUARA
                </p>
            </div>
        </main>
    </div>
</section>
