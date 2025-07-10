<?php

$env = parse_ini_file(__DIR__ . '/../../.env');

$host = $env['DB_HOST'] ?? getenv('DB_HOST');
$db = $env['DB_NAME'] ?? getenv('DB_NAME');
$user = $env['DB_USER'] ?? getenv('DB_USER');;
$pass = $env['DB_PASS'] ?? getenv('DB_PASS');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}