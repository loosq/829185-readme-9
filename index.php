<?php

include_once 'init.php';

$userName = $_SESSION['user-name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (isUserLoggedIn()) {
    redirect('feed.php?block=feed&tab=all');
} else {
    $html = loginFormValidation($con, $email, $password);
    echo $html;
}
