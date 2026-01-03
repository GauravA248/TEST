<?php
session_start();

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'user'
) {
    header("Location: /TEST/login.php");
    exit;
}
