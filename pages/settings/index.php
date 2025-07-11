<?php
session_start();

$browserTitle = 'Settings';

require_once __DIR__ . '/../../config/auth/not-logged.php';
require_once __DIR__ . '/../../config/database/config.php';

include __DIR__ . '/../../includes/components/header.php';
include __DIR__ . '/../../includes/views/settings/settings.php';
include __DIR__ . '/../../includes/components/footer.php';