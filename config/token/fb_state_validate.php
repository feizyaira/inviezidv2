<?php

if (!isset($_POST['fb_state']) || $_POST['fb_state'] !== $_SESSION['fb_state']) {
    die("Fb state error");
}