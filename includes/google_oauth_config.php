<?php
define('GOOGLE_CLIENT_ID', '555355946734-16v7l4ciivadcu9d2cmdc0f4nfk8dpod.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];

// Jika di Localhost, pakai folder '/aquara', jika di Hosting (subdomain) langsung '/views...'
$path = ($host == 'localhost' || $host == '127.0.0.1') ? '/aquara' : '';

define('GOOGLE_REDIRECT_URI', $protocol . '://' . $host . $path . '/views/google_callback.php');
