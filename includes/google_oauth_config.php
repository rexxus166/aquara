<?php
define('GOOGLE_CLIENT_ID', '555355946734-16v7l4ciivadcu9d2cmdc0f4nfk8dpod.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', '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');
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
define('GOOGLE_REDIRECT_URI', $protocol . '://' . $host . '/aquara/views/google_callback.php');
