<?php
require_once __DIR__ . '/config.php';

try {
    $existUser = $pdo->prepare("SELECT * FROM sellers WHERE seller_email = :seller_email");
    $existUser->execute([':seller_email' => $sellerEmail]);
    $checkUser = $existUser->fetchColumn();
} catch (Exception $e) {
    die("Something went wrong. " . $e->getMessage());
}