<?php

$registered_fb_id = $_SESSION['fb_id'] ?? '';
$registered_fb_name = $_SESSION['fb_name'] ?? '';
$registered_fb_email = $_SESSION['fb_email'] ?? '';

$errors = [];

if (isset($_POST['fbsignup']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../../config/token/csrf_validate.php';

    $sellerFbId = trim($_POST['seller_fb_id']);
    $sellerName = trim($_POST['seller_name']);
    $sellerEmail = trim($_POST['seller_email']);
    $sellerPwd = $_POST['seller_pwd'];

    if (empty($_POST['csrf_token'])) {
        unset($_SESSION['fb_id']);
        unset($_SESSION['fb_name']);
        unset($_SESSION['fb_email']);
        session_destroy();
        header('Location: /login');
        exit;
    }

    if (empty($sellerFbId)) {
        unset($_SESSION['fb_id']);
        unset($_SESSION['fb_name']);
        unset($_SESSION['fb_email']);
        session_destroy();
        header('Location: /register');
        exit;
    }

    
}

?>

<h1>Facebook Register</h1>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>"/>
    <input type="hidden" name="seller_fb_id" value="<?= htmlspecialchars($registered_fb_id) ?>"/>

    <input type="text" name="seller_name" value="<?= htmlspecialchars($registered_fb_name) ?>" placeholder="Name" required/>
    <input type="email" name="seller_email" value="<?= htmlspecialchars($registered_fb_email) ?>" placeholder="Email" required/>
    <input type="password" name="seller_pwd" placeholder="Password" required/>
    <input type="password" name="seller_confirm" placeholder="Confirm Password" required/>

    <button type="submit" name="fbsignup">Register</button>
</form>