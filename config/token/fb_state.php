<?php

if (empty($_SESSION['fb_state'])) {
    $_SESSION['fb_state'] = bin2hex(random_bytes(32));
}
$fb_state = $_SESSION['fb_state'];