<?php
// 1. Konfigurasi & Logika Simulasi
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../includes/config.php';

$step = isset($_GET['step']) ? $_GET['step'] : 1;
$pesan_error = "";

// LOGIKA STEP 1: KIRIM KODE (SIMULASI)
if ($step == 1 && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kirim_kode'])) {
    $email = $_POST['email'];
    
    // Cek email di database
    $cek = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($cek->num_rows > 0) {
        // Generate Kode 6 Digit
        $kode_otp = rand(100000, 999999);
        
        // Simpan di session
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_otp'] = $kode_otp;
        
        // TRIK ALERT: Tampilkan kode ke user (Pura-puranya ini email masuk)
        echo "<script>
            alert('Kode Verifikasi telah dikirim ke $email.\\n\\nKODE RAHASIA ANDA: $kode_otp \\n\\n(Silakan catat/copy kode ini)');
            window.location.href = '?page=lupa_password&step=2';
        </script>";
        exit;
    } else {
        $pesan_error = "Email tidak terdaftar di sistem kami.";
    }
}

// LOGIKA STEP 2: VERIFIKASI & GANTI PASSWORD
if ($step == 2 && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ganti_password'])) {
    $input_otp = $_POST['code'];
    $pass_baru = $_POST['password'];
    $pass_konfirm = $_POST['confirm_password'];
    
    if ($input_otp == $_SESSION['reset_otp']) {
        if ($pass_baru === $pass_konfirm) {
            // Update Password
            $email_target = $_SESSION['reset_email'];
            // Gunakan password_hash jika database Anda pakai hash, atau plain text jika tidak
            // Di sini saya pakai default hash sesuai standar keamanan
            $hashed_pass = password_hash($pass_baru, PASSWORD_DEFAULT); 
            
            // NOTE: Jika database Anda masih plain text, ganti $hashed_pass jadi $pass_baru
            $conn->query("UPDATE users SET password = '$hashed_pass' WHERE email = '$email_target'");
            
            // Bersihkan session
            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_otp']);
            
            // Tampilkan notifikasi sukses lewat JS (memanfaatkan desain notifikasi Anda)
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('successNotification').classList.add('show');
                });
            </script>";
        } else {
            $pesan_error = "Konfirmasi password tidak cocok!";
        }
    } else {
        $pesan_error = "Kode verifikasi salah! Cek alert sebelumnya.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Aquara</title>
    <link rel="stylesheet" href="assets/css/lupa_password.css">
    <style>
        /* Tambahan CSS untuk Notifikasi Sukses (agar bisa muncul) */
        .success-notification {
            display: none; /* Default hidden */
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 9999;
            justify-content: center; align-items: center;
        }
        .success-notification.show { display: flex; }
        .notification-content {
            background: white; padding: 30px; border-radius: 15px; text-align: center; width: 90%; max-width: 400px;
        }
        .check-icon { margin-bottom: 15px; }
        .btn-ok {
            background: #10B981; color: white; border: none; padding: 10px 30px;
            border-radius: 8px; cursor: pointer; font-weight: bold; margin-top: 15px;
        }
        .error-msg {
            background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 8px;
            margin-bottom: 15px; text-align: center; font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="background-image"></div>

        <div class="form-card">
            <a href="index.php?page=login" class="back-button">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                Kembali
            </a>

            <div class="logo-container">
                <img src="assets/img/aquara/logo.png" alt="Aquara Logo" class="logo">
            </div>

            <?php if (!empty($pesan_error)): ?>
                <div class="error-msg"><?= $pesan_error ?></div>
            <?php endif; ?>

            <?php if ($step == 1): ?>
                <div class="instructions">
                    <p>Untuk mengatur ulang kata sandi Anda, masukkan email dan tekan tombol "Kirim Kode".</p>
                </div>

                <form method="POST" class="reset-form">
                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M3.33333 3.33334H16.6667C17.5833 3.33334 18.3333 4.08334 18.3333 5.00001V15C18.3333 15.9167 17.5833 16.6667 16.6667 16.6667H3.33333C2.41667 16.6667 1.66667 15.9167 1.66667 15V5.00001C1.66667 4.08334 2.41667 3.33334 3.33333 3.33334Z" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M18.3333 5L10 10.8333L1.66667 5" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <input type="email" id="email" name="email" placeholder="Contoh: email@anda.com" required>
                        </div>
                    </div>
                    <button type="submit" name="kirim_kode" class="submit-btn" style="margin-top: 20px;">Kirim Kode</button>
                </form>

            <?php elseif ($step == 2): ?>
                <div class="instructions">
                    <p>Kode verifikasi telah dikirim (Cek Alert). Masukkan kode dan password baru Anda.</p>
                </div>

                <form method="POST" class="reset-form">
                    <div class="form-group">
                        <label for="code">Kode Verifikasi</label>
                        <div class="input-wrapper">
                            <svg class="input-icon" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M15.8333 9.16667H4.16667C3.24619 9.16667 2.5 9.91286 2.5 10.8333V16.6667C2.5 17.5871 3.24619 18.3333 4.16667 18.3333H15.8333C16.7538 18.3333 17.5 17.5871 17.5 16.6667V10.8333C17.5 9.91286 16.7538 9.16667 15.8333 9.16667Z" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M5.83333 9.16667V5.83333C5.83333 4.72826 6.27232 3.66846 7.05372 2.88706C7.83512 2.10565 8.89493 1.66667 10 1.66667C11.1051 1.66667 12.1649 2.10565 12.9463 2.88706C13.7277 3.66846 14.1667 4.72826 14.1667 5.83333V9.16667" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <input type="text" id="code" name="code" placeholder="Masukkan Kode 6 Digit" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group half">
                            <label for="password">Password Baru</label>
                            <div class="input-wrapper">
                                <input type="password" id="password" name="password" placeholder="Password Baru" required style="padding-left: 15px;">
                            </div>
                        </div>
                        <div class="form-group half">
                            <label for="confirm_password">Konfirmasi</label>
                            <div class="input-wrapper">
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi Password" required style="padding-left: 15px;">
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="ganti_password" class="submit-btn">Simpan Password</button>
                </form>
            <?php endif; ?>
            
            <p class="footer-text">Dengan menggunakan layanan kami, Anda setuju dengan <a href="#">Kebijakan Privasi</a>.</p>
        </div>
    </div>

    <div id="successNotification" class="success-notification">
        <div class="notification-content">
            <svg class="check-icon" width="50" height="50" viewBox="0 0 50 50" fill="none">
                <circle cx="25" cy="25" r="25" fill="#10B981"/>
                <path d="M15 25L22 32L35 18" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h3>Password Berhasil Diubah!</h3>
            <p>Silakan login kembali dengan password baru Anda.</p>
            <button onclick="window.location.href='index.php?page=login'" class="btn-ok">Login Sekarang</button>
        </div>
    </div>

</body>
</html>