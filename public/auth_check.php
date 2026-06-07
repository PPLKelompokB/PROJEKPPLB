<?php
session_start();

function require_login()
{
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php?message=login_required');
        exit;
    }
}

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');
