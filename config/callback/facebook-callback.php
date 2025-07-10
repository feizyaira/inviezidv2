<?php
session_start();

$env = parse_ini_file(__DIR__ . '/../../.env');

$fb_app_id = $env['FB_APP_ID'] ?? getenv('FB_APP_ID');
$fb_app_secret = $env['FB_APP_SECRET'] ?? getenv('FB_APP_SECRET');
$redirect_uri = $env['FB_REDIRECT_URI'] ?? getenv('FB_REDIRECT_URI');

if (isset($_GET['code'])) {
    // Validate state parameter for CSRF protection
    if (!isset($_GET['state']) || !hash_equals($_SESSION['fb_state'] ?? '', $_GET['state'])) {
        die('Invalid state parameter');
    }
    
    // Validate authorization code
    if (empty($_GET['code']) || !is_string($_GET['code'])) {
        die('Invalid authorization code');
    }

    $code = $_GET['code'];

    $token_url = "https://graph.facebook.com/v18.0/oauth/access_token?" . http_build_query([
        'client_id'     => $fb_app_id,
        'redirect_uri'  => $redirect_uri,
        'client_secret' => $fb_app_secret,
        'code'          => $code
    ]);
    $context = stream_context_create([
        'http' => [
            'timeout' => 30,
            'method'  => 'GET'
        ]
    ]);
    $response = @file_get_contents($token_url, false, $context);
    if ($response === false) {
        die('Failed to connect to Facebook API');
    }
    $data = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die('Invalid response from Facebook API');
    }

    if (isset($data['access_token'])) {
        $access_token = $data['access_token'];

        $graph_url = "https://graph.facebook.com/me?fields=id,name,email&access_token=$access_token";
        $user_response = @file_get_contents($graph_url, false, $context);
        if ($user_response === false) {
            die('Failed to fetch user data from Facebook');
        }
        
        $user = json_decode($user_response, true);
        if (json_last_error() !== JSON_ERROR_NONE || !isset($user['id'])) {
            die('Invalid user data received from Facebook');
        }

        $fb_id = $user['id'] ?? '';
        $fb_name = $user['name'] ?? '';
        $fb_email = $user['email'] ?? '';

        if (!$fb_email) {
            $_SESSION['fb_id'] = $fb_id;
            $_SESSION['fb_name'] = $fb_name;

            header('Location: /signup-from-facebook');
            exit;
        }

        $sellerEmail = $fb_email;
        require_once __DIR__ . '/../database/registered-user.php';

        if ($checkUser) {
            header('Location: /signup-from-facebook');
            exit;
        } else {
            header('Location: /dashboard');
            exit;
        }
        
    } else {
        error_log('Facebook OAuth: Failed to obtain access token - ' . json_encode($data));
        die('Authentication failed. Please try again.');
    }
} else {
    error_log('Facebook OAuth: No authorization code received');
    die('Invalid authentication request');
}