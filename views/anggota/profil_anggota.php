<?php
// (Session sudah dimulai di index_anggota.php)

// Ambil data dari session untuk ditampilkan
$nama_user = $_SESSION['nama'] ?? 'Pengguna';
$email_user = $_SESSION['email'] ?? 'Tidak ada email';

// Ambil path foto dari session
$foto_user = isset($_SESSION['foto_profil']) ? trim($_SESSION['foto_profil']) : null;
$path_foto_default = "/assets/img/aquara/profil.png";

if (!empty($foto_user)) {
    // Cek apakah foto adalah URL eksternal (misal dari Google Login)
    // Gunakan strpos agar lebih robust daripada filter_var
    if (strpos($foto_user, 'http') === 0) {
        $path_foto_tampil = $foto_user;
    } else {
        $path_foto_tampil = "/aquara/uploads/profil/" . htmlspecialchars($foto_user);
    }
} else {
    $path_foto_tampil = $path_foto_default;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - AQUARA</title>

    <link rel="stylesheet" href="/assets/css/header_footer_pakar.css">
    <link rel="stylesheet" href="/assets/css/artikel_pakar_custom.css">

    <link rel="stylesheet" href="/assets/css/anggota/profil_anggota.css">
</head>

<body>

    <?php
    // Memuat header
    include '../../includes/anggota/header_home_anggota.php';
    ?>

    <section class="profil-section">
        <div class="profil-container">

            <div id="notifProfil" class="notification"></div>

            <div class="profil-header">
                <div class="profil-avatar-container">
                    <img src="<?php echo $path_foto_tampil; ?>" alt="Foto Profil" class="profil-avatar" id="profilAvatarHeader">
                </div>
                <div class="profil-header-info">
                    <h2><?php echo htmlspecialchars($nama_user); ?></h2>
                    <p><?php echo htmlspecialchars($email_user); ?></p>

                    <form id="uploadFotoForm" enctype="multipart/form-data">
                        <label for="foto_profil" style="font-size: 14px; font-weight: 500;">Ganti Foto (Max 2MB: JPG, PNG)</label>
                        <input type="file" id="foto_profil" name="foto_profil" accept="image/jpeg, image/png">
                        <img id="fotoProfilPreview" src="#" alt="Preview Foto Baru">
                        <button type="submit" class="btn-submit" style="margin-top: 10px;">Upload Foto</button>
                    </form>
                </div>
            </div>
            <form id="profilForm">
                <h3 style="color: #333; border-bottom: 2px solid #3f8686; padding-bottom: 10px; margin-bottom: 20px;">Informasi Akun</h3>

                <div class="form-group">
                    <label for="nama">Nama Lengkap</label>
                    <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($nama_user); ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email_user); ?>">
                </div>

                <button type="submit" class="btn-submit">Simpan Perubahan Info</button>
            </form>

            <hr style="border: 0; border-top: 1px solid #eee; margin: 40px 0;">

            <form id="passwordForm">
                <h3 style="color: #333; border-bottom: 2px solid #3f8686; padding-bottom: 10px; margin-bottom: 20px;">Ubah Password</h3>

                <div class="form-group">
                    <label for="password_lama">Password Lama</label>
                    <input type="password" id="password_lama" name="password_lama" placeholder="Masukkan password Anda saat ini" required>
                </div>

                <div class="form-group">
                    <label for="password_baru">Password Baru</label>
                    <input type="password" id="password_baru" name="password_baru" placeholder="Masukkan password baru Anda" required>
                </div>

                <div class="form-group">
                    <label for="konfirmasi_password">Konfirmasi Password Baru</label>
                    <input type="password" id="konfirmasi_password" name="konfirmasi_password" placeholder="Ketik ulang password baru Anda" required>
                </div>

                <button type="submit" class="btn-submit">Ubah Password</button>
            </form>

        </div>
    </section>

    <?php
    // Memuat footer
    include '../../includes/anggota/footer_konsultasi_anggota.php';
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const uploadForm = document.getElementById('uploadFotoForm');
            const fotoInput = document.getElementById('foto_profil');
            const fotoPreview = document.getElementById('fotoProfilPreview');
            const notifBox = document.getElementById('notifProfil');
            const headerAvatar = document.getElementById('profilAvatarHeader'); // Avatar di halaman
            const headerAvatarNav = document.querySelector('.user-avatar'); // Avatar di navbar (dari header)

            // 1. Tampilkan Preview saat memilih foto
            fotoInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        fotoPreview.src = e.target.result;
                        fotoPreview.style.display = 'block';
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });

            // 2. Kirim Form saat di-submit
            uploadForm.addEventListener('submit', function(e) {
                e.preventDefault(); // Mencegah submit form biasa

                if (!fotoInput.files[0]) {
                    showNotif('Silakan pilih file foto terlebih dahulu.', 'error');
                    return;
                }

                const formData = new FormData();
                formData.append('foto_profil', fotoInput.files[0]);

                const submitButton = uploadForm.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.textContent = 'Mengupload...';

                fetch('../../api/update_foto_profil.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotif('Foto profil berhasil diupdate!', 'success');
                            // Update foto di halaman DAN di header secara instan
                            const newFotoPath = data.newFotoPath + '?' + new Date().getTime(); // Tambah cache buster
                            headerAvatar.src = newFotoPath;
                            if (headerAvatarNav) {
                                headerAvatarNav.src = newFotoPath;
                            }
                            fotoPreview.style.display = 'none';
                            uploadForm.reset();
                        } else {
                            showNotif(data.message || 'Gagal mengupload foto.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotif('Terjadi kesalahan jaringan.', 'error');
                    })
                    .finally(() => {
                        submitButton.disabled = false;
                        submitButton.textContent = 'Upload Foto';
                    });
            });

            // Fungsi untuk menampilkan notifikasi
            function showNotif(message, type) {
                notifBox.className = 'notification'; // Reset class
                notifBox.classList.add(type === 'success' ? 'notif-success' : 'notif-error');
                notifBox.textContent = message;
                notifBox.style.display = 'block';

                // Scroll ke atas agar notif terlihat
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }

            const profilForm = document.getElementById('profilForm');
            const profilSubmitButton = profilForm.querySelector('button[type="submit"]');

            profilForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const nama = document.getElementById('nama').value;
                const email = document.getElementById('email').value;

                if (!nama || !email) {
                    showNotif('Nama dan Email tidak boleh kosong.', 'error');
                    return;
                }

                const originalButtonText = profilSubmitButton.textContent;
                profilSubmitButton.disabled = true;
                profilSubmitButton.textContent = 'Menyimpan...';

                fetch('../../api/update_profil_info.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            nama: nama,
                            email: email
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotif(data.message, 'success');

                            // Update tampilan secara instan
                            const newNama = data.newData.nama;
                            const newEmail = data.newData.email;

                            // Update header navbar
                            document.querySelector('.user-profile .user-name').textContent = newNama;

                            // Update header di halaman profil
                            document.querySelector('.profil-header-info h2').textContent = newNama;
                            document.querySelector('.profil-header-info p').textContent = newEmail;

                        } else {
                            showNotif(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotif('Terjadi kesalahan jaringan.', 'error');
                    })
                    .finally(() => {
                        profilSubmitButton.disabled = false;
                        profilSubmitButton.textContent = originalButtonText;
                    });
            });

            const passwordForm = document.getElementById('passwordForm');
            const passwordSubmitButton = passwordForm.querySelector('button[type="submit"]');

            passwordForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const password_lama = document.getElementById('password_lama').value;
                const password_baru = document.getElementById('password_baru').value;
                const konfirmasi_password = document.getElementById('konfirmasi_password').value;

                // Validasi Sederhana di Sisi Client
                if (!password_lama || !password_baru || !konfirmasi_password) {
                    showNotif('Semua field password harus diisi.', 'error');
                    return;
                }

                if (password_baru !== konfirmasi_password) {
                    showNotif('Password baru dan konfirmasi tidak cocok.', 'error');
                    return;
                }

                const originalButtonText = passwordSubmitButton.textContent;
                passwordSubmitButton.disabled = true;
                passwordSubmitButton.textContent = 'Menyimpan...';

                fetch('../../api/update_password.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            password_lama: password_lama,
                            password_baru: password_baru,
                            konfirmasi_password: konfirmasi_password
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotif(data.message, 'success');
                            passwordForm.reset(); // Kosongkan form setelah berhasil
                        } else {
                            showNotif(data.message, 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showNotif('Terjadi kesalahan jaringan.', 'error');
                    })
                    .finally(() => {
                        passwordSubmitButton.disabled = false;
                        passwordSubmitButton.textContent = originalButtonText;
                    });
            });
        });
    </script>

</body>

</html>