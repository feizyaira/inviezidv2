<?php
session_start();

$browserTitle = 'Register';

require_once __DIR__ . '/../../config/auth/logged.php';
require_once __DIR__ . '/../../config/database/config.php';

include __DIR__ . '/../../includes/components/header.php';
include __DIR__ . '/../../includes/views/auth/register.php';
include __DIR__ . '/../../includes/components/footer.php';