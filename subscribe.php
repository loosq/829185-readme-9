<?php

include_once 'init.php';

if (!isUserLoggedIn()) {
    redirectHome();
}

$userSub = $_GET['user'];
$userId = $_SESSION['user-id'];

if (!db_check_subscription($con, $userId , $userSub)) {
    db_ins_subscription($con, $userId, $userSub);
} else {
    db_del_sub($con, $userId , $userSub);
}
redirectBack();
