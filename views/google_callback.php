<?php
session_start();
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/google_oauth_config.php';

function redirectTo($url){
  header("Location: $url");
  exit;
}

$state = (string)($_GET['state'] ?? '');
if ($state === '' || !isset($_SESSION['google_oauth_state']) || $state !== $_SESSION['google_oauth_state']) {
  redirectTo('../index.php?page=login&error=Invalid+state');
}
unset($_SESSION['google_oauth_state']);

$code = (string)($_GET['code'] ?? '');
if ($code === '') {
  redirectTo('../index.php?page=login&error=Google+login+dibatalkan');
}

$token_url = 'https://oauth2.googleapis.com/token';
$post = [
  'code'          => $code,
  'client_id'     => GOOGLE_CLIENT_ID,
  'client_secret' => GOOGLE_CLIENT_SECRET,
  'redirect_uri'  => GOOGLE_REDIRECT_URI,
  'grant_type'    => 'authorization_code',
];

$ch = curl_init($token_url);
curl_setopt_array($ch, [
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => http_build_query($post),
  CURLOPT_RETURNTRANSFER => true,
]);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
if (!isset($data['id_token'])) {
  redirectTo('../index.php?page=login&error=Token+tidak+valid');
}

// Decode JWT payload (untuk localhost)
$parts = explode('.', $data['id_token']);
$payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);

$email = (string)($payload['email'] ?? '');
$nama  = (string)($payload['name'] ?? $email);
$foto  = (string)($payload['picture'] ?? '');

if ($email === '') {
  redirectTo('../index.php?page=login&error=Email+tidak+tersedia');
}

// cek user
$stmt = $conn->prepare("SELECT id, nama, email, role_id, foto_profil FROM users WHERE email=? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
  $u = $res->fetch_assoc();
  $_SESSION['user_id'] = (int)$u['id'];
  $_SESSION['nama'] = $u['nama'];
  $_SESSION['email'] = $u['email'];
  $_SESSION['role_id'] = (int)$u['role_id'];
  $_SESSION['foto_profil'] = $u['foto_profil'] ?: $foto;
  $_SESSION['logged_in'] = true;
} else {
  // auto register anggota (role 2)
  $role_id = 2;
  $randomPass = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);

  // Kalau kolom foto_profil kamu ada, pakai ini:
  $ins = $conn->prepare("INSERT INTO users (nama, email, password, role_id, foto_profil) VALUES (?, ?, ?, ?, ?)");
  $ins->bind_param("sssis", $nama, $email, $randomPass, $role_id, $foto);

  if (!$ins->execute()) {
    redirectTo('../index.php?page=login&error=Gagal+daftar+user+Google');
  }

  $_SESSION['user_id'] = (int)$ins->insert_id;
  $_SESSION['nama'] = $nama;
  $_SESSION['email'] = $email;
  $_SESSION['role_id'] = $role_id;
  $_SESSION['foto_profil'] = $foto;
  $_SESSION['logged_in'] = true;
}

// Redirect sesuai struktur kamu (SAMA seperti login manual)
$role = (int)$_SESSION['role_id'];
if ($role === 1) redirectTo('../views/admin/dashboard.php');
if ($role === 2) redirectTo('../views/anggota/index_anggota.php');
if ($role === 3) redirectTo('../views/pakar/index_pakar.php');

redirectTo('../index.php?page=home');
