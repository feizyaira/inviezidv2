<?php
session_start();

$env = parse_ini_file(__DIR__ . '/../../.env');

$google_client_id = $env['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID');
$redirect_uri = $env['GOOGLE_REDIRECT_URI'] ?? getenv('GOOGLE_REDIRECT_URI');

// 1. Generate state token & simpan ke session
require_once __DIR__ . '/../../config/token/csrf_token.php';
$state = $csrf_token;
$_SESSION['google_state'] = $state;

// 2. Bangun URL redirect ke Google
$params = [
    'client_id'     => $google_client_id,
    'redirect_uri'  => $redirect_uri,
    'response_type' => 'code',
    'scope'         => 'openid email profile',
    'state'         => $state,
    'access_type'   => 'offline',
    'prompt'        => 'consent', // biar token refresh juga dapet
];

$google_login_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);

header('Location: ' . $google_login_url);
exit;
