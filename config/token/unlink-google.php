<?php
session_start();
require_once __DIR__ . '/../database/config.php';
require_once __DIR__ . '/csrf_token.php';

// Pastikan user login
if (!isset($_SESSION['logged'])) {
    header('Location: /login');
    exit;
}

$user_gid = $_SESSION['logged'];

$env = parse_ini_file(__DIR__ . '/../../.env');
$google_client_id = $env['GOOGLE_CLIENT_ID'] ?? getenv('GOOGLE_CLIENT_ID');
$google_client_secret = $env['GOOGLE_CLIENT_SECRET'] ?? getenv('GOOGLE_CLIENT_SECRET');

// Step 1: Ambil refresh token dari DB
$get = $pdo->prepare("SELECT google_refresh_token FROM sellers WHERE seller_gid = ?");
$get->execute([$user_gid]);
$refresh_token = $get->fetchColumn();

// Step 2: Refresh access token
$access_token = null;
if ($refresh_token) {
    $token_url = 'https://oauth2.googleapis.com/token';
    $data = http_build_query([
        'client_id'     => $google_client_id,
        'client_secret' => $google_client_secret,
        'refresh_token' => $refresh_token,
        'grant_type'    => 'refresh_token',
    ]);

    $context = stream_context_create([
        'http' => [
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => $data
        ]
    ]);

    $result = @file_get_contents($token_url, false, $context);
    if ($result !== false) {
        $response = json_decode($result, true);
        $access_token = $response['access_token'] ?? null;
    }
}

// Step 3: Revoke token ke Google
if ($access_token) {
    $revoke_url = 'https://oauth2.googleapis.com/revoke?token=' . $access_token;
    @file_get_contents($revoke_url, false, stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
        ]
    ]));
}
if (isset($POST['unlinkGoogle']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/csrf_validate.php';
    // Step 4: Update database - hapus GID dan refresh token
    $update = $pdo->prepare("UPDATE sellers SET seller_gid = NULL, google_refresh_token = NULL WHERE seller_gid = ?");
    $update->execute([$user_gid]);

    // Step 5: Bersihin session dan redirect
    unset($_SESSION['google_token']);
    unset($_SESSION['google_state']);

    $_SESSION['flash'] = [
        'type' => 'success',
        'message' => 'Akun Google kamu telah berhasil diputuskan dari sistem.'
    ];

    header('Location: /settings');
    exit;
}
