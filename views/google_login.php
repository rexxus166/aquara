<?php
session_start();

require_once __DIR__ . '/../includes/google_oauth_config.php';

echo "CONFIG LOADED: " . realpath(__DIR__ . '/../includes/google_oauth_config.php') . "<br>";
echo "DEFINED? "; var_dump(defined('GOOGLE_CLIENT_ID'));

// CSRF protection
$state = bin2hex(random_bytes(16));
$_SESSION['google_oauth_state'] = $state;

$params = [
  'client_id'     => GOOGLE_CLIENT_ID,
  'redirect_uri'  => GOOGLE_REDIRECT_URI,
  'response_type' => 'code',
  'scope'         => 'openid email profile',
  'prompt'        => 'select_account',
  'state'         => $state,
];

$url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
header("Location: $url");
exit;
