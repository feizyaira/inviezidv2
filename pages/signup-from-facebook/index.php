<?php
session_start();
if (empty($_SESSION['fb_id']) || empty($_SESSION['fb_name'])) {
    header('Location: /register');
    exit;
}

$browserTitle = "Facebook Registration";

require_once __DIR__ . '/../../config/database/config.php';
require_once __DIR__ . '/../../config/token/csrf_token.php';

include __DIR__ . '/../../includes/components/header.php';
include __DIR__ . '/../../includes/contents/signup-facebook/main.php';
include __DIR__ . '/../../includes/components/footer.php';