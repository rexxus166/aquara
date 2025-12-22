<?php
session_start();
require '../includes/config.php'; // Hubungkan ke database

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['role_id'] == 1) {
    header('Location: index.php?page=dashboard');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? ''; // Gunakan email, bukan username
    $password = $_POST['password'] ?? '';

    // Cari user dengan email tersebut DAN role_id = 1 (Admin)
    $stmt = $conn->prepare("SELECT id, nama, password, role_id FROM users WHERE email = ? AND role_id = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Verifikasi password hash
        if (password_verify($password, $user['password'])) {
            // Login Sukses!
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role_id'] = $user['role_id']; // Penting: simpan role_id
            
            header('Location: index.php?page=dashboard');
            exit;
        } else {
            $error = 'Password salah!';
        }
    } else {
        $error = 'Email tidak ditemukan atau bukan akun admin!';
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - AQUARA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
        }
        .btn-login {
            background-color: #4ecdc4;
            border-color: #4ecdc4;
            color: white;
        }
        .btn-login:hover {
            background-color: #1a5f5f;
            border-color: #1a5f5f;
            color: white;
        }
        .logo {
            color: #1a5f5f;
            font-size: 2rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="text-center mb-4">
                <i class="fas fa-fish fa-3x text-primary mb-3"></i>
                <h2 class="logo">AQUARA Admin</h2>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope me-2"></i>Email
                    </label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-2"></i>Password
                    </label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-login w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>Masuk
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>