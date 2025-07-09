<?php

if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Something went wrong");
}