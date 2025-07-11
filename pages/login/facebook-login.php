<?php

session_start();

$env = parse_ini_file(__DIR__ . '/../../.env');

$fb_app_id = $env['FB_APP_ID'] ?? getenv('FB_APP_ID');
$redirect_uri = $env['FB_REDIRECT_URI'] ?? getenv('FB_REDIRECT_URI');

// 1. Generate state token dan simpan ke session
$state = bin2hex(random_bytes(16));
$_SESSION['fb_state'] = $state;

// 2. Redirect user ke Facebook login dengan state
$params = [
    'client_id'     => $fb_app_id,
    'redirect_uri'  => $redirect_uri,
    'state'         => $state,
    'scope'         => 'email',
    'response_type' => 'code',
];

$fb_login_url = 'https://www.facebook.com/v18.0/dialog/oauth?' . http_build_query($params);

header('Location: ' . $fb_login_url);
exit;
