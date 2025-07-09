<?php

define('IN_APP', true);
$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

switch ($path) {
    case '':
    case '/':
    case '/dashboard':
        require __DIR__ . '/pages/dashboard/index.php';
        break;
    case 'login':
        require __DIR__ . '/pages/login/index.php';
        break;
    // Default
    default:
        http_response_code(404);
        require __DIR__ . '/pages/404/index.php';
}