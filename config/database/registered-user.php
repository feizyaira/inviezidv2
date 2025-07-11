<?php
require_once __DIR__ . '/config.php';

try {
    $existUser = $pdo->prepare("SELECT COUNT(*) FROM sellers WHERE seller_email = :seller_email");
    $existUser->execute([':seller_email' => $sellerEmail]);
    $seller = $existUser->fetch();
    $checkUser = $existUser->fetchColumn() > 0;
} catch (Exception $e) {
    die("Something went wrong. " . $e->getMessage());
}