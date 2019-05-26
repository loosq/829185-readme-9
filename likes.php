<?php

include_once 'init.php';

$userId = $_SESSION['user-id'];
$postId = $_GET['postId'];
$getLike = db_get_like($con, $postId, $userId);

if ($getLike) {
    db_del_like($con, $postId, $userId);
    return true;
} else {
    db_add_like($con, $postId, $userId);
    return false;
}
