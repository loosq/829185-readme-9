<?php

include_once 'init.php';

$userName = $_SESSION['user-name'] ?? '';
$title = 'readme: блог, каким он должен быть';
$userEmail = $_POST['email'] ?? '';
$userPassword = $_POST['password'] ?? '';

if (isUserLoggedIn()) {
    redirect('feed.php?block=feed&tab=all');
} else {
    if ($userEmail || $userPassword) {
        $html = loginFormValidation($con, $userEmail, $userPassword);
    } else {
        $html = include_template('index.php', [
            'userEmail'    => $userEmail,
            'userPassword' => $userPassword,
            ]);
    }

    echo $html;
}
