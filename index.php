<?php

define('IN_APP', true);
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

switch ($path) {
    case '':
    case '/':
    case 'dashboard':
        require __DIR__ . '/pages/dashboard/index.php';
        break;
    case 'login':
        require __DIR__ . '/pages/login/index.php';
        break;
    case 'fb-login':
        require __DIR__ . '/pages/login/facebook-login.php';
        break;
    case 'google-login':
        require __DIR__ . '/pages/login/google-login.php';
        break;
    case 'fbcallback':
        require __DIR__ . '/config/callback/facebook-callback.php';
        break;
    case 'gcallback':
        require __DIR__ . '/config/callback/google-callback.php';
        break;
    case 'signup-from-facebook':
        require __DIR__ . '/pages/signup-from-facebook/index.php';
        break;
    case 'signup-from-google':
        require __DIR__ . '/pages/signup-from-google/index.php';
        break;
    // Default
    default:
        http_response_code(404);
        require __DIR__ . '/pages/404/index.php';
}