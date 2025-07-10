<?php
session_start();

$env = parse_ini_file(__DIR__ . '/../../.env');

$google_client_id = $env['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID');
$google_client_secret = $env['GOOGLE_CLIENT_SECRET'] ?? getenv('GOOGLE_CLIENT_SECRET');
$redirect_uri = $env['GOOGLE_REDIRECT_URI'] ?? getenv('GOOGLE_REDIRECT_URI');

// Validate state
if (!isset($_GET['state']) || !hash_equals($_SESSION['google_state'] ?? '', $_GET['state'])) {
    die('Invalid state parameter');
}

// Validate authorization code
if (empty($_GET['code']) || !is_string($_GET['code'])) {
    die('Invalid authorization code');
}

$code = $_GET['code'];

// 1. Tukar code dengan access_token
$token_response = file_get_contents('https://oauth2.googleapis.com/token', false, stream_context_create([
    'http' => [
        'method'  => 'POST',
        'header'  => 'Content-type: application/x-www-form-urlencoded',
        'content' => http_build_query([
            'code'          => $code,
            'client_id'     => $google_client_id,
            'client_secret' => $google_client_secret,
            'redirect_uri'  => $redirect_uri,
            'grant_type'    => 'authorization_code'
        ])
    ]
]));

$data = json_decode($token_response, true);
if (!isset($data['access_token'])) {
    die('Failed to obtain access token');
}

$access_token = $data['access_token'];

// 2. Ambil data user
$user_response = file_get_contents("https://www.googleapis.com/oauth2/v3/userinfo?access_token=$access_token");
$gaccount = json_decode($user_response, true);

if (!isset($gaccount['email'])) {
    die('Failed to get user info from Google');
}

// Simpan user ke DB, login, atau redirect sesuai status
$sellerEmail = $gaccount['email'];
require_once __DIR__ . '/../database/registered-user.php';

if ($checkUser) {
    $_SESSION['google_email'] = $gaccount['email'];
    $_SESSION['google_name'] = $gaccount['name'] ?? '';
    $_SESSION['google_id'] = $gaccount['sub'] ?? '';
    header('Location: /signup-from-google');
    exit;
} else {
    $_SESSION['logged'] = $seller['seller_id'];
    // Contoh: Redirect ke dashboard
    header('Location: /dashboard');
    exit;
}
