<?php

include_once 'init.php';

if (!isUserLoggedIn()) {
    redirectHome();
}

$userSub = $_GET['user'] ?? '';
$userId = $_SESSION['user-id'] ?? '';
$userName = $_SESSION['user-name'];

if ($userSub && $userId) {
    if (!dbCheckSubscription($con, $userId, $userSub)) {
        dbInsSubscription($con, $userId, $userSub);
        sendEmailSub($con, $userId, $userName, $userSub);
    } else {
        dbDelSub($con, $userId, $userSub);
    }
}
