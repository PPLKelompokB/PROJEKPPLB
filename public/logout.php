<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

session_unset();
session_destroy();

header('Location: login.php?status=logged_out');
exit;
