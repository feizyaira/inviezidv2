<?php
session_start();

$browserTitle = 'Login';

require_once __DIR__ . '/../../config/auth/logged.php';
require_once __DIR__ . '/../../config/database/config.php';

include __DIR__ . '/../../includes/components/header.php';
include __DIR__ . '/../../includes/views/auth/login.php';
include __DIR__ . '/../../includes/components/footer.php';