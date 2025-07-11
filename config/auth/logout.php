<?php
session_start();
require_once __DIR__ . '/../token/csrf_validate.php';
session_destroy();
header('Location: /login');
exit;