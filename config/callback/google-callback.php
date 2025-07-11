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

unset($_SESSION['google_state']);

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
$g_id = trim($gaccount['sub']);
$g_name = trim($gaccount['name']);
$g_email = trim($gaccount['email']);

require_once __DIR__ . '/../database/config.php';
require_once __DIR__ . '/../token/csrf_token.php';

$refresh_token = $data['refresh_token'] ?? null;
if ($refresh_token) {
    // Simpan ke DB
    $save = $pdo->prepare("UPDATE sellers SET google_refresh_token = ? WHERE seller_gid = ?");
    $save->execute([$refresh_token, $g_id]);
}

if (empty($g_id) || empty($g_name) || empty($g_email)) {
    error_log('Failed to retrieve data from Google. - ' . json_encode($data));
    die("Failed to retrieve data from Google");
}

if (!filter_var($g_email, FILTER_VALIDATE_EMAIL)) {
    error_log('There is something wrong while retrieving data. - ' . json_encode($data));
    die("Something wrong while retrieving data");
}

if (!empty($g_id) && !empty($g_email)) {
    $checkUser = $pdo->prepare("SELECT seller_email FROM sellers WHERE seller_email = ?");
    $checkUser->execute([$g_email]);

    if ($checkUser->fetchColumn() > 0) {

        $_SESSION['logged'] = $g_id;

        header('Location: /dashboard');
        exit;
    } else {
        $insertData = $pdo->prepare("INSERT INTO sellers (seller_gid, seller_name, seller_email) VALUES (?, ?, ?)");
        $insertData->execute([$g_id, $g_name, $g_email]);

        $_SESSION['logged'] = $g_id;

        header('Location: /dashboard');
        exit;
    }
}