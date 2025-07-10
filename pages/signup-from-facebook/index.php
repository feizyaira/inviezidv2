<?php
session_start();

require_once __DIR__ . '/../../config/database/config.php';
require_once __DIR__ . '/../../config/token/csrf_token.php';

$registered_fb_id = $_SESSION['fb_id'] ?? '';
$registered_fb_name = $_SESSION['fb_name'] ?? '';
$registered_fb_email = $_SESSION['fb_email'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <h1>Facebook Register</h1>

    <form method="POST">
        <input type="hidden" name="csrf__token" value="<?= htmlspecialchars($csrf_token ?? '') ?>"/>
        <input type="hidden" name="seller_fb_id" value="<?= htmlspecialchars($registered_fb_id) ?>"/>

        <input type="text" name="seller_name" value="<?= htmlspecialchars($registered_fb_name) ?>" placeholder="Name" required/>
        <input type="email" name="seller_email" value="<?= htmlspecialchars($registered_fb_email) ?>" placeholder="Email" required/>
        <input type="password" name="seller_pwd" placeholder="Password" required/>

        <button type="submit">Register</button>
    </form>
</body>
</html>